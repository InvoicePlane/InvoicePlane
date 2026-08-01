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
        require_once APPPATH . 'modules/integrations/libraries/PeppolDocumentType.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfile.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfileRegistry.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceArtifact.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceSchematronValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/FrenchEInvoiceValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/EInvoiceDocumentValidator.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceDocumentService.php';
        require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceSynchronizer.php';
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

        $result = (new IncomingInvoiceSynchronizer())->synchronize(
            $client,
            $merchantClient['merchant_type'],
            (int) $merchant_client_id,
            $driver,
            $items,
            $this->Merchant_responses_model,
            UPLOADS_ARCHIVE_FOLDER
        );

        $this->session->set_flashdata(
            $result['failed'] === 0 ? 'alert_success' : 'alert_error',
            sprintf(
                '%d incoming invoice(s) archived; %d already present; %d rejected.',
                $result['archived'],
                $result['skipped'],
                $result['failed']
            )
        );
        redirect('integrations/incoming');
    }

    public function download($responseId): void
    {
        $this->load->helper('file_security');

        $incoming     = $this->Merchant_responses_model->get_incoming_by_id((int) $responseId);
        $relativePath = $incoming['document_path'] ?? null;

        if ($incoming === []
            || $incoming['document_validation_status'] !== 'valid'
            || ! is_string($relativePath)
            || $relativePath === '') {
            show_404();

            return;
        }

        $path = UPLOADS_ARCHIVE_FOLDER . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if ( ! is_file($path) || ! validate_file_in_directory($path, UPLOADS_ARCHIVE_FOLDER)) {
            show_404();

            return;
        }

        $mimeType = in_array($incoming['document_mime_type'], ['application/pdf', 'application/xml'], true)
            ? $incoming['document_mime_type']
            : 'application/octet-stream';
        $filename = sanitize_filename_for_header(
            $incoming['document_name'] ?: basename($path)
        );

        $content = file_get_contents($path);
        if ($content === false) {
            show_404();

            return;
        }

        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_header('X-Content-Type-Options: nosniff')
            ->set_header('Content-Length: ' . mb_strlen($content, '8bit'))
            ->set_content_type($mimeType)
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_output($content);
    }
}
