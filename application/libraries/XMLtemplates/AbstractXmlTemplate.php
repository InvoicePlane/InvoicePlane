<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * @author      UBL Template author: Verony 2017
 * @warning     This version is meant only as an example!
 *
 */

class AbstractXmlTemplate
{
    public $invoice;

    public $doc;

    public $root;

    public function __construct($params)
    {
        $CI = & get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->filename = $params['filename'];
        $this->currencyCode = get_setting('currency_code'); // $CI->mdl_settings->setting('currency_code');
    }

    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true;
        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlAccountingSupplierParty());
        $this->root->appendChild($this->xmlAccountingCustomerParty());
        $this->root->appendChild($this->xmlPaymentMeans());
        if ($this->invoice->invoice_terms) {
            $this->root->appendChild($this->xmlPaymentTerms());
        }
        $this->root->appendChild($this->xmlTaxTotal($this->items[0]));
        $this->root->appendChild($this->xmlLegalMonetaryTotal());
        foreach ($this->items as $index => $item) {
            $this->root->appendChild($this->xmlInvoiceLine($index + 1, $item));
        }
        $this->doc->appendChild($this->root);
        $this->doc->save(UPLOADS_FOLDER . 'archive/' . $this->filename . '.xml');
    }

    public function ublFormattedFloat($amount, $nb_decimals = 2)
    {
        return number_format((float) $amount, $nb_decimals);
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('Invoice');
        $node->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $node->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $node->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
        $node->appendChild($this->doc->createElement('cbc:UBLVersionID', '2.0'));
        $node->appendChild($this->doc->createElement('cbc:CustomizationID', '1.0'));
        $node->appendChild($this->doc->createElement('cbc:ProfileID', 'EX IP-Community example'));
        $node->appendChild($this->doc->createElement('cbc:ID', $this->invoice->invoice_number));
        $node->appendChild($this->doc->createElement('cbc:IssueDate', $this->invoice->invoice_date_created));
        $node->appendChild($this->doc->createElement('cbc:InvoiceTypeCode', '380'));
        $node->appendChild($this->doc->createElement('cbc:DocumentCurrencyCode', $this->currencyCode));  // Mandatory

        return $node;
    }

    // AccountingSupplierParty
    protected function xmlAccountingSupplierParty()
    {
        $node = $this->doc->createElement('cac:AccountingSupplierParty');
        $node->appendChild($this->xmlSuppParty());

        return $node;
    }

    protected function xmlSuppParty()
    {
        $node = $this->doc->createElement('cac:Party');
        $node->appendChild($this->xmlSuppPartyIdentification());
        $node->appendChild($this->xmlSuppPartyName());
        $node->appendChild($this->xmlSuppPostalAddress());
        $node->appendChild($this->xmlSuppContact());

        return $node;
    }

    protected function xmlSuppPartyIdentification()
    {
        $userVatId = $this->invoice->user_vat_id ?? 'VAT ID for user NOT FILLED';

        $node = $this->doc->createElement('cac:PartyIdentification');
        $nodeID = $this->doc->createElement('cbc:ID', $userVatId);
        $nodeID->setAttribute('schemeAgencyID', $this->invoice->user_country);
        $nodeID->setAttribute('schemeAgencyName', 'Example');
        $node->appendChild($nodeID);

        return $node;
    }

    protected function xmlSuppPartyName()
    {
        // 939: We're looking for user_company here, while we also have client_name.
        $userCompany = $this->invoice->user_company ?? 'Company Name for user NOT FILLED';
        $node = $this->doc->createElement('cac:PartyName');
        $nodeName = $this->doc->createElement('cbc:Name', $userCompany);
        $node->appendChild($nodeName);

        return $node;
    }

    protected function xmlSuppPostalAddress()
    {
        $node = $this->doc->createElement('cac:PostalAddress');
        $node->appendChild($this->doc->createElement('cbc:StreetName', $this->invoice->user_address_1));
        $node->appendChild($this->doc->createElement('cbc:CityName', $this->invoice->user_city));
        $node->appendChild($this->doc->createElement('cbc:PostalZone', $this->invoice->user_zip));
        $nodeCountry = $this->doc->createElement('cac:Country');
        $nodeCountry->appendChild($this->doc->createElement('cbc:IdentificationCode', $this->invoice->user_country));
        $node->appendChild($nodeCountry);

        return $node;
    }

    protected function xmlSuppContact()
    {
        $contactName = $this->invoice->user_invoicing_contact;
        $contactPhone = $this->invoice->user_phone;
        $contactFax = $this->invoice->user_fax;
        $contactEmail = $this->invoice->user_email;
        if ($contactName . $contactPhone . $contactFax . $contactEmail) {
            $node = $this->doc->createElement('cac:Contact');
            if ($contactName) {
                $node->appendChild($this->doc->createElement('cbc:Name', $contactName));
            }
            if ($contactPhone) {
                $node->appendChild($this->doc->createElement('cbc:Telephone', $contactPhone));
            }
            if ($contactFax) {
                $node->appendChild($this->doc->createElement('cbc:Telefax', $contactFax));
            }
            if ($contactEmail) {
                $node->appendChild($this->doc->createElement('cbc:ElectronicMail', $contactEmail));
            }
        }

        return $node;
    }

    //AccountingCustomerParty
    protected function xmlAccountingCustomerParty()
    {
        $node = $this->doc->createElement('cac:AccountingCustomerParty');
        $node->appendChild($this->xmlCustParty());

        return $node;
    }

    protected function xmlCustParty()
    {
        $node = $this->doc->createElement('cac:Party');
        $node->appendChild($this->xmlCustPartyIdentification());
        $node->appendChild($this->xmlCustPartyName());
        $node->appendChild($this->xmlCustPostalAddress());

        return $node;
    }

    protected function xmlCustPartyIdentification()
    {
        $node = $this->doc->createElement('cac:PartyIdentification');
        $nodeID = $this->doc->createElement('cbc:ID', $this->invoice->client_vat_id);
        $nodeID->setAttribute('schemeAgencyID', $this->invoice->client_country);
        $nodeID->setAttribute('schemeAgencyName', 'Example');
        $node->appendChild($nodeID);

        return $node;
    }

    protected function xmlCustPartyName()
    {
        $node = $this->doc->createElement('cac:PartyName');
        $nodeName = $this->doc->createElement('cbc:Name', $this->invoice->client_name);
        $node->appendChild($nodeName);

        return $node;
    }

    protected function xmlCustPostalAddress()
    {
        $node = $this->doc->createElement('cac:PostalAddress');
        $node->appendChild($this->doc->createElement('cbc:StreetName', $this->invoice->client_address_1));
        $node->appendChild($this->doc->createElement('cbc:CityName', $this->invoice->client_city));
        $node->appendChild($this->doc->createElement('cbc:PostalZone', $this->invoice->client_zip));
        $nodeCountry = $this->doc->createElement('cac:Country');
        $nodeCountry->appendChild($this->doc->createElement('cbc:IdentificationCode', $this->invoice->client_country));
        $node->appendChild($nodeCountry);

        return $node;
    }

    //PaymentMeans
    protected function xmlPaymentMeans()
    {
        $paymentDueDate = $this->invoice->invoice_date_due;
        $instructionNote = 'Invoice: #' . $this->invoice->invoice_number;
        $node = $this->doc->createElement('cac:PaymentMeans');
        $nodePMC = $this->doc->createElement('cbc:PaymentMeansCode', '1');
        $nodePMC->setAttribute('listID', 'UN/ECE 4461');
        $nodePMC->setAttribute('listName', 'Payment Means');
        $node->appendChild($nodePMC);
        if ($paymentDueDate) {
            $node->appendChild($this->doc->createElement('cbc:paymentDueDate', $paymentDueDate));
        }
        if ($instructionNote) {
            $node->appendChild($this->doc->createElement('cbc:instructionNote', $instructionNote));
        }
        $node->appendChild($this->xmlPFAccount());

        return $node;
    }

    protected function xmlPFAccount()
    {
        $bankAccIBAN = $this->invoice->user_iban ?? 'IBAN for user not filled';
        $bankAccBIC = $this->invoice->user_bic;
        $node = $this->doc->createElement('cac:PayeeFinancialAccount');
        $nodeID = $this->doc->createElement('cbc:ID', $bankAccIBAN);
        $nodeID->setAttribute('schemeName', 'IBAN');
        $node->appendChild($nodeID);
        if ($bankAccBIC) {
            $nodeFIBranch = $this->doc->createElement('cac:FinancialInstitutionBranch');
            $nodeFInstitution = $this->doc->createElement('cac:FinancialInstitution');
            $nodeFIBranch->appendChild($nodeFInstitution);

            $nodeFInstitutionID = $this->doc->createElement('cbc:ID', $bankAccBIC);
            $nodeFInstitutionID->setAttribute('schemeName', 'BIC');
            $nodeFInstitution->appendChild($nodeFInstitutionID);
            $node->appendChild($nodeFIBranch);
        }

        return $node;
    }

    //PaymentTerms
    protected function xmlPaymentTerms()
    {
        $date = date_create($this->invoice->invoice_date_due);
        $PaymentTerms = 'Pay before: ' . date_format($date, 'd/m/Y');
        if ($PaymentTerms) {
            $node = $this->doc->createElement('cac:PaymentTerms');
            $nodePayTerms = $this->doc->createElement('cbc:Note', $PaymentTerms);
            $node->appendChild($nodePayTerms);
        }

        return $node;
    }

    //TaxTotal
    protected function xmlTaxTotal($item)
    {
        $node = $this->doc->createElement('cac:TaxTotal');
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $this->invoice->invoice_item_tax_total));
        $node->appendChild($this->xmlTaxSubtotal($item->item_tax_rate_percent, $item->item_subtotal));

        return $node;
    }

    protected function xmlTaxSubtotal($percent, $subtotal)
    {
        $percentage = $percent ?? 'Percentage not filled?';
        $taxAmount = $subtotal * $percent / 100;
        $node = $this->doc->createElement('cac:TaxSubtotal');
        $node->appendChild($this->currencyElement('cbc:TaxableAmount', $subtotal));
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $taxAmount));
        $node->appendChild($this->doc->createElement('cbc:Percent', $percentage));

        return $node;
    }

    //LegalMonetaryTotal
    protected function xmlLegalMonetaryTotal()
    {
        $TaxExclAmount = $this->invoice->invoice_total - $this->invoice->invoice_item_tax_total;
        $node = $this->doc->createElement('cac:LegalMonetaryTotal');
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $this->invoice->invoice_item_subtotal));
        $node->appendChild($this->currencyElement('cbc:TaxExclusiveAmount', $TaxExclAmount));
        $node->appendChild($this->currencyElement('cbc:TaxInclusiveAmount', $this->invoice->invoice_total));
        $node->appendChild($this->currencyElement('cbc:PayableAmount', $this->invoice->invoice_balance));

        return $node;
    }

    //InvoiceLine
    protected function xmlInvoiceLine($lineNumber, $item)
    {
        $node = $this->doc->createElement('cac:InvoiceLine');
        $node->appendChild($this->doc->createElement('cbc:ID', $lineNumber));
        $node->appendChild($this->doc->createElement('cbc:InvoicedQuantity', $item->item_quantity));
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $item->item_subtotal));
        $nodeTaxTotal = $this->doc->createElement('cac:TaxTotal');
        $nodeTaxTotal->appendChild($this->currencyElement('cbc:TaxAmount', $item->item_tax_total));
        $nodeTaxTotal->appendChild($this->xmlTaxSubtotal($item->item_tax_rate_percent, $item->item_subtotal));
        $node->appendChild($nodeTaxTotal);
        $nodeItem = $this->doc->createElement('cac:Item');
        $nodeItem->appendChild($this->doc->createElement('cbc:Name', $item->item_name));
        $node->appendChild($nodeItem);
        $nodePrice = $this->doc->createElement('cac:Price');
        $nodePrice->appendChild($this->currencyElement('cbc:PriceAmount', $item->item_price));
        $node->appendChild($nodePrice);

        return $node;
    }

    // ===========================================================================
    // helpers
    // ===========================================================================
    protected function currencyElement($name, $amount, $nb_decimals = 2)
    {
        $el = $this->doc->createElement($name, $this->ublFormattedFloat($amount, $nb_decimals));
        $el->setAttribute('currencyID', $this->currencyCode);

        return $el;
    }
}
