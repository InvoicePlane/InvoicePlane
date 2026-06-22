<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Incoming extends Admin_Controller
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

    public function index()
    {
        $this->layout->set([
            'clients' => $this->Merchant_clients_model->get_enabled_clients(),
            'incoming' => $this->Merchant_responses_model->get_incoming(),
        ]);

        $this->layout->buffer('content', 'integrations/incoming');
        $this->layout->render();
    }

    public function sync($merchant_client_id)
    {
        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchant_client_id);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);

        $client = new IntegrationClient($provider, $settings);

        $response = $client->receiveInvoices();

        $items = $response['response']['data']
            ?? $response['response']['items']
            ?? $response['response']['invoices']
            ?? $response['response']
            ?? [];

        if (isset($items['id']) || isset($items['external_id'])) {
            $items = [$items];
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                $this->Merchant_responses_model->create_inbound_item(
                     (int) $merchant_client_id,
                     $item
                );
            }
        }
        redirect('integrations/incoming');
    }
}

