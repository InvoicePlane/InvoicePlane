<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * invoices/controllers/Ajax.php — the modal_* view-rendering endpoints.
 * These have no required fields of their own (they render forms), so
 * coverage here is route-exercising + no-PHP-errors.
 */
class InvoicesAjaxModalsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_renders_the_copy_invoice_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_copy_invoice', [
            'invoice_id' => (string) $invoiceId,
            'client_id'  => (string) $clientId,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_the_change_user_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_change_user', ['invoice_id' => (string) $invoiceId, 'user_id' => '1']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_the_change_client_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_change_client', ['invoice_id' => (string) $invoiceId, 'client_id' => (string) $clientId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_the_create_invoice_modal(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_create_invoice', ['client_id' => (string) $clientId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_the_create_recurring_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_create_recurring', ['invoice_id' => (string) $invoiceId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_renders_the_create_credit_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->ajax('POST', '/invoices/ajax/modal_create_credit', ['invoice_id' => (string) $invoiceId]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }
}
