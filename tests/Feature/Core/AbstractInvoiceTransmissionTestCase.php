<?php

namespace Tests\Feature\Core;

use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;

/**
 * Shared rig for the per-provider send_invoice transmission tests.
 *
 * Every subclass drives the real route
 *   POST /integrations/send_invoice/{invoiceId}/{merchantClientId}
 * with the outbound HTTP call replaced by a Tests\Fakes\Integration\QueueApiClient
 * (armed through IntegrationTransport by the INTEGRATION_MOCK_RESPONSES fixture)
 * and, by default, the Factur-X / UBL build replaced by a stub artifact. Set
 * INTEGRATION_REAL_ARTIFACT via realArtifact() to run the real generator too.
 *
 * DB (MariaDB), routing, the profile registry, settings decryption and the
 * ip_merchant_responses write all run for real.
 */
abstract class AbstractInvoiceTransmissionTestCase extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->purgeGeneratedArtifacts();
    }

    protected function tearDown(): void
    {
        $this->purgeGeneratedArtifacts();
        parent::tearDown();
    }

    /**
     * Arm the fake transport with an ordered queue of response envelopes.
     * Each entry is merged onto a 200-OK skeleton, so only the keys the
     * provider client reads need to be given.
     *
     * @param array<int, array<string, mixed>> $responses
     */
    protected function mockResponses(array $responses, ?string $tokenError = null): void
    {
        $config = ['responses' => $responses];
        if ($tokenError !== null) {
            $config['token_error'] = $tokenError;
        }
        $this->withEnvironment(['INTEGRATION_MOCK_RESPONSES' => json_encode($config)]);
    }

    /**
     * Additionally run the real EInvoiceDocumentService build instead of the
     * stub artifact (still faking only the outbound HTTP call).
     */
    protected function realArtifact(): void
    {
        $this->withEnvironment(['INTEGRATION_REAL_ARTIFACT' => '1']);
    }

    protected function send(int $invoiceId, int $merchantId, string $method = 'POST'): HttpResponse
    {
        return $this->request($method, '/integrations/send_invoice/' . $invoiceId . '/' . $merchantId);
    }

    /**
     * The EInvoiceSchematronValidator shells out to `java` (Saxon). Without it
     * the EN 16931 rule check cannot run and any real Factur-X / UBL build
     * fails closed.
     */
    protected function javaAvailable(): bool
    {
        exec('command -v java 2>/dev/null', $out, $code);

        return $code === 0;
    }

    protected function setSetting(string $key, string $value): void
    {
        $this->databaseDelete('ip_settings', ['setting_key' => $key]);
        $this->databaseInsert('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
    }

    /**
     * Seed a fully EN 16931 / Factur-X conformant invoice so the real
     * EInvoiceDocumentService build (CII XML → EN 16931 Schematron → hybrid
     * PDF) succeeds: a French seller whose SIREN lives in
     * user_einvoice_identifier, a French buyer, one 20 % VAT line with its
     * amount row, matching invoice totals, and the currency_code / einvoicing
     * settings the PDF helper's embed path requires.
     *
     * @return array{0: int, 1: int} [invoiceId, merchantClientId]
     */
    protected function seedFacturxReadyInvoice(string $merchantType, string $invoiceNumber = 'FX-0001'): array
    {
        $this->databaseUpdate('ip_users', [
            'user_name'                => 'Seller SARL',
            'user_company'             => 'Seller SARL',
            'user_email'               => 'seller@example.fr',
            'user_vat_id'              => 'FR12345678901',
            'user_tax_code'            => '732829320',
            'user_einvoice_identifier' => '732829320',
            'user_iban'                => 'FR7630006000011234567890189',
            'user_bank'                => 'BNP',
            'user_address_1'           => '1 rue du Test',
            'user_city'                => 'Paris',
            'user_zip'                 => '75001',
            'user_country'             => 'FR',
        ], ['user_id' => 1]);

        [$invoiceId, $merchantId] = $this->seedSendable($merchantType, 'Facturxv10', [
            'client_name'      => 'Buyer SAS',
            'client_email'     => 'buyer@example.fr',
            'client_vat_id'    => 'FR98765432109',
            'client_tax_code'  => '552081317',
            'client_peppol_id' => '552081317',
            'client_address_1' => '2 avenue du Client',
            'client_city'      => 'Lyon',
            'client_zip'       => '69001',
            'client_country'   => 'FR',
        ], [
            'invoice_number' => $invoiceNumber,
        ]);

        $this->databaseUpdate('ip_invoice_amounts', [
            'invoice_item_subtotal'  => '100.00',
            'invoice_item_tax_total' => '20.00',
            'invoice_tax_total'      => '0.00',
            'invoice_total'          => '120.00',
            'invoice_paid'           => '0.00',
            'invoice_balance'        => '120.00',
        ], ['invoice_id' => $invoiceId]);

        $taxRateId = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'TVA 20 ' . random_int(1000, 9999),
            'tax_rate_percent' => '20.00',
        ]);
        $itemId = $this->databaseInsert('ip_invoice_items', [
            'invoice_id'           => $invoiceId,
            'item_tax_rate_id'     => $taxRateId,
            'item_product_id'      => 0,
            'item_date_added'      => date('Y-m-d'),
            'item_name'            => 'Consulting',
            'item_description'     => 'Consulting services',
            'item_quantity'        => '1.00',
            'item_price'           => '100.00',
            'item_discount_amount' => '0.00',
            'item_order'           => 1,
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', [
            'item_id'        => $itemId,
            'item_subtotal'  => '100.00',
            'item_tax_total' => '20.00',
            'item_discount'  => '0.00',
            'item_total'     => '120.00',
        ]);

        $this->setSetting('currency_code', 'EUR');
        $this->setSetting('einvoicing', '1');

        return [$invoiceId, $merchantId];
    }

    /**
     * Seed a client with the given e-invoicing profile, an invoice for it, and
     * an enabled merchant client of $merchantType.
     *
     * @param array<string, mixed> $clientOverrides
     * @param array<string, mixed> $invoiceOverrides
     *
     * @return array{0: int, 1: int} [invoiceId, merchantClientId]
     */
    protected function seedSendable(
        string $merchantType,
        string $profileCode,
        array $clientOverrides = [],
        array $invoiceOverrides = []
    ): array {
        $clientId = $this->seedClient(array_merge([
            'client_name'               => ucfirst($merchantType) . ' Peppol Customer',
            'client_einvoicing_active'  => 1,
            'client_einvoicing_version' => $profileCode,
            'client_peppol_id'          => '0088:1234567890123',
        ], $clientOverrides));

        $invoiceId = $this->seedInvoice($clientId, $invoiceOverrides);

        $merchantId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => $merchantType,
            'label'         => ucfirst($merchantType) . ' Live',
            'enabled'       => 1,
            'auth_type'     => $merchantType === 'qonto' ? 'bearer' : 'oauth2',
            'settings_json' => json_encode($this->settingsFor($merchantType)),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return [$invoiceId, $merchantId];
    }

    /**
     * @return array<string, string>
     */
    protected function settingsFor(string $merchantType): array
    {
        return match ($merchantType) {
            'qonto' => [
                'access_token'               => 'qonto-access-token',
                'api_base_url'               => 'https://thirdparty.qonto.com',
                'import_endpoint'            => '/v2/client_invoices/bulk',
                'client_invoices_endpoint'   => '/v2/client_invoices',
                'send_invoice_endpoint'      => '/v2/client_invoices/{id}/send_by_einvoice',
                'invoice_status_endpoint'    => '/v2/client_invoices/{id}',
                'incoming_invoices_endpoint' => '/v2/supplier_invoices',
                'attachment_endpoint'        => '/v2/attachments/{id}',
            ],
            'superpdp' => [
                'client_id'                  => 'sp-client-id',
                'client_secret'              => 'sp-client-secret',
                'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
                'api_base_url'               => 'https://api.superpdp.tech',
                'invoice_endpoint'           => '/v1.beta/invoices',
                'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
                'incoming_invoices_endpoint' => '/v1.beta/invoices',
                'invoice_events_endpoint'    => '/v1.beta/invoice_events',
                'disable_pre_check'          => '0',
            ],
            default => [
                'client_id'                    => 'lp-client-id',
                'client_secret'                => 'lp-client-secret',
                'token_url'                    => 'https://api.letspeppol.eu/oauth2/token',
                'api_base_url'                 => 'https://api.letspeppol.eu',
                'invoice_endpoint'             => '/v1/invoices',
                'invoice_status_endpoint'      => '/v1/invoices/{id}',
                'incoming_invoices_endpoint'   => '/v1/incoming-invoices',
                'invoice_events_endpoint'      => '/v1/invoice-events',
                'credit_note_endpoint'         => '/v1/credit-notes',
                'credit_note_status_endpoint'  => '/v1/credit-notes/{id}',
                'participants_endpoint'        => '/v1/participants',
                'participant_lookup_endpoint'  => '/v1/participants/{id}',
                'transmissions_endpoint'       => '/v1/transmissions',
                'transmission_status_endpoint' => '/v1/transmissions/{id}',
                'documents_endpoint'           => '/v1/documents',
                'document_endpoint'            => '/v1/documents/{id}',
            ],
        };
    }

    /**
     * send_invoice writes the generated (or stub) document to
     * uploads/integrations/outgoing/. Clear it around each test so a leftover
     * file from one test can never be read by, or collide with, another.
     */
    private function purgeGeneratedArtifacts(): void
    {
        $dir = dirname(__DIR__, 3) . '/uploads/integrations/outgoing';
        foreach (glob($dir . '/invoice_*') ?: [] as $file) {
            @unlink($file);
        }
    }
}
