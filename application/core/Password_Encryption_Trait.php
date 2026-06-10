<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Password Encryption Trait.
 *
 * Shared encrypt/decrypt logic for models that store PDF passwords at rest.
 * Used by Mdl_Invoices and Mdl_Quotes.
 */
trait Password_Encryption_Trait
{
    /**
     * Encrypt a PDF password for storage. Returns null for empty input.
     */
    protected function encrypt_password(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

        $this->load->library('crypt');

        return $this->crypt->encode($password);
    }

    /**
     * Decrypt a stored PDF password.
     *
     * Falls back to returning the raw value when decryption fails so that
     * invoices created before at-rest encryption was introduced continue to
     * work until they are next saved (at which point save() encrypts them).
     */
    protected function decrypt_password(?string $password): string
    {
        if ($password === null || $password === '') {
            return '';
        }

        $this->load->library('crypt');

        try {
            $decoded = $this->crypt->decode($password);

            return is_string($decoded) ? $decoded : '';
        } catch (\Exception $e) {
            // Value is likely plaintext from before at-rest encryption was
            // added; return as-is so existing invoices stay accessible.
            return $password;
        }
    }
}
