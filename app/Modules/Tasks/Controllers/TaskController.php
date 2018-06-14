<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Modules\Tasks\Controllers;

use Carbon\Carbon;
use IP\Events\InvoiceCreatedRecurring;
use IP\Events\OverdueNoticeEmailed;
use IP\Http\Controllers\Controller;
use IP\Modules\CustomFields\Models\CustomField;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Invoices\Models\InvoiceItem;
use IP\Modules\MailQueue\Support\MailQueue;
use IP\Modules\RecurringInvoices\Models\RecurringInvoice;
use IP\Support\DateFormatter;
use IP\Support\Parser;
use IP\Support\Statuses\InvoiceStatuses;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function run()
    {
        $this->queueOverdueInvoices();

        $this->queueUpcomingInvoices();

        $this->recurInvoices();
    }

    private function queueOverdueInvoices()
    {
        $days = config('fi.overdueInvoiceReminderFrequency');

        if ($days) {
            $days = explode(',', $days);

            foreach ($days as $daysAgo) {
                $daysAgo = trim($daysAgo);

                if (is_numeric($daysAgo)) {
                    $daysAgo = intval($daysAgo);

                    $date = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

                    $invoices = Invoice::with('client')
                        ->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('sent'))
                        ->whereHas('amount', function ($query) {
                            $query->where('balance', '>', '0');
                        })
                        ->where('due_at', $date)
                        ->get();

                    Log::info('FI::MailQueue - Invoices found due ' . $daysAgo . ' days ago on ' . $date . ': ' . $invoices->count());

                    foreach ($invoices as $invoice) {
                        $parser = new Parser($invoice);

                        $mail = $this->mailQueue->create($invoice, [
                            'to' => [$invoice->client->email],
                            'cc' => [config('fi.mailDefaultCc')],
                            'bcc' => [config('fi.mailDefaultBcc')],
                            'subject' => $parser->parse('overdueInvoiceEmailSubject'),
                            'body' => $parser->parse('overdueInvoiceEmailBody'),
                            'attach_pdf' => config('fi.attachPdf'),
                        ]);

                        $this->mailQueue->send($mail->id);

                        event(new OverdueNoticeEmailed($invoice, $mail));
                    }
                } else {
                    Log::info('FI::MailQueue - Invalid overdue indicator: ' . $daysAgo);
                }
            }
        }
    }

    private function queueUpcomingInvoices()
    {
        $days = config('fi.upcomingPaymentNoticeFrequency');

        if ($days) {
            $days = explode(',', $days);

            foreach ($days as $daysFromNow) {
                $daysFromNow = trim($daysFromNow);

                if (is_numeric($daysFromNow)) {
                    $daysFromNow = intval($daysFromNow);

                    $date = Carbon::now()->addDays($daysFromNow)->format('Y-m-d');

                    $invoices = Invoice::with('client')
                        ->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('sent'))
                        ->whereHas('amount', function ($query) {
                            $query->where('balance', '>', '0');
                        })
                        ->where('due_at', $date)
                        ->get();

                    Log::info('FI::MailQueue - Invoices found due ' . $daysFromNow . ' days from now on ' . $date . ': ' . $invoices->count());

                    foreach ($invoices as $invoice) {
                        $parser = new Parser($invoice);

                        $mail = $this->mailQueue->create($invoice, [
                            'to' => [$invoice->client->email],
                            'cc' => [config('fi.mailDefaultCc')],
                            'bcc' => [config('fi.mailDefaultBcc')],
                            'subject' => $parser->parse('upcomingPaymentNoticeEmailSubject'),
                            'body' => $parser->parse('upcomingPaymentNoticeEmailBody'),
                            'attach_pdf' => config('fi.attachPdf'),
                        ]);

                        $this->mailQueue->send($mail->id);
                    }
                } else {
                    Log::info('FI::MailQueue - Upcoming payment due indicator: ' . $daysFromNow);
                }
            }
        }
    }

    private function recurInvoices()
    {
        $recurringInvoices = RecurringInvoice::recurNow()->get();

        foreach ($recurringInvoices as $recurringInvoice) {
            $invoiceData = [
                'company_profile_id' => $recurringInvoice->company_profile_id,
                'created_at' => $recurringInvoice->next_date,
                'group_id' => $recurringInvoice->group_id,
                'user_id' => $recurringInvoice->user_id,
                'client_id' => $recurringInvoice->client_id,
                'currency_code' => $recurringInvoice->currency_code,
                'template' => $recurringInvoice->template,
                'terms' => $recurringInvoice->terms,
                'footer' => $recurringInvoice->footer,
                'summary' => $recurringInvoice->summary,
                'discount' => $recurringInvoice->discount,
            ];

            $invoice = Invoice::create($invoiceData);

            CustomField::copyCustomFieldValues($recurringInvoice, $invoice);

            foreach ($recurringInvoice->recurringInvoiceItems as $item) {
                $itemData = [
                    'invoice_id' => $invoice->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'tax_rate_id' => $item->tax_rate_id,
                    'tax_rate_2_id' => $item->tax_rate_2_id,
                    'display_order' => $item->display_order,
                ];

                InvoiceItem::create($itemData);
            }

            if ($recurringInvoice->stop_date == '0000-00-00' or ($recurringInvoice->stop_date !== '0000-00-00' and ($recurringInvoice->next_date < $recurringInvoice->stop_date))) {
                $nextDate = DateFormatter::incrementDate(substr($recurringInvoice->next_date, 0, 10), $recurringInvoice->recurring_period, $recurringInvoice->recurring_frequency);
            } else {
                $nextDate = '0000-00-00';
            }

            $recurringInvoice->next_date = $nextDate;
            $recurringInvoice->save();

            event(new InvoiceCreatedRecurring($invoice, $recurringInvoice));
        }

        return count($recurringInvoices);
    }
}