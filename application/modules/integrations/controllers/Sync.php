<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sync extends Admin_Controller
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
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseStatus.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDirection.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseType.php';
        require_once APPPATH . 'modules/integrations/libraries/PeppolDocumentType.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfile.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfileRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceArtifact.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceSchematronValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/FrenchEInvoiceValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceDocumentValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceDocumentService.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceSynchronizer.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSyncService.php';
    }

    public function run($merchant_client_id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);

            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchant_client_id);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            $this->session->set_flashdata('alert_error', trans('merchant_client_not_found'));

            redirect('integrations/settings');

            return;
        }

        try {
            $result = $this->syncService()->run((int) $merchant_client_id, 'manual', 'all');
        } catch (Throwable $e) {
            log_message('error', 'Manual e-invoice sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Provider request failed.');
            redirect('integrations/settings');

            return;
        }

        $this->session->set_flashdata(
            $result['status'] === 'success' ? 'alert_success' : 'alert_error',
            sprintf(
                '%s %d incoming invoice(s) archived; %d already present; %d rejected. Run %s (%s).',
                trans('einvoice_manual_sync_success'),
                $result['incoming']['archived'],
                $result['incoming']['skipped'],
                $result['incoming']['failed'],
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
