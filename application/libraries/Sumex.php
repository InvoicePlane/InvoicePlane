<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sumex
{
    const ROLES = array(
        'physician',
        'physiotherapist',
        'chiropractor',
        'ergotherapist',
        'nutritionist',
        'midwife',
        'logotherapist',
        'hospital',
        'pharmacist',
        'dentist',
        'labtechnician',
        'dentaltechnician',
        'othertechnician',
        'psychologist',
        'wholesaler',
        'nursingstaff',
        'transport',
        'druggist',
        'naturopathicdoctor',
        'naturopathictherapist',
        'other');
    const PLACES = array(
        'practice',
        'hospital',
        'lab',
        'association',
        'company'
    );
    const CANTONS = array(
        "AG",
        "AI",
        "AR",
        "BE",
        "BL",
        "BS",
        "FR",
        "GE",
        "GL",
        "GR",
        "JU",
        "LU",
        "NE",
        "NW",
        "OW",
        "SG",
        "SH",
        "SO",
        "SZ",
        "TI",
        "TG",
        "UR",
        "VD",
        "VS",
        "ZG",
        "ZH",
        "LI",
        "A",
        "D",
        "F",
        "I"
    );
    public $invoice;
    public $doc;
    public $root;
    public $_lang = "it";
    public $_mode = "production";
    public $_copy = "0";
    public $_storno = "0";
    public $_role = "physiotherapist";
    public $_place = "practice";
    public $_currency = "CHF";
    public $_paymentperiod = "P30D";
    public $_canton = "TI";
    public $_esrType = "9";

    public $_patient = array(
        'gender' => 'male',
        'birthdate' => '1970-01-01',
        'familyName' => 'FamilyName',
        'givenName' => 'GivenName',
        'street' => 'ClientStreet 10',
        'zip' => '0000',
        'city' => 'ClientCity',
        'phone' => '000 000 00 00',
        'avs' => '7000000000000'
    );

    public $_casedate = "1970-01-01";
    public $_casenumber = "0";
    public $_insuredid = '1234567';

    public $_treatment = array(
        'start' => '',
        'end' => '',
        'reason' => 'disease',
        'diagnosis' => '.'
    );

    public $_company = array(
        'name' => 'SomeCompany GmbH',
        'street' => 'Via Cantonale 5',
        'zip' => '6900',
        'city' => 'Lugano',
        'phone' => '091 902 11 00',
        'gln' => '123456789123', // EAN 13
        'rcc' => 'C000002'
    );

    public $_insurance = array(
        'gln' => '7634567890000',
        'name' => 'SUVA',
        'street' => 'ChangeMe 12',
        'zip' => '6900',
        'city' => 'Lugano'
    );

    public $_options = array(
        'copy' => "0",
        'storno' => "0"
    );

    public function __construct($params)
    {
        $CI = &get_instance();

        $CI->load->helper('invoice');

        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        if (!is_array(@$params['options'])) {
            $params['options'] = array();
        }
        $this->_options = array_merge($this->_options, $params['options']);

        $this->_storno = $this->_options['storno'];
        $this->_copy = $this->_options['copy'];


        $this->_patient['givenName'] = $this->invoice->client_name;
        $this->_patient['familyName'] = $this->invoice->client_surname;
        $this->_patient['birthdate'] = $this->invoice->client_birthdate;
        $this->_patient['gender'] = ($this->invoice->client_gender == "0" ? "male" : "female");
        $this->_patient['street'] = $this->invoice->client_address_1;
        $this->_patient['zip'] = $this->invoice->client_zip;
        $this->_patient['city'] = $this->invoice->client_city;
        $this->_patient['phone'] = ($this->invoice->client_phone == "" ? null : $this->invoice->client_phone);
        $this->_patient['avs'] = $this->invoice->client_avs;

        $this->_company['name'] = $this->invoice->user_company;
        $this->_company['street'] = $this->invoice->user_address_1;
        $this->_company['zip'] = $this->invoice->user_zip;
        $this->_company['city'] = $this->invoice->user_city;
        $this->_company['phone'] = $this->invoice->user_phone;
        $this->_company['gln'] = $this->invoice->user_gln;
        $this->_company['rcc'] = $this->invoice->user_rcc;

        $this->_casedate = $this->invoice->sumex_casedate;
        $this->_casenumber = $this->invoice->sumex_casenumber;
        $this->_insuredid = $this->invoice->client_insurednumber;


        $treatments = array(
            'disease',
            'accident',
            'maternity',
            'prevention',
            'birthdefect',
            'unknown'
        );


        $this->_treatment = array(
            'start' => $this->invoice->sumex_treatmentstart,
            'end' => $this->invoice->sumex_treatmentend,
            'reason' => $treatments[$this->invoice->sumex_reason],
            'diagnosis' => $this->invoice->sumex_diagnosis,
            'observations' => $this->invoice->sumex_observations,
        );

        $esrTypes = array("9", "red");
        $this->_esrType = $esrTypes[$CI->mdl_settings->setting('sumex_sliptype')];

        $this->currencyCode = $CI->mdl_settings->setting('currency_code');
        $this->_role = Sumex::ROLES[$CI->mdl_settings->setting('sumex_role')];
        $this->_place = Sumex::PLACES[$CI->mdl_settings->setting('sumex_place')];
        $this->_canton = Sumex::CANTONS[$CI->mdl_settings->setting('sumex_canton')];
    }

    public function pdf()
    {
        $ch = curl_init();

        $xml = $this->xml();

        curl_setopt($ch, CURLOPT_URL, SUMEX_URL . "/generateInvoice");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180); //timeout in seconds
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $out = curl_exec($ch);
        curl_close($ch);


        return $out;
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
        // TODO: CHECK!
        // Only to pass XML validation. This DOES NOT represent a valid TARMED file.
        $node = $this->doc->createElement('invoice:processing');
        $node->setAttribute('print_at_intermediate', 'false');
        $node->setAttribute('print_patient_copy', 'true');

        $transport = $this->doc->createElement('invoice:transport');
        $transport->setAttribute('from', $this->_company['gln']);
        $transport->setAttribute('to', '7601003000078'); # Example: SUVA

        $via = $this->doc->createElement('invoice:via');
        $via->setAttribute('via', '7601003000078'); # Example: SUVA
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
        $remark = $this->xmlInvoiceRemark();
        $balance = $this->xmlInvoiceBalance();
        $tiersGarant = $this->xmlInvoiceTiersGarant();
        //$tiersPayant = $this->xmlInvoiceTiersPayant();
        //$mvg = $this->xmlInvoiceMvg();
        $org = $this->xmlInvoiceOrg();
        $treatment = $this->xmlInvoiceTreatment();
        $services = $this->xmlServices();

        $node->appendChild($prolog);
        if ($this->_treatment['observations'] != "") {
            $node->appendChild($remark);
        }
        $node->appendChild($balance);
        $node->appendChild($esr);
        $node->appendChild($tiersGarant);
        //$node->appendChild($tiersPayant);
        $node->appendChild($org);
        $node->appendChild($treatment);
        $node->appendChild($services);

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

        $referenceNumber .= "06"; // Dog Fooding
        $referenceNumber .= sprintf("%05d", $this->invoice->client_id);
        $referenceNumber .= sprintf("%010d", $this->invoice->invoice_id);
        $referenceNumber .= sprintf("%09d", date("Ymd", strtotime($this->invoice->invoice_date_modified)));
        $refCsum = invoice_recMod10($referenceNumber);
        $referenceNumber = $referenceNumber . $refCsum;

        if (!preg_match("/\d{27}/", $referenceNumber)) {
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

        $codingLine = invoice_genCodeline($slipType, $amount, $formattedRN, $subNumb);

        $node->setAttribute('reference_number', $formattedRN);
        $node->setAttribute('coding_line', $codingLine);

        return $node;
    }

    protected function xmlInvoiceEsrRed()
    {
        $node = $this->doc->createElement('invoice:esrRed');

        $reason = $this->doc->createElement('invoice:payment_reason');
        $reason->nodeValue = $this->invoice->invoice_number;

        $subNumb = $this->invoice->user_subscribernumber;
        // postal_account: coding_line2
        // bank_account: coding_line1 + coding_line2
        // Assume always postal: This should be have an option in the future
        $node->setAttribute('payment_to', 'postal_account');
        $node->setAttribute('post_account', $subNumb);


        // IBAN not required
        //$node->setAttribute('iban', 'CH1111111111111111111');
        $node->setAttribute('reference_number', '1112111111');
        $node->setAttribute('coding_line1', '111111111111111111111111111+ 071234567>');
        $node->setAttribute('coding_line2', str_replace('-', '', $subNumb) . '>');

        $node->appendChild($reason);

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

        $node->appendChild($package);
        $node->appendChild($generator);

        return $node;
    }

    protected function xmlInvoiceRemark()
    {
        $node = $this->doc->createElement('invoice:remark');
        $node->nodeValue = $this->_treatment['observations'];
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
        $biller->setAttribute('ean_party', $this->_company['gln']);
        $biller->setAttribute('zsr', $this->_company['rcc']); // Zahlstellenregister-Nummer (RCC)
        //$biller->setAttribute('specialty', 'unknown');

        $bcompany = $this->xmlCompany();
        $biller->appendChild($bcompany);
        // </invoice:biller>

        // <invoice:provider>
        // TODO: Check if **always** same as biller
        // TODO: Check ean_party, zsr, speciality
        $provider->setAttribute('ean_party', $this->_company['gln']);
        $provider->setAttribute('zsr', $this->_company['rcc']); // Zahlstellenregister-Nummer (RCC)
        //$provider->setAttribute('specialty', 'Allgemein');

        $pcompany = $this->xmlCompany();
        $provider->appendChild($pcompany);
        // </invoice:provider>

        // <invoice:patient>
        $patient->setAttribute('gender', $this->_patient['gender']);
        $patient->setAttribute('birthdate', date("Y-m-d\TH:i:s", strtotime($this->_patient['birthdate'])));

        $person = $this->generatePerson($this->_patient['givenName'], $this->_patient['familyName'], $this->_patient['street'], $this->_patient['zip'], $this->_patient['city'], $this->_patient['phone']);
        $patient->appendChild($person);
        // </invoice:patient>

        // <invoice:guarantor>
        $guarantor->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');
        $person = $this->generatePerson($this->_patient['givenName'], $this->_patient['familyName'], $this->_patient['street'], $this->_patient['zip'], $this->_patient['city'], $this->_patient['phone']);
        $guarantor->appendChild($person);
        // </invoice:guarantor>

        $node->appendChild($biller);
        $node->appendChild($provider);
        $node->appendChild($patient);
        $node->appendChild($guarantor);

        return $node;
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

    protected function generatePerson($name, $surname, $street, $zip, $city, $phone)
    {
        $person = $this->doc->createElement('invoice:person');

        $familyName = $this->doc->createElement('invoice:familyname');
        $familyName->nodeValue = $this->_patient['familyName'];

        $givenName = $this->doc->createElement('invoice:givenname');
        $givenName->nodeValue = $this->_patient['givenName'];

        $postal = $this->generatePostal($street, $zip, $city);

        if ($phone != null) {
            $telecom = $this->generateTelecom($phone);
        } else {
            $telecom = null;
        }


        $person->appendChild($familyName);
        $person->appendChild($givenName);
        $person->appendChild($postal);
        if ($telecom != null) {
            $person->appendChild($telecom);
        }

        return $person;
    }

    protected function generateTelecom($phoneNr)
    {
        $telecom = $this->doc->createElement('invoice:telecom');
        $phone = $this->doc->createElement('invoice:phone');
        $phone->nodeValue = $phoneNr;
        $telecom->appendChild($phone);
        return $telecom;
    }

    protected function xmlInvoiceOrg()
    {
        $node = $this->doc->createElement('invoice:org');
        $node->setAttribute('case_date', date("Y-m-d\TH:i:s", strtotime($this->_casedate)));
        if ($this->_casenumber != "") {
            $node->setAttribute('case_id', $this->_casenumber);
        }

        if ($this->_insuredid != "") {
            $node->setAttribute('insured_id', $this->_insuredid);
        }

        return $node;
    }

    protected function xmlInvoiceTreatment()
    {
        $node = $this->doc->createElement('invoice:treatment');
        $node->setAttribute('date_begin', date("Y-m-d\TH:i:s", strtotime($this->_treatment['start'])));
        $node->setAttribute('date_end', date("Y-m-d\TH:i:s", strtotime($this->_treatment['end'])));
        $node->setAttribute('canton', $this->_canton);
        $node->setAttribute('reason', $this->_treatment['reason']);

        if ($this->_treatment['diagnosis'] != "") {
            $diag = $this->doc->createElement('invoice:diagnosis');
            $diag->setAttribute('type', 'freetext');
            //$diag->setAttribute('code', );
            $diag->nodeValue = $this->_treatment['diagnosis'];
            $node->appendChild($diag);
        }

        return $node;
    }

    protected function xmlServices()
    {
        $node = $this->doc->createElement('services');
        $node->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');

        $recordId = 1;
        foreach ($this->items as $item) {
            $node->appendChild($this->generateRecord($recordId, $item));
            $recordId++;
        }

        return $node;
    }

    /**
     * @param integer $recordId
     */
    protected function generateRecord($recordId, $item)
    {
        $node = $this->doc->createElement('invoice:record_other');
        $node->setAttribute('record_id', $recordId);
        $node->setAttribute('tariff_type', 590);
        $node->setAttribute('code', (int)$item->product_sku);
        $node->setAttribute('session', 1);
        $node->setAttribute('quantity', $item->item_quantity);
        $node->setAttribute('date_begin', date("Y-m-d\TH:i:s", strtotime($item->item_date)));
        $node->setAttribute('provider_id', $this->_company['gln']);
        $node->setAttribute('responsible_id', $this->_company['gln']);
        $node->setAttribute('unit', $item->item_price);
        #$node->setAttribute('unit_factor', 1);
        $node->setAttribute('amount', $item->item_total);
        #$node->setAttribute('validate', 0);
        #$node->setAttribute('service_attributes', 0);
        #$node->setAttribute('obligation', 0);
        $node->setAttribute('name', $item->item_name);

        //var_dump($item);
        return $node;
    }

    protected function xmlInvoiceTiersPayant()
    {
        $node = $this->doc->createElement('invoice:tiers_payant');
        $node->setAttribute('payment_period', $this->_paymentperiod);

        $biller = $this->doc->createElement('invoice:biller');
        $provider = $this->doc->createElement('invoice:provider');
        $insurance = $this->doc->createElement('invoice:insurance');
        $patient = $this->doc->createElement('invoice:patient');
        $insured = $this->doc->createElement('invoice:insured');
        $guarantor = $this->doc->createElement('invoice:guarantor');

        // <invoice:biller>
        // TODO: Check ean_party, zsr, specialty
        $biller->setAttribute('ean_party', $this->_company['gln']);
        $biller->setAttribute('zsr', $this->_company['rcc']); // Zahlstellenregister-Nummer (RCC)
        //$biller->setAttribute('specialty', 'unknown');

        $bcompany = $this->xmlCompany();
        $biller->appendChild($bcompany);
        // </invoice:biller>

        // <invoice:provider>
        // TODO: Check if **always** same as biller
        // TODO: Check ean_party, zsr, speciality
        $provider->setAttribute('ean_party', $this->_company['gln']);
        $provider->setAttribute('zsr', $this->_company['rcc']); // Zahlstellenregister-Nummer (RCC)
        //$provider->setAttribute('specialty', 'Allgemein');

        $pcompany = $this->xmlCompany();
        $provider->appendChild($pcompany);
        // </invoice:provider>

        // <invoice:insurance>
        $insurance->setAttribute('ean_party', $this->_insurance['gln']);
        $insuranceCompany = $this->xmlInsurance();
        $insurance->appendChild($insuranceCompany);
        // </invoice:insurance>

        // <invoice:patient>
        $patient->setAttribute('gender', $this->_patient['gender']);
        $patient->setAttribute('birthdate', date("Y-m-d\TH:i:s", strtotime($this->_patient['birthdate'])));

        $person = $this->generatePerson($this->_patient['givenName'], $this->_patient['familyName'], $this->_patient['street'], $this->_patient['zip'], $this->_patient['city'], $this->_patient['phone']);
        $patient->appendChild($person);
        // </invoice:patient>

        // <invoice:insured>
        $insured->setAttribute('gender', $this->_patient['gender']);
        $insured->setAttribute('birthdate', date("Y-m-d\TH:i:s", strtotime($this->_patient['birthdate'])));

        $person = $this->generatePerson($this->_patient['givenName'], $this->_patient['familyName'], $this->_patient['street'], $this->_patient['zip'], $this->_patient['city'], $this->_patient['phone']);
        $insured->appendChild($person);
        // </invoice:insured>

        // <invoice:guarantor>
        $guarantor->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');
        $person = $this->generatePerson($this->_patient['givenName'], $this->_patient['familyName'], $this->_patient['street'], $this->_patient['zip'], $this->_patient['city'], $this->_patient['phone']);
        $guarantor->appendChild($person);
        // </invoice:guarantor>

        $node->appendChild($biller);
        $node->appendChild($provider);
        $node->appendChild($insurance);
        $node->appendChild($patient);
        //$node->appendChild($insured);
        $node->appendChild($guarantor);

        return $node;
    }

    protected function xmlInsurance()
    {
        // <invoice:company>
        $bcompany = $this->doc->createElement('invoice:company');
        $bcompany_name = $this->doc->createElement('invoice:companyname');
        $bcompany_name->nodeValue = $this->_insurance['name'];

        $bcompany->appendChild($bcompany_name);

        $bcompany_postal = $this->generatePostal($this->_insurance['street'], $this->_insurance['zip'], $this->_insurance['city']);
        $bcompany->appendChild($bcompany_postal);

        /*$bcompany_telecom = $this->doc->createElement('invoice:telecom');
        $bcompany_telecom_phone = $this->doc->createElement('invoice:phone');
        $bcompany_telecom_phone->nodeValue = $this->_company['phone'];
        $bcompany_telecom->appendChild($bcompany_telecom_phone);
        $bcompany->appendChild($bcompany_telecom);*/

        // </invoice:company>

        return $bcompany;
    }

    protected function xmlInvoiceMvg()
    {
        $node = $this->doc->createElement('invoice:mvg');
        $node->setAttribute('ssn', $this->_patient['avs']);
        #$node->setAttribute('insured_id', '1234');
        $node->setAttribute('case_date', date("Y-m-d\TH:i:s", strtotime($this->_casedate)));

        return $node;
    }
}
