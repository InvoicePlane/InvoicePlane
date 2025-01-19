<?php

//  Filename      => 'Ublexamv20.php'     -> "Shortid" + "version" + ".php"
//                                            (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)

//  'full-name'   => 'UBL example v2.0',  -> UBL version name visible in the clients drop-down menu
//  'countrycode' => 'EX',                -> associated countrycode (if available in your native language country list)
//  'embedXML'    => false,               -> for 'ZUGFeRD' (and similar = Xml embedded in Pdf) set to true
//  'XMLname'     => 'factur-x.xml',      -> name of the Xml file (if embedded in a CII Pdf)

$xml_setting = [
    'full-name'   => 'UBL example v2.0',
    'countrycode' => 'DE',
    'embedXML'    => true,
    'XMLname'     => 'ZUGFeRD_23-invoice.xml',
];
