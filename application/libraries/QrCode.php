<?php
if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use SepaQr\Data;

class QrCode {
  public $invoice;
  public $recipient;
  public $iban;
  public $bic;
  public $currencyCode;
  public $remittance_text;

  public function __construct($params) {
    $CI = &get_instance();

    $CI->load->helper('template');

    $this->invoice = $params['invoice'];
    $this->recipient = $CI->mdl_settings->setting('qr_code_recipient');
    $this->iban = $CI->mdl_settings->setting('qr_code_iban');
    $this->bic = $CI->mdl_settings->setting('qr_code_bic');
    $this->currencyCode = $CI->mdl_settings->setting('currency_code');
    $this->remittance_text = parse_template(
      $this->invoice,
      $CI->mdl_settings->setting('qr_code_remittance_text')
    );
  }

  public function paymentData() {
    $paymentData = Data::create()
      ->setName($this->recipient)
      ->setIban($this->iban)
      ->setBic($this->bic)
      ->setCurrency($this->currencyCode)
      ->setRemittanceText($this->remittance_text)
      ->setAmount($this->invoice->invoice_total);

    return $paymentData;
  }

  public function generate() {
    $qrCodeDataUri = Builder::create()
      ->data($this->paymentData())
      ->errorCorrectionLevel(new ErrorCorrectionLevelMedium()) // required by EPC standard
      ->build()
      ->getDataUri();

    return $qrCodeDataUri;
  }
}
