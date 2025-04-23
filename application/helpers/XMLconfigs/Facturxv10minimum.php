<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * Factur-X v1.0.7-2 (MINIMUM) : https://ecosio.com/en/peppol-and-xml-document-validator/
 * SpecifiedLegalOrganizationSchemeID (Like EAS code)
 * 0002 : System Information et Repertoire des Entreprise et des Etablissements: SIRENE
 * 0007 : Organisationsnummer
 * 0009 : SIRET-CODE
 */
$xml_setting = [
    'full-name'   => 'Factur-X v1.0 - minimum', // Adjust like : 'Factur-X v1.0 - mini' (if you need)
    'countrycode' => 'FR',
    'embedXML'    => true,
    'XMLname'     => 'factur-x.xml', // The name of file embedded in PDF
    'generator'   => 'Facturxv10', // Use the libraries/XMLtemplates/Facturxv10Xml.php
    // Options in Facturxv10 generator
    'options'     => [
        // urn:cen.eu:en16931:2017#compliant#urn:(zugferd:2.3 | factur-x.eu):1p0:(basic | en16931)
        // https://github.com/descala/xsd-validator/commit/16780d57307f6605dad3127a65e25f11b1146e04
        'GuidelineSpecifiedDocumentContextParameterID' => 'urn:factur-x.eu:1p0:minimum',
        // Need for No tax item(s) and user (seller). Set to Not subject to VAT and use user_tax_code (SIREN / SIRET / national identification number)
        // Need SpecifiedLegalOrganization and SchemeID like EAS code.
        // Note: perplexity suggest to use FR:SIRET or FR:SIREN but unvalid with ecosio
        'user_eas_code' => '0002', // *EAS code for SpecifiedLegalOrganization > schemeID : Adjust with what you need
    ],
];
