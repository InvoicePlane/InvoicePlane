<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 * @Note        Zugferd 2.3 & Factur-X 1.0.7 compatibible
 *
 * @Important   Need LEGACY_CALCULATION=false (In ip_config)
 *               For embeded xml are valid (discounts calculation)
 */

/**
 * Class Facturxv10Xml
 */

include_once 'BaseXml.php'; // ! important

class Facturxv10Xml extends BaseXml
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function xml(): void
    {
        parent::xml();

        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlExchangedDocumentContext());
        $this->root->appendChild($this->xmlExchangedDocument());
        $this->root->appendChild($this->xmlSupplyChainTradeTransaction());

        $this->doc->appendChild($this->root);
        $this->doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
        // return $this->doc->saveXML();
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('rsm:CrossIndustryInvoice');
        $node->setAttribute('xmlns:qdt', 'urn:un:unece:uncefact:data:standard:QualifiedDataType:100');
        $node->setAttribute('xmlns:ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100');
        $node->setAttribute('xmlns:rsm', 'urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100');
        $node->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100');
        $node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        return $node;
    }

    protected function xmlExchangedDocumentContext()
    {
        $node = $this->doc->createElement('rsm:ExchangedDocumentContext');
        // XRechnung-CII-validation
        if (! empty($id = @$this->options['BusinessProcessSpecifiedDocumentContextParameterID'])) {
            $businessNode = $this->doc->createElement('ram:BusinessProcessSpecifiedDocumentContextParameter');
            $businessNode->appendChild($this->doc->createElement('ram:ID', $id));
            $node->appendChild($businessNode);
        }

        $guidelineNode = $this->doc->createElement('ram:GuidelineSpecifiedDocumentContextParameter');
        // urn:cen.eu:en16931:2017#compliant#urn:(zugferd:2.3 | factur-x.eu):1p0:(basic | en16931) ::: en16931 = COMFORT (profil)
        // urn:cen.eu:en16931:2017#conformant#urn:(zugferd:2.3 | factur-x.eu):1p0:extended
        $id = 'urn:cen.eu:en16931:2017'; // KISS
        // XRechnung-CII-validation
        if (! empty($cid = @$this->options['GuidelineSpecifiedDocumentContextParameterID'])) {
            $id = $cid;
        }

        $guidelineNode->appendChild($this->doc->createElement('ram:ID', $id));
        $node->appendChild($guidelineNode);

        return $node;
    }

    protected function xmlExchangedDocument()
    {
        $node = $this->doc->createElement('rsm:ExchangedDocument');

        $node->appendChild($this->doc->createElement('ram:ID', $this->invoice->invoice_number));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 380));

        // IssueDateTime
        $dateNode = $this->doc->createElement('ram:IssueDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_created));

        $node->appendChild($dateNode);

        return $node;
    }

    protected function dateElement($date)
    {
        $el = $this->doc->createElement('udt:DateTimeString', $this->formattedDate($date));
        $el->setAttribute('format', 102);
        return $el;
    }

    protected function xmlSupplyChainTradeTransaction()
    {
        $node = $this->doc->createElement('rsm:SupplyChainTradeTransaction');

        foreach ($this->items as $index => $item) {
            $node->appendChild($this->xmlIncludedSupplyChainTradeLineItem($index + 1, $item));
        }

        $node->appendChild($this->xmlApplicableHeaderTradeAgreement());
        $node->appendChild($this->xmlApplicableHeaderTradeDelivery());
        $node->appendChild($this->xmlApplicableHeaderTradeSettlement());

        return $node;
    }

    protected function xmlSpecifiedTradePaymentTerms()
    {
        $date = date_create($this->invoice->invoice_date_due);
        $PaymentTerms = trans('due_date') . ' ' . date_format($date, get_setting('date_format'));
        $terms = trim(htmlsc(strip_tags($this->invoice->invoice_terms)));
        if ($terms !== '' && $terms !== '0') {
            $PaymentTerms .= PHP_EOL . trans('terms') . PHP_EOL . $terms;
        }

        $node = $this->doc->createElement('ram:SpecifiedTradePaymentTerms');
        $node->appendChild($this->doc->createElement('ram:Description', $PaymentTerms));
        return $node;
    }

    protected function xmlApplicableHeaderTradeAgreement()
    {
        $node = $this->doc->createElement('ram:ApplicableHeaderTradeAgreement');

        // XRechnung-CII-validation ram:BuyerReference
        if (! empty($this->options['CII'])) {
            $node->appendChild($this->doc->createElement('ram:BuyerReference', 'N/A'));
        }

        $node->appendChild($this->xmlSellerTradeParty());
        $node->appendChild($this->xmlBuyerTradeParty());
        return $node;
    }

    protected function xmlSellerTradeParty()
    {
        $node = $this->doc->createElement('ram:SellerTradeParty');
        $this->xmlTradeParty($node, 'user');

        return $node;
    }

    protected function xmlBuyerTradeParty()
    {
        $node = $this->doc->createElement('ram:BuyerTradeParty');
        $this->xmlTradeParty($node, 'client');

        return $node;
    }

    // xml(Seller|Buyer)TradeParty helper
    protected function xmlTradeParty(&$node, $who)
    {
        $prop = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'id name zip address_1 address_2 city country vat_id')));
        if ($who == 'user') {
            $node->appendChild($this->doc->createElement('ram:ID', $this->invoice->{$prop[0]})); // *_id zugferd 2 : SELLER123
        }

        $node->appendChild($this->doc->createElement('ram:Name', htmlsc($this->invoice->{$prop[1]}))); // *_name

        // XRechnung-CII-validation
        if (! empty($this->options['CII'])) {
            $ciip = explode(' ', $who . '_' . implode(' ' . $who . '_', explode(' ', 'tax_code ubl_eas_code invoicing_contact phone mobile email')));

            // Tax code (national identification number)
            $ciiNode = $this->doc->createElement('ram:SpecifiedLegalOrganization');
            $idNode  = $this->doc->createElement('ram:ID', $this->invoice->{$ciip[0]}); // *_tax_code
            $idNode->setAttribute('schemeID', $this->invoice->{$ciip[1]}); // *_ubl_eas_code
            $ciiNode->appendChild($idNode);
            $node->appendChild($ciiNode);
            // Contact name
            $ciiNode = $this->doc->createElement('ram:DefinedTradeContact');
            $ciiNode->appendChild($this->doc->createElement('ram:PersonName', $this->invoice->{$ciip[2]})); // *_invoicing_contact
            // Phone
            $telNode = $this->doc->createElement('ram:TelephoneUniversalCommunication');
            $tel = $this->invoice->{$ciip[3]} ? $this->invoice->{$ciip[3]} : $this->invoice->{$ciip[4]}; // *_phone or *_mobile
            $telNode->appendChild($this->doc->createElement('ram:CompleteNumber', $tel));
            $ciiNode->appendChild($telNode);
            // E-mail
            $melNode = $this->doc->createElement('ram:EmailURIUniversalCommunication');
            $melNode->appendChild($this->doc->createElement('ram:URIID', $this->invoice->{$ciip[5]})); // *_email
            $ciiNode->appendChild($melNode);

            $node->appendChild($ciiNode);
        }

        // PostalTradeAddress
        $addressNode = $this->doc->createElement('ram:PostalTradeAddress');
        $addressNode->appendChild($this->doc->createElement('ram:PostcodeCode', htmlsc($this->invoice->{$prop[2]}))); // *_zip
        $addressNode->appendChild($this->doc->createElement('ram:LineOne', htmlsc($this->invoice->{$prop[3]}))); // *_address_1
        if ($addr = $this->invoice->{$prop[4]}) { // *_address_2
            $addressNode->appendChild($this->doc->createElement('ram:LineTwo', htmlsc($addr))); // *_address_2
        }

        $addressNode->appendChild($this->doc->createElement('ram:CityName', htmlsc($this->invoice->{$prop[5]}))); // *_city
        $addressNode->appendChild($this->doc->createElement('ram:CountryID', htmlsc($this->invoice->{$prop[6]})));
         // *_country
        $node->appendChild($addressNode);

        // XRechnung-CII-validation    URIUniversalCommunicationURIID
        if (! empty($this->options['CII'])) {
            // ram:URIUniversalCommunication/ram:URIID
            $uriNode = $this->doc->createElement('ram:URIUniversalCommunication');
            $idNode = $this->doc->createElement('ram:URIID', $this->invoice->{$ciip[5]}); // *_email
            $idNode->setAttribute('schemeID', 'EM'); // todo $schemeID
            $uriNode->appendChild($idNode);
            $node->appendChild($uriNode);
        }

        // SpecifiedTaxRegistration
        if (! $this->notax) {
            $node->appendChild($this->xmlSpecifiedTaxRegistration('VA', $this->invoice->{$prop[7]})); // *_vat_id zugferd 2
        }
    }

    /**
     * @param string $schemeID
     * @param string $content
     */
    protected function xmlSpecifiedTaxRegistration($schemeID, $content)
    {
        $node = $this->doc->createElement('ram:SpecifiedTaxRegistration');
        $el = $this->doc->createElement('ram:ID', $content);
        $el->setAttribute('schemeID', $schemeID);

        $node->appendChild($el);
        return $node;
    }

    protected function xmlApplicableHeaderTradeDelivery()
    {
        $node = $this->doc->createElement('ram:ApplicableHeaderTradeDelivery');

        // ActualDeliverySupplyChainEvent
        $eventNode = $this->doc->createElement('ram:ActualDeliverySupplyChainEvent');
        $dateNode = $this->doc->createElement('ram:OccurrenceDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_created));

        $eventNode->appendChild($dateNode);

        $node->appendChild($eventNode);
        return $node;
    }

    protected function xmlApplicableHeaderTradeSettlement()
    {
        $node = $this->doc->createElement('ram:ApplicableHeaderTradeSettlement');

        $node->appendChild($this->doc->createElement('ram:PaymentReference', $this->invoice->invoice_number));
        $node->appendChild($this->doc->createElement('ram:InvoiceCurrencyCode', $this->currencyCode));

        // bank
        $node->appendChild($this->xmlSpecifiedTradeSettlementPaymentMeans());

        // taxes
        if (! $this->notax) { // hard? todo? legacy_calculation: like if have discounts (how to find item(s) with amount > of amount discount to get/dispatch % of global VAT's
            foreach ($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal) {
                $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal[0])); // 'S' by default
            }
        } else // Not subject to VAT
        {
            $percent = $this->invoice->invoice_item_tax_total; // it's 0.00 same of invoice_tax_total
            $subtotal = $this->invoice->invoice_item_subtotal; // invoice_subtotal
            $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal, 'O')); // "O" pour "Exonéré" (Out of scope).
        }

        // BillingSpecifiedPeriod (optional)
        $period = $this->doc->createElement('ram:BillingSpecifiedPeriod');
        // StartDateTime
        $dateNode = $this->doc->createElement('ram:StartDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_created));

        $period->appendChild($dateNode);
        // EndDateTime
        $dateNode = $this->doc->createElement('ram:EndDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_due));

        $period->appendChild($dateNode);
        $node->appendChild($period);

        if ($this->invoice->invoice_discount_amount_total != 0) {
            // If global discount (ram:AppliedTradeAllowanceCharge)
            // Must be after BillingSpecifiedPeriod and before SpecifiedTradePaymentTerms !important
            // SpecifiedTradeAllowanceCharge
            $this->addSpecifiedTradeAllowanceCharge_discount($node);
        }

        // SpecifiedTradePaymentTerms (zugferd 2.3 & facturx 1.0.7)
        $node->appendChild($this->xmlSpecifiedTradePaymentTerms());

        // sums
        $node->appendChild($this->xmlSpecifiedTradeSettlementHeaderMonetarySummation());
        return $node;
    }

    // helper to make SpecifiedTradeAllowanceCharge 's
    protected function addSpecifiedTradeAllowanceCharge_discount(&$node)
    {
        // Note: If empty itemsSubtotalGroupedByTaxPercent ($this->notax) Only one `SpecifiedTradeAllowanceCharge` ;)
        if ($this->invoice->invoice_discount_amount_total != 0 && $this->itemsSubtotalGroupedByTaxPercent) {
            $category = 'S';
            $amounts = [];
            // Loop on itemsSubtotalGroupedByTaxPercent to dispatch discount by VAT's rate
            foreach ($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal) {
                // Don't divide per 0
                if ($this->invoice->invoice_subtotal != 0) {
                    // from set_invoice_discount_amount_total (helper)
                    $amounts[$percent] = ($subtotal[1] / $this->invoice->invoice_subtotal) * $this->invoice->invoice_discount_amount_subtotal;
                }
            }
        } else {
            $category = 'O';
            // from set_invoice_discount_amount_total (helper)
            $amounts[0] = $this->invoice->invoice_discount_amount_subtotal;
        }

        // Loop on VAT to dispatch global discount
        foreach ($amounts as $percent => $amount) {
            $discountNode = $this->doc->createElement('ram:SpecifiedTradeAllowanceCharge');
            // ChargeIndicator
            $indicatorNode = $this->doc->createElement('ram:ChargeIndicator');
            $indicatorNode->appendChild($this->doc->createElement('udt:Indicator', 'false')); // false it's a discount
            $discountNode->appendChild($indicatorNode);

            $discountNode->appendChild($this->currencyElement('ram:ActualAmount', $amount)); // of discount of vat rate
            // Not For NLCIUS CII 1.0.3.9 : [BR-NL-32] / [BR-NL-34] The use of an allowance reason code or charge reason code (ram:SpecifiedTradeAllowanceCharge/ram:ReasonCode) are not recommended, both on document level and on line level.
            if (empty($this->options['NoReasonCode'])) {
                $discountNode->appendChild($this->doc->createElement('ram:ReasonCode', '95'));
            }

            $discountNode->appendChild($this->doc->createElement('ram:Reason', rtrim(trans('discount'), ' '))); // todo curious chars ' ' not a space (found in French ip_lang)

            $taxNode = $this->doc->createElement('ram:CategoryTradeTax');
            $taxNode->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));

            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', $category)); // S or O
            if ($category === 'S') {
                $taxNode->appendChild($this->doc->createElement('ram:RateApplicablePercent', $percent)); // of VAT rate
            }

            $discountNode->appendChild($taxNode);

            $node->appendChild($discountNode);
        }
    }

    protected function xmlApplicableTradeTax($percent, $subtotal, $category = 'S')
    {
        $node = $this->doc->createElement('ram:ApplicableTradeTax');
        $node->appendChild($this->currencyElement('ram:CalculatedAmount', $subtotal * $percent / 100));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));
        // For NLCIUS CII 1.0.3.9 : Fatal [BR-O-10]-A VAT Breakdown (BG-23) with VAT Category code (BT-118) " Not subject to VAT" shall have a VAT exemption reason code (BT-121), meaning " Not subject to VAT" or a VAT exemption reason text (BT-120) " Not subject to VAT" (or the equivalent standard text in another language).
        if ($category == 'O' && ! empty($ExemptionReason = @$this->options['ExemptionReason'])) {
            $node->appendChild($this->doc->createElement('ram:ExemptionReason', $ExemptionReason));
        }

        $node->appendChild($this->currencyElement('ram:BasisAmount', $subtotal));
        $node->appendChild($this->doc->createElement('ram:CategoryCode', $category));

        if ($category == 'S') {
            $node->appendChild($this->currencyElement('ram:RateApplicablePercent', $percent));
        } elseif (empty($this->options['NoReasonCode'])) {
            // For auto entreprises not subject to VAT (Catégory 'O') see https://github.com/ConnectingEurope/eInvoicing-EN16931/blob/master/ubl/schematron/codelist/EN16931-UBL-codes.sch#L133
            // Not For NLCIUS CII 1.0.3.9 : Warning for [BR-NL-35] ram:ExemptionReasonCode is not recommended
            $node->appendChild($this->doc->createElement('ram:ExemptionReasonCode', 'VATEX-EU-O'));
            // vatex-eu-132-1a
        }

        return $node;
    }

    /**
     * @param string $name
     * @param number $amount
     * @param bool   $add_code
     *
     * return node
     */
    protected function currencyElement($name, $amount, $add_code = false)
    {
        $el = $this->doc->createElement($name, $this->formattedFloat($amount, $this->decimal_places));
        if ($add_code) {
            $el->setAttribute('currencyID', $this->currencyCode);
        }

        return $el;
    }

    // ===========================================================================
    // elements helpers
    // ===========================================================================

    protected function xmlSpecifiedTradeSettlementPaymentMeans()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradeSettlementPaymentMeans');

        $node->appendChild($this->doc->createElement('ram:TypeCode', '30'));

        // PayeePartyCreditorFinancialAccount
        $payeeNode = $this->doc->createElement('ram:PayeePartyCreditorFinancialAccount');
        $payeeNode->appendChild($this->doc->createElement('ram:IBANID', $this->invoice->user_iban));
        // LOC BANK ACCOUNT (Document should not contain empty elements.)
        if ($this->invoice->user_bank) {
            $payeeNode->appendChild($this->doc->createElement('ram:ProprietaryID', $this->invoice->user_bank));
        }

        $node->appendChild($payeeNode);

        return $node;
    }

    protected function xmlSpecifiedTradeSettlementHeaderMonetarySummation()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradeSettlementHeaderMonetarySummation');

        // LineTotalAmount (sum of net line amounts) Represents the total amount of the invoice lines before charges, discounts and taxes.
        $node->appendChild($this->currencyElement('ram:LineTotalAmount', $this->invoice->invoice_item_subtotal + $this->invoice->invoice_discount_amount_subtotal));

        // ChargeTotalAmount (total charges at document level) Indicates the total amount of additional charges applied to the invoice.
        // $node->appendChild($this->currencyElement('ram:ChargeTotalAmount', 0)); // optional (insurance, ...)

        // AllowanceTotalAmount (total discounts ‘at document level’) Represents the total amount of discounts granted on the invoice. (see set_invoice_discount_amount_total() helper)
        $node->appendChild($this->currencyElement('ram:AllowanceTotalAmount', $this->invoice->invoice_discount_amount_subtotal)); // optional

        $invoiceTotal = $this->invoice->invoice_total; // ApplicableTradeTax>CategoryCode=O
        if (! $this->notax) {
            $invoiceTotal = $this->invoice->invoice_item_subtotal;
        }

        // TaxBasisTotalAmount (total amount excluding VAT)
        $node->appendChild($this->currencyElement('ram:TaxBasisTotalAmount', $invoiceTotal)); // ApplicableTradeTax>CategoryCode= O || S FIX
        $node->appendChild($this->currencyElement('ram:TaxTotalAmount', $this->invoice->invoice_item_tax_total, true));
        $node->appendChild($this->currencyElement('ram:GrandTotalAmount', $this->invoice->invoice_total));
        $node->appendChild($this->currencyElement('ram:TotalPrepaidAmount', $this->invoice->invoice_paid));
        $node->appendChild($this->currencyElement('ram:DuePayableAmount', $this->invoice->invoice_balance));
        return $node;
    }

    // ===========================================================================
    // helpers
    // ===========================================================================

    protected function xmlIncludedSupplyChainTradeLineItem($lineNumber, $item)
    {
        $node = $this->doc->createElement('ram:IncludedSupplyChainTradeLineItem');

        // AssociatedDocumentLineDocument
        $lineNode = $this->doc->createElement('ram:AssociatedDocumentLineDocument');
        $lineNode->appendChild($this->doc->createElement('ram:LineID', $lineNumber));

        $node->appendChild($lineNode);

        // SpecifiedTradeProduct
        $tradeNode = $this->doc->createElement('ram:SpecifiedTradeProduct');
        $itemdesc = $item->item_description ? "\n" . htmlsc($item->item_description) : '';
        $tradeNode->appendChild($this->doc->createElement('ram:Name', htmlsc($item->item_name) . $itemdesc));
        $node->appendChild($tradeNode);

        // SpecifiedLineTradeAgreement
        $node->appendChild($this->xmlSpecifiedLineTradeAgreement($item));

        // SpecifiedLineTradeDelivery
        $deliveyNode = $this->doc->createElement('ram:SpecifiedLineTradeDelivery');
        $deliveyNode->appendChild($this->quantityElement('ram:BilledQuantity', $item->item_quantity));

        $node->appendChild($deliveyNode);

        // SpecifiedLineTradeSettlement
        $node->appendChild($this->xmlSpecifiedLineTradeSettlement($item));

        return $node;
    }

    protected function xmlSpecifiedLineTradeAgreement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedLineTradeAgreement');

        // GrossPriceProductTradePrice
        $grossPriceNode = $this->doc->createElement('ram:GrossPriceProductTradePrice');
        $grossPriceNode->appendChild($this->currencyElement('ram:ChargeAmount', $item->item_price));

        $node->appendChild($grossPriceNode);

        if ($item->item_discount != 0) {
            // AppliedTradeAllowanceCharge
            $discountNode = $this->doc->createElement('ram:AppliedTradeAllowanceCharge');

            $indicatorNode = $this->doc->createElement('ram:ChargeIndicator'); // ChargeIndicator
            // false indicates that this is a discount
            $indicatorNode->appendChild($this->doc->createElement('udt:Indicator', 'false'));

            $discountNode->appendChild($indicatorNode);
            // Represents the amount of the discount
            $discountNode->appendChild($this->currencyElement('ram:ActualAmount', $item->item_discount_amount));

            $grossPriceNode->appendChild($discountNode);
        }

        $price = $item->item_price;

        // XRechnung-CII-validation
        if (! empty($this->options['CII'])) {
            // Item net price MUST equal (Gross price - Allowance amount) when gross price is provided.
            $price -= $item->item_discount_amount;
        }

        // NetPriceProductTradePrice
        $netPriceNode = $this->doc->createElement('ram:NetPriceProductTradePrice');
        $netPriceNode->appendChild($this->currencyElement('ram:ChargeAmount', $price));

        $node->appendChild($netPriceNode);

        return $node;
    }

    /**
     * @param string $name
     * @param mixed  $quantity
     */
    protected function quantityElement($name, $quantity)
    {
        $el = $this->doc->createElement($name, $this->formattedFloat($quantity, $this->item_decimals));
        $el->setAttribute('unitCode', 'C62');
        return $el;
    }

    protected function xmlSpecifiedLineTradeSettlement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedLineTradeSettlement');

        // ApplicableTradeTax
        $taxNode = $this->doc->createElement('ram:ApplicableTradeTax');
        $taxNode->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));

        if ($item->item_tax_rate_percent != 0) {
            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', 'S'));
            $taxNode->appendChild($this->doc->createElement('ram:RateApplicablePercent', $item->item_tax_rate_percent));
        } else {
            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', 'O'));
        }

        $node->appendChild($taxNode);

        // SpecifiedTradeSettlementLineMonetarySummation
        $sumNode = $this->doc->createElement('ram:SpecifiedTradeSettlementLineMonetarySummation');
        // ApplicableTradeTax>CategoryCode=O OR S
        $sumNode->appendChild($this->currencyElement('ram:LineTotalAmount', $item->item_subtotal - $item->item_discount));

        $node->appendChild($sumNode);

        return $node;
    }
}
