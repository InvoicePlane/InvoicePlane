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

namespace IP\Modules\MailQueue\Support;

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
            'smtp' => trans('ip.email_send_method_smtp'),
            'mail' => trans('ip.email_send_method_phpmail'),
            'sendmail' => trans('ip.email_send_method_sendmail'),
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
            '0' => trans('ip.none'),
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ];
    }
}