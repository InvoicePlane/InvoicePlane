<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

use horstoeko\zugferd\ZugferdPdfWriter; // by chrissie
use horstoeko\zugferd\ZugferdSettings;  // by chrissie
/**
 * Converts a plain PDF to PDF/A-3b (without attachments).
 * use horstoeko zugpferd library
 * @param string $sourcePdf Source PDF file name
 * @param string $destPdf Destination PDF file name (can be the same as the source)
 * @param string $title Title metadata
 * @param string $author Author metadata
 * @param string $creatorTool Creator tool metadata
 * @return void
 */
function convert_pdf_to_pdfa(string $sourcePdf, string $destPdf, string $title, string $author, string $ass_file_name='', string $ass_file_path='', string $creatorTool='')
{
    $pdfWriter = new ZugferdPdfWriter();

    // Copy pages from the original PDF
    $pageCount = $pdfWriter->setSourceFile($sourcePdf);

    for ($pageNumber = 1; $pageNumber <= $pageCount; ++$pageNumber) {
        $pageContent = $pdfWriter->importPage($pageNumber, '/MediaBox');
        $pdfWriter->AddPage();
        $pdfWriter->useTemplate($pageContent, 0, 0, null, null, true);
    }

    // Set PDF version 1.7 according to PDF/A-3 ISO 32000-1
    $pdfWriter->setPdfVersion('1.7', true);

    // Update meta data (e.g. such as author, producer, title)
    $pdfMetadata = array(
            'author' => $author,
            'keywords' => '',
            'title' => $title,
            'subject' => '',
            'createdDate' => date('Y-m-d\TH:i:s') . '+00:00',
            'modifiedDate' => date('Y-m-d\TH:i:s') . '+00:00',
            );
    $pdfWriter->setPdfMetadataInfos($pdfMetadata);

    $xmp = simplexml_load_file(ZugferdSettings::getFullXmpMetaDataFilename());
    $descriptionNodes = $xmp->xpath('rdf:Description');

    // rdf:Description urn:factur-x:pdfa:CrossIndustryDocument:invoice:1p0#
    // $descriptionNodes[0] not applicable

    // Factur-X PDFA Extension Schema http://www.aiim.org/pdfa/ns/extension/
    // $descriptionNodes[1] not applicable

    // rdf:Description http://www.aiim.org/pdfa/ns/id/
    // PDF/A-3b declaration
    $descPdfAid = $descriptionNodes[2];
    $pdfWriter->addMetadataDescriptionNode($descPdfAid->asXML());

    // rdf:Description http://purl.org/dc/elements/1.1/
    $descDc = $descriptionNodes[3];
    $descNodes = $descDc->children('dc', true);
    $descNodes->title->children('rdf', true)->Alt->li = $pdfMetadata['title'];
    $descNodes->creator->children('rdf', true)->Seq->li = $pdfMetadata['author'];
    $descNodes->description->children('rdf', true)->Alt->li = $pdfMetadata['subject'];
    $pdfWriter->addMetadataDescriptionNode($descDc->asXML());

    // rdf:Description http://ns.adobe.com/pdf/1.3/
    $descAdobe = $descriptionNodes[4];
    $descAdobe->children('pdf', true)->{'Producer'} = 'Xtra-PDF';
    $pdfWriter->addMetadataDescriptionNode($descAdobe->asXML());

    // rdf:Description http://ns.adobe.com/xap/1.0/
    $descXmp = $descriptionNodes[5];
    $xmpNodes = $descXmp->children('xmp', true);
    $xmpNodes->{'CreatorTool'} = $creatorTool;
    $xmpNodes->{'CreateDate'} = $pdfMetadata['createdDate'];
    $xmpNodes->{'ModifyDate'} = $pdfMetadata['modifiedDate'];
    $pdfWriter->addMetadataDescriptionNode($descXmp->asXML());

    // add zugpferd/erechnung xml again because it is now removed
    if (!empty($ass_file_name)) {
        $pdfWriter->attach($ass_file_path, $ass_file_name);
    }

    $pdfWriter->Output($destPdf, 'F');
}


