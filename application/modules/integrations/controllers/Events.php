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

        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClient.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDriver.php';
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
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $response = $client->getInvoiceEvents();
        } catch (Throwable $e) {
            log_message('error', 'E-invoice event sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Provider request failed.');
            redirect('integrations/events');

            return;
        }

        $items  = IntegrationResponseNormalizer::extractItems($response, ['data', 'items', 'events']);
        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        foreach ($items as $item) {
            $this->Merchant_responses_model->create_event_item(
                (int) $merchant_client_id,
                $item,
                $driver
            );
        }

        redirect('integrations/events');
    }
}
