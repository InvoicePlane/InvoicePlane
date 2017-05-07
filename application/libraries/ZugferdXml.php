<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class ZugferdXml
 */
class ZugferdXml
{
    var $invoice;
    var $doc;
    var $root;

    public function __construct($params)
    {
        $CI = &get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->currencyCode = $CI->mdl_settings->setting('currency_code');
    }

    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;

        $this->root = $this->xmlRoot();
        $this->root->appendChild($this->xmlSpecifiedExchangedDocumentContext());
        $this->root->appendChild($this->xmlHeaderExchangedDocument());
        $this->root->appendChild($this->xmlSpecifiedSupplyChainTradeTransaction());

        $this->doc->appendChild($this->root);
        return $this->doc->saveXML();
    }

    protected function xmlRoot()
    {
        $node = $this->doc->createElement('rsm:CrossIndustryDocument');
        $node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $node->setAttribute('xmlns:rsm', 'urn:ferd:CrossIndustryDocument:invoice:1p0');
        $node->setAttribute('xmlns:ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12');
        $node->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:standard:UnqualifiedDataType:15');
        return $node;
    }

    protected function xmlSpecifiedExchangedDocumentContext()
    {
        $node = $this->doc->createElement('rsm:SpecifiedExchangedDocumentContext');
        $guidelineNode = $this->doc->createElement('ram:GuidelineSpecifiedDocumentContextParameter');
        $guidelineNode->appendChild($this->doc->createElement('ram:ID', 'urn:ferd:CrossIndustryDocument:invoice:1p0:basic'));
        $node->appendChild($guidelineNode);
        return $node;
    }

    protected function xmlHeaderExchangedDocument()
    {
        $node = $this->doc->createElement('rsm:HeaderExchangedDocument');

        $node->appendChild($this->doc->createElement('ram:ID', $this->invoice->invoice_number));
        $node->appendChild($this->doc->createElement('ram:Name', trans('invoice')));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 380));

        // IssueDateTime
        $dateNode = $this->doc->createElement('ram:IssueDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_created));
        $node->appendChild($dateNode);

        // IncludedNote
        $noteNode = $this->doc->createElement('ram:IncludedNote');
        $noteNode->appendChild($this->doc->createElement('ram:Content', htmlsc($this->invoice->invoice_terms)));
        $node->appendChild($noteNode);

        return $node;
    }

    protected function dateElement($date)
    {
        $el = $this->doc->createElement('udt:DateTimeString', $this->zugferdFormattedDate($date));
        $el->setAttribute('format', 102);
        return $el;
    }

    /**
     * @return string|null
     */
    function zugferdFormattedDate($date)
    {
        if ($date && $date <> '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format('Ymd');
        }
        return '';
    }

    protected function xmlSpecifiedSupplyChainTradeTransaction()
    {
        $node = $this->doc->createElement('rsm:SpecifiedSupplyChainTradeTransaction');
        $node->appendChild($this->xmlApplicableSupplyChainTradeAgreement());
        $node->appendChild($this->xmlApplicableSupplyChainTradeDelivery());
        $node->appendChild($this->xmlApplicableSupplyChainTradeSettlement());
        foreach ($this->items as $index => $item) {
            $node->appendChild($this->xmlIncludedSupplyChainTradeLineItem($index + 1, $item));
        }
        return $node;
    }

    protected function xmlApplicableSupplyChainTradeAgreement()
    {
        $node = $this->doc->createElement('ram:ApplicableSupplyChainTradeAgreement');
        $node->appendChild($this->xmlSellerTradeParty());
        $node->appendChild($this->xmlBuyerTradeParty());
        return $node;
    }

    protected function xmlSellerTradeParty($index = '', $item = '')
    {
        $node = $this->doc->createElement('ram:SellerTradeParty');
        $node->appendChild($this->doc->createElement('ram:Name', htmlsc($this->invoice->user_name)));

        // PostalTradeAddress
        $addressNode = $this->doc->createElement('ram:PostalTradeAddress');
        $addressNode->appendChild($this->doc->createElement('ram:PostcodeCode', htmlsc($this->invoice->user_zip)));
        $addressNode->appendChild($this->doc->createElement('ram:LineOne', htmlsc($this->invoice->user_address_1)));
        $addressNode->appendChild($this->doc->createElement('ram:LineTwo', htmlsc($this->invoice->user_address_2)));
        $addressNode->appendChild($this->doc->createElement('ram:CityName', htmlsc($this->invoice->user_city)));
        $addressNode->appendChild($this->doc->createElement('ram:CountryID', htmlsc($this->invoice->user_country)));

        $node->appendChild($addressNode);
        return $node;
    }

    protected function xmlBuyerTradeParty($index = '', $item = '')
    {
        $node = $this->doc->createElement('ram:BuyerTradeParty');
        $node->appendChild($this->doc->createElement('ram:Name', $this->invoice->client_name));

        // PostalTradeAddress
        $addressNode = $this->doc->createElement('ram:PostalTradeAddress');
        $addressNode->appendChild($this->doc->createElement('ram:PostcodeCode', htmlsc($this->invoice->client_zip)));
        $addressNode->appendChild($this->doc->createElement('ram:LineOne', htmlsc($this->invoice->client_address_1)));
        $addressNode->appendChild($this->doc->createElement('ram:LineTwo', htmlsc($this->invoice->client_address_2)));
        $addressNode->appendChild($this->doc->createElement('ram:CityName', htmlsc($this->invoice->client_city)));
        $addressNode->appendChild($this->doc->createElement('ram:CountryID', htmlsc($this->invoice->client_country)));
        $node->appendChild($addressNode);

        // SpecifiedTaxRegistration
        $node->appendChild($this->xmlSpecifiedTaxRegistration('VA', $this->invoice->client_vat_id));
        $node->appendChild($this->xmlSpecifiedTaxRegistration('FC', htmlsc($this->invoice->client_tax_code)));

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

    protected function xmlApplicableSupplyChainTradeDelivery()
    {
        $node = $this->doc->createElement('ram:ApplicableSupplyChainTradeDelivery');

        // ActualDeliverySupplyChainEvent
        $eventNode = $this->doc->createElement('ram:ActualDeliverySupplyChainEvent');
        $dateNode = $this->doc->createElement('ram:OccurrenceDateTime');
        $dateNode->appendChild($this->dateElement($this->invoice->invoice_date_created));
        $eventNode->appendChild($dateNode);

        $node->appendChild($eventNode);
        return $node;
    }

    protected function xmlApplicableSupplyChainTradeSettlement()
    {
        $node = $this->doc->createElement('ram:ApplicableSupplyChainTradeSettlement');

        $node->appendChild($this->doc->createElement('ram:PaymentReference', $this->invoice->invoice_number));
        $node->appendChild($this->doc->createElement('ram:InvoiceCurrencyCode', $this->currencyCode));

        // taxes
        foreach ($this->itemsSubtotalGroupedByTaxPercent() as $percent => $subtotal) {
            $node->appendChild($this->xmlApplicableTradeTax($percent, $subtotal));
        }

        // sums
        $node->appendChild($this->xmlSpecifiedTradeSettlementMonetarySummation());

        return $node;
    }

    function itemsSubtotalGroupedByTaxPercent()
    {
        $result = [];
        foreach ($this->items as $item) {
            if ($item->item_tax_rate_percent == 0) {
                continue;
            }

            if (!isset($result[$item->item_tax_rate_percent])) {
                $result[$item->item_tax_rate_percent] = 0;
            }
            $result[$item->item_tax_rate_percent] += $item->item_subtotal;
        }
        return $result;
    }

    protected function xmlApplicableTradeTax($percent, $subtotal)
    {
        $node = $this->doc->createElement('ram:ApplicableTradeTax');
        $node->appendChild($this->currencyElement('ram:CalculatedAmount', $subtotal * $percent / 100));
        $node->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));
        $node->appendChild($this->currencyElement('ram:BasisAmount', $subtotal));
        $node->appendChild($this->doc->createElement('ram:CategoryCode', 'S'));
        $node->appendChild($this->doc->createElement('ram:ApplicablePercent', $percent));
        return $node;
    }

    /**
     * @param string $name
     */
    protected function currencyElement($name, $amount, $nb_decimals = 2)
    {
        $el = $this->doc->createElement($name, $this->zugferdFormattedFloat($amount, $nb_decimals));
        $el->setAttribute('currencyID', $this->currencyCode);
        return $el;
    }

    // ===========================================================================
    // elements helpers
    // ===========================================================================

    function zugferdFormattedFloat($amount, $nb_decimals = 2)
    {
        return number_format((float)$amount, $nb_decimals);
    }

    protected function xmlSpecifiedTradeSettlementMonetarySummation()
    {
        $node = $this->doc->createElement('ram:SpecifiedTradeSettlementMonetarySummation');
        $node->appendChild($this->currencyElement('ram:LineTotalAmount', $this->invoice->invoice_item_subtotal));
        $node->appendChild($this->currencyElement('ram:ChargeTotalAmount', 0));
        $node->appendChild($this->currencyElement('ram:AllowanceTotalAmount', 0));
        $node->appendChild($this->currencyElement('ram:TaxBasisTotalAmount', $this->invoice->invoice_item_subtotal));
        $node->appendChild($this->currencyElement('ram:TaxTotalAmount', $this->invoice->invoice_item_tax_total));
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

        // SpecifiedSupplyChainTradeAgreement
        $node->appendChild($this->xmlSpecifiedSupplyChainTradeAgreement($item));

        // SpecifiedSupplyChainTradeDelivery
        $deliveyNode = $this->doc->createElement('ram:SpecifiedSupplyChainTradeDelivery');
        $deliveyNode->appendChild($this->quantityElement('ram:BilledQuantity', $item->item_quantity));
        $node->appendChild($deliveyNode);

        // SpecifiedSupplyChainTradeSettlement
        $node->appendChild($this->xmlSpecifiedSupplyChainTradeSettlement($item));

        // SpecifiedTradeProduct
        $tradeNode = $this->doc->createElement('ram:SpecifiedTradeProduct');
        $tradeNode->appendChild($this->doc->createElement('ram:Name', htmlsc($item->item_name) . "\n" . htmlsc($item->item_description)));
        $node->appendChild($tradeNode);

        return $node;
    }

    // ===========================================================================
    // helpers
    // ===========================================================================

    protected function xmlSpecifiedSupplyChainTradeAgreement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedSupplyChainTradeAgreement');

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
        $el = $this->doc->createElement($name, $this->zugferdFormattedFloat($quantity, 4));
        $el->setAttribute('unitCode', 'C62');
        return $el;
    }

    protected function xmlSpecifiedSupplyChainTradeSettlement($item)
    {
        $node = $this->doc->createElement('ram:SpecifiedSupplyChainTradeSettlement');

        // ApplicableTradeTax
        if ($item->item_tax_rate_percent > 0) {
            $taxNode = $this->doc->createElement('ram:ApplicableTradeTax');
            $taxNode->appendChild($this->doc->createElement('ram:TypeCode', 'VAT'));
            $taxNode->appendChild($this->doc->createElement('ram:ApplicablePercent', $item->item_tax_rate_percent));
            $node->appendChild($taxNode);
        }

        // SpecifiedTradeSettlementMonetarySummation
        $sumNode = $this->doc->createElement('ram:SpecifiedTradeSettlementMonetarySummation');
        $sumNode->appendChild($this->currencyElement('ram:LineTotalAmount', $item->item_subtotal));
        $node->appendChild($sumNode);

        return $node;
    }
}
