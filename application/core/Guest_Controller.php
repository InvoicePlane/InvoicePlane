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
class Guest_Controller extends User_Controller
{
    use XSS_Protection_Trait;

    /** @var array */
    public $user_clients = [];

    /**
     * Guest_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', 2);
        $this->setSecurityHeaders();

        $this->load->model('user_clients/mdl_user_clients');

        $user_clients = $this->mdl_user_clients->assigned_to($this->session->userdata('user_id'))->get()->result();

        if ( ! $user_clients) {
            show_error(trans('guest_account_denied'), 403);
            exit;
        }

        foreach ($user_clients as $user_client) {
            $this->user_clients[$user_client->client_id] = $user_client->client_id;
        }

        // Automatically filter all POST input to prevent XSS attacks
        // This applies to all guest controllers
        if ($this->input->method() === 'post' && ! empty($_POST)) {
            $this->filter_input();
        }
    }

    protected function setSecurityHeaders(): void
    {
        $this->output
            ->set_header('X-Frame-Options: ' . env('X_FRAME_OPTIONS', 'SAMEORIGIN'))
            ->set_header("Content-Security-Policy: frame-ancestors 'self'; object-src 'none'; base-uri 'self'")
            ->set_header('Referrer-Policy: strict-origin-when-cross-origin');

        if (env_bool('ENABLE_X_CONTENT_TYPE_OPTIONS', 'true')) {
            $this->output->set_header('X-Content-Type-Options: nosniff');
        }
    }
}
