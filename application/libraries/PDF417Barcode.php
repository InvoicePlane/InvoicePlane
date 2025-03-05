<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Radoid\PDF417\PDF417;
use Radoid\PDF417\Renderers\ImageRenderer;

#[AllowDynamicProperties]
class PDF417Barcode {
  public $invoice;
  public $recipient;
  public $iban;
  public $bic;
  public $currencyCode;
  public $remittance_text;

  public function __construct($params) {
    $CI = &get_instance();

    $CI->load->helper('template');

    #$invoice = $CI->mdl_invoices->get_by_id($invoice_id);
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
    $paymentData  = "HRVHUB30\n";                                     // Zaglavlje
    $paymentData .= "EUR\n";                                          // Valuta
    $paymentData .= "000000000012355\n";                              // Iznos
    $paymentData .= "ZELJKO SENEKOVIC\n";                             // Platitelj
    $paymentData .= "IVANECKA ULICA 125\n";                           // Adresa platitelja (ulica i broj)
    $paymentData .= "42000 VARAZDIN\n";                               // Adresa platitelja (poštanski broj i mjesto)
    $paymentData .= "2DBK d.d.\n";                                    // Primatelj
    $paymentData .= "ALKARSKI PROLAZ 13B\n";                          // Adresa primatelja (ulica i broj)
    $paymentData .= "21230 SINJ\n";                                   // Adresa primatelja (poštanski broj i mjesto)
    $paymentData .= "$this->iban\n";                                  // Broj računa primatelja (IBAN)
    $paymentData .= "HR00\n";                                         // Model kontrole poziva na broj primatelja
    $paymentData .= "$this->remittance_text\n";                       // Poziv na broj primatelja
    $paymentData .= "\n";                                             // Šifra namjene
    $paymentData .= "Placanje po racunu $this->remittance_text\n";    // Opis plaćanja
    

    return $paymentData;
  }

  public function generate() {

    // Encode the data
    $pdf417 = new PDF417();
    $data = $pdf417->encode($this->paymentData());

    $options = [
      'scale' => 1,
      'ratio' => 2,
      'padding' => 5,
    ];

    $renderer = new ImageRenderer($options);

    $dataURL = $renderer->renderDataUrl($data);
    
    return $dataURL;
  }
}