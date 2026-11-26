<?php

/*
 * Runs the real Sumex::pdf() (internal-PDF branch, SUMEX_URL empty) with every collaborator stubbed, and prints one
 * JSON line describing where the embedded SUMEX XML was written and what filename was handed to pdf_create().
 *
 * argv[1] = base64(invoice_number), argv[2] = existing directory used as STORAGE_TEMP_FOLDER (trailing slash added).
 */
define('BASEPATH', '/');
define('APPPATH', dirname(__DIR__, 3) . '/application/');
define('SUMEX_URL', '');
define('STORAGE_TEMP_FOLDER', rtrim($argv[2], '/') . '/');

function log_message($l, $m) {}
function trans($k)
{
    return $k;
}
function set_language($l) {}
function config_item($k) {}
function select_pdf_invoice_template($invoice)
{
    return 'InvoicePlane';
}
function render_template_view($view, $data, $return)
{
    return '<p>stub</p>';
}
function pdf_create(...$args)
{
    $GLOBALS['pdfCreateArgs'] = $args;

    return $args['associated_files'][0]['path'];
}

class FakeSettings
{
    public function setting($k)
    {
        return ['sumex_sliptype' => '0', 'currency_code' => 'CHF', 'sumex_role' => '1', 'sumex_place' => '0', 'sumex_canton' => '24'][$k] ?? null;
    }
}

class FakeQuery
{
    public function where($k, $v)
    {
        return $this;
    }

    public function get()
    {
        return $this;
    }

    public function result()
    {
        return [];
    }
}

class FakeCustomFields
{
    public function get_values_for_fields($m, $id)
    {
        return [];
    }
}

function &get_instance()
{
    static $ci;
    $ci = (object) [
        'mdl_settings'          => new FakeSettings(),
        'mdl_invoice_tax_rates' => new FakeQuery(),
        'mdl_custom_fields'     => new FakeCustomFields(),
        'load'                  => new class () {
            public function helper($h) {}

            public function model($m) {}
        },
    ];

    return $ci;
}

require APPPATH . 'helpers/file_security_helper.php';
require APPPATH . 'helpers/invoice_helper.php';
require APPPATH . 'libraries/Sumex.php';

$invoice = (object) ['invoice_id' => 7, 'invoice_number' => base64_decode($argv[1]), 'invoice_total' => '150.00', 'invoice_date_modified' => '2026-03-02 10:00:00', 'invoice_password' => '', 'payment_method' => 0, 'quote_id' => 0, 'user_id' => 1, 'client_id' => 3, 'client_language' => 'english', 'client_name' => 'Anna', 'client_surname' => 'Muster', 'client_birthdate' => '1980-05-06', 'client_gender' => '1', 'client_address_1' => 'Hauptstrasse 1', 'client_zip' => '8000', 'client_city' => 'Zurich', 'client_phone' => '', 'client_avs' => '7561234567890', 'client_insurednumber' => '999', 'user_company' => 'Praxis AG', 'user_address_1' => 'Via Cantonale 5', 'user_zip' => '6900', 'user_city' => 'Lugano', 'user_phone' => '091', 'user_gln' => '7601000000001', 'user_rcc' => 'C000002', 'user_subscribernumber' => '01-123456-7', 'sumex_casedate' => '2026-02-01', 'sumex_casenumber' => '42', 'sumex_treatmentstart' => '2026-02-01', 'sumex_treatmentend' => '2026-02-10', 'sumex_reason' => 0, 'sumex_diagnosis' => 'Flu', 'sumex_observations' => ''];
$item    = (object) ['item_name' => 'Consult', 'product_sku' => '120', 'item_quantity' => '1.00', 'item_price' => '50.00', 'item_total' => '50.00', 'item_date' => '2026-02-05', 'item_discount' => '0.00'];

$sumex = new Sumex(['invoice' => $invoice, 'items' => [$item], 'options' => []]);
$sumex->pdf();

echo 'PROBE:' . json_encode([
    'filename'  => $GLOBALS['pdfCreateArgs']['filename'],
    'xml_path'  => $GLOBALS['pdfCreateArgs']['associated_files'][0]['path'],
    'xml_exist' => is_file($GLOBALS['pdfCreateArgs']['associated_files'][0]['path']),
]) . "\n";
