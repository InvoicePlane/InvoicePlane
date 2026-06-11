<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pdp extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pdp/Pdp_model');
        $this->load->helper(array('url', 'form'));
        require_once APPPATH . 'modules/pdp/libraries/PdpProviderRegistry.php';
    }

    public function index()
    {
        $data = array(
            'settings' => $this->Pdp_model->get_settings(),
            'transmissions' => $this->Pdp_model->transmissions(),
            'providers' => (new PdpProviderRegistry())->providers(),
        );
        $this->layout->buffer('content', 'pdp/index', $data);
        $this->layout->render();
    }

    public function settings()
    {
        if ($this->input->method() === 'post') {
            $this->Pdp_model->save_settings(array(
                'provider' => $this->input->post('provider', true),
                'api_url' => $this->input->post('api_url', true),
                'client_id' => $this->input->post('client_id', true),
                'client_secret' => $this->input->post('client_secret', true),
                'access_token' => $this->input->post('access_token', true),
                'auth_type' => $this->input->post('auth_type', true),
                'api_key' => $this->input->post('api_key', true),
                'api_key_header' => $this->input->post('api_key_header', true),
                'token_url' => $this->input->post('token_url', true),
                'scope' => $this->input->post('scope', true),
                'send_endpoint' => $this->input->post('send_endpoint', true),
                'status_endpoint' => $this->input->post('status_endpoint', true),
                'receive_endpoint' => $this->input->post('receive_endpoint', true),
                'events_endpoint' => $this->input->post('events_endpoint', true),
                'file_field' => $this->input->post('file_field', true),
                'extra_payload_json' => $this->input->post('extra_payload_json', false),
                'disable_pre_check' => $this->input->post('disable_pre_check', true),
                'enabled' => (int) (bool) $this->input->post('enabled'),
            ));
            redirect('pdp/settings?saved=1');
        }
        $this->layout->buffer('content', 'pdp/settings', array(
            'settings' => $this->Pdp_model->get_settings(),
            'providers' => (new PdpProviderRegistry())->providers(),
        ));
        $this->layout->render();
    }

    /**
     * Page facture cote PDP : verification Factur-X + actions transmettre/statut.
     * URL : index.php/pdp/invoice/{invoice_id}
     */
    public function invoice($invoiceId)
    {
        $invoiceId = (int) $invoiceId;
        $settings = $this->Pdp_model->get_settings();
        $latest = $this->Pdp_model->latest_for_invoice($invoiceId);
        $pdf = $this->find_existing_facturx_pdf($invoiceId);

        $data = array(
            'invoice_id' => $invoiceId,
            'settings' => $settings,
            'latest' => $latest,
            'pdf' => $pdf,
        );
        $this->layout->buffer('content', 'pdp/invoice', $data);
        $this->layout->render();
    }

    /**
     * Transmet le PDF Factur-X existant ou genere par InvoicePlane a la PA/PDP.
     * URL : index.php/pdp/send/{invoice_id}
     */
    public function send($invoiceId)
    {
        $invoiceId = (int) $invoiceId;
        $settings = $this->Pdp_model->get_settings();

        if (empty($settings['enabled'])) {
            show_error('Le connecteur PA/PDP est desactive. Va dans Facturation electronique > Configuration.');
        }

        require_once APPPATH . 'modules/pdp/libraries/PdpClient.php';

        $filePath = $this->resolve_facturx_pdf($invoiceId);
        $meta = $this->pdf_meta($filePath);
        $request = array(
            'file' => $filePath,
            'file_name' => basename($filePath),
            'sha256' => $meta['sha256'],
            'size' => $meta['size'],
            'has_facturx_xml' => $meta['has_facturx_xml'],
        );

        $transmissionId = $this->Pdp_model->create_transmission($invoiceId, $settings['provider'] ?? 'demo', $request);

        $client = new PdpClient($settings);
        $response = $client->sendInvoice($filePath, array(
            'invoice_id' => $invoiceId,
            'file_name' => basename($filePath),
            'sha256' => $meta['sha256'],
            'size' => $meta['size'],
            'has_facturx_xml' => $meta['has_facturx_xml'],
            'external_id' => 'IP-' . $invoiceId,
        ));
        $this->Pdp_model->update_transmission($transmissionId, $response);

        redirect('pdp/invoice/' . $invoiceId);
    }

    public function status($invoiceId)
    {
        $invoiceId = (int) $invoiceId;
        $settings = $this->Pdp_model->get_settings();
        $latest = $this->Pdp_model->latest_for_invoice($invoiceId);
        if (!$latest || empty($latest['external_id'])) {
            show_error('Aucune transmission PDP avec identifiant externe pour cette facture.');
        }

        require_once APPPATH . 'modules/pdp/libraries/PdpClient.php';
        $client = new PdpClient($settings);
        $response = $client->getInvoiceStatus($latest['external_id']);
        $this->Pdp_model->update_transmission((int) $latest['id'], $response);
        redirect('pdp/invoice/' . $invoiceId);
    }

    public function receive()
    {
        $settings = $this->Pdp_model->get_settings();
        require_once APPPATH . 'modules/pdp/libraries/PdpClient.php';
        $client = new PdpClient($settings);
        $response = $client->receiveInvoices();

        if (!empty($response['raw']) && is_array($response['raw'])) {
            $items = $response['raw']['items'] ?? $response['raw']['data'] ?? $response['raw'];
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (is_array($item)) {
                        $item['provider'] = $settings['provider'] ?? 'api';
                        $this->Pdp_model->save_incoming($item);
                    }
                }
            }
        }

        redirect('pdp');
    }

    /**
     * Retourne un PDF Factur-X pret a etre transmis.
     * Strategie V0.7 :
     * 1. reprendre le PDF deja genere/copie dans uploads/facturx ;
     * 2. rechercher un PDF de facture existant dans uploads ;
     * 3. generer le PDF via le helper PDF natif InvoicePlane, puis le sauvegarder ;
     * 4. refuser si aucun PDF exploitable n'est trouve.
     */
    private function resolve_facturx_pdf(int $invoiceId): string
    {
        $existing = $this->find_existing_facturx_pdf($invoiceId);
        if ($existing !== null) {
            return $existing;
        }

        $targetDir = FCPATH . 'uploads/facturx/';
        $targetPath = $targetDir . 'invoice_' . $invoiceId . '.pdf';

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            show_error('Impossible de creer le dossier Factur-X : ' . $targetDir);
        }

        $generatedPath = $this->generate_facturx_pdf_with_invoiceplane($invoiceId, $targetPath);
        if ($generatedPath !== null && $this->is_readable_pdf($generatedPath)) {
            return $generatedPath;
        }

        show_error(
            'PDF Factur-X introuvable pour la facture #' . $invoiceId . '. ' .
            'Genere d abord le PDF depuis InvoicePlane et verifie avec : pdfdetach -list fichier.pdf. ' .
            'Chemin attendu en priorite : ' . $targetPath
        );
    }

    private function find_existing_facturx_pdf(int $invoiceId): ?string
    {
        foreach ($this->candidate_facturx_paths($invoiceId) as $candidatePath) {
            if ($this->is_readable_pdf($candidatePath)) {
                return $candidatePath;
            }
        }
        return null;
    }

    private function generate_facturx_pdf_with_invoiceplane(int $invoiceId, string $targetPath): ?string
    {
        if (function_exists('generate_invoice_pdf') === false) {
            $this->load->helper('pdf');
        }

        if (function_exists('generate_invoice_pdf') === false) {
            return null;
        }

        try {
            ob_start();
            $result = generate_invoice_pdf($invoiceId, false, null);
            $buffer = ob_get_clean();

            if (is_string($result) && substr($result, 0, 4) === '%PDF') {
                file_put_contents($targetPath, $result);
                return $targetPath;
            }

            if (is_string($buffer) && substr($buffer, 0, 4) === '%PDF') {
                file_put_contents($targetPath, $buffer);
                return $targetPath;
            }

            if (is_string($result) && is_file($result) && $this->is_readable_pdf($result)) {
                @copy($result, $targetPath);
                return $this->is_readable_pdf($targetPath) ? $targetPath : $result;
            }
        } catch (Throwable $e) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            log_message('error', 'PDP Factur-X generation failed for invoice ' . $invoiceId . ': ' . $e->getMessage());
        }

        return null;
    }

    private function candidate_facturx_paths(int $invoiceId): array
    {
        $paths = array(
            FCPATH . 'uploads/facturx/invoice_' . $invoiceId . '.pdf',
            FCPATH . 'uploads/factur-x/invoice_' . $invoiceId . '.pdf',
            FCPATH . 'uploads/e-invoices/invoice_' . $invoiceId . '.pdf',
            FCPATH . 'uploads/temp/invoice_' . $invoiceId . '.pdf',
            FCPATH . 'uploads/archive/invoice_' . $invoiceId . '.pdf',
        );

        // Recherche large : utile car InvoicePlane peut nommer le PDF avec le numero de facture.
        $roots = array(FCPATH . 'uploads', FCPATH . 'uploads/archive', FCPATH . 'uploads/temp', FCPATH . 'uploads/facturx');
        foreach ($roots as $root) {
            if (!is_dir($root)) {
                continue;
            }
            foreach (glob($root . '/*.pdf') ?: array() as $pdf) {
                if (strpos($pdf, (string) $invoiceId) !== false) {
                    $paths[] = $pdf;
                }
            }
        }

        return array_values(array_unique($paths));
    }

    private function pdf_meta(string $path): array
    {
        return array(
            'size' => filesize($path),
            'sha256' => hash_file('sha256', $path),
            'has_facturx_xml' => $this->pdf_has_facturx_xml($path),
        );
    }

    private function pdf_has_facturx_xml(string $path): bool
    {
        // Test simple sans dependance : le nom de la piece jointe apparait souvent dans le PDF.
        $chunk = @file_get_contents($path, false, null, 0, min(filesize($path), 5242880));
        if (is_string($chunk) && (stripos($chunk, 'factur-x.xml') !== false || stripos($chunk, 'zugferd-invoice.xml') !== false)) {
            return true;
        }

        // Test plus fiable si poppler-utils/pdfdetach est installe.
        if (function_exists('shell_exec')) {
            $out = @shell_exec('pdfdetach -list ' . escapeshellarg($path) . ' 2>/dev/null');
            if (is_string($out) && (stripos($out, 'factur-x.xml') !== false || stripos($out, 'zugferd-invoice.xml') !== false)) {
                return true;
            }
        }

        return false;
    }

    private function is_readable_pdf(string $path): bool
    {
        return is_file($path) && is_readable($path) && filesize($path) > 100 && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
