<?php

namespace Tests\Feature\Core\Integrations;

use DOMDocument;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * LetsPeppol — the generation half: the real Ublv24Xml build (UBL / Peppol BIS
 * 3.0) plus the XSD and Schematron validators run; only the outbound HTTP call
 * is faked.
 */
#[Group('integration')]
#[Group('einvoice-generation')]
final class LetsPeppolEInvoiceGenerationTest extends AbstractInvoiceTransmissionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if ( ! $this->javaAvailable()) {
            self::markTestSkipped('UBL Schematron validation requires `java` (Saxon) on PATH.');
        }
        $this->realArtifact();
    }

    #[Test]
    public function it_generates_a_valid_ubl_invoice_and_transmits_it(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedUblReadyInvoice('UBL-LP-001');
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['id' => 'lp-ubl-1', 'status' => 'sent']],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $attempt = $this->databaseFetchOne('ip_merchant_responses', [
            'invoice_id' => $invoiceId,
            'direction'  => 'out',
        ]);
        self::assertNotNull($attempt);
        self::assertSame('lp-ubl-1', $attempt['merchant_response_reference'], (string) ($attempt['error_detail'] ?? ''));

        $xml = $this->generatedXml($invoiceId);
        $doc = new DOMDocument();
        self::assertTrue($doc->loadXML($xml), 'The artifact must be well-formed XML.');
        self::assertSame('Invoice', $doc->documentElement->localName);
        self::assertStringContainsString('UBL-LP-001', $xml);
        self::assertStringContainsString('Seller SARL', $xml);
        self::assertStringContainsString('Buyer SAS', $xml);
        self::assertStringContainsString('EUR', $xml);
    }

    #[Test]
    public function it_does_not_transmit_when_the_currency_is_missing(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedUblReadyInvoice('UBL-LP-002');
        $this->databaseDelete('ip_settings', ['setting_key' => 'currency_code']);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['id' => 'must-not-happen']],
        ]);

        /* Act */
        $this->send($invoiceId, $merchantId);

        /* Assert */
        $attempt = $this->databaseFetchOne('ip_merchant_responses', [
            'invoice_id' => $invoiceId,
            'direction'  => 'out',
        ]);
        self::assertNotNull($attempt);
        self::assertSame(0, (int) $attempt['http_code'], 'No HTTP request may have been made.');
        self::assertNotSame('must-not-happen', $attempt['merchant_response_reference']);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function seedUblReadyInvoice(string $number): array
    {
        $this->databaseUpdate('ip_users', [
            'user_name'                => 'Seller SARL',
            'user_company'             => 'Seller SARL',
            'user_email'               => 'seller@example.fr',
            'user_vat_id'              => 'FR12345678901',
            'user_tax_code'            => '732829320',
            'user_einvoice_identifier' => '0009:73282932000074',
            'user_iban'                => 'FR7630006000011234567890189',
            'user_address_1'           => '1 rue du Test',
            'user_city'                => 'Paris',
            'user_zip'                 => '75001',
            'user_country'             => 'FR',
        ], ['user_id' => 1]);

        [$invoiceId, $merchantId] = $this->seedSendable('letspeppol', 'UblPeppolV21', [
            'client_name'      => 'Buyer SAS',
            'client_email'     => 'buyer@example.fr',
            'client_vat_id'    => 'FR98765432109',
            'client_peppol_id' => '0009:55208131766522',
            'client_address_1' => '2 avenue du Client',
            'client_city'      => 'Lyon',
            'client_zip'       => '69001',
            'client_country'   => 'FR',
        ], ['invoice_number' => $number]);

        $this->seedStandardInvoiceBody($invoiceId);

        return [$invoiceId, $merchantId];
    }

    private function generatedXml(int $invoiceId): string
    {
        $file = ROOT_PATH . '/uploads/integrations/outgoing/invoice_' . $invoiceId . '.xml';
        if ( ! is_file($file)) {
            self::fail('Generated UBL not found: ' . $file . ' (found: ' . implode(',', glob(dirname($file) . '/*') ?: []) . ')');
        }

        return (string) file_get_contents($file);
    }
}
