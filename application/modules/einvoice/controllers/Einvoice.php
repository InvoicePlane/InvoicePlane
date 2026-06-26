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
            ->set_output(json_encode($registry->all(), JSON_PRETTY_PRINT));
    }

    public function send_invoice($invoiceId, $merchantClientId): void
    {
        $invoiceId = (int) $invoiceId;
        $merchantClientId = (int) $merchantClientId;

        $merchantClient = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);

        $registry = new MerchantProviderRegistry();

        $this->load->helper('pdf');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');

        $invoice = $this->mdl_invoices->get_by_id($invoiceId);

        if (!$invoice) {
            show_error(trans('invoice_not_found'));
            return;
        }

        $items = $this->mdl_items
            ->where('invoice_id', $invoiceId)
            ->get()
            ->result();

        $documentDir = FCPATH . 'uploads/einvoice/outgoing/';

        if (!is_dir($documentDir)) {
            if (!mkdir($documentDir, 0775, true) && !is_dir($documentDir)) {
                show_error('Unable to create e-invoice output directory.');
                return;
            }
        }

        $documentPath = $documentDir . 'invoice_' . $invoiceId . '.pdf';
        if (file_exists($documentPath) && !unlink($documentPath)) {
            show_error('Unable to replace existing invoice PDF.');
            return;
	}

        $pdfContent = generate_invoice_pdf($invoiceId, false, null, null);

        if (empty($pdfContent)) {
            show_error('InvoicePlane did not return PDF content.');
            return;
        }

        if (is_string($pdfContent) && file_exists($pdfContent)) {
            if (!copy($pdfContent, $documentPath)) {
                show_error('Unable to copy generated invoice PDF.');
                return;
            }
        } else {
            if (file_put_contents($documentPath, $pdfContent) === false) {
                show_error('Unable to write generated invoice PDF.');
                return;
            }
        }

        if (!file_exists($documentPath) || filesize($documentPath) === 0) {
            show_error('Invoice PDF not found after generation.');
            return;
        }

        $metadata = [
            'invoice_id' => $invoiceId,
            'format' => 'factur-x',
            'profile' => 'EN16931',
        ];

        $metadata = $provider->buildInvoicePayload($invoice, $items, $metadata);

        try {
            $provider = $registry->getProvider($merchantClient['merchant_type']);
            $client = new MerchantClient($provider, $settings);
            $response = $client->sendInvoice($documentPath, $metadata);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('alert_error', $e->getMessage());
            redirect('invoices/view/' . (int) $invoiceId);
            return;
        }
        $this->Merchant_responses_model->create_outbound(
            $merchantClientId,
            $invoiceId,
            $response,
            [
                'document_path' => $documentPath,
                'metadata' => $metadata,
            ]
        );

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

        redirect('invoices/view/' . $invoiceId);
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

        try {
            $provider = $registry->getProvider($merchantClient['merchant_type']);
            $client = new MerchantClient($provider, $settings);
            $response = $client->receiveInvoices();
	} catch (RuntimeException $e) {
	    $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(500)
                 ->set_output(json_encode([
                       'success' => false,
                       'message' => $e->getMessage(),
                 ], JSON_PRETTY_PRINT));
            return;
	}
        $payload = is_array($response['response'] ?? null) ? $response['response'] : [];

        $items = $payload['data']
            ?? $payload['items']
            ?? $payload['invoices']
            ?? $payload
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

        try {
            $provider = $registry->getProvider($merchantClient['merchant_type']);
            $client = new MerchantClient($provider, $settings);
            $events = $client->getInvoiceEvents();
        } catch (RuntimeException $e) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(500)
                 ->set_output(json_encode([
                     'success' => false,
                     'message' => $e->getMessage(),
                 ], JSON_PRETTY_PRINT));
            return;
        }

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
        $merchantClient = $this->Merchant_clients_model->get_by_id(
            (int) $merchantClientId
        );

        if (!$merchantClient) {
            show_error(trans('merchant_client_not_found'));
        }

        $lastResponse = $this->Merchant_responses_model
            ->get_last_response_by_invoice_and_client(
                (int) $invoiceId,
                (int) $merchantClientId
            );


        if (!$lastResponse) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_transmission_found')
            );

            redirect('invoices/view/' . (int) $invoiceId);
        }

	$this->load->model('einvoice/Merchant_clients_model');
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

    public function status($invoiceId, $merchantClientId): void
    {
        $invoiceId = (int) $invoiceId;
        $merchantClientId = (int) $merchantClientId;

        $merchantClient = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if (!$merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));
            return;
        }

        $lastResponse = $this->Merchant_responses_model
            ->get_last_response_by_invoice($invoiceId);

        if (!$lastResponse) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_transmission_found')
            );

            redirect('invoices/view/' . $invoiceId);
            return;
        }

        if (empty($lastResponse['external_id'])) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_external_reference')
            );

            redirect('invoices/view/' . $invoiceId);
            return;
        }

        $settings = $this->Merchant_clients_model->get_settings($merchantClient);
        $registry = new MerchantProviderRegistry();

        try {
            $provider = $registry->getProvider($merchantClient['merchant_type']);
            $client = new MerchantClient($provider, $settings);

            $status = $client->getInvoiceStatus($lastResponse['external_id']);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('alert_error', $e->getMessage());

            redirect('invoices/view/' . $invoiceId);
            return;
        }

        $this->Merchant_responses_model->save_status(
            $invoiceId,
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

        redirect('invoices/view/' . $invoiceId);
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
