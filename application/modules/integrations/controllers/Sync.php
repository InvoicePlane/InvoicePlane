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

        foreach ($incomingItems as $item) {
            $this->Merchant_responses_model->create_inbound_item(
                (int) $merchant_client_id,
                $item,
                $driver
            );
        }

        $eventItems = IntegrationResponseNormalizer::extractItems($events, ['data', 'items', 'events']);

        foreach ($eventItems as $item) {
            $this->Merchant_responses_model->create_event_item(
                (int) $merchant_client_id,
                $item,
                $driver
            );
        }

        $this->session->set_flashdata(
            'alert_success',
            trans('einvoice_manual_sync_success')
        );

        redirect('integrations/events');
    }
}
