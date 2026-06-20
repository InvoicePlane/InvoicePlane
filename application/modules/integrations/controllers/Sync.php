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
    }

    public function run($merchant_client_id)
    {
        $merchantClientId = (int) $merchant_client_id;
        $merchantClient   = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            $this->session->set_flashdata('alert_error', trans('merchant_client_not_found'));
            redirect('integrations/settings');
            return;
        }

        $driver   = MerchantResponseDriver::from($merchantClient['merchant_type']);
        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client   = new IntegrationClient($provider, $settings);

        $incoming      = $client->receiveInvoices();
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
                    $merchantClientId,
                    $item,
                    $driver,
                );
            }
        }

        $events     = $client->getInvoiceEvents();
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
                    $merchantClientId,
                    $item,
                    $driver,
                );
            }
        }

        $this->session->set_flashdata('alert_success', trans('einvoice_manual_sync_success'));
        redirect('integrations/events');
    }
}
