<?php

if ( ! defined('BASEPATH')) {
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
        require_once APPPATH . 'modules/integrations/libraries/IntegrationResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDriver.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseStatus.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDirection.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseType.php';
    }

    public function index()
    {
        // Build peppol_id → client lookup map.
        $raw_clients = $this->db
            ->select('client_id, client_name, client_peppol_id')
            ->where('client_peppol_id IS NOT NULL')
            ->where('client_peppol_id !=', '')
            ->get('ip_clients')
            ->result_array();

        $client_map = [];
        foreach ($raw_clients as $c) {
            $client_map[$c['client_peppol_id']] = $c;
        }

        $this->layout->set([
            'clients'    => $this->Merchant_clients_model->get_enabled_clients(),
            'incoming'   => $this->Merchant_responses_model->get_incoming(),
            'client_map' => $client_map,
        ]);

        $this->layout->buffer('content', 'integrations/incoming');
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
            $response = $client->receiveInvoices();
        } catch (Throwable $e) {
            log_message('error', 'Incoming e-invoice sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Provider request failed.');
            redirect('integrations/incoming');

            return;
        }

        $items  = IntegrationResponseNormalizer::extractItems($response, ['data', 'items', 'invoices']);
        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        foreach ($items as $item) {
            $this->Merchant_responses_model->create_inbound_item(
                (int) $merchant_client_id,
                $item,
                $driver,
                $item['sender'] ?? $item['peppol_participant_id'] ?? null,
            );
        }
        redirect('integrations/incoming');
    }
}
