<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Integrations extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file_security');

        $this->load->model('integrations/Merchant_clients_model');
        $this->load->model('integrations/Merchant_responses_model');
        $this->load->model('integrations/Integration_sync_runs_model');

        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClient.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationResponseNormalizer.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDriver.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseStatus.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDirection.php';
        require_once APPPATH . 'modules/integrations/libraries/MerchantResponseType.php';
        require_once APPPATH . 'modules/integrations/libraries/PeppolDocumentType.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfile.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfileRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceArtifact.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceSchematronValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/FrenchEInvoiceValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/FrenchEReportingValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceDocumentValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceDocumentService.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceDocumentService.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceSynchronizer.php';
        require_once APPPATH . 'modules/integrations/libraries/IntegrationSyncService.php';
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

        $this->load->helper(['pdf', 'e-invoice']);
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

        try {
            $profileCode = (string) ($invoice->client_einvoicing_version ?? '');
            $profile     = EInvoiceProfileRegistry::builtIn()->get($profileCode);
            if ( ! $profile->supportsProvider($merchantClient['merchant_type'])) {
                throw new RuntimeException('The selected provider does not support this e-invoice profile.');
            }

            $service  = new EInvoiceDocumentService();
            $artifact = $service->generate(
                $invoiceId,
                $invoice,
                $items,
                $profile,
                UPLOADS_FOLDER . 'integrations/outgoing/'
            );
            $documentPath = $artifact->path();
            $metadata     = array_merge(['invoice_id' => $invoiceId], $artifact->metadata());

            $settings = $this->Merchant_clients_model->get_settings($merchantClient);
            $registry = new IntegrationClientRegistry();
            $provider = $registry->getClient($merchantClient['merchant_type']);
            $client   = new IntegrationClient($provider, $settings);
            $metadata = $provider->buildInvoicePayload($invoice, $items, $metadata);
            $response = $client->sendInvoice($documentPath, $metadata);

            $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']);
            if ($driver === null) {
                throw new RuntimeException('Unrecognized integration provider: ' . $merchantClient['merchant_type']);
            }
        } catch (Throwable $e) {
            log_message('error', 'E-invoice send failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', trans('einvoice_send_failed'));
            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

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
            $result = $this->syncService()->run((int) $merchantClientId, 'api', 'incoming');
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

        $this->output
            ->set_content_type('application/json')
            ->set_status_header($result['status'] === 'failed' ? 502 : ($result['status'] === 'skipped' ? 409 : 200))
            ->set_output(json_encode([
                'success' => $result['status'] === 'success',
                'run'     => $result,
            ], JSON_PRETTY_PRINT));
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
            $result = $this->syncService()->run((int) $merchantClientId, 'api', 'events');
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

        $this->output
            ->set_content_type('application/json')
            ->set_status_header($result['status'] === 'failed' ? 502 : ($result['status'] === 'skipped' ? 409 : 200))
            ->set_output(json_encode([
                'success' => $result['status'] === 'success',
                'run'     => $result,
            ], JSON_PRETTY_PRINT));
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

            $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']);
            if ($driver === null) {
                throw new RuntimeException('Unrecognized integration provider: ' . $merchantClient['merchant_type']);
            }
        } catch (Throwable $e) {
            log_message('error', 'E-invoice status request failed: ' . sanitize_for_logging($e->getMessage()));
            $this->session->set_flashdata('alert_error', 'Unable to retrieve status');
            redirect('invoices/view/' . (int) $invoiceId);

            return;
        }

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

        $response    = $result['response'] ?? [];
        $participant = $response['entity'] ?? $response['participant'] ?? $response;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'reachable' => (bool) ($participant['reachable'] ?? false),
                'name'      => $participant['name'] ?? null,
                'country'   => $participant['country'] ?? null,
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

    private function syncService(): IntegrationSyncService
    {
        return new IntegrationSyncService(
            $this->db,
            $this->Merchant_clients_model,
            $this->Merchant_responses_model,
            $this->Integration_sync_runs_model,
            UPLOADS_ARCHIVE_FOLDER
        );
    }
}
