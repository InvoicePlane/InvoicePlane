<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sumex
{
    public $invoice;
    public $doc;
    public $root;

    public $_lang = "it";
    public $_mode = "production";
    public $_copy = "0";
    public $_storno = "0";
    public $_role = "physician";
    public $_place = "practice";
    public $_currency = "CHF";
    public $_paymentperiod = "P30D";
    public $_canton = "TI";
    public $_esrType = "9";

    public $_patient = array(
        'gender' => 'male',
        'birthdate' => '1996-09-29',
        'familyName' => 'Vitali',
        'givenName' => 'Denys'
    );

    public $_casedate = "2017-02-10";

    public $_treatment = array(
        'start' => '2017-01-10',
        'end' => '2017-02-01',
        'reason' => 'disease' // TODO: Check if need required value
    );

    public $_company = array(
        'name' => 'SomeCompany GmbH',
        'street' => 'Via Cantonale 5',
        'zip' => '6900',
        'city' => 'Lugano',
        'phone' => '091 902 11 00'
    );

    public function __construct($params)
    {
        //define('IP_VERSION', '1.5.0');
        $CI = &get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];

        $this->_patient['givenName'] = $this->invoice->client_custom_nome;
        $this->_patient['familyName'] = $this->invoice->client_custom_cognome;
        $this->_patient['birthdate'] = $this->invoice->client_custom_data_di_nascita;
        $this->_patient['gender'] = ($this->invoice->client_custom_sesso=="2"?"male":"female");

        $this->_company['name'] = $this->invoice->user_company;
        $this->_company['street'] = $this->invoice->user_address_1;
        $this->_company['zip'] = $this->invoice->user_zip;
        $this->_company['phone'] = $this->invoice->user_phone;

        file_put_contents(UPLOADS_FOLDER."/customer_files/test.json", json_encode($params));
        //throw new Error(UPLOADS_FOLDER);

        $this->currencyCode = $CI->mdl_settings->setting('currency_code');
    }

    public function pdf()
    {
        $ch = curl_init();

        $xml = $this->xml();

        curl_setopt($ch, CURLOPT_URL, SUMEX_URL."/generateInvoice");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180); //timeout in seconds
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $out = curl_exec($ch);
        curl_close($ch);


        return $out;
        //return file_get_contents('req2.xml');
    }

    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;

        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlInvoiceProcessing());
        $this->root->appendChild($this->xmlInvoicePayload());

        $this->doc->appendChild($this->root);
        return $this->doc->saveXML();
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('invoice:request');
        $node->setAttribute('xmlns:invoice', 'http://www.forum-datenaustausch.ch/invoice');
        $node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $node->setAttribute('xmlns:nsxenc', 'http://www.w3.org/2001/04/xmlenc#');
        $node->setAttribute('xsi:schemaLocation', 'http://www.forum-datenaustausch.ch/invoice generalInvoiceRequest_440.xsd');
        $node->setAttribute('modus', $this->_mode);
        $node->setAttribute('language', $this->_lang);
        return $node;
    }

    protected function xmlInvoiceProcessing()
    {
        // Only to pass XML validation. This DOES NOT represent a valid TARMED file.
        $node = $this->doc->createElement('invoice:processing');
        $node->setAttribute('print_at_intermediate', '1');

        $transport = $this->doc->createElement('invoice:transport');
        $transport->setAttribute('from', '2000000000002');
        $transport->setAttribute('to', '2000000000000');

        $via = $this->doc->createElement('invoice:via');
        $via->setAttribute('via', '2000000000000');
        $via->setAttribute('sequence_id', '1');

        $transport->appendChild($via);
        $node->appendChild($transport);
        return $node;
    }

    protected function xmlInvoicePayload()
    {
        $node = $this->doc->createElement('invoice:payload');
        $node->setAttribute('type', 'invoice');
        $node->setAttribute('copy', $this->_copy);
        $node->setAttribute('storno', $this->_storno);

        $invoiceInvoice = $this->doc->createElement('invoice:invoice');
        $invoiceInvoice->setAttribute('request_timestamp', time());
        $invoiceInvoice->setAttribute('request_id', $this->invoice->invoice_number);
        $invoiceInvoice->setAttribute('request_date', date("Y-m-d\TH:i:s", strtotime($this->invoice->invoice_date_modified)));

        $invoiceBody = $this->xmlInvoiceBody();

        $node->appendChild($invoiceInvoice);
        $node->appendChild($invoiceBody);

        return $node;
    }

    protected function xmlInvoiceBody()
    {
        $node = $this->doc->createElement('invoice:body');
        $node->setAttribute('role', $this->_role);
        $node->setAttribute('place', $this->_place);

        if ($this->_esrType == "9") {
            $esr = $this->xmlInvoiceEsr9();
        } else {
            // Red
          $esr = $this->xmlInvoiceEsrRed();
        }

        $prolog = $this->xmlInvoiceProlog();
        $balance = $this->xmlInvoiceBalance();
        $tiersGarant = $this->xmlInvoiceTiersGarant();
        $mvg = $this->xmlInvoiceMvg();
        $treatment = $this->xmlInvoiceTreatment();
        $services = $this->xmlServices();

        $node->appendChild($prolog);
        $node->appendChild($balance);
        $node->appendChild($esr);
        $node->appendChild($tiersGarant);
        $node->appendChild($mvg);
        $node->appendChild($treatment);
        $node->appendChild($services);

        return $node;
    }

    protected function xmlInvoiceProlog()
    {
        $node = $this->doc->createElement('invoice:prolog');

        $package = $this->doc->createElement('invoice:package');
        $package->setAttribute('version', '150');
        $package->setAttribute('name', 'InvoicePlane');

        $generator = $this->doc->createElement('invoice:generator');
        $generator->setAttribute('name', 'PHP_Sumex');
        $generator->setAttribute('version', '100');

        // Depends on...?
        /*$dependson = $this->doc->createElement('invoice:depends_on');
        $dependson->setAttribute('name', 'Nothing');
        $dependson->setAttribute('version', '300');

        $generator->appendChild($dependson);*/

        $node->appendChild($package);
        $node->appendChild($generator);

        return $node;
    }

    protected function xmlInvoiceBalance()
    {
        $node = $this->doc->createElement('invoice:balance');
        $node->setAttribute('currency', $this->_currency);

        // TODO: Integrate Invoice details

        $node->setAttribute('amount', 1.23);
        $node->setAttribute('amount_due', 4.56);
        $node->setAttribute('amount_obligations', 7.89);

        $vat = $this->doc->createElement('invoice:vat');
        $vat->setAttribute('vat', "0.00");

        $vatRate = $this->doc->createElement('invoice:vat_rate');
        $vatRate->setAttribute('vat_rate', "0.00");
        $vatRate->setAttribute('amount', "9.99");
        $vatRate->setAttribute('vat', "0.00");

        $vat->appendChild($vatRate);
        $node->appendChild($vat);

        return $node;
    }

    protected function xmlInvoiceEsr9()
    {
        $node = $this->doc->createElement('invoice:esr9');

        $subNumb = $this->invoice->user_subscribernumber;

        $node->setAttribute('participant_number', $subNumb); // MUST begin with 01
        $node->setAttribute('type', '16or27'); // 16or27 = 01, 16or27plus = 04

        // 26numbers + 1 chek
        $referenceNumber = "";

        // Custom style, we create the reference number as following:
        // 5 digits for client id, 10 digits for invoice ID, 9 digits for Invoice Date, 1 for checksum

        $referenceNumber.= "06"; // Dog Fooding
        $referenceNumber.= sprintf("%05d", $this->invoice->client_id);
        $referenceNumber.= sprintf("%010d", $this->invoice->invoice_id);
        $referenceNumber.= sprintf("%09d", date("Ymd", strtotime($this->invoice->invoice_date_modified)));
        $refCsum = $this->recMod10($referenceNumber);
        $referenceNumber = $referenceNumber.$refCsum;

        if(!preg_match("/\d{27}/", $referenceNumber))
        {
            throw new Error("Invalid reference number!");
        }

        $slipType = "01"; // ISR in CHF
        $amount = $this->invoice->invoice_total;

        $formattedRN = "";
        $formattedRN .= substr($referenceNumber, 0, 2);
        $formattedRN .= " ";
        $formattedRN .= substr($referenceNumber, 2, 5);
        $formattedRN .= " ";
        $formattedRN .= substr($referenceNumber, 7, 5);
        $formattedRN .= " ";
        $formattedRN .= substr($referenceNumber, 12, 5);
        $formattedRN .= " ";
        $formattedRN .= substr($referenceNumber, 17, 5);
        $formattedRN .= " ";
        $formattedRN .= substr($referenceNumber, 22, 5);

        $codingLine = $this->genCodeline($slipType, $amount, $formattedRN, $subNumb);

        $node->setAttribute('reference_number', $formattedRN);
        $node->setAttribute('coding_line', $codingLine);

        return $node;
    }

    public function genCodeline($slipType, $amount, $rnumb, $subNumb)
    {
        $isEur = false;
        if ((int) $slipType > 14) {
            $isEur = true;
        } else {
            $amount = .5 * round((float) $amount/.5, 1);
        }
        if (!$isEur && $amount > 99999999.95) {
            throw new Error("Invalid amount");
        } elseif ($isEur && $amount > 99999999.99) {
            throw new Error("Invalid amount");
        }
        $amountLine = sprintf("%010d", $amount * 100);
        $checkSlAmount = $this->recMod10($slipType.$amountLine);
        if (!preg_match("/\d{2}-\d{1,6}-\d{1}/", $subNumb)) {
            throw new Error("Invalid subscriber number");
        }
        $subNumb = explode("-", $subNumb);
        $fullSub = $subNumb[0].sprintf("%06d", $subNumb[1]).$subNumb[2];
        $rnumb = preg_replace('/\s+/', '', $rnumb);
        return $slipType.$amountLine.$checkSlAmount.">".$rnumb."+ ".$fullSub.">";
    }

    public function recMod10($in)
    {
        $line = [0,9,4,6,8,2,7,1,3,5];
        $carry = 0;
        $chars = str_split($in);
        foreach ($chars as $char) {
            $carry = $line[($carry+intval($char))% 10];
        }
        return (10-$carry) %10;
    }

    protected function xmlInvoiceEsrRed()
    {
        $node = $this->doc->createElement('invoice:esrRed');

        $reason = $this->doc->createElement('invoice:payment_reason');
        $reason->nodeValue = "Payment Reason";

        // postal_account: coding_line2
        // bank_account: coding_line1 + coding_line2
        $node->setAttribute('payment_to', 'postal_account');
        //$node->setAttribute('esr_attributes', '1');
        $node->setAttribute('post_account', '99-123-9');

        // IBAN not required
        //$node->setAttribute('iban', 'CH1111111111111111111');
        $node->setAttribute('reference_number', '1111111111');
        $node->setAttribute('coding_line1', '111111111111111111111111111+ 071234567>');
        $node->setAttribute('coding_line2', '111111111>');

        $node->appendChild($reason);

        return $node;
    }

    protected function xmlInvoiceTiersGarant()
    {
        $node = $this->doc->createElement('invoice:tiers_garant');
        $node->setAttribute('payment_period', $this->_paymentperiod);

        $biller = $this->doc->createElement('invoice:biller');
        $provider = $this->doc->createElement('invoice:provider');
        $patient = $this->doc->createElement('invoice:patient');
        $guarantor = $this->doc->createElement('guarantor');

        // <invoice:biller>
        // TODO: Check ean_party, zsr, specialty
        $biller->setAttribute('ean_party', '2000000000002');
        $biller->setAttribute('zsr', 'C000002');
        //$biller->setAttribute('specialty', 'unknown');

        $bcompany = $this->xmlCompany();
        $biller->appendChild($bcompany);
        // </invoice:biller>

        // <invoice:provider>
        // TODO: Check if **always** same as biller
        // TODO: Check ean_party, zsr, speciality
        $provider->setAttribute('ean_party', '2000000000002');
        $provider->setAttribute('zsr', 'C000002');
        //$provider->setAttribute('specialty', 'Allgemein');

        $pcompany = $this->xmlCompany();
        $provider->appendChild($pcompany);
        // </invoice:provider>

        // <invoice:patient>
        $patient->setAttribute('gender', $this->_patient['gender']);
        $patient->setAttribute('birthdate', date("Y-m-d\TH:i:s", strtotime($this->_patient['birthdate'])));

        // TODO: Person based on Client
        $person = $this->generatePerson("Denys", "Vitali", "Address", "6900", "Lugano", "079 000 00 00");
        $patient->appendChild($person);
        // </invoice:patient>

        // <invoice:guarantor>
        $guarantor->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');
        $person = $this->generatePerson("Denys", "Vitali", "Address", "6900", "Lugano", "079 000 00 00");
        $guarantor->appendChild($person);
        // </invoice:guarantor>

        $node->appendChild($biller);
        $node->appendChild($provider);
        $node->appendChild($patient);
        $node->appendChild($guarantor);

        return $node;
    }

    protected function xmlInvoiceMvg()
    {
        $node = $this->doc->createElement('invoice:mvg');
        $node->setAttribute('ssn', '7560000000011');
        $node->setAttribute('insured_id', 'ladc7a5f5fb43ef76018');
        $node->setAttribute('case_date', date("Y-m-d\TH:i:s", strtotime($this->_casedate)));

        return $node;
    }

    protected function xmlInvoiceTreatment()
    {
        $node = $this->doc->createElement('invoice:treatment');
        $node->setAttribute('date_begin', date("Y-m-d\TH:i:s", strtotime($this->_treatment['start'])));
        $node->setAttribute('date_end', date("Y-m-d\TH:i:s", strtotime($this->_treatment['end'])));
        $node->setAttribute('canton', $this->_canton);
        $node->setAttribute('reason', $this->_treatment['reason']);

        $diag = $this->doc->createElement('invoice:diagnosis');
        $diag->setAttribute('type', 'by_contract');
        $diag->setAttribute('code', 'A1');

        $node->appendChild($diag);

        return $node;
    }

    protected function xmlServices()
    {
        $node = $this->doc->createElement('services');
        $node->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');

        $node->appendChild($this->generateRecord());

        return $node;
    }

    protected function generateRecord()
    {
        $node = $this->doc->createElement('invoice:record_other');
        $node->setAttribute('record_id', 1);
        $node->setAttribute('tariff_type', 590);
        $node->setAttribute('code', 1200);
        $node->setAttribute('session', 1);
        $node->setAttribute('quantity', 4);
        $node->setAttribute('date_begin', date("Y-m-d\TH:i:s", strtotime("2017-01-01")));
        $node->setAttribute('provider_id', '7634567890111');
        $node->setAttribute('responsible_id', '7634567890333');
        $node->setAttribute('unit', "10.00");
        $node->setAttribute('unit_factor', 1);
        $node->setAttribute('amount', "40.00");
        $node->setAttribute('validate', 0);
        $node->setAttribute('service_attributes', 0);
        $node->setAttribute('obligation', 0);
        $node->setAttribute('name', 'Anamnese / Untersuchung / Diagnostik / Befunderhebung, pro 5 Min.');

        return $node;
    }

    protected function generatePerson($name, $surname, $street, $zip, $city, $phone)
    {
        $person = $this->doc->createElement('invoice:person');

        $familyName = $this->doc->createElement('invoice:familyname');
        $familyName->nodeValue = $this->_patient['familyName'];

        $givenName = $this->doc->createElement('invoice:givenname');
        $givenName->nodeValue = $this->_patient['givenName'];

        $postal = $this->generatePostal("Via la Santa 2", "6962", "Viganello");
        $telecom = $this->generateTelecom("079 123 45 67");

        $person->appendChild($familyName);
        $person->appendChild($givenName);
        $person->appendChild($postal);
        $person->appendChild($telecom);

        return $person;
    }

    protected function generatePostal($street, $zip, $city)
    {
        $postal = $this->doc->createElement('invoice:postal');

        $postal_street = $this->doc->createElement('invoice:street');
        $postal_street->nodeValue = $street;

        $postal_zip = $this->doc->createELement('invoice:zip');
        $postal_zip->nodeValue = $zip;

        $postal_city = $this->doc->createElement('invoice:city');
        $postal_city->nodeValue = $city;

        $postal->appendChild($postal_street);
        $postal->appendChild($postal_zip);
        $postal->appendChild($postal_city);

        return $postal;
    }

    protected function generateTelecom($phoneNr)
    {
        $telecom = $this->doc->createElement('invoice:telecom');
        $phone = $this->doc->createElement('invoice:phone');
        $phone->nodeValue = $phoneNr;
        $telecom->appendChild($phone);
        return $telecom;
    }

    protected function xmlCompany()
    {
        // <invoice:company>
      $bcompany = $this->doc->createElement('invoice:company');
        $bcompany_name = $this->doc->createElement('invoice:companyname');
        $bcompany_name->nodeValue = $this->_company['name'];

        $bcompany->appendChild($bcompany_name);

        $bcompany_postal = $this->generatePostal($this->_company['street'], $this->_company['zip'], $this->_company['city']);
        $bcompany->appendChild($bcompany_postal);

        $bcompany_telecom = $this->doc->createElement('invoice:telecom');
        $bcompany_telecom_phone = $this->doc->createElement('invoice:phone');
        $bcompany_telecom_phone->nodeValue = $this->_company['phone'];

        $bcompany_telecom->appendChild($bcompany_telecom_phone);
        $bcompany->appendChild($bcompany_telecom);
      // </invoice:company>

      return $bcompany;
    }
}
