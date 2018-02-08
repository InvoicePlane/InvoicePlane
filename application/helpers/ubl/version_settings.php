<?php 

//  'id'          => 'Ublv21',      -> ref (without spaces, dots or special characters)
//  'full-name'   => 'UBL v2.1',    -> UBL version name visible in the clients drop-down menu
//  'countrycode' => 'UBL',         -> associated countrycode (if available in your native language country list)
//  'embedXML'    => '',            -> for 'Zugferd' (and similar = Xml embedded in Pdf) set to true (without '')
//  'embedPDF'    => true,          -> if the Pdf-file should be embedded in the xml-file set to true

$ubl = array( 
  array(
    'id'          => 'Ublexam20',
    'full-name'   => 'UBL example 2.0',
    'countrycode' => 'EX',
    'embedXML'    => '',
    'embedPDF'    => '',     
  ),
  array( 
    'id'          => 'Zugferd',
    'full-name'   => 'ZUGFeRD',
    'countrycode' => 'DE',
    'embedXML'    => true,
    'embedPDF'    => '',  
  )        
);
