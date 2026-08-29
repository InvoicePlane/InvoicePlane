<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression tests for #1601: Adding items to invoices fails.
 *
 * Issue: In 1.7.2, sequential AJAX calls to save invoices with items failed
 * when csrf_regenerate=true. After the first save call, the CSRF token was
 * regenerated, causing the second call to fail validation.
 *
 * Root cause: With csrf_regenerate=true, each POST invalidates the token.
 * The UI wasn't receiving refreshed tokens in responses, so subsequent
 * AJAX calls would fail with stale tokens.
 */
class InvoicesItemSaveRegressionTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'invoices_due_after', 'setting_value' => '30']);
    }

    #[Test]
    public function it_saves_an_invoice_with_items_on_first_attempt(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act - Save invoice with one item */
        $savePayload = [
            'invoice_id' => (string) $invoiceId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created' => date('H:i:s'),
            'invoice_status_id' => '1',
            'invoice_discount_percent' => '0',
            'invoice_discount_amount' => '0',
            'items' => json_encode([
                [
                    'item_id' => '',
                    'item_name' => 'Test Item',
                    'item_description' => '',
                    'item_quantity' => '1',
                    'item_price' => '100.00',
                    'item_tax_rate_id' => '0',
                ],
            ]),
        ];

        $saveResponse = $this->ajax('POST', '/invoices/ajax/save', $savePayload);
        $saveData = json_decode($saveResponse->body(), true);

        /* Assert */
        $this->assertResponseStatusCode($saveResponse, 200);
        self::assertSame(1, $saveData['success'] ?? null, 'Failed to save invoice. Response: ' . $saveResponse->body());
        $this->assertDatabaseHas('ip_invoice_items', [
            'invoice_id' => $invoiceId,
            'item_name' => 'Test Item',
        ]);
    }

    #[Test]
    public function it_allows_sequential_create_and_save_calls_with_csrf_regeneration(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        $createPayload = [
            'client_id' => (string) $clientId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created' => date('H:i:s'),
            'invoice_group_id' => '1',
            'user_id' => '1',
        ];

        /* Act - Create an invoice (first AJAX call) */
        $createResponse = $this->ajax('POST', '/invoices/ajax/create', $createPayload);
        $invoiceData = json_decode($createResponse->body(), true);
        $invoiceId = (int) $invoiceData['invoice_id'];

        /* Assert create succeeded */
        self::assertSame(1, $invoiceData['success'] ?? null, 'Create failed: ' . $createResponse->body());

        /* Act - Save invoice with items (second AJAX call - would fail with csrf_regenerate=true if not handled) */
        $savePayload = [
            'invoice_id' => (string) $invoiceId,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => date('Y-m-d', strtotime('+30 days')),
            'invoice_time_created' => date('H:i:s'),
            'invoice_status_id' => '1',
            'invoice_discount_percent' => '0',
            'invoice_discount_amount' => '0',
            'items' => json_encode([
                [
                    'item_id' => '',
                    'item_name' => 'Item 1',
                    'item_description' => '',
                    'item_quantity' => '1',
                    'item_price' => '100.00',
                    'item_tax_rate_id' => '0',
                ],
                [
                    'item_id' => '',
                    'item_name' => 'Item 2',
                    'item_description' => '',
                    'item_quantity' => '2',
                    'item_price' => '50.00',
                    'item_tax_rate_id' => '0',
                ],
            ]),
        ];
        $saveResponse = $this->ajax('POST', '/invoices/ajax/save', $savePayload);
        $saveData = json_decode($saveResponse->body(), true);

        /* Assert */
        self::assertSame(1, $saveData['success'] ?? null, 'Second AJAX call (save) failed with CSRF token issue: ' . $saveResponse->body());
        $this->assertDatabaseHas('ip_invoice_items', [
            'invoice_id' => $invoiceId,
            'item_name' => 'Item 1',
        ]);
        $this->assertDatabaseHas('ip_invoice_items', [
            'invoice_id' => $invoiceId,
            'item_name' => 'Item 2',
        ]);
    }

    private function seedProduct($overrides = []): int
    {
        $product = array_merge([
            'product_name' => 'Test Product',
            'product_price' => 100.00,
            'tax_rate_id' => null,
        ], $overrides);

        return $this->databaseInsert('ip_products', $product);
    }
}
