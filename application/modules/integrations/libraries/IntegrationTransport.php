<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Test seam for the outbound e-invoicing path (send_invoice).
 *
 * In production every method returns null and the caller uses the real HTTP
 * transport / document generator. When the process runs under the PHPUnit
 * harness (ENVIRONMENT === 'testing' or CI_TESTING) *and* the test has
 * published a response queue via the INTEGRATION_MOCK_RESPONSES environment
 * variable, these hand back test doubles so the whole send_invoice controller
 * flow — provider selection, settings decryption, provider client build,
 * response normalisation, merchant_responses persistence, flash + redirect —
 * can be exercised without a network call or a real Factur-X / UBL build.
 *
 * Mirrors the pattern PaypalLib::testHandlerStack() uses for the PayPal gateway.
 * INTEGRATION_MOCK_RESPONSES is a JSON object: {"responses": [...], "token": {...},
 * "token_error": "..."} — or a bare JSON array, treated as {"responses": [...]}.
 */
final class IntegrationTransport
{
    public static function isTestMode(): bool
    {
        if (ENVIRONMENT !== 'testing' && ! defined('CI_TESTING')) {
            return false;
        }

        $fixture = getenv('INTEGRATION_MOCK_RESPONSES');

        return is_string($fixture) && $fixture !== '';
    }

    /**
     * A queue-driven ApiClientInterface when the test seam is armed, else null.
     */
    public static function httpClient(): ?ApiClientInterface
    {
        if ( ! self::isTestMode()) {
            return null;
        }

        $config = json_decode((string) getenv('INTEGRATION_MOCK_RESPONSES'), true);

        if ( ! is_array($config)) {
            return null;
        }

        $fakeClass = 'Tests\\Fakes\\Integration\\QueueApiClient';

        if ( ! class_exists($fakeClass)) {
            throw new RuntimeException('Integration test HTTP client is unavailable.');
        }

        return new $fakeClass($config);
    }

    /**
     * A stub artifact pointing at a minimal PDF, so send_invoice can skip the
     * real EInvoiceDocumentService build. The profile lookup and the
     * provider-support check upstream of this still run for real.
     *
     * Returns null — i.e. the real generator runs — when the test also sets
     * INTEGRATION_REAL_ARTIFACT, so a generation test can exercise the actual
     * Factur-X / UBL build while still faking only the outbound HTTP call.
     */
    public static function artifact(int $invoiceId, EInvoiceProfile $profile, string $directory): ?EInvoiceArtifact
    {
        if ( ! self::isTestMode()) {
            return null;
        }

        if (getenv('INTEGRATION_REAL_ARTIFACT')) {
            return null;
        }

        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create e-invoice output directory.');
        }

        $path = $directory . 'invoice_' . $invoiceId . '.' . $profile->extension();

        if (file_put_contents($path, "%PDF-1.4\n% integration test artifact\n%%EOF\n") === false) {
            throw new RuntimeException('Unable to write the test e-invoice artifact.');
        }

        return new EInvoiceArtifact($path, $profile, []);
    }
}
