<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Integrations extends Admin_Controller
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

    public function providers(): void
    {
        $registry = new IntegrationClientRegistry();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($registry->all(), JSON_PRETTY_PRINT));
    }

    public function send_invoice($invoiceId, $merchantClientId): void
    {
        if ( ! $this->isPostRequest()) {
            return;
        }

        $invoiceId        = (int) $invoiceId;
        $merchantClientId = (int) $merchantClientId;

        $merchantClient = $this->Merchant_clients_model->get_by_id($merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

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

        $documentDir = FCPATH . 'uploads/integrations/outgoing/';

        if ( ! is_dir($documentDir)) {
            if ( ! mkdir($documentDir, 0775, true) && ! is_dir($documentDir)) {
                show_error('Unable to create e-invoice output directory.');

                return;
            }
        }

        $documentPath = $documentDir . 'invoice_' . $invoiceId . '.pdf';
        if (file_exists($documentPath) && ! unlink($documentPath)) {
            show_error('Unable to replace existing invoice PDF.');

            return;
        }

        $pdfContent = generate_invoice_pdf($invoiceId, false, null, null);

        if (empty($pdfContent)) {
            show_error('InvoicePlane did not return PDF content.');

            return;
        }

        if (is_string($pdfContent) && file_exists($pdfContent)) {
            if ( ! copy($pdfContent, $documentPath)) {
                show_error('Unable to copy generated invoice PDF.');

                return;
            }
        } else {
            if (file_put_contents($documentPath, $pdfContent) === false) {
                show_error('Unable to write generated invoice PDF.');

                return;
            }
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

        try {
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $metadata = $provider->buildInvoicePayload($invoice, $items, $metadata);
            $response = $client->sendInvoice($documentPath, $metadata);
        } catch (Throwable $e) {
            log_message('error', 'E-invoice send failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('einvoice_send_failed'));
            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        $this->Merchant_responses_model->create_outbound(
            $merchantClientId,
            $invoiceId,
            $response,
            $driver
        );

        if ( ! empty($response['success'])) {
            $this->session->set_flashdata(
                'alert_success',
                trans('einvoice_send_success')
            );
        } else {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_send_failed')
            );
        }

        redirect('invoices/view/' . $invoiceId);
    }

    public function receive($merchantClientId): void
    {
        if ( ! $this->isPostRequest()) {
            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchantClientId);

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
            log_message('error', 'E-invoice receive failed: ' . sanitize_for_logging($e->getMessage()));
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Provider request failed.',
                ], JSON_PRETTY_PRINT));

            return;
        }

        $items  = IntegrationResponseNormalizer::extractItems($response, ['data', 'items', 'invoices']);
        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        foreach ($items as $item) {
            $this->Merchant_responses_model->create_inbound_item(
                (int) $merchantClientId,
                $item,
                $driver
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function sync_events($merchantClientId)
    {
        if ( ! $this->isPostRequest()) {
            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_by_id((int) $merchantClientId);

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

        try {
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);

            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);

            $events   = $client->getInvoiceEvents();
        } catch (Throwable $e) {
            log_message('error', 'E-invoice event sync failed: ' . sanitize_for_logging($e->getMessage()));
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Provider request failed.',
                ], JSON_PRETTY_PRINT));

            return;
        }

        $items  = IntegrationResponseNormalizer::extractItems($events, ['data', 'items', 'events']);
        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        foreach ($items as $item) {
            $this->Merchant_responses_model->create_event_item(
                (int) $merchantClientId,
                $item,
                $driver
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($events, JSON_PRETTY_PRINT));
    }

    public function status($invoiceId, $merchantClientId)
    {
        if ( ! $this->isPostRequest()) {
            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_by_id(
            (int) $merchantClientId
        );

        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            show_error(trans('merchant_client_not_found'));

            return;
        }

        $lastResponse = $this->Merchant_responses_model
            ->get_last_response_by_invoice((int) $invoiceId, (int) $merchantClientId);

        if ( ! $lastResponse) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_transmission_found')
            );

            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

        if (empty($lastResponse['merchant_response_reference'])) {
            $this->session->set_flashdata(
                'alert_error',
                trans('einvoice_no_external_reference')
            );

            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

        try {
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $status   = $client->getInvoiceStatus(
                $lastResponse['merchant_response_reference']
            );
        } catch (Throwable $e) {
            log_message('error', 'E-invoice status request failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Unable to retrieve status');
            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

        $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']) ?? MerchantResponseDriver::LetsPeppol;

        $this->Merchant_responses_model->save_status(
            (int) $invoiceId,
            $status,
            $driver,
            $lastResponse
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

        redirect('invoices/view/' . (int) $invoiceId);
    }

    public function validate_participant(): void
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);

            return;
        }

        $participantId = trim((string) $this->input->post('participant_id'));

        if ($participantId === '' || mb_strlen($participantId) > 100) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(422)
                ->set_output(json_encode(['reachable' => false, 'error' => 'Invalid participant identifier.']));

            return;
        }

        $merchantClient = $this->Merchant_clients_model->get_default_enabled();

        if ( ! $merchantClient) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['reachable' => false, 'error' => trans('peppol_no_provider')]));

            return;
        }

        try {
            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $result   = $client->lookupParticipant($participantId);
        } catch (Throwable $e) {
            log_message('error', 'Peppol participant validation failed: ' . sanitize_for_logging($e->getMessage()));
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(502)
                ->set_output(json_encode(['reachable' => false, 'error' => 'Provider request failed.']));

            return;
        }

        $response = $result['response'] ?? [];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'reachable' => (bool) ($response['reachable'] ?? false),
                'name'      => $response['name'] ?? null,
                'country'   => $response['country'] ?? null,
            ]));
    }

    public function history($invoiceId): void
    {
        $this->layout->set([
            'invoice_id' => (int) $invoiceId,
            'history'    => $this->Merchant_responses_model->get_outbound_by_invoice((int) $invoiceId),
        ]);

        $this->layout->buffer('content', 'integrations/history');
        $this->layout->render();
    }

    public function history_client($clientId): void
    {
        $this->layout->set([
            'client_id' => (int) $clientId,
            'history'   => $this->Merchant_responses_model->get_by_client((int) $clientId),
        ]);

        $this->layout->buffer('content', 'integrations/history_client');
        $this->layout->render();
    }

    private function isPostRequest(): bool
    {
        if ($this->input->method() === 'post') {
            return true;
        }

        show_error('Method not allowed', 405);

        return false;
    }
}
