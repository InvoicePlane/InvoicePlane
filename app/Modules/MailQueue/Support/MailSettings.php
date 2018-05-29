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

namespace FI\Modules\MailQueue\Support;

class MailSettings
{
    /**
     * Provide a list of send methods.
     *
     * @return array
     */
    static function listSendMethods()
    {
        return [
            '' => '',
            'smtp' => trans('fi.email_send_method_smtp'),
            'mail' => trans('fi.email_send_method_phpmail'),
            'sendmail' => trans('fi.email_send_method_sendmail'),
        ];
    }

    /**
     * Provide a list of encryption methods.
     *
     * @return array
     */
    static function listEncryptions()
    {
        return [
            '0' => trans('fi.none'),
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ];
    }
}