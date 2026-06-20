<?php

namespace Tests\Unit\Invoices;

use Tests\Support\CITestCase;

/**
 * Tests for invoice status definitions in Mdl_Invoices.
 *
 * These tests intentionally do not hit the database; they only verify
 * the status metadata (labels, CSS classes, hrefs) that drive the UI
 * and will need to stay consistent for the Peppol BIS 3.0 status mapping.
 */
class InvoiceStatusTest extends CITestCase
{
    private \Mdl_Invoices $model;

    protected function setUp(): void
    {
        parent::setUp();

        require_once APPPATH . 'modules/invoices/models/Mdl_invoices.php';

        $this->model = new \Mdl_Invoices();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_four_invoice_statuses(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertCount(4, $statuses);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_keys_statuses_by_numeric_strings(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        foreach (array_keys($statuses) as $key) {
            $this->assertMatchesRegularExpression('/^\d+$/', (string) $key, "Status key '{$key}' should be numeric");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_label_class_and_href_in_every_status(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        foreach ($statuses as $id => $status) {
            $this->assertArrayHasKey('label', $status, "Status {$id} missing 'label'");
            $this->assertArrayHasKey('class', $status, "Status {$id} missing 'class'");
            $this->assertArrayHasKey('href', $status, "Status {$id} missing 'href'");
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_assigns_the_draft_class_to_status_1(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertArrayHasKey('1', $statuses);
        $this->assertSame('draft', $statuses['1']['class']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_assigns_the_sent_class_to_status_2(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertSame('sent', $statuses['2']['class']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_assigns_the_viewed_class_to_status_3(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertSame('viewed', $statuses['3']['class']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_assigns_the_paid_class_to_status_4(): void
    {
        /* Arrange */

        /* Act */
        $statuses = $this->model->statuses();

        /* Assert */
        $this->assertSame('paid', $statuses['4']['class']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_ip_invoices_as_the_table_name(): void
    {
        /* Arrange */

        /* Act + Assert */
        $this->assertSame('ip_invoices', $this->model->table);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_the_fully_qualified_column_as_the_primary_key(): void
    {
        /* Arrange */

        /* Act + Assert */
        $this->assertSame('ip_invoices.invoice_id', $this->model->primary_key);
    }
}
