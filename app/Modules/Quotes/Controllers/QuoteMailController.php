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

namespace IP\Modules\Quotes\Controllers;

use IP\Events\QuoteEmailed;
use IP\Events\QuoteEmailing;
use IP\Http\Controllers\Controller;
use IP\Modules\MailQueue\Support\MailQueue;
use IP\Modules\Quotes\Models\Quote;
use IP\Requests\SendEmailRequest;
use IP\Support\Contacts;
use IP\Support\Parser;

class QuoteMailController extends Controller
{
    private $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function create()
    {
        $quote = Quote::find(request('quote_id'));

        $contacts = new Contacts($quote->client);

        $parser = new Parser($quote);

        return view('quotes._modal_mail')
            ->with('quoteId', $quote->id)
            ->with('redirectTo', urlencode(request('redirectTo')))
            ->with('subject', $parser->parse('quoteEmailSubject'))
            ->with('body', $parser->parse('quoteEmailBody'))
            ->with('contactDropdownTo', $contacts->contactDropdownTo())
            ->with('contactDropdownCc', $contacts->contactDropdownCc())
            ->with('contactDropdownBcc', $contacts->contactDropdownBcc());
    }

    public function store(SendEmailRequest $request)
    {
        $quote = Quote::find($request->input('quote_id'));

        event(new QuoteEmailing($quote));

        $mail = $this->mailQueue->create($quote, $request->except('quote_id'));

        if ($this->mailQueue->send($mail->id)) {
            event(new QuoteEmailed($quote));
        } else {
            return response()->json(['errors' => [[$this->mailQueue->getError()]]], 400);
        }
    }
}