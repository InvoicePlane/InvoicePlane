<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            ''         => '',
            'smtp'     => trans('fi.email_send_method_smtp'),
            'mail'     => trans('fi.email_send_method_phpmail'),
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
            '0'   => trans('fi.none'),
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ];
    }
}