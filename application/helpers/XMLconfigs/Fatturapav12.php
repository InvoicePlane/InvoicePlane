<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * FatturaPA v1.2.2
 * https://www.fatturapa.gov.it/it/norme-e-regole/documentazione-fattura-elettronica/formato-fatturapa/index.html
 * https://github.com/s2software/fatturapa (See in libraries/XMLtemplates/fatturapa folder)
 * https://github.com/s2software/fatturapa/wiki/Costanti#formato-trasmissione
 */
$xml_setting = [
    'full-name'   => 'FatturaPA v1.2.2',
    'countrycode' => 'IT',
    'embedXML'    => false,
    'XMLname'     => '',
    'options'     => [
        'regimefisc' => 'RF01',
    ],
];
