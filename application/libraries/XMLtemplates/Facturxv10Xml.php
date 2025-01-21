<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 * @Note        Zugferd 2.3 & Factur-X 1.0.7 compatible
 */

/**
 * Class Facturxv10Xml
 */
class Facturxv10Xml
{
    public $invoice;
    public $doc;
    public $root;

    public function __construct($params)
    {
        $CI = &get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->filename = $params['filename'];
        $this->currencyCode = get_setting('currency_code'); // $CI->mdl_settings->setting('currency_code');
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

        //~ $node->appendChild($this->doc->createElement('ram:Name', trans('invoice'))); // not expected

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

    protected function xmlSellerTradeParty($index = '', $item = '')
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
        if($this->invoice->invoice_tax_total > 0) // todo? != ApplicableTradeTax>CategoryCode=O
        {
            $schemeID = 'VA';
            $content = $this->invoice->user_vat_id;
            $node->appendChild($this->xmlSpecifiedTaxRegistration($schemeID, $content)); // zugferd 2
        }

        return $node;
    }

    protected function xmlBuyerTradeParty($index = '', $item = '')
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
        if($this->invoice->invoice_tax_total > 0) // todo? != ApplicableTradeTax>CategoryCode=O
        {
            $node->appendChild($this->xmlSpecifiedTaxRegistration('VA', $this->invoice->client_vat_id));
        }

        return $node;
    }

    /**
     * @param string $schemeID
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
        $notax = true;
        foreach ($this->itemsSubtotalGroupedByTaxPercent() as $percent => $subtotal) {
            $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal)); // 'S' by default
            $notax = false;
        }
        if($notax) // Not subject to VAT
        {
            $percent = $this->invoice->invoice_tax_total; // invoice_item_tax_total
            $subtotal = $this->invoice->invoice_total; // invoice_item_subtotal
            $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal, 'O')); //  "O" pour "Exonéré" (Out of scope).
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

        // zugferd 2.3, facturx 1.0.7
        $node->appendChild($this->xmlSpecifiedTradePaymentTerms());

        // sums
        $node->appendChild($this->xmlSpecifiedTradeSettlementHeaderMonetarySummation()); // or PayeeTradeParty
        return $node;
    }

    function itemsSubtotalGroupedByTaxPercent()
    {
        $result = [];
        foreach ($this->items as $item)
        {
            if ($item->item_tax_rate_percent == 0)
            {
                continue;
            }

            if (!isset($result[$item->item_tax_rate_percent]))
            {
                $result[$item->item_tax_rate_percent] = 0;
            }
            $result[$item->item_tax_rate_percent] += $item->item_subtotal;
        }
        return $result;
    }

    protected function xmlApplicableTradeTax($percent, $subtotal, $category = 'S')
    {
        $node = $this->doc->createElement('ram:ApplicableTradeTax');
        $node->appendChild($this->currencyElement('ram:CalculatedAmount', $subtotal * $percent / 100));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));
        $node->appendChild($this->currencyElement('ram:BasisAmount', $subtotal));
        $node->appendChild($this->doc->createElement('ram:CategoryCode', $category));

        if($category == 'S')
        {
            $node->appendChild($this->doc->createElement('ram:RateApplicablePercent', $percent));
        }
        else // Pour les auto-entrepreneurs non assujettis à la TVA
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
        // Représente le montant total des lignes de la facture avant les frais, remises et taxes.
        $node->appendChild($this->currencyElement('ram:LineTotalAmount', $this->invoice->invoice_item_subtotal));
        // Indique le montant total des frais supplémentaires appliqués à la facture.
        $node->appendChild($this->currencyElement('ram:ChargeTotalAmount', 0)); // facultatif (todo from db)
        // Représente le montant total des remises accordées sur la facture.
        $node->appendChild($this->currencyElement('ram:AllowanceTotalAmount', 0)); // facultatif (todo from db)
        $node->appendChild($this->currencyElement('ram:TaxBasisTotalAmount', $this->invoice->invoice_item_subtotal));
        $node->appendChild($this->currencyElement('ram:TaxTotalAmount', $this->invoice->invoice_item_tax_total, 2, true));
        $node->appendChild($this->currencyElement('ram:GrandTotalAmount', $this->invoice->invoice_total));
        $node->appendChild($this->currencyElement('ram:TotalPrepaidAmount', $this->invoice->invoice_paid));
        $node->appendChild($this->currencyElement('ram:DuePayableAmount', $this->invoice->invoice_balance));
        return $node;
    }

    protected function xmlIncludedSupplyChainTradeLineItem($lineNumber, $item)
    {
        $node = $this->doc->createElement('ram:IncludedSupplyChainTradeLineItem');

        // AssociatedDocumentLineDocument
        $lineNode = $this->doc->createElement('ram:AssociatedDocumentLineDocument');
        $lineNode->appendChild($this->doc->createElement('ram:LineID', $lineNumber));
        $node->appendChild($lineNode);

        // SpecifiedTradeProduct
        $tradeNode = $this->doc->createElement('ram:SpecifiedTradeProduct');
        $tradeNode->appendChild($this->doc->createElement('ram:Name', htmlsc($item->item_name) /* . "\n" . htmlsc($item->item_description) */));
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

    // ===========================================================================
    // helpers
    // ===========================================================================

    protected function xmlSpecifiedLineTradeAgreement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedLineTradeAgreement');

        // GrossPriceProductTradePrice
        $grossPriceNode = $this->doc->createElement('ram:GrossPriceProductTradePrice');
        $grossPriceNode->appendChild($this->currencyElement('ram:ChargeAmount', $item->item_price, 4));
        $node->appendChild($grossPriceNode);

        // NetPriceProductTradePrice
        $netPriceNode = $this->doc->createElement('ram:NetPriceProductTradePrice');
        $netPriceNode->appendChild($this->currencyElement('ram:ChargeAmount', $item->item_price, 4));
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

        if ($item->item_tax_rate_percent > 0) // todo? != ApplicableTradeTax>CategoryCode=O
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
        $sumNode->appendChild($this->currencyElement('ram:LineTotalAmount', $item->item_subtotal));
        $node->appendChild($sumNode);

        return $node;
    }
}
