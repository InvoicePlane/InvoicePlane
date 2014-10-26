<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Sessions extends Base_Controller {

    public function index()
    {
        redirect('sessions/login');
    }

    public function login()
    {

        $view_data = array(
            'login_logo' => $this->mdl_settings->setting('login_logo')
        );

        if ($this->input->post('btn_login'))
        {

            $this->db->where('user_email', $this->input->post('email'));
            $query = $this->db->get('ip_users');
            $user = $query->row();

            // Check if the user exists
            if ( empty($user) )
            {
                $view_data = array(
                    'login_logo' => $this->mdl_settings->setting('login_logo'),
                    'login_alert' => lang('loginalert_user_not_found')
                );
            } else {

                // Check if the user is marked as active
                if ($user->user_active == 0)
                {
                    $view_data = array(
                        'login_logo' => $this->mdl_settings->setting('login_logo'),
                        'login_alert' => lang('loginalert_user_inactive')
                    );
                } else {

                    if ($this->authenticate($this->input->post('email'), $this->input->post('password')))
                    {
                        if ($this->session->userdata('user_type') == 1)
                        {
                            redirect('dashboard');
                        }
                        elseif ($this->session->userdata('user_type') == 2)
                        {
                            redirect('guest');
                        }
                    } else {
                        $view_data = array(
                            'login_logo' => $this->mdl_settings->setting('login_logo'),
                            'login_alert' => lang('loginalert_credentials_incorrect')
                        );
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

        if ($this->mdl_sessions->auth($email_address, $password))
        {
            return TRUE;
        }

        return FALSE;
    }

}

?>