<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('integrations/Merchant_clients_model');
    }

    public function index()
    {
        $this->sync_provider_registry();

        $this->layout->set([
            'providers' => $this->Merchant_clients_model->get_all(),
        ]);

        $this->layout->buffer('content', 'integrations/settings');
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

        $this->layout->buffer('content', 'integrations/provider_form');
        $this->layout->render();
    }

    public function save($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Validate URL fields for SSRF before touching the database.
        // Full URLs (token_url, api_base_url) must be HTTPS and must not
        // resolve to private/internal IP ranges.
        // Endpoint path fields must be relative paths, not full URLs.
        $this->load->library('SsrfGuard');

        $urlFields  = ['token_url', 'api_base_url'];
        $pathFields = [
            'upload_endpoint', 'invoice_endpoint', 'send_invoice_endpoint',
            'invoice_status_endpoint', 'incoming_invoices_endpoint', 'invoice_events_endpoint',
        ];

        foreach ($urlFields as $field) {
            $value = (string) ($this->input->post($field) ?? '');
            try {
                $this->ssrfguard->validate($value);
            } catch (\InvalidArgumentException $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect('integrations/settings/edit/' . (int) $id);
                return;
            }
        }

        foreach ($pathFields as $field) {
            $value = (string) ($this->input->post($field) ?? '');
            try {
                $this->ssrfguard->validateRelativePath($value);
            } catch (\InvalidArgumentException $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect('integrations/settings/edit/' . (int) $id);
                return;
            }
        }

        $settings = [
            'client_id' => $this->input->post('client_id'),
            'client_secret' => $this->input->post('client_secret'),
            'access_token' => $this->input->post('access_token'),
            'api_key' => $this->input->post('api_key'),
            'staging_token' => $this->input->post('staging_token'),
            'token_url' => $this->input->post('token_url'),
            'api_base_url' => $this->input->post('api_base_url'),
            'upload_endpoint' => $this->input->post('upload_endpoint'),
            'invoice_endpoint' => $this->input->post('invoice_endpoint'),
            'send_invoice_endpoint' => $this->input->post('send_invoice_endpoint'),
            'invoice_status_endpoint' => $this->input->post('invoice_status_endpoint'),
            'incoming_invoices_endpoint' => $this->input->post('incoming_invoices_endpoint'),
            'invoice_events_endpoint' => $this->input->post('invoice_events_endpoint'),
            'disable_pre_check' => $this->input->post('disable_pre_check') ? true : false,
        ];

        $settings = array_filter($settings, static function ($value) {
            return $value !== null;
        });

	$enabled = $this->input->post('enabled') ? 1 : 0;

        if ($enabled === 1) {
            $this->Merchant_clients_model->disable_all_except((int) $id);
        }

        $data = [
            'label' => $this->input->post('label'),
            'enabled' => $enabled,
            'auth_type' => $this->input->post('auth_type') ?: 'oauth2',
            'settings_json' => json_encode($settings),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->Merchant_clients_model->update_client((int) $id, $data);

        redirect('integrations/settings');
    }

    private function sync_provider_registry(): void
    {
        $this->load->library('integrations/IntegrationClientRegistry');

        $registry = new IntegrationClientRegistry();

        if (method_exists($registry, 'syncDatabaseProviders')) {
            $registry->syncDatabaseProviders();
        }
    }
}

