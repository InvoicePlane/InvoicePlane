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
 * @Note        Valid with https://test.peppolautoriteit.nl/validate & https://validator.invoice-portal.de/ & ecosio
 * @Info        github.com/OpenPEPPOL/peppol-bis-invoice-3/blob/master/rules/examples
 *              docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/
 *
 * Todo:        Why not for Credit note (but need positive amounts) and (maybe) little retail xml head
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
        $this->xmlPaymentTerms($this->root);
        // Invoice global discount (Optional)
        if ($this->invoice->invoice_discount_amount_subtotal != 0) {
            // Dispach by VAT Rate
            $this->docFalseAllowanceCharge($this->root);
        }
        $this->root->appendChild($this->xmlTaxTotal());
        $this->root->appendChild($this->xmlLegalMonetaryTotal());
        // Item lines
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
        $node->appendChild($this->doc->createElement('cbc:IssueDate', $this->formattedDate($this->invoice->invoice_date_created)));
        $node->appendChild($this->doc->createElement('cbc:DueDate', $this->formattedDate($this->invoice->invoice_date_due)));
        $node->appendChild($this->doc->createElement('cbc:InvoiceTypeCode', '380'));
        $node->appendChild($this->doc->createElement('cbc:DocumentCurrencyCode', $this->currencyCode));
        // $node->appendChild($this->doc->createElement('cbc:AccountingCost', 'N/A')); // Optional : https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cbc-AccountingCost/
        // $node->appendChild($this->doc->createElement('cbc:BuyerReference', 'N/A')); // Optional : https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cbc-BuyerReference/
        $ID = $this->doc->createElement('cbc:ID', 'N/A');
        $OrderReference = $this->doc->createElement('cac:OrderReference');
        $OrderReference->appendChild($ID);
        $node->appendChild($OrderReference);

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
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'ubl_eas_code tax_code')));
        $schm = $this->invoice->{$prop[0]}; // *_ubl_eas_code
        $ninb = $this->invoice->{$prop[1]}; // *_tax_code

        $schemeID = $schm; // AES code (Electronic Address Scheme) : https://docs.peppol.eu/poacc/billing/3.0/codelist/eas/
        $id       = $ninb; // National identification number (Like SIRET in France)

        // https://docs.peppol.eu/pint/pint-eu/pint-eu/trn-invoice/syntax/cac-AccountingSupplierParty/cac-Party/cbc-EndpointID/schemeID/
        $nodeEndpointID = $this->doc->createElement('cbc:EndpointID', $id);
        $nodeEndpointID->setAttribute('schemeID', $schemeID);

        $node->appendChild($nodeEndpointID); //

        $node->appendChild($this->xmlPartyIdentification($who, $id, $schemeID));
        $node->appendChild($this->xmlPartyName($who)); // company (or name)
        $node->appendChild($this->xmlPostalAddress($who));
        if ( ! $this->notax) {
            $node->appendChild($this->xmlPartyTaxScheme($who)); // need vat_id
        }
        $node->appendChild($this->xmlPartyLegalEntity($who, $id, $schemeID)); // company or name
        if ($NodeContact = $this->xmlContact($who)) {
            $node->appendChild($NodeContact); // optional
        }

        return $node;
    }

    // xmlParty helper
    protected function xmlPartyTaxScheme($who)
    {
        $node = $this->doc->createElement('cac:PartyTaxScheme');
        $prop = $who . '_vat_id';
        $nodeID = $this->doc->createElement('cbc:CompanyID', $this->invoice->$prop);
        $node->appendChild($nodeID);

        $nodeTax = $this->doc->createElement('cac:TaxScheme');
        $nodeID = $this->doc->createElement('cbc:ID', 'VAT');
        $nodeTax->appendChild($nodeID);
        $node->appendChild($nodeTax);

        return $node;
    }

    // xmlParty helper
    protected function xmlPartyLegalEntity($who, $id, $schemeID)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'company name')));
        $node = $this->doc->createElement('cac:PartyLegalEntity');
        $name = $this->invoice->{$prop[0]} ? $this->invoice->{$prop[0]} : $this->invoice->{$prop[1]}; // *_company (Or *_name if empty)
        $nodeName = $this->doc->createElement('cbc:RegistrationName', $name);
        $node->appendChild($nodeName);

        return $node;
    }

    // xmlParty helper
    protected function xmlPartyIdentification($who, $id, $schemeID, $schemeVersionID = null)
    {
        $node = $this->doc->createElement('cac:PartyIdentification');
        $nodeID = $this->doc->createElement('cbc:ID', $id);
        // docs.peppol.eu/poacc/billing/3.0/codelist/ICD/
        $nodeID->setAttribute('schemeID', $schemeID);
        if ($schemeVersionID)
            $nodeID->setAttribute('schemeVersionID', $schemeVersionID); // FOR EM

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
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'invoicing_contact phone email')));
        $contactName  = $this->invoice->{$prop[0]}; // *_invoicing_contact;
        $contactPhone = $this->invoice->{$prop[1]}; // *_phone;
        $contactEmail = $this->invoice->{$prop[2]}; // *_email;
        if ($contactName . $contactPhone . $contactEmail) {
            $node = $this->doc->createElement('cac:Contact');
            if ($contactName) {
                $node->appendChild($this->doc->createElement('cbc:Name', $contactName));
            }
            if ($contactPhone) {
                $node->appendChild($this->doc->createElement('cbc:Telephone', $contactPhone));
            }
            if ($contactEmail) {
                $node->appendChild($this->doc->createElement('cbc:ElectronicMail', $contactEmail));
            }
            return $node;
        }
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
        // Bank name (Optional)
        if ($this->invoice->user_bank) {
            $nodeBank = $this->doc->createElement('cbc:PaymentID', $this->invoice->user_bank);
            $node->appendChild($nodeBank);
        }

        $node->appendChild($this->xmlPayeeFinancialAccount());
        return $node;
    }

    // PaymentMeans helper
    protected function xmlPayeeFinancialAccount()
    {
        $node = $this->doc->createElement('cac:PayeeFinancialAccount');
        $nodeID = $this->doc->createElement('cbc:ID', $this->invoice->user_iban);
        $node->appendChild($nodeID);

        return $node;
    }

    // xml helper
    protected function xmlPaymentTerms(& $node)
    {
        if ($note = trim(htmlsc(strip_tags($this->invoice->invoice_terms)))) {
            $nodeNote = $this->doc->createElement('cbc:Note', $note);
            $nodeTerms = $this->doc->createElement('cac:PaymentTerms');
            $nodeTerms->appendChild($nodeNote);
            $node->appendChild($nodeTerms);
        }
    }

    // TaxTotal
    protected function xmlTaxTotal()
    {
        $node = $this->doc->createElement('cac:TaxTotal');
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $this->invoice->invoice_item_tax_total));

        // Not subject to VAT
        if ($this->notax) {
            $percent = $this->invoice->invoice_item_tax_total; // it's 0.00 same of invoice_tax_total
            $subtotal = $this->invoice->invoice_item_subtotal; // invoice_subtotal
            $node->appendChild($this->xmlTaxSubtotal($percent, $subtotal, 'O')); // "O" for "Exonerated" (Out of scope).
        }
        else {
            foreach ($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal) {
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
        $node->appendChild($this->currencyElement('cbc:TaxAmount', $taxamount));

        $nodeTaxCategory = $this->doc->createElement('cac:TaxCategory');
        $nodeTaxCategory->appendChild($this->doc->createElement('cbc:ID', $category));
        // Subject to VAT
        if ($category == 'S') {
            $nodeTaxCategory->appendChild($this->doc->createElement('cbc:Percent', $percent));
        }
        else {
            // No: Category 'O'
            $nodeTaxCategory->appendChild($this->doc->createElement('cbc:TaxExemptionReason', 'Not subject to VAT'));
        }

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
        // Calculation mode: LEGACY_CALCULATION true (in ipconfig) Note: OK only with(out) item taxes, but not with discounts, Sorry)
        if ($this->legacy_calculation) {
            $LineExtensionAmount = $this->invoice->invoice_item_subtotal;
            $TaxExclusiveAmount = $this->invoice->invoice_total - ($this->invoice->invoice_tax_total + $this->invoice->invoice_item_tax_total);
        } else {
            // legacy false: No global taxes allowed (invoice_tax_total)
            $LineExtensionAmount = $this->invoice->invoice_subtotal - $this->invoice->items_discount_amount_total;
            $TaxExclusiveAmount = $this->invoice->invoice_item_subtotal;
        }

        $node = $this->doc->createElement('cac:LegalMonetaryTotal');
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $LineExtensionAmount));
        $node->appendChild($this->currencyElement('cbc:TaxExclusiveAmount', $TaxExclusiveAmount));
        $node->appendChild($this->currencyElement('cbc:TaxInclusiveAmount', $this->invoice->invoice_total));
        // Optonnal Only For global discount ! Important
        if ($this->invoice->invoice_discount_amount_subtotal != 0) {
            $node->appendChild($this->currencyElement('cbc:AllowanceTotalAmount', $this->invoice->invoice_discount_amount_subtotal));
        }
        // $node->appendChild($this->currencyElement('cbc:ChargeTotalAmount', '0.00')); // Todo? Optonnal Insurance ...

        $node->appendChild($this->currencyElement('cbc:PayableAmount', $this->invoice->invoice_balance));

        return $node;
    }

    // InvoiceLine xml helper (ex. https://www.datypic.com/sc/ubl21/e-cac_InvoiceLine.html)
    protected function xmlInvoiceLine($lineNumber, $item)
    {
        $node = $this->doc->createElement('cac:InvoiceLine');
        $node->appendChild($this->doc->createElement('cbc:ID', $lineNumber));
        $nodeQty = $this->doc->createElement('cbc:InvoicedQuantity', $this->formattedQuantity($item->item_quantity));
        // [BR-23]-An Invoice line (BG-25) shall have an Invoiced quantity unit of measure code (BT-130).
        $nodeQty->setAttribute('unitCode', $item->item_product_unit?:'PI');
        $node->appendChild($nodeQty);
        $node->appendChild($this->currencyElement('cbc:LineExtensionAmount', $item->item_subtotal - $item->item_discount));

        $nodeLineRef = $this->doc->createElement('cac:OrderLineReference');
        $nodeLineRef->appendChild($this->doc->createElement('cbc:LineID', $item->item_order));
        $node->appendChild($nodeLineRef);

        if ($item->item_discount != 0) {
            $node->appendChild($this->xmlFalseAllowanceCharge($item->item_discount));
        }

        $nodeItem = $this->doc->createElement('cac:Item');
        // Optional
        if ($item->item_description) {
            $nodeItem->appendChild($this->doc->createElement('cbc:Description', $item->item_description));
        }
        $nodeItem->appendChild($this->doc->createElement('cbc:Name', $item->item_name));

        // item TaxCategory
        $category = $this->notax ? 'O' : 'S'; // VAT or Not
        $nodeTax = $this->doc->createElement('cac:ClassifiedTaxCategory');
        $nodeTax->appendChild($this->doc->createElement('cbc:ID', $category));
        // Subject to VAT
        if ($category == 'S') {
            $nodeTax->appendChild($this->doc->createElement('cbc:Percent', $item->item_tax_rate_percent)); // O Document MUST not contain empty elements.
        }
        $taxSheme = $this->doc->createElement('cac:TaxScheme');
        $ID = $this->doc->createElement('cbc:ID', 'VAT');
        $taxSheme->appendChild($ID);
        $nodeTax->appendChild($taxSheme);

        $nodeItem->appendChild($nodeTax);

        $node->appendChild($nodeItem);
        $nodePrice = $this->doc->createElement('cac:Price');
        $nodePrice->appendChild($this->currencyElement('cbc:PriceAmount', $item->item_price));
        $node->appendChild($nodePrice);

        return $node;
    }

    // Discount AllowanceCharge (xmlInvoiceLine discount helper)
    protected function xmlFalseAllowanceCharge($amount, $percent = null)
    {
        // docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5189/
        $node = $this->doc->createElement('cac:AllowanceCharge');
        $node->appendChild($this->doc->createElement('cbc:ChargeIndicator', 'false'));
        $node->appendChild($this->doc->createElement('cbc:AllowanceChargeReasonCode', '95'));
        $node->appendChild($this->doc->createElement('cbc:AllowanceChargeReason', rtrim(trans('discount'), ' '))); // todo curious chars ' ' not a space (found in French ip_lang)
        $node->appendChild($this->currencyElement('cbc:Amount', $amount));

        // TaxCategory (global discount(s) helper)
        if ($percent !== null) {
            $category = $percent ? 'S' : 'O'; // VAT or Not
            $nodeTax  = $this->doc->createElement('cac:TaxCategory');
            $nodeTax->appendChild($this->doc->createElement('cbc:ID', $category));
            // Subject to VAT
            if ($category == 'S') {
                $nodeTax->appendChild($this->doc->createElement('cbc:Percent', $percent)); // O Document MUST not contain empty elements.
            }
            $nodeShe  = $this->doc->createElement('cac:TaxScheme');
            $nodeShe->appendChild($this->doc->createElement('cbc:ID', 'VAT'));
            $nodeTax->appendChild($nodeShe);
            $node->appendChild($nodeTax);
        }

        return $node;
    }

    // helper
    protected function docFalseAllowanceCharge( & $node) // bep
    {
        // Note: If empty itemsSubtotalGroupedByTaxPercent ($this->notax) Only one `AllowanceChargee`
        if ($this->invoice->invoice_discount_amount_total > 0 && $this->itemsSubtotalGroupedByTaxPercent) {
            // category = 'S'
            $amounts = [];
            // Loop on itemsSubtotalGroupedByTaxPercent to dispatch discount by VAT rate's
            foreach($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal) {
                // Don't divide per 0
                if($this->invoice->invoice_subtotal != 0) {
                    // from set_invoice_discount_amount_total (BaseXml helper)
                    $amounts[$percent] = ($subtotal[1] / $this->invoice->invoice_subtotal) * $this->invoice->invoice_discount_amount_subtotal;
                }
            }
        } else {
            // category = 'O' from set_invoice_discount_amount_total (BaseXml helper)
            $amounts[0] = $this->invoice->invoice_discount_amount_subtotal;
        }

        // Loop on VAT to dispatch global discount
        foreach($amounts as $percent => $amount) {
            $node->appendChild($this->xmlFalseAllowanceCharge($amount, $percent));
        }
    }

    // helper
    protected function currencyElement($name, $amount)
    {
        $el = $this->doc->createElement($name, $this->formattedFloat($amount, $this->decimal_places));
        $el->setAttribute('currencyID', $this->currencyCode);

        return $el;
    }

    /**
     * @return string|null
     */
    public function formattedDate($date, $format = 'Y-m-d')
    {
        if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $date)) {
            return $date;
        }
        return parent::formattedDate($date, $format);
    }
}
