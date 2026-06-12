<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Einvoice extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('einvoice/Merchant_clients_model');
        $this->load->model('einvoice/Merchant_responses_model');

        require_once APPPATH . 'modules/einvoice/libraries/MerchantProviderInterface.php';
        require_once APPPATH . 'modules/einvoice/libraries/MerchantProviderRegistry.php';
        require_once APPPATH . 'modules/einvoice/libraries/MerchantClient.php';
    }

    public function providers(): void
    {
        $registry = new MerchantProviderRegistry();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($registry->discover(), JSON_PRETTY_PRINT));
    }

    public function send_invoice($invoiceId, $merchantClientId): void
    {
        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchantClientId);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);
        $registry = new MerchantProviderRegistry();
        $provider = $registry->getProvider($merchantClient['merchant_type']);
        $client = new MerchantClient($provider, $settings);

        // TODO: Replace with InvoicePlane Factur-X generated document path.
        $this->load->helper('pdf');

        $documentDir = FCPATH . 'uploads/einvoice/outgoing/';

        if (!is_dir($documentDir)) {
            mkdir($documentDir, 0775, true);
        }

        $documentPath = $documentDir . 'invoice_' . (int) $invoiceId . '.pdf';

        // Génère le PDF Factur-X InvoicePlane sans streaming navigateur
        $pdfContent = generate_invoice_pdf((int) $invoiceId, false, null, null);
	
	if (empty($pdfContent)) {
            show_error('InvoicePlane did not return PDF content.');
        }

	if (is_string($pdfContent) && file_exists($pdfContent)) {
            copy($pdfContent, $documentPath);
        } else {
            file_put_contents($documentPath, $pdfContent);
        }

        if (!file_exists($documentPath) || filesize($documentPath) === 0) {
            show_error('Invoice PDF not found after generation.');
        }

        $metadata = [
            'invoice_id' => (int) $invoiceId,
            'format' => 'factur-x',
            'profile' => 'EN16931',
        ];

        $response = $client->sendInvoice($documentPath, $metadata);

        $this->Merchant_responses_model->create_outbound(
            (int) $merchantClientId,
            (int) $invoiceId,
            $response,
            [
                'document_path' => $documentPath,
                'metadata' => $metadata,
            ]
        );
/*
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
*/
	if (!empty($response['success'])) {
            $this->session->set_flashdata(
                'alert_success',
                trans('einvoice_send_success')
            );
        } else {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_send_failed') . ' : ' . ($response['message'] ?? '')
            );
        }

        redirect('invoices/view/' . (int) $invoiceId);
    }

    public function receive($merchantClientId): void
    {
        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchantClientId);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);
        $registry = new MerchantProviderRegistry();
        $provider = $registry->getProvider($merchantClient['merchant_type']);
        $client = new MerchantClient($provider, $settings);

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
                    (int) $merchantClientId,
                    $item
                );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function sync_events($merchantClientId)
    {
        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchantClientId);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new MerchantProviderRegistry();
        $provider = $registry->getProvider($merchantClient['merchant_type']);
        $client = new MerchantClient($provider, $settings);

        $events = $client->getInvoiceEvents();

        $items = $events['response']['data']
            ?? $events['response']['items']
            ?? $events['response']['events']
            ?? $events['response']
            ?? [];

        if (isset($items['id']) || isset($items['external_id'])) {
            $items = [$items];
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                $this->Merchant_responses_model->create_event_item(
                    (int) $merchantClientId,
                    $item
                );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($events, JSON_PRETTY_PRINT));
    }

    public function status($invoiceId, $merchantClientId)
    {
        $this->load->model('einvoice/Merchant_clients_model');
        $this->load->model('einvoice/Merchant_responses_model');

        $merchantClient = $this->Merchant_clients_model->get_by_id(
            (int) $merchantClientId
        );

        if (!$merchantClient) {
            show_error(trans('merchant_client_not_found'));
        }

        $lastResponse = $this->Merchant_responses_model
            ->get_last_response_by_invoice((int) $invoiceId);

        if (!$lastResponse) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_transmission_found')
            );

            redirect('invoices/view/' . (int) $invoiceId);
        }

        $settings = $this->Merchant_clients_model
            ->get_settings($merchantClient);

        $registry = new MerchantProviderRegistry();

        $provider = $registry->getProvider(
            $merchantClient['merchant_type']
        );

        $client = new MerchantClient(
            $provider,
            $settings
        );
        if (empty($lastResponse['external_id'])) {
            $this->session->set_flashdata(
                 'alert_error',
                 trans('einvoice_no_external_reference')
            );

            redirect('invoices/view/' . (int) $invoiceId);
            return;
	}

        $status = $client->getInvoiceStatus(
            $lastResponse['external_id']
        );

        $this->Merchant_responses_model->save_status(
            (int) $invoiceId,
	    $status,
	    $lastResponse
        );

        if (!empty($status['success'])) {
            $this->session->set_flashdata(
                'alert_success',
                'PDP status: ' . ($status['status'] ?? 'unknown')
            );
        } else {
            $this->session->set_flashdata(
                'alert_error',
                $status['message'] ?? 'Unable to retrieve status'
            );
        }

        redirect('invoices/view/' . (int) $invoiceId);
    }

    public function history($invoiceId)
    {
        $this->load->model('einvoice/Merchant_responses_model');

        $this->layout->set([
            'invoice_id' => (int) $invoiceId,
            'history' => $this->Merchant_responses_model->get_by_invoice((int) $invoiceId),
        ]);

        $this->layout->buffer('content', 'einvoice/history');
        $this->layout->render();
    }
}
