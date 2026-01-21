<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\Reference\QrPaymentReferenceGenerator;

class Mdl_qr_code_swiss extends Response_Model
{
    public function __construct()
    {
        $CI = &get_instance();

        $CI->load->helper('template');
    }

    public function createQrBill($invoice, $CI)
    {
        $qrBill = QrBill::create();

        $qrBill->setCreditor(CombinedAddress::create(
            $invoice->user_name,
            $invoice->user_address_1,
            $invoice->user_zip . ' ' . $invoice->user_city,
            $invoice->user_country
        ));
        $qrBill->setCreditorInformation(CreditorInformation::create(
            $invoice->user_iban
        ));
        $qrBill->setUltimateDebtor(CombinedAddress::create(
            $invoice->client_surname . ' ' . $invoice->client_name,
            $invoice->client_address_1,
            $invoice->client_zip . ' ' . $invoice->client_city,
            $invoice->client_country
        ));
        $qrBill->setPaymentAmountInformation(
            PaymentAmountInformation::create(
                $CI->mdl_settings->setting('currency_code'),
                $invoice->invoice_total
            )
        );

        $besrid = $CI->mdl_settings->setting('payment_method_type_qr_code_swiss_besrid');

        $paymentReference = null;

        if ($besrid == '') {
            $paymentReference = PaymentReference::create(
                PaymentReference::TYPE_NON,
                ''
            );
        } else {
            $paymentReference = PaymentReference::create(
                PaymentReference::TYPE_QR,
                QrPaymentReferenceGenerator::generate(
                    $besrid,
                    $invoice->invoice_id
                )
            );
        }
        $qrBill->setPaymentReference($paymentReference);

        $qrBill->setAdditionalInformation(AdditionalInformation::create(
            parse_template(
                $invoice,
                $CI->mdl_settings->setting('payment_method_type_qr_code_swiss_additional_text')
            )
        ));

        return $qrBill;
    }

    public function generate($callType, $params)
    {
        $CI = &get_instance();

        $invoice = $params['invoice'];

        $qrBill = $this->createQrBill($invoice, $CI);

        if ($callType == 'pdf') {
            $mpdf = $params['mpdf'];

            $fpdf = new \Fpdf\Fpdf('P', 'mm', 'A4');
            $fpdf->AddPage();
            try {
                $output = new FpdfOutput($qrBill, 'fr', $fpdf);
                $output
                    ->setPrintable(false)
                    ->getPaymentPart();

                $fpdf->Output(UPLOADS_TEMP_MPDF_FOLDER . 'qr_swiss.pdf', 'F');
            } catch (Exception $e) {
                $errs = '';
                foreach ($qrBill->getViolations() as $violation) {
                    $errs .= $violation->getMessage() . "\n";
                }
                throw new Exception('Errors: ' . $errs);
            }

            $mpdf->AddPage();
            $pageId = $mpdf->SetSourceFile(UPLOADS_TEMP_MPDF_FOLDER . 'qr_swiss.pdf');
            $tplId = $mpdf->ImportPage($pageId);
            $mpdf->UseTemplate($tplId);
            unlink(UPLOADS_TEMP_MPDF_FOLDER . 'qr_swiss.pdf');

            return;
        }
        if ($callType == 'html') {
            $output = new HtmlOutput($qrBill, 'en');

            return $output
                ->setPrintable(false)
                ->getPaymentPart();
        }
        throw new Exception('Call format not supported by QR Code Swiss');

    }

    public function getGeneralOptions(): array
    {
        return [
            'besrid' => [
                'type' => 'text',
                'label' => trans('payment_method_type_qr_code_swiss_besrid'),
                'required' => false,
            ],
            'additional_text' => [
                'type' => 'text',
                'label' => trans('payment_method_type_qr_code_swiss_additional_text'),
                'default' => '{{{invoice_number}}}',
                'required' => true,
                'tags' => true,
            ],
        ];
    }

    public function getInvoiceOptions(): array
    {
        return [];
    }
}
