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
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $incoming = $client->receiveInvoices();
            $events   = $client->getInvoiceEvents();
        } catch (Throwable $e) {
            log_message('error', 'Manual e-invoice sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Provider request failed.');
            redirect('integrations/settings');

            return;
        }

        $driver        = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;
        $incomingItems = IntegrationResponseNormalizer::extractItems($incoming, ['data', 'items', 'invoices']);

        $incomingResult = (new IncomingInvoiceSynchronizer())->synchronize(
            $client,
            $merchantClient['merchant_type'],
            (int) $merchant_client_id,
            $driver,
            $incomingItems,
            $this->Merchant_responses_model,
            UPLOADS_ARCHIVE_FOLDER
        );

        $eventItems = IntegrationResponseNormalizer::extractItems($events, ['data', 'items', 'events']);

        foreach ($eventItems as $item) {
            $this->Merchant_responses_model->create_event_item(
                (int) $merchant_client_id,
                $item,
                $driver
            );
        }

        $this->session->set_flashdata(
            $incomingResult['failed'] === 0 ? 'alert_success' : 'alert_error',
            sprintf(
                '%s %d incoming invoice(s) archived; %d already present; %d rejected.',
                trans('einvoice_manual_sync_success'),
                $incomingResult['archived'],
                $incomingResult['skipped'],
                $incomingResult['failed']
            )
        );

        redirect('integrations/events');
    }
}
