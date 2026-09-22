<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Qonto — the *generation* half of the flow: POST /integrations/send_invoice
 * with INTEGRATION_REAL_ARTIFACT set, so send_invoice runs the real
 * EInvoiceDocumentService build instead of the stub. Only the outbound HTTP
 * call to Qonto is faked.
 *
 * The build chain that runs for real here:
 *   generate_xml_invoice_file()  -> Factur-X CII XML (Facturxv10Xml)
 *   EInvoiceDocumentValidator    -> Factur-X 1.09 EN 16931 XSD
 *   EInvoiceSchematronValidator  -> EN 16931 Schematron via Saxon (needs java)
 *   FrenchEInvoiceValidator      -> DGFiP 3.2 SIREN / legal-note checks
 *   generate_invoice_pdf()       -> mpdf hybrid PDF with the CII XML embedded
 *   EInvoiceDocumentValidator    -> hybrid-PDF structural check (/EmbeddedFiles)
 *
 * Requires `java` on PATH (Saxon). Where it is absent the schematron step
 * fails closed and generation cannot complete — those cases skip with a clear
 * message; CI provides a JDK so they run for real there.
 */
#[Group('integration')]
#[Group('einvoice-generation')]
final class QontoEInvoiceGenerationTest extends AbstractInvoiceTransmissionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if ( ! $this->javaAvailable()) {
            self::markTestSkipped('Factur-X EN 16931 Schematron validation requires `java` (Saxon) on PATH.');
        }
        $this->realArtifact();
    }

    #[Test]
    public function it_generates_a_facturx_hybrid_pdf_and_transmits_it_to_qonto(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedFacturxReadyInvoice('qonto', 'FX-QONTO-001');
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['client_invoices' => [['invoice_id' => 'ci-fx-1']]]],
            ['success' => true, 'http_code' => 200, 'response' => []],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert — the real build passed every validator and the send completed */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'                  => $invoiceId,
            'merchant_client_id'          => $merchantId,
            'merchant_response_driver'    => 'qonto',
            'direction'                   => 'out',
            'merchant_response_reference' => 'ci-fx-1',
            'status'                      => 'pending',
        ]);

        $pdf = $this->generatedArtifact($invoiceId);
        self::assertStringStartsWith('%PDF-', $pdf['bytes']);
        self::assertGreaterThan(10_000, strlen($pdf['bytes']), 'A rendered invoice PDF should be more than a stub.');
        self::assertStringContainsString('/EmbeddedFiles', $pdf['bytes'], 'The hybrid PDF must declare an embedded file.');
        self::assertStringContainsString('factur-x.xml', $pdf['bytes'], 'The embedded file must be the Factur-X attachment.');
    }

    #[Test]
    public function it_does_not_transmit_when_the_seller_has_no_siren(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedFacturxReadyInvoice('qonto', 'FX-QONTO-002');
        $this->databaseUpdate('ip_users', [
            'user_einvoice_identifier' => '',
            'user_tax_code'            => '',
        ], ['user_id' => 1]);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['client_invoices' => [['invoice_id' => 'must-not-happen']]]],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert — DGFiP SIREN check fails closed; nothing is sent or logged */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
            'direction'          => 'out',
        ]);
    }

    #[Test]
    public function it_does_not_transmit_when_the_invoice_currency_is_missing(): void
    {
        /* Arrange */
        [$invoiceId, $merchantId] = $this->seedFacturxReadyInvoice('qonto', 'FX-QONTO-003');
        $this->databaseDelete('ip_settings', ['setting_key' => 'currency_code']);
        $this->mockResponses([
            ['success' => true, 'http_code' => 201, 'response' => ['client_invoices' => [['invoice_id' => 'must-not-happen']]]],
        ]);

        /* Act */
        $response = $this->send($invoiceId, $merchantId);

        /* Assert — EN 16931 BR-05 (currency code) fails the Schematron step */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_merchant_responses', [
            'invoice_id'         => $invoiceId,
            'merchant_client_id' => $merchantId,
            'direction'          => 'out',
        ]);
    }

    /**
     * @return array{path: string, bytes: string}
     */
    private function generatedArtifact(int $invoiceId): array
    {
        $path       = rtrim((string) getenv('CI_TEST_UPLOADS'), '/');
        $candidates = array_filter([
            $path !== '' ? $path . '/integrations/outgoing/invoice_' . $invoiceId . '.pdf' : null,
            getcwd() . '/uploads/integrations/outgoing/invoice_' . $invoiceId . '.pdf',
            dirname(__DIR__, 3) . '/uploads/integrations/outgoing/invoice_' . $invoiceId . '.pdf',
        ]);
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return ['path' => $candidate, 'bytes' => (string) file_get_contents($candidate)];
            }
        }
        self::fail('Generated Factur-X PDF not found for invoice ' . $invoiceId . ' (looked in: ' . implode(', ', $candidates) . ')');
    }
}
