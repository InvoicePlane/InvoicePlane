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
        // Load project-specific crypto helper for cryptographically secure token generation
        $CI = &get_instance();
        $CI->load->helper('ip_security');

        return generate_secure_salt();
    }

    /**
     * Hashes a password using bcrypt ($2y$).
     * The $salt parameter is retained for API compatibility but is ignored;
     * PHP's password_hash() generates a cryptographically secure salt
     * internally and embeds it in the returned hash.
     *
     * @param string $password
     */
    public function generate_password($password, string $salt = ''): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifies a password against a stored hash.
     * password_verify() handles both legacy $2a$ and current $2y$ hashes
     * transparently, so no migration of existing rows is required.
     *
     * @param string $hash
     * @param string $password
     */
    public function check_password($hash, $password): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @param string $data
     */
    public function encode($data): string
    {
        return Cryptor::Encrypt($data, $this->getEncryptionKey());
    }

    /**
     * @param string $data
     */
    public function decode($data): string
    {
        if (empty($data)) {
            return '';
        }

        return Cryptor::Decrypt($data, $this->getEncryptionKey());
    }

    /**
     * Get the encryption key, decoding if it's base64-encoded.
     *
     * @return string The encryption key
     */
    private function getEncryptionKey(): string
    {
        // env(), not getenv(): Dotenv (bootstrap/kernel.php) only populates
        // $_ENV/$_SERVER, it never calls putenv(), so getenv() here always
        // silently returned '' and every encode()/decode() ran with an empty
        // key — self-consistent (both ends shared the same bug) so nothing
        // crashed, but it meant encryption-at-rest for gateway credentials and
        // invoice/quote passwords provided no real confidentiality.
        $key = (string) env('ENCRYPTION_KEY', '');
        if (preg_match('/^base64:(.*)$/', $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        return $key;
    }
}
