<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * eInvoicing add-ons by Verony
 */

/**
 * Print a html for global discounts table template.
 *
 * @param obj  $obj ($invoice or $quote object)
 * @param bool $show_item_discounts
 * @param str  $is 'invoice' or 'quote'
 *
 * @scope views/[invoice|quote]_templates/pdf/InvoicePlane[| - paid| - overdue].pdf
 *
 * @return void
 */
function discount_global_print_in_pdf($obj, $show_item_discounts, $is = 'invoice')
{
    $type = ['p' => $is . '_discount_percent', 'a' => $is . '_discount_amount'];
    $discount = 0;
    if ($obj->{$type['p']} != '0.00') { // discount_percent
        $discount = format_amount($obj->{$type['p']}) . '%';
    } elseif ($obj->{$type['a']} != '0.00') { // discount_amount
        $discount = format_currency($obj->{$type['a']});
    }

    if ($discount) {
?>
            <tr>
                <td class="text-right" colspan="<?php echo $show_item_discounts ? '5' : '4'; ?>"><?php
                    echo rtrim(trans('discount'), ' '); // Rem not space char (in French ip_lang & maybe other)
                ?></td>
                <td class="text-right"><?php echo $discount; ?></td>
            </tr>
<?php
    }
}

/**
 * Generate the PDF for an invoice.
 *
 * @param      $invoice_id
 * @param bool $stream
 * @param null $invoice_template
 * @param null $is_guest
 *
 * @return string
 */
function generate_invoice_pdf($invoice_id, $stream = true, $invoice_template = null, $is_guest = null)
{
    $CI = & get_instance();

    $CI->load->model(
        [
            'invoices/mdl_items',
            'invoices/mdl_invoices',
            'invoices/mdl_invoice_tax_rates',
            'custom_fields/mdl_custom_fields',
            'payment_methods/mdl_payment_methods',
        ]
    );

    $CI->load->helper(
        [
            'country',
            'client',
            'e-invoice', // eInvoicing++
        ]
    );

    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    $invoice = $CI->mdl_invoices->get_payments($invoice);

    // Override system language with client language
    set_language($invoice->client_language);

    if (! $invoice_template) {
        $CI->load->helper('template');
        $invoice_template = select_pdf_invoice_template($invoice);
    }

    $payment_method = $CI->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
    if ($invoice->payment_method == 0) {
        $payment_method = false;
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result();

    // Discount settings
    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
            break;
        }
    }

    // Get all custom fields
    $custom_fields =
    [
        'invoice' => $CI->mdl_custom_fields->get_values_for_fields('mdl_invoice_custom', $invoice->invoice_id),
        'client'  => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $invoice->client_id),
        'user'    => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $invoice->user_id),
    ];

    if ($invoice->quote_id) {
        $custom_fields['quote'] = $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $invoice->quote_id);
    }

    // START eInvoicing
    $filename = trans('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);

    // Generate the appropriate UBL/CII
    $xml_id    = $invoice->client_einvoicing_version;
    $options   = [];
    $generator = $xml_id;
    $embed_xml = false;
    $path      = APPPATH . 'helpers/XMLconfigs/';
    if (file_exists($path . $xml_id . '.php') && include $path . $xml_id . '.php') {
        $embed_xml = $xml_setting['embedXML'];
        $XMLname   = $xml_setting['XMLname'];
        $options   = (empty($xml_setting['options']) ? $options : $xml_setting['options']); // Optional
        $generator = (empty($xml_setting['generator']) ? $generator : $xml_setting['generator']); // Optional
    }

    // PDF associated or embedded (CII e.g. Zugferd, Factur-X) Xml file
    $associatedFiles = null;
    if ($embed_xml && $invoice->client_einvoicing_active == 1) {
        // Create the CII XML file
        $associatedFiles = [[
            'name'           => $XMLname,
            'mime'           => 'text/xml',
            'description'    => $xml_id . ' CII Invoice',
            'AFRelationship' => 'Alternative',
            'path'           => generate_xml_invoice_file($invoice, $items, $generator, $filename, $options),
        ]];
    }

    $data = [
        'invoice'             => $invoice,
        'invoice_tax_rates'   => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items'               => $items,
        'payment_method'      => $payment_method,
        'output_type'         => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields'       => $custom_fields,
        'legacy_calculation'  => config_item('legacy_calculation'),
    ];

    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, true);

    // Create PDF with or without an embedded XML
    $CI->load->helper('mpdf');

    $retval = pdf_create(
        html:             $html,
        filename:         $filename,
        stream:           $stream,
        password:         $invoice->invoice_password,
        isInvoice:        true,
        is_guest:         $is_guest,
        embed_xml:        $embed_xml,
        associated_files: $associatedFiles
    );

    // To Simplify xml validation (remove einvoice_test.xml file in uploads/temp when debug is over)
    if (IP_DEBUG) {
        @unlink(UPLOADS_TEMP_FOLDER . 'einvoice_test.xml'); // Same file but Always new (when get pdf)
        @copy(UPLOADS_TEMP_FOLDER . $filename . '.xml', UPLOADS_TEMP_FOLDER . 'einvoice_test.xml');
    }

    if ($embed_xml && file_exists(UPLOADS_TEMP_FOLDER . $filename . '.xml')) {
        // Delete the tmp CII-XML file
        unlink(UPLOADS_TEMP_FOLDER . $filename . '.xml');
    }

    // Create the UBL XML file if not embed & the client e-Invoicing active
    if ($xml_id != '' && $embed_xml !== true) {
        // Added the (unnecessary) prefix "date(Y-m-d)_" to the invoice file name to get the same ".pdf" and ".xml" file names!
        $filename = date('Y-m-d') . '_' . $filename;

        if ($invoice->client_einvoicing_active == 1) {
            generate_xml_invoice_file($invoice, $items, $generator, $filename, $options);
        }
    }
    // END eInvoicing

    return $retval;
}

