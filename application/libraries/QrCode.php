<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use SepaQr\SepaQrData;

#[AllowDynamicProperties]
class QrCode
{
    public $invoice;

    public $recipient;

    public $iban;

    public $bic;

    public $currencyCode;

    public $remittance_text;

    public function __construct(array $params)
    {
        $CI = & get_instance();

        $CI->load->helper('template');

        $this->invoice = $params['invoice'];

        // Determine recipient with the following priority:
        // First priority: Get recipient from qr code settings
        $recipient = $CI->mdl_settings->setting('qr_code_recipient');
        // Second priority: If still empty, get recipient from invoice's user_company
        if (empty($recipient)) {
            $recipient = $this->invoice->user_company;
        }
        // Third priority: If still empty, get recipient from invoice's user_name
        if (empty($recipient)) {
            $recipient = $this->invoice->user_name;
        }
        $this->recipient       = $recipient;
        $this->iban            = $this->invoice->user_iban ?: $CI->mdl_settings->setting('qr_code_iban');
        $this->bic             = $this->invoice->user_bic ?: $CI->mdl_settings->setting('qr_code_bic');
        $this->currencyCode    = $CI->mdl_settings->setting('currency_code');
        $this->remittance_text = parse_template(
            $this->invoice,
            $this->invoice->user_remittance_text ?: $CI->mdl_settings->setting('qr_code_remittance_text')
        );
    }

    public function paymentData(): SepaQrData
    {
        // sepa-qr-data 3 types every setter strictly. The recipient falls back through
        // user_company and user_name, both nullable columns, and passing null to a
        // userland string parameter is a TypeError rather than the deprecation v1
        // produced -- so values are cast explicitly. An empty name or IBAN still fails
        // validation below, which is correct: a payment QR without them is invalid.
        return (new SepaQrData())
            ->setName((string) $this->recipient)
            ->setIban((string) $this->iban)
            ->setBic((string) $this->bic)
            ->setCurrency((string) $this->currencyCode)
            ->setRemittanceText((string) $this->remittance_text)
            ->setAmount((float) $this->invoice->invoice_balance);
    }

    public function generate(): string
    {
        return (new Builder(
            data: (string) $this->paymentData(),
            // Required by the EPC standard. Stated explicitly: endroid/qr-code 6 lowered
            // the default to Low, so omitting it would silently change the QR.
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
        ))->build()->getDataUri();
    }
}
