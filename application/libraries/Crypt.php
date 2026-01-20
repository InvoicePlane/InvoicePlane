<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Crypt
{
    public function salt(): string
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * @param string $password
     *
     */
    public function generate_password($password, string $salt): string
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    /**
     * @param string $hash
     * @param string $password
     */
    public function check_password($hash, $password): bool
    {
        $new_hash = crypt($password, $hash);

        return $hash == $new_hash;
    }

    /**
     * @param string $data
     */
    public function encode($data): string
    {
        $key = getenv('ENCRYPTION_KEY');
        if (preg_match('/^base64:(.*)$/', $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        return Cryptor::Encrypt($data, $key);
    }

    /**
     * @param string $data
     */
    public function decode($data): string
    {
        if (empty($data)) {
            return '';
        }

        $key = getenv('ENCRYPTION_KEY');
        if (preg_match('/^base64:(.*)$/', $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        return Cryptor::Decrypt($data, $key);
    }
}
