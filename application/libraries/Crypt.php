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
    /**
     * @return bool|string
     */
    public function salt()
    {
        return mb_substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function generate_password($password, $salt)
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    /**
     * @param string $hash
     * @param string $password
     *
     * @return bool
     */
    public function check_password($hash, $password)
    {
        $new_hash = crypt($password, $hash);

        return $hash == $new_hash;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function encode($data)
    {
        $key = getenv('ENCRYPTION_KEY');
        if (preg_match('/^base64:(.*)$/', $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        return Cryptor::Encrypt($data, $key);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function decode($data)
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
