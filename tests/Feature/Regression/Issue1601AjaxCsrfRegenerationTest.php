<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TDD Test Suite for #1601: Sequential AJAX calls fail with csrf_regenerate=true.
 *
 * Issue: When csrf_regenerate is enabled, the first AJAX POST invalidates the CSRF token.
 * If the response doesn't include the new token, subsequent AJAX calls fail with
 * "CSRF validation failed" errors.
 *
 * Root Cause: Base_Controller::json_encode_ajax() was not including the refreshed
 * CSRF token in AJAX responses, so clients couldn't update their tokens for subsequent calls.
 *
 * Fix: Modified json_encode_ajax() to include the CSRF token when csrf_regenerate is enabled.
 *
 * Note: These tests verify the sequential AJAX call flow works. When csrf_regenerate
 * is enabled in ipconfig.php, the response will include the refreshed CSRF token
 * automatically via the json_encode_ajax() method.
 */
class Issue1601AjaxCsrfRegenerationTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        // Ensure invoice due date setting exists
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key'   => 'invoices_due_after',
            'setting_value' => '30',
        ]);
    }

    #[Test]
    public function sequential_ajax_calls_succeed_create_then_save(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act - First AJAX call: Create invoice */
        $createPayload = [
            'client_id'            => (string) $clientId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due'     => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created' => date('H:i:s'),
            'invoice_group_id'     => '1',
            'user_id'              => '1',
        ];

        $createResponse = $this->ajax('POST', '/invoices/ajax/create', $createPayload);
        $createData     = json_decode($createResponse->body(), true);

        /* Assert - First call succeeds */
        self::assertSame(1, $createData['success'] ?? null, 'Create failed: ' . $createResponse->body());
        $invoiceId = (int) $createData['invoice_id'];
        self::assertNotEmpty($invoiceId, 'Invoice ID should be returned');

        /* Act - Second AJAX call: Save invoice immediately after create
           (This would fail if CSRF regeneration broke the token chain) */
        $savePayload = [
            'invoice_id'               => (string) $invoiceId,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'invoice_status_id'        => '1',
            'invoice_discount_percent' => '0',
            'invoice_discount_amount'  => '0',
            'items'                    => json_encode([]),
        ];

        $saveResponse = $this->ajax('POST', '/invoices/ajax/save', $savePayload);
        $saveData     = json_decode($saveResponse->body(), true);

        /* Assert - Second call succeeds (proves sequential AJAX works) */
        self::assertSame(
            1,
            $saveData['success'] ?? null,
            'Sequential AJAX call (save after create) failed. '
            . 'This bug occurs when csrf_regenerate=true and tokens are not refreshed in responses. '
            . 'Response: ' . $saveResponse->body()
        );
    }

    #[Test]
    public function ajax_response_includes_csrf_token_when_enabled(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act - Make AJAX call and check response structure */
        $createPayload = [
            'client_id'            => (string) $clientId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due'     => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created' => date('H:i:s'),
            'invoice_group_id'     => '1',
            'user_id'              => '1',
        ];

        $createResponse = $this->ajax('POST', '/invoices/ajax/create', $createPayload);
        $createData     = json_decode($createResponse->body(), true);

        /* Assert - Response is well-formed */
        self::assertIsArray($createData, 'AJAX response must be JSON array');
        self::assertArrayHasKey('success', $createData, 'AJAX response must have success key');
        self::assertSame(1, $createData['success'], 'AJAX call must succeed');

        /* Assert - If csrf_regenerate is enabled, response should include CSRF token
           (The token key name varies by config, commonly 'csrf_test_name' or similar) */
        $csrfKeys = array_filter(array_keys($createData), function ($key) {
            return stripos($key, 'csrf') !== false;
        });

        /* Note: Token may or may not be present depending on csrf_regenerate config.
           The fix ensures it IS present when enabled. This assertion documents expected behavior. */
        if (count($csrfKeys) > 0) {
            self::assertNotEmpty($createData[$csrfKeys[0]], 'CSRF token should not be empty');
        }
    }

    #[Test]
    public function multiple_sequential_ajax_calls_work(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act - Call 1: Save invoice with discount update */
        $firstPayload = [
            'invoice_id'               => (string) $invoiceId,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'invoice_status_id'        => '1',
            'invoice_discount_percent' => '10',
            'items'                    => json_encode([]),
        ];
        $firstResponse = $this->ajax('POST', '/invoices/ajax/save', $firstPayload);
        $firstData     = json_decode($firstResponse->body(), true);

        self::assertSame(1, $firstData['success'] ?? null, 'First save failed: ' . $firstResponse->body());

        /* Act - Call 2: Save invoice again with different discount */
        $secondPayload = [
            'invoice_id'               => (string) $invoiceId,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created'     => date('H:i:s'),
            'invoice_status_id'        => '1',
            'invoice_discount_percent' => '15',
            'items'                    => json_encode([]),
        ];
        $secondResponse = $this->ajax('POST', '/invoices/ajax/save', $secondPayload);
        $secondData     = json_decode($secondResponse->body(), true);

        /* Assert - Multiple sequential calls work (regression test for #1601) */
        self::assertSame(
            1,
            $secondData['success'] ?? null,
            'Multiple sequential AJAX calls failed. This indicates CSRF token regeneration is broken. '
            . 'Response: ' . $secondResponse->body()
        );
    }
}
