<?php
if (!defined('BASEPATH')) {
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

/**
 * Class Mdl_Sessions
 */
class Mdl_Sessions extends CI_Model
{

    /** @var string */
    public $log_path = LOGS_FOLDER;

    /** @var string */
    public $log_file_name = 'sessionevents';

    /** @var string */
    public $log_file_ext = 'log';

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function auth($email, $password)
    {
        $this->db->where('user_email', $email);

        $query = $this->db->get('ip_users');

        if ($query->num_rows()) {
            $user = $query->row();

            $this->load->library('crypt');

            /**
             * Password hashing changed after 1.2.0
             * Check to see if user has logged in since the password change
             */
            if (!$user->user_psalt) {
                /**
                 * The user has not logged in, so we're going to attempt to
                 * update their record with the updated hash
                 */
                if (md5($password) == $user->user_password) {
                    /**
                     * The md5 login validated - let's update this user
                     * to the new hash
                     */
                    $salt = $this->crypt->salt();
                    $hash = $this->crypt->generate_password($password, $salt);

                    $db_array = [
                        'user_psalt' => $salt,
                        'user_password' => $hash,
                    ];

                    $this->db->where('user_id', $user->user_id);
                    $this->db->update('ip_users', $db_array);

                    $this->db->where('user_email', $email);
                    $user = $this->db->get('ip_users')->row();

                } else {
                    /**
                     * The password didn't verify against original md5
                     */
                    return false;
                }
            }

            if ($this->crypt->check_password($user->user_password, $password)) {
                $session_data = [
                    'user_type' => $user->user_type,
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_name,
                    'user_email' => $user->user_email,
                    'user_company' => $user->user_company,
                    'user_language' => isset($user->user_language) ? $user->user_language : 'system',
                ];

                $this->session->set_userdata($session_data);

                return true;
            }
        }

        return false;
    }

    /**
     * Log a session event to a separate log file
     *
     * @param $message
     * @return bool
     */
    public function log_sessionevent($message)
    {
        // Discard log if session logs are disabled
        if (!env_bool('ENABLE_SESSIONLOGS', false)) {
            return false;
        }

        // Construct the file path and check if the file is available
        $logfile = $this->log_path . $this->log_file_name . '.' . $this->log_file_ext;

        if (!$fp = @fopen($logfile, 'ab')) {
            return false;
        }

        // Get the user IP
        $ip = $this->input->ip_address();

        // Construct the log line with the IP, the datetime and the message
        $log_line = date('Y/m/d H:i:s') . ' [' . $ip . '] ' . $message . "\n";

        // Write the log line
        file_put_contents($logfile, $log_line, FILE_APPEND | LOCK_EX);
        fclose($fp);

        return true;
    }
}
