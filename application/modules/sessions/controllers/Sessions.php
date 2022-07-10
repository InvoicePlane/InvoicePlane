<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Sessions
 */
class Sessions extends Base_Controller
{
    public function index()
    {
        redirect('sessions/login');
    }

    public function login()
    {
        $view_data = array(
            'login_logo' => get_setting('login_logo')
        );

        if ($this->input->post('btn_login')) {

            $this->db->where('user_email', $this->input->post('email'));
            $query = $this->db->get('ip_users');
            $user = $query->row();

            // Check if the user exists
            if (empty($user)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect('sessions/login');
            } else {

                // Check if the user is marked as active
                if ($user->user_active == 0) {
                    $this->session->set_flashdata('alert_error', trans('loginalert_user_inactive'));
                    redirect('sessions/login');
                } else {

                    if ($this->authenticate($this->input->post('email'), $this->input->post('password'))) {
                        if ($this->session->userdata('user_type') == 1) {
                            redirect('dashboard');
                        } elseif ($this->session->userdata('user_type') == 2) {
                            redirect('guest');
                        }
                    } else {
                        $this->session->set_flashdata('alert_error', trans('loginalert_credentials_incorrect'));
                        redirect('sessions/login');
                    }

                }

            }

        }

        $this->load->view('session_login', $view_data);
    }

    /**
     * @param $email_address
     * @param $password
     * @return bool
     */
    public function authenticate($email_address, $password)
    {
        $this->load->model('mdl_sessions');
        //check if user is banned
        $login_log = $this->_login_log_check($email_address);
        if(empty($login_log)||$login_log->log_count < 10)
        {
            if ($this->mdl_sessions->auth($email_address, $password)) {
                $this->_login_log_reset($email_address);
                return true;
            }
            else
            {
                //track failed attempt
                $this->_login_log_addfailure($email_address);
            }
        }
        return false;
    }

    public function logout()
    {
        $this->session->sess_destroy();

        redirect('sessions/login');
    }

