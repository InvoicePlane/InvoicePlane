<?php

defined('BASEPATH') || exit('No direct script access allowed');
$xml_setting = [
    'full-name'   => 'Factur-X v1.09 - EN 16931',
    'countrycode' => 'FR',
    'embedXML'    => true,
    'XMLname'     => 'factur-x.xml',
    'options'     => [
        'GuidelineSpecifiedDocumentContextParameterID' => 'urn:cen.eu:en16931:2017',
        'FrenchSiren'                                  => true,
    ],
];
