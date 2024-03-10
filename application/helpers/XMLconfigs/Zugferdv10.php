<?php 

//  Filename      => 'Ublexamv20.php'     -> "Shortid" + "version" + ".php" 
//                                            (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)

//  'full-name'   => 'UBL example v2.0',  -> UBL version name visible in the clients drop-down menu
//  'countrycode' => 'EX',                -> associated countrycode (if available in your native language country list)
//  'embedXML'    => false,               -> for 'Zugferd' (and similar = Xml embedded in Pdf) set to true

$xml_setting = array( 
  'full-name'   => 'ZUGFeRD v1.0',
  'countrycode' => 'DE',
  'embedXML'    => true,       
);
