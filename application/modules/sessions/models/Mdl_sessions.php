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

                $session_data = [
                    'user_type'     => $user->user_type,
                    'user_id'       => $user->user_id,
                    'user_name'     => $user->user_name,
                    'user_email'    => $user->user_email,
                    'user_company'  => $user->user_company,
                    'user_language' => $user->user_language ?? 'system',
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
     * @param string|int $user_id
     */
    public function invalidate_user_sessions($user_id): void
    {
        $session_path = $this->session->sess_save_path;

        if ( ! is_dir($session_path)) {
            return;
        }

        try {
            $files = scandir($session_path);
            if ($files === false) {
                return;
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file_path = $session_path . DIRECTORY_SEPARATOR . $file;

                if ( ! is_file($file_path)) {
                    continue;
                }

                $session_data = @unserialize(file_get_contents($file_path));
                if ($session_data === false || ! is_array($session_data)) {
                    continue;
                }

                if (isset($session_data['user_id']) && (string) $session_data['user_id'] === (string) $user_id) {
                    @unlink($file_path);
                }
            }
        } catch (Exception $e) {
            // Log but don't crash if session cleanup fails
            log_message('error', 'Session invalidation failed for user ' . (int) $user_id);
        }
    }
}
