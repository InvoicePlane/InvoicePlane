<?php

if (!defined('BASEPATH')) {
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
    }

    public function run($merchant_client_id)
    {
        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchant_client_id);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            $this->session->set_flashdata('alert_error', trans('merchant_client_not_found'));
            redirect('integrations/settings');
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client = new IntegrationClient($provider, $settings);

        $incoming = $client->receiveInvoices();
        $incomingItems = $incoming['response']['data']
            ?? $incoming['response']['items']
            ?? $incoming['response']['invoices']
            ?? $incoming['response']
            ?? [];

        if (isset($incomingItems['id']) || isset($incomingItems['external_id'])) {
            $incomingItems = [$incomingItems];
        }

        foreach ($incomingItems as $item) {
            if (is_array($item)) {
                $this->Merchant_responses_model->create_inbound_item(
                    (int) $merchant_client_id,
                    $item
                );
            }
        }

        $events = $client->getInvoiceEvents();
        $eventItems = $events['response']['data']
            ?? $events['response']['items']
            ?? $events['response']['events']
            ?? $events['response']
            ?? [];

        if (isset($eventItems['id']) || isset($eventItems['external_id'])) {
            $eventItems = [$eventItems];
        }

        foreach ($eventItems as $item) {
            if (is_array($item)) {
                $this->Merchant_responses_model->create_event_item(
                    (int) $merchant_client_id,
                    $item
                );
            }
        }

        $this->session->set_flashdata(
            'alert_success',
            trans('einvoice_manual_sync_success')
        );

        redirect('integrations/events');
    }
}
