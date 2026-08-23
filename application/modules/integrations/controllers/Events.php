<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Events extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('integrations/Merchant_clients_model');
        $this->load->model('integrations/Merchant_responses_model');
        $this->load->model('integrations/Integration_sync_runs_model');

        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClient.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDriver.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSyncService.php';
    }

    public function index()
    {
        $this->layout->set([
            'clients' => $this->Merchant_clients_model->get_enabled_clients(),
            'events'  => $this->Merchant_responses_model->get_events(),
        ]);

        $this->layout->buffer('content', 'integrations/events');
        $this->layout->render();
    }

    public function sync($merchant_client_id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);

            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchant_client_id);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

        try {
            $result = $this->syncService()->run((int) $merchant_client_id, 'manual', 'events');
        } catch (Throwable $e) {
            log_message('error', 'E-invoice event sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Provider request failed.');
            redirect('integrations/events');

            return;
        }

        $this->session->set_flashdata(
            $result['status'] === 'success' ? 'alert_success' : 'alert_error',
            sprintf(
                '%d event(s) stored; %d already present. Run %s (%s).',
                $result['events']['created'],
                $result['events']['skipped'],
                $result['correlation_id'],
                $result['status']
            )
        );

        redirect('integrations/events');
    }

    private function syncService(): IntegrationSyncService
    {
        return new IntegrationSyncService(
            $this->db,
            $this->Merchant_clients_model,
            $this->Merchant_responses_model,
            $this->Integration_sync_runs_model,
            UPLOADS_ARCHIVE_FOLDER
        );
    }
}
