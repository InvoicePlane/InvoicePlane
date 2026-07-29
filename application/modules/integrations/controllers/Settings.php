<?php

if ( ! defined('BASEPATH')) {
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

        if ( ! $provider) {
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

            return;
        }

        $provider = $this->Merchant_clients_model->get_by_id((int) $id);

        if ( ! $provider) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

        $existingSettings = json_decode($provider['settings_json'] ?? '{}', true) ?: [];
        $urlFields  = ['token_url', 'api_base_url'];
        $pathFields = ['upload_endpoint', 'invoice_endpoint', 'send_invoice_endpoint',
            'invoice_status_endpoint', 'incoming_invoices_endpoint', 'invoice_events_endpoint'];

        foreach ($urlFields as $field) {
            $val = (string) $this->input->post($field);
            if ($val === '') {
                continue;
            }
            if ( ! $this->_is_safe_url($val)) {
                redirect('integrations/settings/edit/' . (int) $id);

                return;
            }
        }

        foreach ($pathFields as $field) {
            $val = (string) $this->input->post($field);
            if ($val !== '' && filter_var($val, FILTER_VALIDATE_URL) !== false) {
                redirect('integrations/settings/edit/' . (int) $id);

                return;
            }
        }

        $settings = [
            'client_id'                  => $this->input->post('client_id'),
            'client_secret'              => $this->input->post('client_secret'),
            'access_token'               => $this->input->post('access_token'),
            'api_key'                    => $this->input->post('api_key'),
            'staging_token'              => $this->input->post('staging_token'),
            'token_url'                  => $this->input->post('token_url'),
            'api_base_url'               => $this->input->post('api_base_url'),
            'upload_endpoint'            => $this->input->post('upload_endpoint'),
            'invoice_endpoint'           => $this->input->post('invoice_endpoint'),
            'send_invoice_endpoint'      => $this->input->post('send_invoice_endpoint'),
            'invoice_status_endpoint'    => $this->input->post('invoice_status_endpoint'),
            'incoming_invoices_endpoint' => $this->input->post('incoming_invoices_endpoint'),
            'invoice_events_endpoint'    => $this->input->post('invoice_events_endpoint'),
            'disable_pre_check'          => $this->input->post('disable_pre_check') ? true : false,
        ];

        $settings = array_filter($settings, static function ($value) {
            return $value !== null;
        });

        foreach (['client_secret', 'access_token', 'api_key', 'staging_token'] as $sensitiveField) {
            if (($settings[$sensitiveField] ?? '') === '') {
                if (array_key_exists($sensitiveField, $existingSettings)) {
                    $settings[$sensitiveField] = $existingSettings[$sensitiveField];
                } else {
                    unset($settings[$sensitiveField]);
                }
            }
        }

        $enabled = $this->input->post('enabled') ? 1 : 0;

        $this->db->trans_start();

        if ($enabled === 1) {
            $this->db
                ->where('id !=', (int) $id)
                ->update('ip_merchant_clients', [
                    'enabled'    => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $data = [
            'label'         => $this->input->post('label'),
            'enabled'       => $enabled,
            'auth_type'     => $this->input->post('auth_type') ?: 'oauth2',
            'settings_json' => json_encode($settings),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $this->Merchant_clients_model->update_client((int) $id, $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('alert_error', 'Unable to save provider settings.');
            redirect('integrations/settings/edit/' . (int) $id);

            return;
        }

        redirect('integrations/settings');
    }

    private function _is_safe_url(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parsed = parse_url($url);
        if (($parsed['scheme'] ?? '') !== 'https') {
            return false;
        }

        $host = $parsed['host'] ?? '';
        if ($host === '') {
            return false;
        }

        $ip = filter_var($host, FILTER_VALIDATE_IP);

        return ! ($ip !== false && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
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