function generate_invoice_sumex($invoice_id, $stream = true, $client = false)
{
    $CI = & get_instance();

    $CI->load->model('invoices/mdl_items');
    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    $CI->load->library('Sumex', [
        'invoice' => $invoice,
        'items'   => $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
    ]);

    // Append a copy at the end and change the title:
    // WARNING: The title depends on what invoice type is (TP, TG)
    // and is language-dependant. Fix accordingly if you really need this hack
    $temp     = tempnam('/tmp', 'invsumex_');
    $tempCopy = tempnam('/tmp', 'invsumex_');
    $pdf      = new FPDI();
    $sumexPDF = $CI->sumex->pdf();

    $sha1sum  = sha1($sumexPDF);
    $shortsum = mb_substr($sha1sum, 0, 8);
    $filename = trans('invoice') . '_' . $invoice->invoice_number . '_' . $shortsum;

    if (! $client) {
        file_put_contents($temp, $sumexPDF);

        // Hackish
        $sumexPDF = str_replace(
            'Giustificativo per la richiesta di rimborso',
            'Copia: Giustificativo per la richiesta di rimborso',
            $sumexPDF
        );

        file_put_contents($tempCopy, $sumexPDF);

        $pageCount = $pdf->setSourceFile($temp);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size       = $pdf->getTemplateSize($templateId);

            if ($size['w'] > $size['h']) {
                $pageFormat = 'L';  //  landscape
            } else {
                $pageFormat = 'P';  //  portrait
            }

            $pdf->addPage($pageFormat, [$size['w'], $size['h']]);
            $pdf->useTemplate($templateId);
        }

        $pageCount = $pdf->setSourceFile($tempCopy);

        for ($pageNo = 2; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size       = $pdf->getTemplateSize($templateId);

            if ($size['w'] > $size['h']) {
                $pageFormat = 'L';  //  landscape
            } else {
                $pageFormat = 'P';  //  portrait
            }

            $pdf->addPage($pageFormat, [$size['w'], $size['h']]);
            $pdf->useTemplate($templateId);
        }

        unlink($temp);
        unlink($tempCopy);

        if ($stream) {
            header('Content-Type', 'application/pdf');
            $pdf->Output($filename . '.pdf', 'I');

            return;
        }

        $filePath = UPLOADS_TEMP_FOLDER . $filename . '.pdf';
        $pdf->Output($filePath, 'F');

        return $filePath;
    }
    if ($stream) {
        return $sumexPDF;
    }

    $filePath = UPLOADS_TEMP_FOLDER . $filename . '.pdf';
    file_put_contents($filePath, $sumexPDF);

    return $filePath;
}

/**
 * Generate the PDF for a quote.
 *
 * @param      $quote_id
 * @param bool $stream
 * @param null $quote_template
 *
 * @return string
 *
 * @throws \Mpdf\MpdfException
 */
function generate_quote_pdf($quote_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model(
        [
            'quotes/mdl_quotes',
            'quotes/mdl_quote_items',
            'quotes/mdl_quote_tax_rates',
            'custom_fields/mdl_custom_fields',
        ]
    );
    $CI->load->helper(
        [
            'country',
            'client',
        ]
    );

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    // Override language with system language
    set_language($quote->client_language);

    if (! $quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result();

    $show_item_discounts = false;
    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_item_discounts = true;
            break;
        }
    }

    // Get all custom fields
    $custom_fields =
    [
        'quote'  => $CI->mdl_custom_fields->get_values_for_fields('mdl_quote_custom', $quote->quote_id),
        'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $quote->client_id),
        'user'   => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $quote->user_id),
    ];

    $data =
    [
        'quote'               => $quote,
        'quote_tax_rates'     => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items'               => $items,
        'output_type'         => 'pdf',
        'show_item_discounts' => $show_item_discounts,
        'custom_fields'       => $custom_fields,
        'legacy_calculation'  => config_item('legacy_calculation'),
    ];

    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, trans('quote') . '_' . str_replace(['\\', '/'], '_', $quote->quote_number), $stream, $quote->quote_password);
}
