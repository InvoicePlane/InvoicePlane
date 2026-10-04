<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Extracts the common invoice data used by the supplier invoice module.
 *
 * The parser deliberately uses local-name() XPath expressions because UBL and
 * CII documents commonly use different namespace prefixes.
 */
final class SupplierInvoiceDocumentParser
{
    public function parse(string $path): array
    {
        $temporaryPath = null;
        $xmlPath = $path;

        if (str_starts_with((string) file_get_contents($path), '%PDF-')) {
            $temporaryPath = $this->extractFacturX($path);
            $xmlPath = $temporaryPath;
        }

        try {
            $document = new DOMDocument();
            $document->resolveExternals = false;
            $document->substituteEntities = false;
            $previous = libxml_use_internal_errors(true);
            $loaded = $document->load($xmlPath, LIBXML_NONET | LIBXML_NOBLANKS);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if ( ! $loaded || $document->documentElement === null) {
                throw new RuntimeException('The supplier invoice XML could not be read.');
            }

            $xpath = new DOMXPath($document);
            $root = strtolower($document->documentElement->localName ?? '');

            return $root === 'invoice' ? $this->parseUbl($xpath, $document->documentElement) : $this->parseCii($xpath, $document->documentElement);
        } finally {
            if ($temporaryPath !== null && is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    private function parseCii(DOMXPath $xpath, DOMElement $root): array
    {
        if (strtolower($root->localName ?? '') !== 'crossindustryinvoice') {
            throw new RuntimeException('Unsupported supplier invoice XML syntax.');
        }

        $seller = $this->first($xpath, './/*[local-name()="SellerTradeParty"]', $root);
        $settlement = $this->first($xpath, './/*[local-name()="ApplicableHeaderTradeSettlement"]', $root);
        $summary = $this->first($xpath, './/*[local-name()="SpecifiedTradeSettlementHeaderMonetarySummation"]', $root);

        $items = [];
        foreach ($this->nodes($xpath, './/*[local-name()="IncludedSupplyChainTradeLineItem"]', $root) as $line) {
            $items[] = [
                'item_name' => $this->value($xpath, './/*[local-name()="SpecifiedTradeProduct"]/*[local-name()="Name"]', $line) ?: 'Line item',
                'item_description' => $this->value($xpath, './/*[local-name()="SpecifiedTradeProduct"]/*[local-name()="Description"]', $line),
                'quantity' => $this->number($this->value($xpath, './/*[local-name()="BilledQuantity"]', $line), 1),
                'unit_price' => $this->number($this->value($xpath, './/*[local-name()="NetPriceProductTradePrice"]/*[local-name()="ChargeAmount"]', $line)),
                'tax_rate' => $this->number($this->value($xpath, './/*[local-name()="ApplicableTradeTax"]/*[local-name()="RateApplicablePercent"]', $line)),
                'subtotal' => $this->number($this->value($xpath, './/*[local-name()="SpecifiedTradeSettlementLineMonetarySummation"]/*[local-name()="LineTotalAmount"]', $line)),
                'tax_total' => null,
                'total' => $this->number($this->value($xpath, './/*[local-name()="SpecifiedTradeSettlementLineMonetarySummation"]/*[local-name()="LineTotalAmount"]', $line)),
            ];
        }

        return [
            'supplier' => $this->supplierData($xpath, $seller, 'cii'),
            'invoice' => [
                'supplier_invoice_number' => $this->value($xpath, './/*[local-name()="ExchangedDocument"]/*[local-name()="ID"]', $root),
                'supplier_invoice_date' => $this->date($this->value($xpath, './/*[local-name()="ExchangedDocument"]//*[local-name()="IssueDateTime"]//*[local-name()="DateTimeString"]', $root)),
                'supplier_due_date' => $this->date($this->value($xpath, './/*[local-name()="SpecifiedTradePaymentTerms"]//*[local-name()="DateTimeString"]', $settlement)),
                'currency_code' => $this->value($xpath, './/*[local-name()="InvoiceCurrencyCode"]', $settlement) ?: 'EUR',
                'subtotal' => $this->number($this->value($xpath, './/*[local-name()="TaxBasisTotalAmount"]', $summary)),
                'tax_total' => $this->number($this->value($xpath, './/*[local-name()="TaxTotalAmount"]', $summary)),
                'total' => $this->number($this->value($xpath, './/*[local-name()="GrandTotalAmount"]', $summary)),
            ],
            'items' => $items,
        ];
    }

    private function parseUbl(DOMXPath $xpath, DOMElement $root): array
    {
        $seller = $this->first($xpath, './/*[local-name()="AccountingSupplierParty"]/*[local-name()="Party"]', $root);
        $taxTotal = $this->first($xpath, './/*[local-name()="TaxTotal"]', $root);
        $items = [];

        foreach ($this->nodes($xpath, './/*[local-name()="InvoiceLine"]', $root) as $line) {
            $items[] = [
                'item_name' => $this->value($xpath, './/*[local-name()="Item"]/*[local-name()="Name"]', $line) ?: 'Line item',
                'item_description' => $this->value($xpath, './/*[local-name()="Item"]/*[local-name()="Description"]', $line),
                'quantity' => $this->number($this->value($xpath, './/*[local-name()="InvoicedQuantity"]', $line), 1),
                'unit_price' => $this->number($this->value($xpath, './/*[local-name()="Price"]/*[local-name()="PriceAmount"]', $line)),
                'tax_rate' => $this->number($this->value($xpath, './/*[local-name()="ClassifiedTaxCategory"]/*[local-name()="Percent"]', $line)),
                'subtotal' => $this->number($this->value($xpath, './/*[local-name()="LineExtensionAmount"]', $line)),
                'tax_total' => null,
                'total' => $this->number($this->value($xpath, './/*[local-name()="LineExtensionAmount"]', $line)),
            ];
        }

        $currency = $this->value($xpath, './*[local-name()="DocumentCurrencyCode"]', $root) ?: 'EUR';
        return [
            'supplier' => $this->supplierData($xpath, $seller, 'ubl'),
            'invoice' => [
                'supplier_invoice_number' => $this->value($xpath, './*[local-name()="ID"]', $root),
                'supplier_invoice_date' => $this->date($this->value($xpath, './*[local-name()="IssueDate"]', $root)),
                'supplier_due_date' => $this->date($this->value($xpath, './*[local-name()="DueDate"]', $root)),
                'currency_code' => $currency,
                'subtotal' => $this->number($this->value($xpath, './/*[local-name()="TaxExclusiveAmount"]', $root)),
                'tax_total' => $this->number($this->value($xpath, './/*[local-name()="TaxAmount"]', $taxTotal)),
                'total' => $this->number($this->value($xpath, './/*[local-name()="PayableAmount"]', $root)),
            ],
            'items' => $items,
        ];
    }

    private function supplierData(DOMXPath $xpath, ?DOMNode $seller, string $syntax): array
    {
        if ($seller === null) {
            throw new RuntimeException('Supplier information is missing from the invoice document.');
        }

        return [
            'supplier_name' => $this->value($xpath, './/*[local-name()="Name"]', $seller),
            'supplier_company' => $this->value($xpath, './/*[local-name()="Name"]', $seller),
            'supplier_vat_id' => $this->value($xpath, $syntax === 'ubl'
                ? './/*[local-name()="PartyTaxScheme"]/*[local-name()="CompanyID"]'
                : './/*[local-name()="SpecifiedTaxRegistration"]/*[local-name()="ID"]', $seller),
            'supplier_tax_code' => $this->value($xpath, $syntax === 'ubl'
                ? './/*[local-name()="PartyLegalEntity"]/*[local-name()="CompanyID"]'
                : './/*[local-name()="SpecifiedLegalOrganization"]/*[local-name()="ID"]', $seller),
            'supplier_peppol_id' => $this->value($xpath, $syntax === 'ubl'
                ? './*[local-name()="EndpointID"]'
                : './/*[local-name()="URIUniversalCommunication"]/*[local-name()="URIID"]', $seller),
            'supplier_email' => $this->value($xpath, './/*[local-name()="ElectronicMail"]', $seller),
            'supplier_address_1' => $this->value($xpath, './/*[local-name()="StreetName"] | .//*[local-name()="LineOne"]', $seller),
            'supplier_city' => $this->value($xpath, './/*[local-name()="CityName"] | .//*[local-name()="CityName"]', $seller),
            'supplier_zip' => $this->value($xpath, './/*[local-name()="PostalZone"] | .//*[local-name()="PostcodeCode"]', $seller),
            'supplier_country' => $this->value($xpath, './/*[local-name()="Country"]/*[local-name()="IdentificationCode"] | .//*[local-name()="CountryID"]', $seller),
        ];
    }

    private function first(DOMXPath $xpath, string $expression, DOMNode $context): ?DOMNode
    {
        $nodes = $xpath->query($expression, $context);

        return $nodes !== false && $nodes->length > 0 ? $nodes->item(0) : null;
    }

    private function nodes(DOMXPath $xpath, string $expression, DOMNode $context): array
    {
        $nodes = $xpath->query($expression, $context);
        $result = [];
        if ($nodes !== false) {
            foreach ($nodes as $node) {
                $result[] = $node;
            }
        }

        return $result;
    }

    private function value(DOMXPath $xpath, string $expression, ?DOMNode $context): string
    {
        if ($context === null) {
            return '';
        }

        return trim((string) $xpath->evaluate('string((' . $expression . ')[1])', $context));
    }

    private function number(string $value, ?float $default = null): ?float
    {
        if ($value === '' || ! is_numeric(str_replace(',', '.', $value))) {
            return $default;
        }

        return (float) str_replace(',', '.', $value);
    }

    private function date(string $value): ?string
    {
        $value = trim($value);
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $value, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        return null;
    }

    private function extractFacturX(string $pdfPath): string
    {
        if ( ! function_exists('proc_open')) {
            throw new RuntimeException('Factur-X import requires PHP proc_open.');
        }

        $list = $this->runPdfDetach(['-list', $pdfPath]);
        if (preg_match('/^\s*(\d+)\s*:\s*factur-x\.xml\s*$/mi', $list, $matches) !== 1) {
            throw new RuntimeException('The Factur-X PDF does not contain a factur-x.xml attachment.');
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'ip-supplier-invoice-');
        if ($outputPath === false) {
            throw new RuntimeException('Unable to create a temporary Factur-X XML path.');
        }
        unlink($outputPath);
        $this->runPdfDetach(['-save', $matches[1], '-o', $outputPath, $pdfPath]);

        if ( ! is_file($outputPath) || filesize($outputPath) === 0) {
            throw new RuntimeException('The Factur-X XML attachment could not be extracted.');
        }

        return $outputPath;
    }

    private function runPdfDetach(array $arguments): string
    {
        $pipes = [];
        $process = @proc_open(array_merge(['pdfdetach'], $arguments), [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, null, ['bypass_shell' => true]);
        if ( ! is_resource($process)) {
            throw new RuntimeException('Unable to start the Factur-X extractor.');
        }

        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            throw new RuntimeException('Factur-X extraction failed: ' . trim($error));
        }

        return $output;
    }
}
