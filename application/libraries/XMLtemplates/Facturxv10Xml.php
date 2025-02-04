<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 * @Note        Zugferd 2.3 & Factur-X 1.0.7 compatibility
 *
 * @Important   Need TAXES_AFTER_DISCOUNTS=false (In ip_config)
 *               For embeded xml are valid (discounts calculation)
 *
 * @todos?
 *  settings('default_item_decimals')
 *  settings('tax_rate_decimal_places')
 *  Like in (and/or calls) currencyElement, facturxFormatedFloat functions
 */

/**
 * Class Facturxv10Xml
 */
class Facturxv10Xml
{
    public $invoice;
    public $items;
    public $doc;
    public $filename;
    public $currencyCode;
    public $root;
    public $notax;
    public $itemsSubtotalGroupedByTaxPercent = [];

    public function __construct($params)
    {
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->filename = $params['filename'];
        $this->currencyCode = get_setting('currency_code');
        $this->set_invoice_discount_amount_total();
        $this->setItemsSubtotalGroupedByTaxPercent();
        $this->notax = empty($this->itemsSubtotalGroupedByTaxPercent);
    }

    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;

        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlExchangedDocumentContext());
        $this->root->appendChild($this->xmlExchangedDocument());
        $this->root->appendChild($this->xmlSupplyChainTradeTransaction());

        $this->doc->appendChild($this->root);
        $this->doc->save(UPLOADS_FOLDER . 'temp/' . $this->filename . '.xml');
        // return $this->doc->saveXML();
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('rsm:CrossIndustryInvoice');
        $node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $node->setAttribute('xmlns:rsm', 'urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100');
        $node->setAttribute('xmlns:ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100');
        $node->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100');
        return $node;
    }

    protected function xmlExchangedDocumentContext()
    {
        $node = $this->doc->createElement('rsm:ExchangedDocumentContext');
        $guidelineNode = $this->doc->createElement('ram:GuidelineSpecifiedDocumentContextParameter');
        // urn:cen.eu:en16931:2017#compliant#urn:(zugferd:2.3 | factur-x.eu):1p0:(basic | en16931) : en16931 = COMFORT (profil)
        // urn:cen.eu:en16931:2017#conformant#urn:(zugferd:2.3 | factur-x.eu):1p0:extended
        $guidelineNode->appendChild($this->doc->createElement('ram:ID', 'urn:cen.eu:en16931:2017')); // KISS
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

        // IncludedNote
        if($this->invoice->invoice_terms)
        {
            $noteNode = $this->doc->createElement('ram:IncludedNote');
            $noteNode->appendChild($this->doc->createElement('ram:Content', htmlsc($this->invoice->invoice_terms)));
            $node->appendChild($noteNode);
        }

        //~ $node->appendChild($this->doc->createElement('ram:Name', trans('invoice'))); // not expected (here) todo? Where?

        return $node;
    }

    protected function dateElement($date)
    {
        $el = $this->doc->createElement('udt:DateTimeString', $this->facturxFormattedDate($date));
        $el->setAttribute('format', 102);
        return $el;
    }

    /**
     * @return string|null
     */
    function facturxFormattedDate($date)
    {
        if ($date)
        {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format('Ymd');
        }
        return '';
    }

    protected function xmlSupplyChainTradeTransaction()
    {
        $node = $this->doc->createElement('rsm:SupplyChainTradeTransaction');

        foreach ($this->items as $index => $item)
        {
            $node->appendChild($this->xmlIncludedSupplyChainTradeLineItem($index + 1, $item));
        }

        $node->appendChild($this->xmlApplicableHeaderTradeAgreement());
        $node->appendChild($this->xmlApplicableHeaderTradeDelivery());
        $node->appendChild($this->xmlApplicableHeaderTradeSettlement());
        return $node;
    }

    protected function xmlSpecifiedTradePaymentTerms()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradePaymentTerms');
        // todo: improve? (Like: Payment within 30 days) invoice_date_due / get_settings('invoices_due_after')
        $node->appendChild($this->doc->createElement('ram:Description', $this->invoice->invoice_terms));
        return $node;
    }

    protected function xmlApplicableHeaderTradeAgreement()
    {
        $node = $this->doc->createElement('ram:ApplicableHeaderTradeAgreement');
        $node->appendChild($this->xmlSellerTradeParty());
        $node->appendChild($this->xmlBuyerTradeParty());
        return $node;
    }

    protected function xmlSellerTradeParty()
    {
        $node = $this->doc->createElement('ram:SellerTradeParty');
        $node->appendChild($this->doc->createElement('ram:ID', $this->invoice->user_id)); // zugferd 2 : SELLER123
        $node->appendChild($this->doc->createElement('ram:Name', htmlsc($this->invoice->user_name)));

        // PostalTradeAddress
        $addressNode = $this->doc->createElement('ram:PostalTradeAddress');
        $addressNode->appendChild($this->doc->createElement('ram:PostcodeCode', htmlsc($this->invoice->user_zip)));
        $addressNode->appendChild($this->doc->createElement('ram:LineOne', htmlsc($this->invoice->user_address_1)));
        $addressNode->appendChild($this->doc->createElement('ram:LineTwo', htmlsc($this->invoice->user_address_2)));
        $addressNode->appendChild($this->doc->createElement('ram:CityName', htmlsc($this->invoice->user_city)));
        $addressNode->appendChild($this->doc->createElement('ram:CountryID', htmlsc($this->invoice->user_country)));
        $node->appendChild($addressNode);

        // SpecifiedTaxRegistration
        if( ! $this->notax)
        {
            $node->appendChild($this->xmlSpecifiedTaxRegistration('VA', $this->invoice->user_vat_id)); // zugferd 2
        }

        return $node;
    }

    protected function xmlBuyerTradeParty()
    {
        $node = $this->doc->createElement('ram:BuyerTradeParty');
        $node->appendChild($this->doc->createElement('ram:Name', htmlsc($this->invoice->client_name)));

        // PostalTradeAddress
        $addressNode = $this->doc->createElement('ram:PostalTradeAddress');
        $addressNode->appendChild($this->doc->createElement('ram:PostcodeCode', htmlsc($this->invoice->client_zip)));
        $addressNode->appendChild($this->doc->createElement('ram:LineOne', htmlsc($this->invoice->client_address_1)));
        $addressNode->appendChild($this->doc->createElement('ram:LineTwo', htmlsc($this->invoice->client_address_2)));
        $addressNode->appendChild($this->doc->createElement('ram:CityName', htmlsc($this->invoice->client_city)));
        $addressNode->appendChild($this->doc->createElement('ram:CountryID', htmlsc($this->invoice->client_country)));
        $node->appendChild($addressNode);

        // SpecifiedTaxRegistration
        if( ! $this->notax)
        {
            $node->appendChild($this->xmlSpecifiedTaxRegistration('VA', $this->invoice->client_vat_id)); // zugferd 2
        }

        return $node;
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
        if( ! $this->notax) // #wip: like if have discounts (how to find item(s) with amount > of amount discount to get/dispatch % of VAT
        {
            foreach ($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal)
            {
                $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal[0])); // 'S' by default
            }
        }
        else // Not subject to VAT
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

        if($this->invoice->invoice_discount_amount_total > 0)
        {
            // Si remise globale (ram:AppliedTradeAllowanceCharge)
            // Doit être après BillingSpecifiedPeriod et avant SpecifiedTradePaymentTerms !important
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
    protected function addSpecifiedTradeAllowanceCharge_discount( & $node)
    {
        // note: If empty itemsSubtotalGroupedByTaxPercent ($this->notax) Only one `SpecifiedTradeAllowanceCharge` ;)
        if($this->invoice->invoice_discount_amount_total > 0 && $this->itemsSubtotalGroupedByTaxPercent)
        {
            $category = 'S';
            $amounts = [];
            // Loop on itemsSubtotalGroupedByTaxPercent to dispatch discount by VAT's rate
            foreach($this->itemsSubtotalGroupedByTaxPercent as $percent => $subtotal)
            {
                // Don't divide per 0
                if($this->invoice->invoice_subtotal != 0)
                {
                    // from set_invoice_discount_amount_total (helper)
                    $amounts[$percent] = ($subtotal[1] / $this->invoice->invoice_subtotal) * $this->invoice->invoice_discount_amount_subtotal;
                }
            }
        }
        else
        {
            $category = 'O';
            // from set_invoice_discount_amount_total (helper)
            $amounts[0] = $this->invoice->invoice_discount_amount_subtotal;
        }

        // Loop on VAT to dispatch global discount
        foreach($amounts as $percent => $amount)
        {
            $discountNode = $this->doc->createElement('ram:SpecifiedTradeAllowanceCharge');
            // ChargeIndicator
            $indicatorNode = $this->doc->createElement('ram:ChargeIndicator');
            $indicatorNode->appendChild($this->doc->createElement('udt:Indicator', 'false')); // false it's a discount
            $discountNode->appendChild($indicatorNode);

            $discountNode->appendChild($this->currencyElement('ram:ActualAmount', $amount)); // of discount of vat rate
            $discountNode->appendChild($this->doc->createElement('ram:ReasonCode', '95'));
            $discountNode->appendChild($this->doc->createElement('ram:Reason', rtrim(trans('discount'), ' '))); // todo curious chars ' ' not a space (found in French ip_lang)

            $taxNode = $this->doc->createElement('ram:CategoryTradeTax');
            $taxNode->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));

            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', $category)); // S or O
            if($category == 'S')
            {
                $taxNode->appendChild($this->doc->createElement('ram:RateApplicablePercent', $percent)); // of VAT rate
            }

            $discountNode->appendChild($taxNode);

            $node->appendChild($discountNode);
        }
    }

    /*
     * Add missing discount invoice vars [for factur-x validation](ecosio.com/en/peppol-and-xml-document-validator)
     *
     * invoice_subtotal                    Scope TaxBasisTotalAmount
     * invoice_discount_amount_total       Scope AllowanceTotalAmount (todo: it's used)
     * invoice_discount_amount_subtotal    Scope SpecifiedTradeAllowanceCharge > ActualAmount>
     */
    protected function set_invoice_discount_amount_total()
    {
        $item_discount = $item_subtotal = $discount = 0.0;

        foreach ($this->items as $item)
        {
            $item_discount += $item->item_discount;
            $item_subtotal += $item->item_subtotal;
        }

        if($this->invoice->invoice_discount_amount > 0)
        {
            $discount = $this->invoice->invoice_discount_amount;
        }
        elseif($this->invoice->invoice_discount_percent > 0)
        {
            $discount = $item_subtotal * ($this->invoice->invoice_discount_percent / 100);
        }
        $this->invoice->invoice_subtotal = $this->facturxFormattedFloat($item_subtotal);
        $this->invoice->invoice_discount_amount_total = $this->facturxFormattedFloat($item_discount + $discount);
        $this->invoice->invoice_discount_amount_subtotal = $this->facturxFormattedFloat($discount);
     }

    protected function setItemsSubtotalGroupedByTaxPercent()
    {
        $result = [];
        foreach ($this->items as $item)
        {
            if ($item->item_tax_rate_percent == 0)
            {
                continue;
            }

            if ( ! isset($result[$item->item_tax_rate_percent]))
            {
                $result[$item->item_tax_rate_percent] = [0, 0];
            }

            $result[$item->item_tax_rate_percent] =
            [
                $result[$item->item_tax_rate_percent][0] += ($item->item_total - $item->item_tax_total),
                $result[$item->item_tax_rate_percent][1] += $item->item_subtotal, // without discounts
            ];

        }
        $this->itemsSubtotalGroupedByTaxPercent = $result; // help to dispatch invoice global discount tax rate + same for vat's of invoices
    }

    protected function xmlApplicableTradeTax($percent, $subtotal, $category = 'S')
    {
        $node = $this->doc->createElement('ram:ApplicableTradeTax');
        $node->appendChild($this->currencyElement('ram:CalculatedAmount', $subtotal * $percent / 100));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));
        $node->appendChild($this->currencyElement('ram:BasisAmount', $subtotal));
        $node->appendChild($this->doc->createElement('ram:CategoryCode', $category));

        if ($category == 'S')
        {
            $node->appendChild($this->doc->createElement('ram:RateApplicablePercent', $percent));
        }
        else // Pour les auto-entrepreneurs non assujettis à la TVA (Catégorie 'O')
        {
            $node->appendChild($this->doc->createElement('ram:ExemptionReasonCode', 'VATEX-EU-O')); //see https://github.com/ConnectingEurope/eInvoicing-EN16931/blob/master/ubl/schematron/codelist/EN16931-UBL-codes.sch#L133
        }

        return $node;
    }

    /**
     * @param string $name
     * @param number $amount
     * @param int    $nb_decimals
     * @param bool   $add_code
     *
     * return node
     */
    protected function currencyElement($name, $amount, $nb_decimals = 2, $add_code = false)
    {
        $el = $this->doc->createElement($name, $this->facturxFormattedFloat($amount, $nb_decimals));
        if($add_code)
        {
            $el->setAttribute('currencyID', $this->currencyCode);
        }
        return $el;
    }

    // ===========================================================================
    // elements helpers
    // ===========================================================================

    function facturxFormattedFloat($amount, $nb_decimals = 2)
    {
        return number_format((float)$amount, $nb_decimals);
    }

    protected function xmlSpecifiedTradeSettlementPaymentMeans()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradeSettlementPaymentMeans');

        $node->appendChild($this->doc->createElement('ram:TypeCode', '30'));

        // PayeePartyCreditorFinancialAccount
        $payeeNode = $this->doc->createElement('ram:PayeePartyCreditorFinancialAccount');
        $payeeNode->appendChild($this->doc->createElement('ram:IBANID', $this->invoice->user_iban));
        $payeeNode->appendChild($this->doc->createElement('ram:ProprietaryID', $this->invoice->user_bank)); // LOC BANK ACCOUNT

        $node->appendChild($payeeNode);

        return $node;
    }

    protected function xmlSpecifiedTradeSettlementHeaderMonetarySummation()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradeSettlementHeaderMonetarySummation');

        // LineTotalAmount (somme des montants nets des lignes) Représente le montant total des lignes de la facture avant les frais, remises et taxes.
        $node->appendChild($this->currencyElement('ram:LineTotalAmount', $this->invoice->invoice_item_subtotal + $this->invoice->invoice_discount_amount_subtotal));

        // ChargeTotalAmount (total des frais au niveau du document) Indique le montant total des frais supplémentaires appliqués à la facture.
        //~ $node->appendChild($this->currencyElement('ram:ChargeTotalAmount', 0)); // optional (todo? from db: maybe global taxes)

        // AllowanceTotalAmount (total des remises 'au niveau du document') Représente le montant total des remises accordées sur la facture. (voir set_invoice_discount_amount_total() helper)
        $node->appendChild($this->currencyElement('ram:AllowanceTotalAmount', $this->invoice->invoice_discount_amount_subtotal)); // optional

        $invoiceTotal = $this->invoice->invoice_total; // ApplicableTradeTax>CategoryCode=O
        if( ! $this->notax)
        {
            $invoiceTotal = $this->invoice->invoice_item_subtotal;
        }
        // TaxBasisTotalAmount (montant total hors TVA)
        $node->appendChild($this->currencyElement('ram:TaxBasisTotalAmount', $invoiceTotal)); // ApplicableTradeTax>CategoryCode= O || S FIX
        $node->appendChild($this->currencyElement('ram:TaxTotalAmount', $this->invoice->invoice_item_tax_total, 2, true));
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

        if($item->item_discount > 0)
        {
            // AppliedTradeAllowanceCharge
            $discountNode = $this->doc->createElement('ram:AppliedTradeAllowanceCharge');

            $indicatorNode = $this->doc->createElement('ram:ChargeIndicator'); // ChargeIndicator
            $indicatorNode->appendChild($this->doc->createElement('udt:Indicator', 'false')); // false indique qu'il s'agit d'une remise

            $discountNode->appendChild($indicatorNode);
            $discountNode->appendChild($this->currencyElement('ram:ActualAmount', $item->item_discount_amount)); // Représente le montant de la remise

            $grossPriceNode->appendChild($discountNode);
        }

        // NetPriceProductTradePrice
        $netPriceNode = $this->doc->createElement('ram:NetPriceProductTradePrice');
        $netPriceNode->appendChild($this->currencyElement('ram:ChargeAmount', $item->item_price));
        $node->appendChild($netPriceNode);

        return $node;
    }

    /**
     * @param string $name
     */
    protected function quantityElement($name, $quantity)
    {
        $el = $this->doc->createElement($name, $this->facturxFormattedFloat($quantity, 4));
        $el->setAttribute('unitCode', 'C62');
        return $el;
    }

    protected function xmlSpecifiedLineTradeSettlement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedLineTradeSettlement');

        // ApplicableTradeTax
        $taxNode = $this->doc->createElement('ram:ApplicableTradeTax');
        $taxNode->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));

        if ($item->item_tax_rate_percent > 0)
        {
            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', 'S')); // todo from db?
            $taxNode->appendChild($this->doc->createElement('ram:RateApplicablePercent', $item->item_tax_rate_percent));
        }
        else
        {
            $taxNode->appendChild($this->doc->createElement('ram:CategoryCode', 'O')); // todo from db?
        }
        $node->appendChild($taxNode);

        // SpecifiedTradeSettlementLineMonetarySummation
        $sumNode = $this->doc->createElement('ram:SpecifiedTradeSettlementLineMonetarySummation');
        $sumNode->appendChild($this->currencyElement('ram:LineTotalAmount', $item->item_subtotal - $item->item_discount)); // ApplicableTradeTax>CategoryCode=O OR S FIX²
        $node->appendChild($sumNode);

        return $node;
    }
}
