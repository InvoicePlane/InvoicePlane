<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use FI\Events\InvoiceEmailed;
use FI\Events\InvoiceEmailing;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\MailQueue\Support\MailQueue;
use FI\Requests\SendEmailRequest;
use FI\Support\Contacts;
use FI\Support\Parser;

class InvoiceMailController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function create()
    {
        $invoice = Invoice::find(request('invoice_id'));

        $contacts = new Contacts($invoice->client);

        $parser = new Parser($invoice);

        if (!$invoice->is_overdue)
        {
            $subject = $parser->parse('invoiceEmailSubject');
            $body    = $parser->parse('invoiceEmailBody');
        }
        else
        {
            $subject = $parser->parse('overdueInvoiceEmailSubject');
            $body    = $parser->parse('overdueInvoiceEmailBody');
        }

        return view('invoices._modal_mail')
            ->with('invoiceId', $invoice->id)
            ->with('redirectTo', urlencode(request('redirectTo')))
            ->with('subject', $subject)
            ->with('body', $body)
            ->with('contactDropdownTo', $contacts->contactDropdownTo())
            ->with('contactDropdownCc', $contacts->contactDropdownCc())
            ->with('contactDropdownBcc', $contacts->contactDropdownBcc());
    }

    public function store(SendEmailRequest $request)
    {
        $invoice = Invoice::find($request->input('invoice_id'));

        event(new InvoiceEmailing($invoice));

        $mail = $this->mailQueue->create($invoice, $request->except('invoice_id'));

        if ($this->mailQueue->send($mail->id))
        {
            event(new InvoiceEmailed($invoice));
        }
        else
        {
            return response()->json(['errors' => [[$this->mailQueue->getError()]]], 400);
        }
    }
}