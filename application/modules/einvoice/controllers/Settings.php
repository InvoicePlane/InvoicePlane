<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('einvoice/Merchant_clients_model');
    }

    public function index()
    {
        $this->layout->set([
            'providers' => $this->Merchant_clients_model->get_all(),
        ]);

        $this->layout->buffer('content', 'einvoice/settings');
        $this->layout->render();
    }

    public function edit($id)
    {
        $provider = $this->Merchant_clients_model->get_by_id((int) $id);

        if (!$provider) {
            show_error(trans('merchant_client_not_found'));
        }

        $this->layout->set([
            'provider' => $provider,
            'settings' => json_decode($provider['settings_json'] ?? '{}', true) ?: [],
        ]);

        $this->layout->buffer('content', 'einvoice/provider_form');
        $this->layout->render();
    }

    public function save($id)
    {
        $settings = [
            'client_id' => $this->input->post('client_id'),
            'client_secret' => $this->input->post('client_secret'),
            'token_url' => $this->input->post('token_url'),
            'api_base_url' => $this->input->post('api_base_url'),
            'invoice_endpoint' => $this->input->post('invoice_endpoint'),
            'invoice_status_endpoint' => $this->input->post('invoice_status_endpoint'),
            'incoming_invoices_endpoint' => $this->input->post('incoming_invoices_endpoint'),
            'invoice_events_endpoint' => $this->input->post('invoice_events_endpoint'),
            'disable_pre_check' => $this->input->post('disable_pre_check') ? true : false,
        ];

        $data = [
            'label' => $this->input->post('label'),
            'enabled' => $this->input->post('enabled') ? 1 : 0,
            'auth_type' => $this->input->post('auth_type') ?: 'oauth2',
            'settings_json' => json_encode($settings),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->Merchant_clients_model->update_client((int) $id, $data);

        redirect('einvoice/settings');
    }
}