/**
 * Create a PDF
 *
 * @param $html
 * @param $filename
 * @param bool $stream
 * @param null $password
 * @param null $isInvoice
 * @param null $is_guest
 * @param bool $zugferd_invoice
 * @param null $associated_files
 * @param $pdf_stamp
 *   default "", no stamp. true with default stamp,
 *   can also use a filename for multiple different stamps
 *
 * @return string
 * @throws \Mpdf\MpdfException
 */

use mikehaertl\pdftk\Pdf;

function pdf_create(
    $html,
    $filename,
    $stream = true,
    $password = null,
    $isInvoice = null,
    $is_guest = null,
    $zugferd_invoice = false,
    $associated_files = null,

    $pdf_stamp = "",
    $additionalFooter =""
) {
    $CI = &get_instance();

    // Get the invoice from the archive if available
    $invoice_array = array();

    // mPDF loading
    $mpdf = new \Mpdf\Mpdf([
            'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
    ]);

    // special footer with page numeration
    $invoiceNrAndPageOnFooter = env('INVOICE_PAGE_FOOTER');

    // size, margin by chrissie, what is this unit? it was trial and error
    if ($invoiceNrAndPageOnFooter == true ) {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',
                'margin_left'   => 19,
                'margin_right'  => 10,
                'margin_top'    => 40,
                'margin_bottom' => 22,
                'margin_header' => 0,
                'margin_footer' => 7,
                'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
        ]);
    } else {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',
                'margin_left'   => 19,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
                'margin_header' => 0,
                'margin_footer' => 15,
                'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
        ]);
    }

    // how to add new font by chrissie
    //./vendor/mpdf/mpdf/ttfonts/Raleway-Medium.ttf
    // change default dejavusanscondensed
    // to raleway - your mileage may vary
    $mpdf->fontdata=[];
    $mpdf->fontdata['dejavusanscondensed'] = [
        'R' => 'Raleway-Medium.ttf',
        'I' => 'Raleway-Italic.ttf',
        'B' => 'Raleway-Bold.ttf',
        ];
    // dejavuserifcondensed needed for watermark
    $mpdf->fontdata['dejavuserifcondensed'] = [
        'R' => 'Raleway-Medium.ttf',
        'I' => 'Raleway-Italic.ttf',
        'B' => 'Raleway-Bold.ttf',
        ];


    // mPDF configuration
    $mpdf->useAdobeCJK = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;

    if (IP_DEBUG) {
        // Enable image error logging
        $mpdf->showImageErrors = true;
    }

    // Include zugferd if enabled
    if ($zugferd_invoice) {
        $CI->load->helper('zugferd');
        $mpdf->PDFA = true;
        $mpdf->PDFAauto = true;
        $mpdf->SetAdditionalXmpRdf(zugferd_rdf());
        $mpdf->SetAssociatedFiles($associated_files);
    }

    // Set a password if set for the voucher
    if (!empty($password)) {
        $mpdf->SetProtection(array('copy', 'print'), $password, $password);
    }

    // Check if the archive folder is available
    if (!(is_dir(UPLOADS_ARCHIVE_FOLDER) || is_link(UPLOADS_ARCHIVE_FOLDER))) {
        mkdir(UPLOADS_ARCHIVE_FOLDER, '0777');
    }

    // Set the footer if voucher is invoice and if set in settings
    if (!empty($CI->mdl_settings->settings['pdf_invoice_footer']) && $isInvoice) {
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }

    // Set the footer if voucher is quote and if set in settings
    if (!empty($CI->mdl_settings->settings['pdf_quote_footer']) && strpos($filename, trans('quote')) !== false) {
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_quote_footer'] . '</div>');
    }

    // by chrissie: special page nr footer and addidional footer
    if ($isInvoice) {
        $f="";
        if (!empty($additionalFooter)) {
            $f .= '<div id="footer"><p align="center">'.$additionalFooter.'</p></div>';
        }
        if ($invoiceNrAndPageOnFooter == true) {
            $my_invoice_nr = "";
            if (!empty($CI->load->_ci_cached_vars['invoice']->invoice_number))
                $my_invoice_nr = "Rechnung Nr. ".$CI->load->_ci_cached_vars['invoice']->invoice_number." / ";
            $f .= '<div id="footer"><p align=right>'.$my_invoice_nr.' Seite {PAGENO} von {nbpg}</p></div>';
        }
        if (!empty ($f)) {
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetHTMLFooter($f);
        }
    }
    // END special page footer


    // Watermark
    if (get_setting('pdf_watermark')) {
        $mpdf->showWatermarkText = true;
    }

    $mpdf->WriteHTML((string) $html);

    if ($isInvoice) {
        // invoice copy by chrissie with watermark
        $invoice_copy = env('INVOICE_COPY');
        $invoice_copy_watermark = env('INVOICE_COPY_WATERMARK');

        // only returne archived files when no copy - todo improfe!
        if ($invoice_copy != true) {
            foreach (glob(UPLOADS_ARCHIVE_FOLDER . '*' . $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }

            if (!empty($invoice_array) && !is_null($is_guest)) {
                rsort($invoice_array);

                if ($stream) {
                    return $mpdf->Output($filename . '.pdf', 'I');
                } else {
                    return $invoice_array[0];
                }
            }
        }

        // generate new pdf
        $archived_file = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '.pdf';
        $mpdf->Output($archived_file, 'F');


        if ($invoice_copy == true) {
            $archived_file_copy = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '-copy.pdf';
            $xpdf = new \Mpdf\Mpdf([
                    'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
            ]);
            $xpdf->SetWatermarkText($invoice_copy_watermark);
            $xpdf->showWatermarkText = true;
            $pagecount = $xpdf->SetSourceFile($archived_file);
            $tplId = $xpdf->importPage($pagecount);
            $xpdf->useTemplate($tplId);
            $xpdf->Output($archived_file_copy, 'F');
        }

        // pdf stamping invoice by chrissie
        if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
            $pdf = new Pdf($archived_file);	// here pdftk mikehaertl is being used
            $pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
                ->saveAs($archived_file);

            // invoice copy by chrissie with watermark
            if ($invoice_copy == true) {
                // stamping of copy
                if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
                    $pdf = new Pdf($archived_file_copy);
                    $pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
                        ->saveAs($archived_file_copy);
                }

                // concatenate both pdf
                $pdf = new Pdf();
                $pdf->addFile($archived_file);
                $pdf->addFile($archived_file_copy);
                $pdf->saveAs($archived_file_copy);

                $archived_file = $archived_file_copy;
            }
        }

        // generate a new pdf/3a by chrissie only for invoice.
        $invoide_pdf3a = env('INVOICE_PDF3A');
        if ($invoide_pdf3a == true) {
            $archived_file_a = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '-A.pdf';

            //function convert_pdf_to_pdfa(string $sourcePdf, string $destPdf, string $title, string $author, , ass files name, ass files path, string $creatorTool=''):
            if ($zugferd_invoice) {
                convert_pdf_to_pdfa($archived_file, $archived_file_a, "Title", "Author", $associated_files[0]['name'], $associated_files[0]['path']) ;
            } else {
                convert_pdf_to_pdfa($archived_file, $archived_file_a, "Title", "Author");
            }

            // now copy over new generated file
            copy($archived_file_a, $archived_file);
        }
        // end pdf/3a

        // using readfile, setting header!
        if ($stream) {
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            @readfile ($archived_file);
            return;
        } else {
            return $archived_file;
        }
    } // END $isInvoice


    // generate new pdf : other files but not invoice
    $t = UPLOADS_TEMP_FOLDER . $filename . '.pdf';
    $mpdf->Output($t, 'F');

    // pdf stamping other by chrissie
    if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
        $pdf = new Pdf($t);	// here pdftk is being used
        $pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
            ->saveAs($t);
    }

    // If $stream is true (default) the PDF will be displayed directly in the browser
    // otherwise will be returned as a download
    if ($stream) {
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        @readfile ($t);
    } else {
        return $t;
    }
}
