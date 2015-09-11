<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
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
            'login_logo' => $this->mdl_settings->setting('login_logo')
        );

        if ($this->input->post('btn_login')) {

            $this->db->where('user_email', $this->input->post('email'));
            $query = $this->db->get('ip_users');
            $user = $query->row();

            // Check if the user exists
            if (empty($user)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
            } else {

                // Check if the user is marked as active
                if ($user->user_active == 0) {
                    $this->session->set_flashdata('alert_error', lang('loginalert_user_inactive'));
                } else {

                    if ($this->authenticate($this->input->post('email'), $this->input->post('password'))) {
                        if ($this->session->userdata('user_type') == 1) {
                            redirect('dashboard');
                        } elseif ($this->session->userdata('user_type') == 2) {
                            redirect('guest');
                        }
                    } else {
                        $this->session->set_flashdata('alert_error', lang('loginalert_credentials_incorrect'));
                    }

                }

            }

        }

        $this->load->view('session_login', $view_data);
    }

    public function logout()
    {
        $this->session->sess_destroy();

        redirect('sessions/login');
    }

    public function authenticate($email_address, $password)
    {
        $this->load->model('mdl_sessions');

        if ($this->mdl_sessions->auth($email_address, $password)) {
            return true;
        }

        return false;
    }

    public function passwordreset($token = null)
    {
        // Check if a token was provided
        if ($token) {
            $this->db->where('user_passwordreset_token', $token);
            $user = $this->db->get('ip_users');
            $user = $user->row();

            if (empty($user)) {
                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_success', lang('wrong_passwordreset_token'));
                redirect('sessions/login');
            }

            $formdata = array(
                'user_id' => $user->user_id
            );

            return $this->load->view('session_new_password', $formdata);

        }

        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password')) {
            $new_password = $this->input->post('new_password');
            $user_id = $this->input->post('user_id');

            if (empty($user_id) || empty($new_password)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_no_password'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Call the save_change_password() function from users model
            $this->load->model('users/mdl_users');
            $this->mdl_users->save_change_password(
                $user_id, $new_password
            );

            // Update the user and set him active again
            $db_array = array(
                'user_passwordreset_token' => '',
            );

            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $db_array);

            // Redirect back to the login form
            redirect('sessions/login');

        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset')) {
            $email = $this->input->post('email');
            if (empty($email)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Test if a user with this email exists
            if ($this->db->where('user_email', $email)) {
                // Create a passwordreset token
                $email = $this->input->post('email');
                $token = md5(time() . $email);

                // Save the token to the database and set the user to inactive
                $db_array = array(
                    'user_passwordreset_token' => $token,
                );

                $this->db->where('user_email', $email);
                $this->db->update('ip_users', $db_array);

                // Send the email with reset link
                $this->load->library('email');

                // Preprare some variables for the email
                $email_resetlink = base_url() . 'sessions/passwordreset/' . $token;
                $email_message = $this->load->view('emails/passwordreset', array(
                    'resetlink' => $email_resetlink
                ), true);
                $email_from = 'system@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", base_url());

                // Set email configuration
                $config['mailtype'] = 'html';
                $this->email->initialize($config);

                // Set the email params
                $this->email->from($email_from);
                $this->email->to($email);
                $this->email->subject(lang('password_reset'));
                $this->email->message($email_message);

                // Send the reset email
                $this->email->send();

                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));
                redirect('sessions/login');
            }
        }

        return $this->load->view('session_passwordreset');
    }

}
