<?php
if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

use Sprain\SwissQrBill as QrBill;

class QrCodeSwiss {
  public $invoice;
  public $creditor;
  public $creditorInformation;
  public $debitor;
  public $additionalInformation;
  public $currency;

  public function __construct($params) {
    $CI = &get_instance();

    $this->invoice = $params['invoice'];

    $this->creditor = QrBill\DataGroup\Element\CombinedAddress::create(
      $this->invoice->user_name,
      $this->invoice->user_address_1,
      $this->invoice->user_zip . " " . $this->invoice->user_city,
      $this->invoice->user_country
    );
    $this->creditorInformation = QrBill\DataGroup\Element\CreditorInformation::create(
      $this->invoice->user_iban
    );

    $this->debitor = QrBill\DataGroup\Element\CombinedAddress::create(
      $this->invoice->client_surname . " " . $this->invoice->client_name,
      $this->invoice->client_address_1,
      $this->invoice->client_zip . " " . $this->invoice->client_city,
      $this->invoice->client_country
    );

    $besrid = $CI->mdl_settings->setting('qr_code_swiss_besrid');

    if ($besrid == "") {
      $this->paymentReference = QrBill\DataGroup\Element\PaymentReference::create(
          QrBill\DataGroup\Element\PaymentReference::TYPE_NON,
          ""
      );
    } else {
      $this->paymentReference = QrBill\DataGroup\Element\PaymentReference::create(
          QrBill\DataGroup\Element\PaymentReference::TYPE_QR,
          QrBill\Reference\QrPaymentReferenceGenerator::generate(
            $besrid,
            $this->invoice->invoice_id
        )
      );
    }

    $this->additionalInformation = QrBill\DataGroup\Element\AdditionalInformation::create(
        "Facture " . $this->invoice->invoice_number
    );

    $this->currency = $CI->mdl_settings->setting('qr_code_swiss_currency');
  }

  public function generate() {
    $qrBill = QrBill\QrBill::create();

    $qrBill->setCreditor($this->creditor);
    $qrBill->setCreditorInformation($this->creditorInformation);
    $qrBill->setUltimateDebtor($this->debitor);
    $qrBill->setPaymentAmountInformation(
      QrBill\DataGroup\Element\PaymentAmountInformation::create(
        $this->currency,
          $this->invoice->invoice_total
      )
    );

    $qrBill->setPaymentReference($this->paymentReference);

    $qrBill->setAdditionalInformation($this->additionalInformation);

    return $qrBill;
  }
}