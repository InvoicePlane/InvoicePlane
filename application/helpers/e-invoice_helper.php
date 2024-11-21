<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__, 2) . '/enums/UblTypeEnum.php';

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * @added       Verony-makesIT 2023
 */

/**
 * @AllowDynamicProperties
 */
/*function generate_xml_invoice_file($invoice, $items, $xml_lib, $filename)
{
    $CI = &get_instance();

    $CI->load->library('XMLtemplates/' . $xml_lib . 'Xml', ['invoice' => $invoice, 'items' => $items, 'filename' => $filename], 'ublciixml');
    $CI->ublciixml->xml();
    $path = './uploads/temp/' . $filename . '.xml';

    return $path;
}*/

function generate_xml_invoice_file($invoice, $items, $templateType, $filename)
{
    $generatorClass = match ($templateType) {
        UblTypeEnum::CIUS_V20    => 'Cius20Xml',
        UblTypeEnum::NLCIUS_V20  => 'NlCius20Xml',
        UblTypeEnum::ZUGFERD_V10 => 'Zugferd10Xml',
        UblTypeEnum::ZUGFERD_V23 => 'Zugferd23Xml',
        default                  => 'Cius20Xml',
    };

    require_once APPPATH . "libraries/XMLtemplates/{$generatorClass}.php";

    $CI = &get_instance();
    $generator = new $generatorClass([
        'invoice'      => $invoice,
        'items'        => $items,
        'filename'     => $filename,
        'currencyCode' => $CI->mdl_settings->setting('currency_code'),
        'templateType' => $templateType,
    ]);
    $generator->xml();

    return "./uploads/invoices/{$filename}.xml";
}

function include_rdf($filename)
{
    $s = '<rdf:Description rdf:about="" xmlns:zf="urn:ferd:pdfa:CrossIndustryDocument:invoice:1p0#">' . "\n";
    $s .= '  <zf:DocumentType>INVOICE</zf:DocumentType>' . "\n";
    $s .= '  <zf:DocumentFileName>ZUGFeRD-invoice.xml</zf:DocumentFileName>' . "\n";
    //     $s .= '  <zf:DocumentFileName>'. $filename .'</zf:DocumentFileName>' . "\n";
    $s .= '  <zf:Version>1.0</zf:Version>' . "\n";
    $s .= '  <zf:ConformanceLevel>COMFORT</zf:ConformanceLevel>' . "\n";
    $s .= '</rdf:Description>' . "\n";

    return $s;
}

/**
 * Returns all available xml-template items.
 *
 * @return array
 */
function get_xml_templates()
{
    require_once dirname(__FILE__, 2) . '/enums/UblTypeEnum.php';

    return array_map(function ($case) {
        return $case->value;
    }, UblTypeEnum::cases());
}

function get_ubl_template_details($xml_id)
{
    require_once dirname(__FILE__, 2) . '/enums/UblTypeEnum.php';

    $templateDetails = [
        UblTypeEnum::CIUS_V20 => [
            'full-name'   => 'CIUS Invoice', //UBL example v2.0
            'countrycode' => 'EU',
            'embedXML'    => false,
        ],
        UblTypeEnum::NLCIUS_V20 => [
            'full-name'   => 'Dutch CIUS Invoice', //Dutch UBL example v2.0
            'countrycode' => 'NL',
            'embedXML'    => false,
        ],
        UblTypeEnum::ZUGFERD_V10 => [
            'full-name'   => 'ZUGFeRD v1.0',
            'countrycode' => 'DE',
            'embedXML'    => true,
        ],
    	UblTypeEnum::ZUGFERD_V23 => [
    		'full-name'   => 'ZUGFeRD v2.3',
    		'countrycode' => 'DE',
    		'embedXML'    => true,
    	],
    ];

    // Check if the template exists in the details array
    if (array_key_exists($xml_id, $templateDetails)) {
        $xml_setting = $templateDetails[$xml_id];

        return $xml_setting['full-name'] . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
    }

    // If no match is found, return a default or null
}

function get_xml_full_name($xml_id)
{
    if (file_exists(APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php')) {
        include APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php';

        return $xml_setting['full-name'] . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
    }
}

function get_template_type($clientTemplate = null)
{
    if ( ! empty($clientTemplate) && UblTypeEnum::tryFrom($clientTemplate)) {
        return UblTypeEnum::from($clientTemplate);
    }

    $defaultSetting = get_setting('default_template_type');
    if ($defaultSetting && UblTypeEnum::tryFrom($defaultSetting)) {
        return UblTypeEnum::from($defaultSetting);
    }

    $envTemplate = env('UBL_STANDARD', 'CIUS_V20');

    return UblTypeEnum::tryFrom($envTemplate) ? UblTypeEnum::from($envTemplate) : UblTypeEnum::CIUS_V20;
}
