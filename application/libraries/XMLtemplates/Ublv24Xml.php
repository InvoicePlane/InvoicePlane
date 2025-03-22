<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * @Note        Valid with 'UBL invoice 2.4' rule set (ecosio.com/en/peppol-and-xml-document-validator/)
 * @Info        github.com/OpenPEPPOL/peppol-bis-invoice-3/blob/master/rules/examples
 *
 * Todo:        Add discounts lines (item & global)
 */

include_once 'BaseXml.php'; // ! important

class Ublv24Xml extends BaseXml
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function xml(): void
    {
        parent::xml();

        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlAccountingSupplierParty());
        $this->root->appendChild($this->xmlAccountingCustomerParty());
        $this->root->appendChild($this->xmlPaymentMeans());
        if ($this->invoice->invoice_terms) {
            $this->root->appendChild($this->xmlPaymentTerms());
        }
        $this->root->appendChild($this->xmlTaxTotal());
        $this->root->appendChild($this->xmlLegalMonetaryTotal());
        foreach ($this->items as $index => $item) {
            $this->root->appendChild($this->xmlInvoiceLine($index + 1, $item));
        }
        $this->doc->appendChild($this->root);
        $this->doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('Invoice');
        $node->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $node->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $node->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
        $node->appendChild($this->doc->createElement('cbc:CustomizationID', 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0'));
        $node->appendChild($this->doc->createElement('cbc:ProfileID', 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0'));
        $node->appendChild($this->doc->createElement('cbc:ID', trans('invoice') . ' #' . $this->invoice->invoice_number)); // $this->filename Snippet1
        $node->appendChild($this->doc->createElement('cbc:IssueDate', $this->invoice->invoice_date_created));
        $node->appendChild($this->doc->createElement('cbc:DueDate', $this->invoice->invoice_date_due));
        $node->appendChild($this->doc->createElement('cbc:InvoiceTypeCode', '380'));
        $node->appendChild($this->doc->createElement('cbc:DocumentCurrencyCode', $this->currencyCode));
        // $node->appendChild($this->doc->createElement('cbc:AccountingCost', '4025:123:4343'));         // optionnal : https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cbc-AccountingCost/
        // $node->appendChild($this->doc->createElement('cbc:BuyerReference', $this->invoice->user_id)); // optionnal : https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cbc-BuyerReference/

        return $node;
    }

    // AccountingSupplierParty
    protected function xmlAccountingSupplierParty()
    {
        $node = $this->doc->createElement('cac:AccountingSupplierParty');
        $node->appendChild($this->xmlParty('user'));

        return $node;
    }

    // Accounting[Supplier|Customer]Party helper
    protected function xmlParty($who)
    {
        $node = $this->doc->createElement('cac:Party');
        // $node->appendChild($this->doc->createElement('cbc:EndpointID', '002')); // optionnal : Electronic Address Scheme (EAS) : https://docs.peppol.eu/poacc/billing/3.0/codelist/eas/
        if ( ! $this->notax) {
            $node->appendChild($this->xmlPartyIdentification($who)); // need vat id
            $node->appendChild($this->xmlPartyName($who)); // user company (or name)
        }
        $node->appendChild($this->xmlPostalAddress($who));
        $node->appendChild($this->xmlContact($who)); // maybe not?

        return $node;
    }

    // xmlParty helper
    protected function xmlPartyIdentification($who)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'vat_id')));
        $node = $this->doc->createElement('cac:PartyIdentification');
        $nodeID = $this->doc->createElement('cbc:ID', $this->invoice->{$prop[0]}); // *_vat_id
        $node->appendChild($nodeID);

        return $node;
    }

    // xmlParty helper
    protected function xmlPartyName($who)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'company name'))); // *_company *_name
        $node = $this->doc->createElement('cac:PartyName');
        $nodeName = $this->doc->createElement('cbc:Name', $this->invoice->{$prop[0]} ? $this->invoice->{$prop[0]} : $this->invoice->{$prop[1]});
        $node->appendChild($nodeName);

        return $node;
    }

    // xmlParty helper
    protected function xmlPostalAddress($who)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'address_1 address_2 city zip country')));
        $node = $this->doc->createElement('cac:PostalAddress');
        $node->appendChild($this->doc->createElement('cbc:StreetName', $this->invoice->{$prop[0]})); // *_address_1
        if ( ! empty($this->invoice->{$prop[1]})) {
            $node->appendChild($this->doc->createElement('cbc:AdditionalStreetName', $this->invoice->{$prop[1]})); // *_address_2
        }
        $node->appendChild($this->doc->createElement('cbc:CityName',   $this->invoice->{$prop[2]})); // *_city
        $node->appendChild($this->doc->createElement('cbc:PostalZone', $this->invoice->{$prop[3]})); // *_zip
        $nodeCountry = $this->doc->createElement('cac:Country');
        $nodeCountry->appendChild($this->doc->createElement('cbc:IdentificationCode', $this->invoice->{$prop[4]})); // *_country
        $node->appendChild($nodeCountry);

        return $node;
    }

    // xmlParty helper
    protected function xmlContact($who)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'invoicing_contact phone fax email')));
        $contactName  = $this->invoice->{$prop[0]}; // *_invoicing_contact;
        $contactPhone = $this->invoice->{$prop[1]}; // *_phone;
        $contactFax   = $this->invoice->{$prop[2]}; // *_fax;
        $contactEmail = $this->invoice->{$prop[3]}; // *_email;
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

    // AccountingCustomerParty
    protected function xmlAccountingCustomerParty()
    {
        $node = $this->doc->createElement('cac:AccountingCustomerParty');
        $node->appendChild($this->xmlParty('client'));

        return $node;
    }

    // PaymentMeans docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cac-PaymentMeans
    protected function xmlPaymentMeans()
    {
        $node = $this->doc->createElement('cac:PaymentMeans');
        $nodePMC = $this->doc->createElement('cbc:PaymentMeansCode', '30'); // docs.peppol.eu/poacc/billing/3.0/codelist/UNCL4461/
        $nodePMC->setAttribute('name', 'Credit transfer');
        $node->appendChild($nodePMC);
        // Bank name (Optionnal)
        if ($this->invoice->user_bank) {
            $nodeBank = $this->doc->createElement('cbc:PaymentID', $this->invoice->user_bank);
            $node->appendChild($nodeBank);
        }

        $node->appendChild($this->xmlPFAccount());
        return $node;
    }

    // PaymentMeans helper
    protected function xmlPFAccount()
    {
        $node = $this->doc->createElement('cac:PayeeFinancialAccount');
        $nodeID = $this->doc->createElement('cbc:ID', $this->invoice->user_iban);
        $nodeID->setAttribute('schemeName', 'IBAN');
        $node->appendChild($nodeID);
        if ($this->invoice->user_bic) {
            $nodeFIBranch = $this->doc->createElement('cac:FinancialInstitutionBranch');
            $nodeFInstitution = $this->doc->createElement('cac:FinancialInstitution');
            $nodeFIBranch->appendChild($nodeFInstitution);

            $nodeFInstitutionID = $this->doc->createElement('cbc:ID', $this->invoice->user_bic);
            $nodeFInstitutionID->setAttribute('schemeName', 'BIC');
            $nodeFInstitution->appendChild($nodeFInstitutionID);
            $node->appendChild($nodeFIBranch);
        }

        return $node;
    }

    // PaymentTerms
    protected function xmlPaymentTerms()
    {
        $date = date_create($this->invoice->invoice_date_due);
        $PaymentTerms = trans('due_date') . ' ' . date_format($date, 'd/m/Y'); // todo date format from settings
        if ($PaymentTerms) {
            $node = $this->doc->createElement('cac:PaymentTerms');
            $nodePayTerms = $this->doc->createElement('cbc:Note', $PaymentTerms);
            $node->appendChild($nodePayTerms);
        }

        return $node;
    }

    // TaxTotal
    protected function xmlTaxTotal()
    {
        $node = $this->doc->createElement('cac:TaxTotal');
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $this->invoice->invoice_item_tax_total));

        // taxes
        if($this->notax) { // Not subject to VAT
            $percent = $this->invoice->invoice_item_tax_total; // it's 0.00 same of invoice_tax_total
            $subtotal = $this->invoice->invoice_item_subtotal; // invoice_subtotal
            $node->appendChild($this->xmlTaxSubtotal($percent, $subtotal, 'O')); // "O" pour "Exonéré" (Out of scope).
        }
        else {
            foreach ($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal)
            {
                $node->appendChild($this->xmlTaxSubtotal($percent, $subtotal[0])); // 'S' by default
            }
        }

        return $node;
    }

    // xmlTaxTotal helper
    protected function xmlTaxSubtotal($percent, $subtotal, $category = 'S')
    {
        $taxamount = $subtotal * $percent / 100;
        $node = $this->doc->createElement('cac:TaxSubtotal');
        $node->appendChild($this->currencyElement('cbc:TaxableAmount', $subtotal));

        $nodeTaxCategory = $this->doc->createElement('cac:TaxCategory');
        $nodeTaxCategory->appendChild($this->doc->createElement('cbc:ID', $category));
        if ($category == 'S') {
            $nodeTaxCategory->appendChild($this->doc->createElement('cbc:Percent', $percent)); // vat
        }
        else {// Pour les auto-entrepreneurs non assujettis à la TVA (Catégorie 'O')
            $nodeTaxCategory->appendChild($this->doc->createElement('cbc:TaxExemptionReason', 'Not subject to VAT')); // no vat
        }
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $taxamount));

        $nodeTaxSheme = $this->doc->createElement('cac:TaxScheme');
        $nodeTaxShemeID = $this->doc->createElement('cbc:ID', 'VAT');
        $nodeTaxSheme->appendChild($nodeTaxShemeID);
        $nodeTaxCategory->appendChild($nodeTaxSheme);
        $node->appendChild($nodeTaxCategory);

        return $node;
    }

    // LegalMonetaryTotal
    protected function xmlLegalMonetaryTotal()
    {
        $TaxExclAmount = $this->invoice->invoice_total - $this->invoice->invoice_item_tax_total;
        $node = $this->doc->createElement('cac:LegalMonetaryTotal');
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $this->invoice->invoice_item_subtotal));
        $node->appendChild($this->currencyElement('cbc:TaxExclusiveAmount', $TaxExclAmount));
        $node->appendChild($this->currencyElement('cbc:TaxInclusiveAmount', $this->invoice->invoice_total));
        // $node->appendChild($this->currencyElement('cbc:ChargeTotalAmount', '0.00')); // Todo? Optonnal? Insurance ... global taxes?
        $node->appendChild($this->currencyElement('cbc:PayableAmount', $this->invoice->invoice_balance));

        return $node;
    }

    // InvoiceLine xml helper
    protected function xmlInvoiceLine($lineNumber, $item)
    {
        $node = $this->doc->createElement('cac:InvoiceLine');
        $node->appendChild($this->doc->createElement('cbc:ID', $lineNumber));
        $node->appendChild($this->doc->createElement('cbc:InvoicedQuantity', $this->formattedQuantity($item->item_quantity)));
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $item->item_subtotal));

        $nodeLineRef = $this->doc->createElement('cac:OrderLineReference');
        $nodeLineRef->appendChild($this->doc->createElement('cbc:LineID', $lineNumber)); // Todo real line number?
        $node->appendChild($nodeLineRef);

        $nodeItem = $this->doc->createElement('cac:Item');
        $nodeItem->appendChild($this->doc->createElement('cbc:Description', $item->item_description));
        $nodeItem->appendChild($this->doc->createElement('cbc:Name', $item->item_name));

        $category = $this->notax ? 'O' : 'S'; // VAT or Not
        $nodeTax = $this->doc->createElement('cac:ClassifiedTaxCategory');
        $nodeTax->appendChild($this->doc->createElement('cbc:ID', $category));
        if ($category == 'S') {
            $nodeTax->appendChild($this->doc->createElement('cbc:Percent', $item->item_tax_rate_percent,));
        }
        $nodeItem->appendChild($nodeTax);

        $node->appendChild($nodeItem);
        $nodePrice = $this->doc->createElement('cac:Price');
        $nodePrice->appendChild($this->currencyElement('cbc:PriceAmount', $item->item_price));
        $node->appendChild($nodePrice);

        return $node;
    }

    // helper
    protected function currencyElement($name, $amount)
    {
        $el = $this->doc->createElement($name, $this->formattedFloat($amount, $this->decimal_places));
        $el->setAttribute('currencyID', $this->currencyCode);

        return $el;
    }
}
