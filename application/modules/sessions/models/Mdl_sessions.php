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
class Mdl_Sessions extends CI_Model
{
    /**
     * @param $email
     * @param $password
     *
     * @return bool
     */
    public function auth($email, $password)
    {
        $this->db->where('user_email', $email);

        $query = $this->db->get('ip_users');

        if ($query->num_rows()) {
            $user = $query->row();

            $this->load->library('crypt');

            /*
             * Legacy pre-1.2.0 path: users without user_psalt still have md5 hashes.
             * If validated, their password is upgraded to the modern salted hash.
             */
            if ( ! $user->user_psalt) {
                /*
                 * The user has not logged in, so we're going to attempt to
                 * update their record with the updated hash
                 */
                if (hash_equals($user->user_password, md5($password))) {
                    /**
                     * Legacy md5 login validated - upgrade this user to the
                     * modern salted hash.
                     */
                    $salt = $this->crypt->salt();
                    $hash = $this->crypt->generate_password($password, $salt);

                    $db_array = [
                        'user_psalt'    => $salt,
                        'user_password' => $hash,
                    ];

                    $this->db->where('user_id', $user->user_id);
                    $this->db->update('ip_users', $db_array);

                    $this->db->where('user_email', $email);
                    $user = $this->db->get('ip_users')->row();
                } else {
                    // The password didn't verify against original md5
                    return false;
                }
            }

            // Modern path: verify against the salted password hash.
            if ($this->crypt->check_password($user->user_password, $password)) {
                // Verify the account is active before granting authentication.
                if ((int) $user->user_active !== 1) {
                    return false;
                }

                $this->load->helper('ip_security');

                $session_data = [
                    'user_credential' => session_credential_fingerprint((string) $user->user_password),
                    'user_type'       => $user->user_type,
                    'user_id'         => $user->user_id,
                    'user_name'       => $user->user_name,
                    'user_email'      => $user->user_email,
                    'user_company'    => $user->user_company,
                    'user_language'   => $user->user_language ?? 'system',
                ];

                // Regenerate session ID on login to prevent session fixation attacks.
                $this->session->sess_regenerate(true);
                $this->session->set_userdata($session_data);

                return true;
            }
        }

        return false;
    }

    /**
     * Destroy all active sessions for a given user_id. This forces immediate
     * session invalidation when a user's role or active status is changed,
     * revoking any stale authenticated sessions.
     *
     * This implementation supports only the files session driver (the default
     * for CodeIgniter 3 and InvoicePlane). Other drivers (database, Redis,
     * memcached) would require direct backend access and are not invalidated.
     * The primary per-request re-validation in User_Controller provides the
     * main defense; this method is an optimization for the files driver.
     *
     * @param string|int $user_id
     */
    public function invalidate_user_sessions($user_id): void
    {
        if ( ! function_exists('sanitize_for_logging')) {
            $this->load->helper('file_security');
        }

        $session_path = (string) $this->config->item('sess_save_path');
        $cookie_name  = (string) $this->config->item('sess_cookie_name');

        // Without the cookie name there is no way to tell a session file from any other file, and the
        // save path may be a shared folder (it defaults to the system temp directory).
        if ($cookie_name === '' || ! is_dir($session_path)) {
            return;
        }

        // File name written by the files driver: cookie name, optionally the md5 of the client IP
        // (sess_match_ip), then the session id.
        $name_pattern = '/^' . preg_quote($cookie_name, '/') . '(?:[0-9a-f]{32})?[0-9a-zA-Z,-]+$/';

        try {
            $files = scandir($session_path);
            if ($files === false) {
                return;
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || ! preg_match($name_pattern, $file)) {
                    continue;
                }

                $file_path = $session_path . DIRECTORY_SEPARATOR . $file;

                if ( ! is_file($file_path)) {
                    continue;
                }

                // Session files are written with session.serialize_handler=php ("key|serialized"
                // repeated), which unserialize() cannot parse, so walk the top-level entries.
                $owner = $this->session_file_user_id((string) @file_get_contents($file_path));
                if ($owner !== null && $owner === (string) (int) $user_id) {
                    @unlink($file_path);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Session invalidation failed for user ' . sanitize_for_logging((int) $user_id));
        }
    }

    /**
     * The user_id of a session file in the "php" serialize-handler format ("key|serialized" repeated),
     * read from the top-level entries only, so that text inside another value (a name, a flash message)
     * or a nested array cannot pass for the owner. Null when there is no user_id or the file holds
     * something this reader does not understand (objects, a truncated file).
     */
    private function session_file_user_id(string $raw): ?string
    {
        $pos    = 0;
        $length = strlen($raw);

        while ($pos < $length) {
            $bar = strpos($raw, '|', $pos);
            if ($bar === false) {
                return null;
            }

            $key   = substr($raw, $pos, $bar - $pos);
            $start = $bar + 1;
            $end   = $this->serialized_value_end($raw, $start, 0);
            if ($end === null) {
                return null;
            }

            if ($key === 'user_id') {
                return preg_match('/^(?:s:\d+:"(\d+)"|i:(\d+));$/', substr($raw, $start, $end - $start), $m) === 1
                    ? ($m[1] !== '' ? $m[1] : $m[2])
                    : null;
            }

            $pos = $end;
        }

        return null;
    }

    /**
     * Offset just past the serialized value that starts at $pos, or null when it is not one of the
     * scalar or array forms a CodeIgniter session holds.
     */
    private function serialized_value_end(string $raw, int $pos, int $depth): ?int
    {
        if ($depth > 16 || $pos >= strlen($raw)) {
            return null;
        }

        if (preg_match('/\G(?:N;|[bi]:-?\d+;|d:[^;]*;)/', $raw, $m, 0, $pos) === 1) {
            return $pos + strlen($m[0]);
        }

        if (preg_match('/\Gs:(\d+):"/', $raw, $m, 0, $pos) === 1) {
            $end = $pos + strlen($m[0]) + (int) $m[1];

            return substr($raw, $end, 2) === '";' ? $end + 2 : null;
        }

        if (preg_match('/\Ga:(\d+):\{/', $raw, $m, 0, $pos) === 1) {
            $cursor = $pos + strlen($m[0]);

            for ($i = 0; $i < (int) $m[1] * 2; $i++) {
                $cursor = $this->serialized_value_end($raw, $cursor, $depth + 1);
                if ($cursor === null) {
                    return null;
                }
            }

            return ($raw[$cursor] ?? '') === '}' ? $cursor + 1 : null;
        }

        return null;
    }
}
