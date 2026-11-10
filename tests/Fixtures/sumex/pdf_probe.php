<?php

define('BASEPATH', '/');
define('APPPATH', dirname(__DIR__, 3) . '/application/');
define('SUMEX_URL', $argv[1]);
function log_message($l, $m)
{
    echo "LOG[{$l}]: {$m}\n";
}
class FakeSettings
{
    public function setting($k)
    {
        return ['sumex_sliptype' => '0', 'currency_code' => 'CHF', 'sumex_role' => '1', 'sumex_place' => '0', 'sumex_canton' => '24'][$k] ?? null;
    }
}
function &get_instance()
{
    static $ci;
    $ci = (object) ['mdl_settings' => new FakeSettings(), 'load' => new class () {
        public function helper($h) {}

        public function model($m) {}
    }];

    return $ci;
}
require APPPATH . 'helpers/invoice_helper.php';
require APPPATH . 'libraries/Sumex.php';
$invoice = (object) ['invoice_id' => 7, 'invoice_number' => 'INV-7', 'invoice_total' => '150.00', 'invoice_date_modified' => '2026-03-02 10:00:00', 'client_id' => 3, 'client_name' => 'Anna', 'client_surname' => 'Muster', 'client_birthdate' => '1980-05-06', 'client_gender' => '1', 'client_address_1' => 'Hauptstrasse 1', 'client_zip' => '8000', 'client_city' => 'Zurich', 'client_phone' => '', 'client_avs' => '7561234567890', 'client_insurednumber' => '999', 'user_company' => 'Praxis AG', 'user_address_1' => 'Via Cantonale 5', 'user_zip' => '6900', 'user_city' => 'Lugano', 'user_phone' => '091', 'user_gln' => '7601000000001', 'user_rcc' => 'C000002', 'user_subscribernumber' => '01-123456-7', 'sumex_casedate' => '2026-02-01', 'sumex_casenumber' => '42', 'sumex_treatmentstart' => '2026-02-01', 'sumex_treatmentend' => '2026-02-10', 'sumex_reason' => 0, 'sumex_diagnosis' => 'Flu', 'sumex_observations' => ''];
$item    = (object) ['item_name' => 'Consult', 'product_sku' => '120', 'item_quantity' => '1.00', 'item_price' => '50.00', 'item_total' => '50.00', 'item_date' => '2026-02-05', 'item_description' => '', 'item_tax_rate_percent' => '0'];
$s       = new Sumex(['invoice' => $invoice, 'items' => [$item], 'options' => []]);
echo 'RESULT:' . var_export($s->pdf(), true) . "\n";