    /**
     * @param null $token
     * @return mixed
     */
    public function passwordreset($token = null)
    {
        // Check if a token was provided
        if ($token) {

            //prevent brute force attacks by counting times a token is used
            $login_log_check = $this->_login_log_check($token);
            if(!empty($login_log_check) && $login_log_check->log_count>10)
            {
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                //the use of a token counts as a failure
                $this->_login_log_addfailure($token);
            }
            
            $this->db->where('user_passwordreset_token', $token);
            $user = $this->db->get('ip_users');
            $user = $user->row();

            if (empty($user)) {
                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_error', trans('wrong_passwordreset_token'));
                redirect('sessions/passwordreset');
            }
            else
            {
                //if token is valid, delete the failure attempt from
                //the login_log table
                $this->_login_log_reset($token);
            }

            $formdata = array(
                'token' => $token,
                'user_id' => $user->user_id,
            );

            return $this->load->view('session_new_password', $formdata);
        }

        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password')) {
            $new_password = $this->input->post('new_password');
            $user_id = $this->input->post('user_id');

            if (empty($user_id) || empty($new_password)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_no_password'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            $this->load->model('users/mdl_users');

            // Check for the reset token
            $user = $this->mdl_users->get_by_id($user_id);

            if (empty($user)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if (empty($user->user_passwordreset_token) || $this->input->post('token') !== $user->user_passwordreset_token) {
                $this->session->set_flashdata('alert_error', trans('loginalert_wrong_auth_code'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Call the save_change_password() function from users model
            $this->mdl_users->save_change_password(
                $user_id, $new_password
            );

            // Update the user and set him active again
            $db_array = array(
                'user_passwordreset_token' => '',
            );

            //delete failed attempts from login_log table
            $user = $this->db->where('user_id',$user_id)->get('ip_users')->row();
            $this->_login_log_reset($user->user_email);

            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $db_array);

            // Redirect back to the login form
            redirect('sessions/login');

        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset')) {
            $email = $this->input->post('email');

            if (empty($email)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            //prevent brute force attacks by counting password resets
            $login_log_check = $this->_login_log_check($email);
            if(!empty($login_log_check) && $login_log_check->log_count>10)
            {
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                //a password recovery attempt counts as failed login
                $this->_login_log_addfailure($email);
            }

            // Test if a user with this email exists
            if ($recovery_result = $this->db->where('user_email', $email)) {
                // Create a passwordreset token. 
                $email = $this->input->post('email');
                //use salt to prevent predictability of the reset token (CVE-2021-29023)
                $this->load->library('crypt'); 
                $token = md5(time() . $email . $this->crypt->salt()); 

                // Save the token to the database and set the user to inactive
                $db_array = array(
                    'user_passwordreset_token' => $token,
                );

                $this->db->where('user_email', $email);
                $this->db->update('ip_users', $db_array);

                // Send the email with reset link
                $this->load->helper('mailer');

                // Preprare some variables for the email
                $email_resetlink = site_url('sessions/passwordreset/' . $token);
                $email_message = $this->load->view('emails/passwordreset', array(
                    'resetlink' => $email_resetlink
                ), true);

                $email_from = get_setting('smtp_mail_from');
                if (empty($email_from)) {
                    $email_from = 'system@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", base_url());
                }

                // Mail the invoice with the pre-configured mailer if possible
                if (mailer_configured()) {

                    $this->load->helper('mailer/phpmailer');

                    if (!phpmail_send($email_from, $email, trans('password_reset'), $email_message)) {
                        $email_failed = true;
                    }

                } else {

                    $this->load->library('email');

                    // Set email configuration
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);

                    // Set the email params
                    $this->email->from($email_from);
                    $this->email->to($email);
                    $this->email->subject(trans('password_reset'));
                    $this->email->message($email_message);

                    // Send the reset email
                    if (!$this->email->send()) {
                        $email_failed = true;
                        log_message('error', $this->email->print_debugger());
                    }
                }

                // Redirect back to the login screen with an alert
                if (isset($email_failed)) {
                    $this->session->set_flashdata('alert_error', trans('password_reset_failed'));
                } else {
                    $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
                }

                redirect('sessions/login');
            }
        }

        return $this->load->view('session_passwordreset');
    }

    /**
     * Checks if the login_log table has records for the
     * given
     *
     * @param string $username
     * @return object
     */
    private function _login_log_check($username)
    {
        $login_log_query =  $this->db->where('login_name',$username)->get('ip_login_log')->row();
        
        if(!empty($login_log_query) && $login_log_query->log_count > 10)
        {
            $current_time = new DateTime();
            $interval = $current_time->diff(new DateTime($login_log_query->log_create_timestamp));
            //if the last recorded failed attempt is over 12 hours ago, then unlock the account
            //the fails are only counted up to 11, this means that the account is also unlocked
            //if the last failed 11th login attempt is over 12 hours ago.
            if($interval->h>12)
            {
                $this->_login_log_reset($username);
                return null;
            }
        }
        return $login_log_query;
    }

    /**
     * If the username has a record in the login_log
     * table the count is incremented by 1, otherwise
     * a record for the given user is created.
     *
     * @param string $username
     */
    private function _login_log_addfailure($username)
    {
        if(empty($login_log_check = $this->_login_log_check($username)))
        {
            //create the log
            $this->db->insert('ip_login_log',[
                'login_name' => $username,
                'log_count' => 1,
                'log_create_timestamp' => date('c')
            ]);
        }
        else
        {
            //update the log
            $this->db->set([
                'log_count'=>$login_log_check->log_count+1,
                'log_create_timestamp' => date('c')
            ])
            ->where('login_name',$username)
            ->update('ip_login_log');
        }
    }

    /**
     * The record of the given user is deleted from the
     * login_log table.
     *
     * @param string $username
     */
    private function _login_log_reset($username)
    {
        $this->db->delete('ip_login_log',['login_name'=>$username]);
    }
}
