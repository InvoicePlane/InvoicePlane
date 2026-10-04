<?php

declare(strict_types=1);

namespace Tests\Unit\SupplierInvoices;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SupplierInvoiceDocumentParser;

require_once dirname(__DIR__, 3) . '/application/modules/supplier_invoices/libraries/SupplierInvoiceDocumentParser.php';

final class SupplierInvoiceDocumentParserTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    #[Test]
    public function it_extracts_supplier_totals_and_lines_from_cii(): void
    {
        $path = $this->writeDocument(<<<'XML'
<CrossIndustryInvoice xmlns="urn:example">
  <ExchangedDocument><ID>INV-42</ID><IssueDateTime><DateTimeString format="102">20261004</DateTimeString></IssueDateTime></ExchangedDocument>
  <SupplyChainTradeTransaction>
    <IncludedSupplyChainTradeLineItem>
      <SpecifiedTradeProduct><Name>Consulting</Name></SpecifiedTradeProduct>
      <SpecifiedLineTradeAgreement><NetPriceProductTradePrice><ChargeAmount>100</ChargeAmount></NetPriceProductTradePrice></SpecifiedLineTradeAgreement>
      <SpecifiedLineTradeDelivery><BilledQuantity>2</BilledQuantity></SpecifiedLineTradeDelivery>
      <SpecifiedLineTradeSettlement><ApplicableTradeTax><RateApplicablePercent>20</RateApplicablePercent></ApplicableTradeTax><SpecifiedTradeSettlementLineMonetarySummation><LineTotalAmount>200</LineTotalAmount></SpecifiedTradeSettlementLineMonetarySummation></SpecifiedLineTradeSettlement>
    </IncludedSupplyChainTradeLineItem>
    <ApplicableHeaderTradeAgreement><SellerTradeParty><Name>Supplier Ltd</Name><SpecifiedTaxRegistration><ID>FR123</ID></SpecifiedTaxRegistration></SellerTradeParty></ApplicableHeaderTradeAgreement>
    <ApplicableHeaderTradeSettlement><InvoiceCurrencyCode>EUR</InvoiceCurrencyCode><SpecifiedTradeSettlementHeaderMonetarySummation><TaxBasisTotalAmount>200</TaxBasisTotalAmount><TaxTotalAmount>40</TaxTotalAmount><GrandTotalAmount>240</GrandTotalAmount></SpecifiedTradeSettlementHeaderMonetarySummation></ApplicableHeaderTradeSettlement>
  </SupplyChainTradeTransaction>
</CrossIndustryInvoice>
XML);

        $invoice = (new SupplierInvoiceDocumentParser())->parse($path);

        self::assertSame('Supplier Ltd', $invoice['supplier']['supplier_name']);
        self::assertSame('', $invoice['supplier']['supplier_tax_code']);
        self::assertSame('INV-42', $invoice['invoice']['supplier_invoice_number']);
        self::assertSame(40.0, $invoice['invoice']['tax_total']);
        self::assertSame(240.0, $invoice['invoice']['total']);
        self::assertSame(2.0, $invoice['items'][0]['quantity']);
    }

    #[Test]
    public function it_extracts_supplier_totals_and_lines_from_ubl(): void
    {
        $path = $this->writeDocument(<<<'XML'
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
  <ID>UBL-7</ID><IssueDate>2026-10-04</IssueDate><DocumentCurrencyCode>EUR</DocumentCurrencyCode>
  <AccountingSupplierParty><Party><EndpointID>0088:123</EndpointID><PartyName><Name>UBL Supplier</Name></PartyName><PartyTaxScheme><CompanyID>FR456</CompanyID></PartyTaxScheme></Party></AccountingSupplierParty>
  <InvoiceLine><InvoicedQuantity>3</InvoicedQuantity><LineExtensionAmount>30</LineExtensionAmount><Item><Name>Product</Name><ClassifiedTaxCategory><Percent>10</Percent></ClassifiedTaxCategory></Item><Price><PriceAmount>10</PriceAmount></Price></InvoiceLine>
  <TaxTotal><TaxAmount>3</TaxAmount></TaxTotal><LegalMonetaryTotal><TaxExclusiveAmount>30</TaxExclusiveAmount><PayableAmount>33</PayableAmount></LegalMonetaryTotal>
</Invoice>
XML);

        $invoice = (new SupplierInvoiceDocumentParser())->parse($path);

        self::assertSame('UBL Supplier', $invoice['supplier']['supplier_name']);
        self::assertSame('UBL-7', $invoice['invoice']['supplier_invoice_number']);
        self::assertSame(33.0, $invoice['invoice']['total']);
        self::assertSame(3.0, $invoice['items'][0]['quantity']);
    }

    private function writeDocument(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'ip-parser-test-');
        self::assertNotFalse($path);
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
