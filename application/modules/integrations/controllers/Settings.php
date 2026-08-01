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
        $this->load->model('integrations/Integration_sync_runs_model');

        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSettingsForm.php';
    }

    public function index()
    {
        $this->sync_provider_registry();

        $this->layout->set([
            'providers'        => $this->Merchant_clients_model->get_all(),
            'latest_sync_runs' => $this->Integration_sync_runs_model->latest_by_client(),
        ]);

        $this->layout->buffer('content', 'integrations/settings');
        $this->layout->render();
    }

    public function edit($id)
    {
        $provider = $this->Merchant_clients_model->get_by_id((int) $id);

        if ( ! $provider) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

        try {
            $definition = (new IntegrationClientRegistry())
                ->getSettingsDefinition($provider['merchant_type']);
        } catch (Throwable $e) {
            log_message('error', 'Unable to load integration settings schema: ' . sanitize_for_logging($e->getMessage()));
            show_error(trans('merchant_client_not_found'));

            return;
        }

        try {
            $storedSettings = $this->Merchant_clients_model->get_settings($provider);
        } catch (Throwable $e) {
            log_message('error', 'Unable to decrypt integration settings: ' . sanitize_for_logging($e->getMessage()));
            show_error('Unable to decrypt provider settings. Check ENCRYPTION_KEY.', 500);

            return;
        }
        $provider['auth_type'] = $definition['auth_type'];

        $this->layout->set([
            'provider'        => $provider,
            'settings'        => array_replace($definition['defaults'], $storedSettings),
            'settings_schema' => $definition['schema'],
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

        try {
            $existingSettings = $this->Merchant_clients_model->get_settings($provider);
            $definition       = (new IntegrationClientRegistry())
                ->getSettingsDefinition($provider['merchant_type']);
            $settings = IntegrationSettingsForm::collect(
                $definition['schema'],
                $existingSettings,
                fn (string $field) => $this->input->post($field)
            );
            $settingsJson = $this->Merchant_clients_model->protect_settings(
                $provider['merchant_type'],
                $settings
            );
        } catch (Throwable $e) {
            log_message('error', 'Invalid integration settings: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('einvoice_provider_settings_invalid'));
            redirect('integrations/settings/edit/' . (int) $id);

            return;
        }

        $label = $this->input->post('label');
        if ( ! is_string($label) || trim($label) === '' || mb_strlen($label) > 255) {
            $this->session->set_flashdata('alert_error', trans('einvoice_provider_settings_invalid'));
            redirect('integrations/settings/edit/' . (int) $id);

            return;
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
            'label'         => trim($label),
            'enabled'       => $enabled,
            'auth_type'     => $definition['auth_type'],
            'settings_json' => $settingsJson,
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

    private function sync_provider_registry(): void
    {
        $this->load->library('integrations/IntegrationClientRegistry');

        $registry = new IntegrationClientRegistry();

        if (method_exists($registry, 'syncDatabaseProviders')) {
            $registry->syncDatabaseProviders();
        }
    }
}
