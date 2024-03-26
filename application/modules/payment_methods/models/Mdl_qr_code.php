<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use SepaQr\Data;

class Mdl_qr_code extends Response_Model
{
    public function __construct() {
        $CI = &get_instance();

        $CI->load->helper('template');
    }

    public function generate($params)
    {
        $CI = &get_instance();
        $invoice = $params['invoice'];

        $paymentData = Data::create()
            ->setName($CI->mdl_settings->setting('payment_method_type_qr_code_recipient'))
            ->setIban($CI->mdl_settings->setting('payment_method_type_qr_code_iban'))
            ->setBic($CI->mdl_settings->setting('payment_method_type_qr_code_bic'))
            ->setCurrency($CI->mdl_settings->setting('currency_code'))
            ->setRemittanceText(parse_template(
                $invoice,
                $CI->mdl_settings->setting('payment_method_type_qr_code_remittance_text')
            ))
            ->setAmount($invoice->invoice_total);

        $qrCodeDataUri = Builder::create()
            ->data($paymentData)
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium()) // required by EPC standard
            ->build()
            ->getDataUri();

        return '<img src="' . $qrCodeDataUri . '" alt="QR Code" id="invoice-qr-code">';
    }

    public function getGeneralOptions(): array
    {
        return [
            "recipient" => [
                "type" => "text",
                "label" => trans("payment_method_type_qr_code_recipient"),
                "required" => false,
            ],
            "iban" => [
                "type" => "text",
                "label" => trans("payment_method_type_qr_code_iban"),
                "required" => false,
            ],
            "bic" => [
                "type" => "text",
                "label" => trans("payment_method_type_qr_code_bic"),
                "required" => false,
            ],
            "remittance_text" => [
                "type" => "text",
                "label" => trans("payment_method_type_qr_code_remittance_text"),
                "default" => "{{{invoice_number}}}",
                "required" => false,
                "tags" => true,
            ]
        ];
    }

    public function getInvoiceOptions(): array
    {
        return [];
    }
}
