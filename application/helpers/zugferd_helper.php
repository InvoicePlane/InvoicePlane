<?php defined('BASEPATH') OR exit('No direct script access allowed');

use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfBuilder;
use Mpdf\Mpdf;

if (!function_exists('createZugferdPdfAndXml')) {
    function createZugferdPdfAndXml($invoice_id)
    {
        $CI =& get_instance();
        $CI->load->model('invoices/Mdl_invoices');     // Lade das Modell mit dem korrekten Pfad
        $CI->load->model('invoices/Mdl_items');        // Lade das Modell mit dem korrekten Pfad
        $CI->load->model('clients/Mdl_clients');       // Lade das Modell mit dem korrekten Pfad
        $CI->load->model('settings/Mdl_settings');     // Lade das Modell für die Einstellungen

        // Überprüfen, ob das Modell geladen wurde
        if (!isset($CI->Mdl_settings)) {
            throw new Exception("Mdl_settings model not loaded.");
        }

        // Lade die Invoice-Daten
        $invoice = $CI->Mdl_invoices->get_by_id($invoice_id);
        $invoice_items = $CI->Mdl_items->where('invoice_id', $invoice_id)->get()->result();
        $client = $CI->Mdl_clients->get_by_id($invoice->client_id);

        // Konvertiere die Datumsangaben in DateTime-Objekte
        $documentDate = !empty($invoice->invoice_date_created) ? new DateTime($invoice->invoice_date_created) : null;
        $effectiveSpecifiedPeriod = !empty($invoice->invoice_date_due) ? new DateTime($invoice->invoice_date_due) : null;

        // Überprüfen, ob die Datumsangaben korrekt sind
        if ($documentDate === null) {
            throw new Exception("Invalid invoice_date_created: " . $invoice->invoice_date_created);
        }

        // Lade das ausgewählte HTML-Template aus den Einstellungen
        $template = $CI->Mdl_settings->setting('pdf_invoice_template');
        if (!$template) {
            throw new Exception("No template selected in settings.");
        }

        // Debugging-Ausgabe
        log_message('debug', 'Selected template: ' . $template);

        $template_path = APPPATH . 'views/invoice_templates/pdf/' . $template;
        if (!file_exists($template_path) || is_dir($template_path)) {
            throw new Exception("Template not found or is a directory: " . $template_path);
        }

        // Lade das HTML-Template
        ob_start();
        include $template_path;
        $htmlContent = ob_get_clean();

        // Konvertiere HTML zu PDF mit mPDF
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($htmlContent);

        // Definiere den Pfad zum Speichern des PDF-Dokuments
        $outputPdfPath = FCPATH . 'uploads/invoice_' . $invoice->invoice_id . '.pdf';
        $mpdf->Output($outputPdfPath, \Mpdf\Output\Destination::FILE); // PDF als Datei speichern

        // Erstelle die XML-Daten aus den InvoicePlane-Daten
        $documentBuilder = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);
        $documentBuilder->setDocumentInformation(
            $invoice->invoice_number,           // Invoice Number
            '380',                              // Standard-Dokumenttypcode für Rechnung
            $documentDate,                      // Document Date
            $invoice->user_company,             // User Company
            $invoice->user_address_1,           // User Address
            $invoice->user_zip,                 // User ZIP
            $invoice->user_city,                // User City
            $effectiveSpecifiedPeriod           // Effective Specified Period (nullable)
        );
        $documentBuilder->setBuyerInformation(
            $client->client_name,               // Client Name
            $client->client_address_1,          // Client Address
            $client->client_zip,                // Client ZIP
            $client->client_city,               // Client City
            $client->client_country             // Client Country
        );

        foreach ($invoice_items as $item) {
            $documentBuilder->addDocumentItem(
                $item->item_name,                // Item Name
                $item->item_quantity,            // Item Quantity
                $item->item_price,               // Item Price
                $item->item_subtotal             // Item Subtotal
            );
        }

        // Erstelle ein neues ZUGFeRD-Dokument
        $documentPdfBuilder = ZugferdDocumentPdfBuilder::fromFile($documentBuilder, $outputPdfPath);

        // Generiere das ZUGFeRD PDF-Dokument
        $documentPdfBuilder->generateDocument();
        $documentPdfBuilder->saveDocument($outputPdfPath);

        return $outputPdfPath;
    }
}