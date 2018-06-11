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

namespace FI\Modules\MailQueue\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\MailQueue\Models\MailQueue;

class MailLogController extends Controller
{
    public function index()
    {
        $mails = MailQueue::sortable(['created_at' => 'desc'])
            ->keywords(request('search'))
            ->paginate(config('fi.resultsPerPage'));

        return view('mail_log.index')
            ->with('mails', $mails)
            ->with('displaySearch', true);
    }

    public function content()
    {
        $mail = MailQueue::select('subject', 'body')
            ->where('id', request('id'))
            ->first();

        return view('mail_log._modal_content')
            ->with('mail', $mail);
    }

    public function delete($id)
    {
        MailQueue::destroy($id);

        return redirect()->route('mailLog.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }
}