<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Integrations extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('integrations/Merchant_clients_model');
        $this->load->model('integrations/Merchant_responses_model');
    }

    public function providers(): void
    {
        $registry = new IntegrationClientRegistry();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($registry->all(), JSON_PRETTY_PRINT));
    }

    public function send_invoice($invoiceId, $merchantClientId): void
    {
        $invoiceId        = (int) $invoiceId;
        $merchantClientId = (int) $merchantClientId;

        $merchantClient = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $driver   = MerchantResponseDriver::from($merchantClient['merchant_type']);
        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client   = new IntegrationClient($provider, $settings);

        $this->load->helper('pdf');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');

        $invoice = $this->mdl_invoices->get_by_id($invoiceId);

        if ( ! $invoice) {
            show_error(trans('invoice_not_found'));
            return;
        }

        $items = $this->mdl_items
            ->where('invoice_id', $invoiceId)
            ->get()
            ->result();

        if ( ! defined('FCPATH') || ! is_writable(dirname(FCPATH))) {
            show_error('FCPATH is not defined or not writable.');
            return;
        }

        $documentDir = FCPATH . 'uploads/integrations/outgoing/';

        if ( ! is_dir($documentDir)) {
            mkdir($documentDir, 0775, true);
        }

        $nonce        = bin2hex(random_bytes(8));
        $documentPath = $documentDir . 'invoice_' . $invoiceId . '_' . $nonce . '.pdf';

        $pdfContent = generate_invoice_pdf($invoiceId, false, null, null);

        if (empty($pdfContent)) {
            show_error('InvoicePlane did not return PDF content.');
            return;
        }

        if (is_string($pdfContent) && file_exists($pdfContent)) {
            copy($pdfContent, $documentPath);
        } else {
            file_put_contents($documentPath, $pdfContent);
        }

        if ( ! file_exists($documentPath) || filesize($documentPath) === 0) {
            show_error('Invoice PDF not found after generation.');
            return;
        }

        $metadata = [
            'invoice_id' => $invoiceId,
            'format'     => 'factur-x',
            'profile'    => 'EN16931',
        ];

        $metadata = $provider->buildInvoicePayload($invoice, $items, $metadata);
        $response = $client->sendInvoice($documentPath, $metadata);

        $this->Merchant_responses_model->create_outbound(
            $merchantClientId,
            $invoiceId,
            $response,
            $driver,
        );

        if ( ! empty($response['success'])) {
            $this->session->set_flashdata('alert_success', trans('einvoice_send_success'));
        } else {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_send_failed') . ' : ' . ($response['message'] ?? '')
            );
        }

        redirect('invoices/view/' . $invoiceId);
    }

    public function receive($merchantClientId): void
    {
        $merchantClientId = (int) $merchantClientId;
        $merchantClient   = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $driver   = MerchantResponseDriver::from($merchantClient['merchant_type']);
        $settings = $this->Merchant_clients_model->get_settings($merchantClient);
        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client   = new IntegrationClient($provider, $settings);

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
                    $merchantClientId,
                    $item,
                    $driver,
                );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function sync_events($merchantClientId)
    {
        $merchantClientId = (int) $merchantClientId;
        $merchantClient   = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $driver   = MerchantResponseDriver::from($merchantClient['merchant_type']);
        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client   = new IntegrationClient($provider, $settings);

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
                    $merchantClientId,
                    $item,
                    $driver,
                );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($events, JSON_PRETTY_PRINT));
    }

    public function status($invoiceId, $merchantClientId)
    {
        $invoiceId        = (int) $invoiceId;
        $merchantClientId = (int) $merchantClientId;
        $merchantClient   = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient) {
            show_error(trans('merchant_client_not_found'));
        }

        $lastResponse = $this->Merchant_responses_model
            ->get_last_response_by_invoice($invoiceId);

        if ( ! $lastResponse) {
            $this->session->set_flashdata('alert_error', trans('einvoice_no_transmission_found'));
            redirect('invoices/view/' . $invoiceId);
        }

        $driver   = MerchantResponseDriver::from($merchantClient['merchant_type']);
        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new IntegrationClientRegistry();
        $provider = $registry->getClient($merchantClient['merchant_type']);
        $client   = new IntegrationClient($provider, $settings);

        if (empty($lastResponse['external_id'])) {
            $this->session->set_flashdata('alert_error', trans('einvoice_no_external_reference'));
            redirect('invoices/view/' . $invoiceId);
            return;
        }

        $status = $client->getInvoiceStatus($lastResponse['external_id']);

        $this->Merchant_responses_model->save_status(
            $invoiceId,
            $status,
            $driver,
            $lastResponse,
        );

        if ( ! empty($status['success'])) {
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

        redirect('invoices/view/' . $invoiceId);
    }

    public function history($invoiceId)
    {
        $this->layout->set([
            'invoice_id' => (int) $invoiceId,
            'history'    => $this->Merchant_responses_model->get_by_invoice((int) $invoiceId),
        ]);

        $this->layout->buffer('content', 'integrations/history');
        $this->layout->render();
    }
}
