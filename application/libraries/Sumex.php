<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sumex
{
    var $invoice;
    var $doc;
    var $root;

    var $_lang = "it";
    var $_mode = "production";
    var $_copy = "0";
    var $_storno = "0";
    var $_role = "physician";
    var $_place = "practice";
    var $_currency = "CHF";
    var $_paymentperiod = "P30D";
    var $_canton = "TI";

    var $_patient = array(
        'gender' => 'male',
        'birthdate' => '1996-09-29',
        'familyName' => 'Vitali',
        'givenName' => 'Denys'
    );

    var $_casedate = "2017-02-10";

    var $_treatment = array(
        'start' => '2017-01-10',
        'end' => '2017-02-01',
        'reason' => 'disease' // TODO: Check if need required value
    );

    public function __construct($params)
    {
        define('IP_VERSION', '1.5.0');
        $CI = &get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->currencyCode = $CI->mdl_settings->setting('currency_code');
    }

    public function pdf(){
        $ch = curl_init();

        $xml = $this->xml();

        curl_setopt($ch, CURLOPT_URL, "http://192.168.1.150:8080/generateInvoice");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
        $invoiceInvoice->setAttribute('request_id', '000001000001');
        $invoiceInvoice->setAttribute('request_date', date("Y-m-d\TH:i:s"));

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

        $prolog = $this->xmlInvoiceProlog();
        $balance = $this->xmlInvoiceBalance();
        $esr9 = $this->xmlInvoiceEsr9();
        $tiersGarant = $this->xmlInvoiceTiersGarant();
        $mvg = $this->xmlInvoiceMvg();
        $treatment = $this->xmlInvoiceTreatment();
        $services = $this->xmlServices();

        $node->appendChild($prolog);
        $node->appendChild($balance);
        $node->appendChild($esr9);
        $node->appendChild($tiersGarant);
        $node->appendChild($mvg);
        $node->appendChild($treatment);
        $node->appendChild($services);

        return $node;
    }

    protected function xmlInvoiceProlog(){
        $node = $this->doc->createElement('invoice:prolog');

        $package = $this->doc->createElement('invoice:package');
        $package->setAttribute('version', '100');
        $package->setAttribute('name', 'InvoicePlane');

        $generator = $this->doc->createElement('invoice:generator');
        $generator->setAttribute('name', 'PHP_Sumex');
        $generator->setAttribute('version', '100');

        $dependson = $this->doc->createElement('invoice:depends_on');
        $dependson->setAttribute('name', 'Nothing');
        $dependson->setAttribute('version', '300');

        $generator->appendChild($dependson);
        // Depends on...?

        $node->appendChild($package);
        $node->appendChild($generator);

        return $node;
    }

    protected function xmlInvoiceBalance(){
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

    protected function xmlInvoiceEsr9(){
        $node = $this->doc->createElement('invoice:esr9');

        // TODO: Integrate participant_number
        $node->setAttribute('participant_number', '01-12648-2');

        // TODO: CHECK
        $node->setAttribute('type', '16or27');

        // TODO: Autogenerate coding_line
        $node->setAttribute('reference_number', '15 45300 00000 00000 00100 00018');
        $node->setAttribute('coding_line', '0100000017758>154530000000000000010000018+ 010126482>');
        return $node;
    }

    protected function xmlInvoiceTiersGarant(){
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
        $biller->setAttribute('specialty', 'Allgemein');

        $bcompany = $this->xmlCompany();
        $biller->appendChild($bcompany);
        // </invoice:biller>

        // <invoice:provider>
        // TODO: Check if **always** same as biller
        // TODO: Check ean_party, zsr, speciality
        $provider->setAttribute('ean_party', '2000000000002');
        $provider->setAttribute('zsr', 'C000002');
        $provider->setAttribute('specialty', 'Allgemein');

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

    protected function xmlInvoiceMvg(){
        $node = $this->doc->createElement('invoice:mvg');
        $node->setAttribute('ssn', '7560000000011');
        $node->setAttribute('insured_id', 'ladc7a5f5fb43ef76018');
        $node->setAttribute('case_date', date("Y-m-d\TH:i:s", strtotime($this->_casedate)));

        return $node;
    }

    protected function xmlInvoiceTreatment(){
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

    protected function xmlServices(){
        $node = $this->doc->createElement('services');
        $node->setAttribute('xmlns', 'http://www.forum-datenaustausch.ch/invoice');

        $node->appendChild($this->generateRecord());

        return $node;
    }

    protected function generateRecord(){
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

    protected function generatePerson($name, $surname, $street, $zip, $city, $phone){
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

    protected function generatePostal($street, $zip, $city){
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

    protected function generateTelecom($phoneNr){
      $telecom = $this->doc->createElement('invoice:telecom');
      $phone = $this->doc->createElement('invoice:phone');
      $phone->nodeValue = $phoneNr;
      $telecom->appendChild($phone);
      return $telecom;
    }

    protected function xmlCompany(){
      // <invoice:company>
      $bcompany = $this->doc->createElement('invoice:company');
      $bcompany_name = $this->doc->createElement('invoice:companyname');
      $bcompany_name->nodeValue = "Some Company GmbH";

      $bcompany->appendChild($bcompany_name);

      $bcompany_postal = $this->generatePostal("Via Cantonale", "6900", "Lugano");
      $bcompany->appendChild($bcompany_postal);

      $bcompany_telecom = $this->doc->createElement('invoice:telecom');
      $bcompany_telecom_phone = $this->doc->createElement('invoice:phone');
      $bcompany_telecom_phone->nodeValue = '091 000 00 00';

      $bcompany_telecom->appendChild($bcompany_telecom_phone);
      $bcompany->appendChild($bcompany_telecom);
      // </invoice:company>

      return $bcompany;
    }

    // ===========================================================================
    // elements helpers
    // ===========================================================================

    protected function currencyElement($name, $amount, $nb_decimals = 2)
    {
        $el = $this->doc->createElement($name, $this->zugferdFormattedFloat($amount, $nb_decimals));
        $el->setAttribute('currencyID', $this->currencyCode);
        return $el;
    }

    protected function quantityElement($name, $quantity)
    {
        $el = $this->doc->createElement($name, $this->zugferdFormattedFloat($quantity, 4));
        $el->setAttribute('unitCode', 'C62');
        return $el;
    }

    protected function dateElement($date)
    {
        $el = $this->doc->createElement('udt:DateTimeString', $this->zugferdFormattedDate($date));
        $el->setAttribute('format', 102);
        return $el;
    }

    // ===========================================================================
    // helpers
    // ===========================================================================

    function zugferdFormattedDate($date)
    {
        if ($date && $date <> '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format('Ymd');
        }
        return '';
    }

    function zugferdFormattedFloat($amount, $nb_decimals = 2)
    {
        return number_format((float)$amount, $nb_decimals);
    }

    function itemsSubtotalGroupedByTaxPercent()
    {
        $result = [];
        foreach ($this->items as $item) {
            if ($item->item_tax_rate_percent == 0) continue;

            if (!isset($result[$item->item_tax_rate_percent])) {
                $result[$item->item_tax_rate_percent] = 0;
            }
            $result[$item->item_tax_rate_percent] += $item->item_subtotal;
        }
        return $result;
    }
}
