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
        if ($this->input->post('btn_login'))
        {
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
            }
        }

        $data = array(
            'login_logo' => $this->mdl_settings->setting('login_logo')
        );

        $this->load->view('session_login', $data);
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