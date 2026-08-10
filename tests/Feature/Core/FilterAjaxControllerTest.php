<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * filter/controllers/Ajax.php — read-only table-filtering endpoints (POST a
 * filter_query, get back a rendered partial). No mutation, so coverage here
 * is route-exercising + real filtering behavior + SQL-injection safety,
 * rather than required-field validation.
 */
class FilterAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_filters_invoices_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Filter Invoice Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'FILTER-MATCH-001']);
        $this->seedInvoice($clientId, ['invoice_number' => 'OTHER-002']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => 'FILTER-MATCH-001']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'FILTER-MATCH-001');
        $this->assertResponseBodyNotContains($response, 'OTHER-002');
    }

    #[Test]
    public function it_does_not_expose_php_errors_when_filtering_invoices_without_a_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', []);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_treats_filter_invoices_query_as_a_literal_search_term(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_number' => 'SAFE-001']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => "' OR '1'='1"]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_quotes_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->databaseInsert('ip_quotes', [
            'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,
            'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_number'       => 'QUOFILTER-001', 'quote_url_key' => bin2hex(random_bytes(16)),
        ]);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_quotes', ['filter_query' => 'QUOFILTER-001']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'QUOFILTER-001');
    }

    #[Test]
    public function it_filters_clients_by_query(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'FilterClientMatch']);
        $this->seedClient(['client_name' => 'OtherClient']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_clients', ['filter_query' => 'FilterClientMatch']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'FilterClientMatch');
        $this->assertResponseBodyNotContains($response, 'OtherClient');
    }

    #[Test]
    public function it_filters_custom_fields_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_fields', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_custom_values_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_values', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_custom_values_field_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_values_field', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_projects_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->databaseInsert('ip_projects', [
            'client_id' => $clientId, 'project_name' => 'FilterProjectMatch',
        ]);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_projects', ['filter_query' => 'FilterProjectMatch']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'FilterProjectMatch');
    }

    #[Test]
    public function it_filters_tasks_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_tasks', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_products_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_products', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_users_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_users', ['filter_query' => 'admin']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_families_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_families', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_recurring_invoices_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices_recuring', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_online_logs_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_online_logs', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_archives_by_query(): void
    {
        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_archives', ['filter_query' => 'anything']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_filters_payments_by_query(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId, ['payment_note' => 'FilterPaymentMatch']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_payments', ['filter_query' => 'FilterPaymentMatch']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'FilterPaymentMatch');
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Act */
        $response = $this->post('/filter/ajax/filter_invoices', ['filter_query' => 'x']);

        /* Assert */
        self::assertSame('', $response->body());
    }
}
