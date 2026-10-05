<?php

namespace Tests\Feature\Core;

use Ajax;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * filter/controllers/Ajax.php — read-only table-filtering endpoints (POST a
 * filter_query, get back a rendered partial). No mutation, so coverage here
 * is route-exercising + real filtering behavior + SQL-injection safety,
 * rather than required-field validation.
 */
#[CoversClass(Ajax::class)]
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
        $this->assertResponseBodyContains($response, 'FILTER-MATCH-001');
        $this->assertResponseBodyNotContains($response, 'OTHER-002');
    }

    #[Test]
    public function it_lists_every_invoice_when_the_filter_query_is_absent(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_number' => 'NOQUERY-001']);
        $this->seedInvoice($clientId, ['invoice_number' => 'NOQUERY-002']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', []);

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, 'NOQUERY-001');
        $this->assertResponseBodyContains($response, 'NOQUERY-002');
    }

    #[Test]
    public function it_treats_filter_invoices_query_as_a_literal_search_term(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_number' => 'SAFE-001']);

        /* Act: a classic tautology payload must be matched as text, not executed as SQL */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices', ['filter_query' => "' OR '1'='1"]);

        /* Assert: injection would have returned SAFE-001; a literal search term matches nothing */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyNotContains($response, 'SAFE-001');
    }

    #[Test]
    public function it_filters_quotes_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        foreach (['QUOFILTER-001', 'QUOOTHER-002'] as $number) {
            $this->databaseInsert('ip_quotes', [
                'user_id'            => 1, 'client_id' => $clientId, 'invoice_group_id' => 1, 'quote_status_id' => 2,
                'quote_date_created' => date('Y-m-d'), 'quote_date_modified' => date('Y-m-d H:i:s'),
                'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
                'quote_number'       => $number, 'quote_url_key' => bin2hex(random_bytes(16)),
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_quotes', ['filter_query' => 'QUOFILTER-001']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'QUOFILTER-001');
        $this->assertResponseBodyNotContains($response, 'QUOOTHER-002');
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
        $this->assertResponseBodyContains($response, 'FilterClientMatch');
        $this->assertResponseBodyNotContains($response, 'OtherClient');
    }

    #[Test]
    public function it_filters_custom_fields_by_query(): void
    {
        /* Arrange */
        foreach (['FilterFieldMatch', 'UnrelatedFieldLabel'] as $label) {
            $this->databaseInsert('ip_custom_fields', [
                'custom_field_table' => 'ip_client_custom', 'custom_field_label' => $label, 'custom_field_type' => 'TEXT',
                'custom_field_order' => 1, 'custom_field_location' => 0,
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_fields', ['filter_query' => 'FilterFieldMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterFieldMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedFieldLabel');
    }

    #[Test]
    public function it_filters_custom_values_by_query(): void
    {
        /* Arrange: two choice fields, each owning one value; the filter matches on the value text */
        foreach (['Matching field' => 'FilterValueMatch', 'Other field' => 'UnrelatedValueText'] as $label => $value) {
            $fieldId = $this->databaseInsert('ip_custom_fields', [
                'custom_field_table' => 'ip_client_custom', 'custom_field_label' => $label, 'custom_field_type' => 'SINGLE-CHOICE',
                'custom_field_order' => 1, 'custom_field_location' => 0,
            ]);
            $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => $value]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_values', ['filter_query' => 'FilterValueMatch']);

        /* Assert: the field owning the matching value is listed; the other field is not */
        $this->assertResponseBodyContains($response, 'Matching field');
        $this->assertResponseBodyNotContains($response, 'Other field');
    }

    #[Test]
    public function it_filters_the_values_of_the_field_named_in_the_referer(): void
    {
        /* Arrange */
        $fieldId = $this->databaseInsert('ip_custom_fields', [
            'custom_field_table' => 'ip_client_custom', 'custom_field_label' => 'Choice field', 'custom_field_type' => 'SINGLE-CHOICE',
            'custom_field_order' => 1, 'custom_field_location' => 0,
        ]);
        $otherFieldId = $this->databaseInsert('ip_custom_fields', [
            'custom_field_table' => 'ip_client_custom', 'custom_field_label' => 'Other field', 'custom_field_type' => 'SINGLE-CHOICE',
            'custom_field_order' => 2, 'custom_field_location' => 0,
        ]);
        $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'FieldScopedMatch']);
        $this->databaseInsert('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'FieldScopedOther']);
        $this->databaseInsert('ip_custom_values', ['custom_values_field' => $otherFieldId, 'custom_values_value' => 'FieldScopedMatchElsewhere']);
        $this->withServer(['HTTP_REFERER' => 'http://localhost/custom_values/field/' . $fieldId]);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_custom_values_field', ['filter_query' => 'FieldScopedMatch']);

        /* Assert: query narrows within the referer's field, and values of other fields never leak in */
        $this->assertResponseBodyContains($response, 'FieldScopedMatch');
        $this->assertResponseBodyNotContains($response, 'FieldScopedOther');
        $this->assertResponseBodyNotContains($response, 'FieldScopedMatchElsewhere');
    }

    #[Test]
    public function it_filters_projects_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->databaseInsert('ip_projects', ['client_id' => $clientId, 'project_name' => 'FilterProjectMatch']);
        $this->databaseInsert('ip_projects', ['client_id' => $clientId, 'project_name' => 'UnrelatedProjectName']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_projects', ['filter_query' => 'FilterProjectMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterProjectMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedProjectName');
    }

    #[Test]
    public function it_filters_tasks_by_query(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $projectId = $this->databaseInsert('ip_projects', ['client_id' => $clientId, 'project_name' => 'Task Filter Project']);
        foreach (['FilterTaskMatch', 'UnrelatedTaskName'] as $name) {
            $this->databaseInsert('ip_tasks', [
                'project_id' => $projectId, 'task_name' => $name, 'task_description' => '', 'task_price' => '10.00',
                'task_finish_date' => date('Y-m-d'), 'task_status' => 1, 'tax_rate_id' => 0,
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_tasks', ['filter_query' => 'FilterTaskMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterTaskMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedTaskName');
    }

    #[Test]
    public function it_filters_products_by_query(): void
    {
        /* Arrange */
        foreach (['FilterProductMatch', 'UnrelatedProductName'] as $name) {
            $this->databaseInsert('ip_products', ['product_name' => $name, 'product_description' => '', 'product_price' => '5.00']);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_products', ['filter_query' => 'FilterProductMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterProductMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedProductName');
    }

    #[Test]
    public function it_filters_users_by_query(): void
    {
        /* Arrange */
        foreach (['FilterUserMatch', 'UnrelatedUserName'] as $name) {
            $this->databaseInsert('ip_users', [
                'user_type' => 2, 'user_name' => $name, 'user_email' => strtolower($name) . '@test.local',
                'user_password' => password_hash('secret123', PASSWORD_DEFAULT), 'user_psalt' => bin2hex(random_bytes(8)),
                'user_language' => 'system', 'user_active' => 1,
                'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_users', ['filter_query' => 'FilterUserMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterUserMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedUserName');
    }

    #[Test]
    public function it_filters_families_by_query(): void
    {
        /* Arrange */
        foreach (['FilterFamilyMatch', 'UnrelatedFamilyName'] as $name) {
            $this->databaseInsert('ip_families', ['family_name' => $name]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_families', ['filter_query' => 'FilterFamilyMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterFamilyMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedFamilyName');
    }

    #[Test]
    public function it_filters_recurring_invoices_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        foreach (['RECURFILTER-001', 'RECUROTHER-002'] as $number) {
            $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => $number]);
            $this->databaseInsert('ip_invoices_recurring', [
                'invoice_id' => $invoiceId, 'recur_start_date' => date('Y-m-d'), 'recur_end_date' => null,
                'recur_frequency' => '1M', 'recur_next_date' => date('Y-m-d', strtotime('+1 month')),
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_invoices_recuring', ['filter_query' => 'RECURFILTER-001']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'RECURFILTER-001');
        $this->assertResponseBodyNotContains($response, 'RECUROTHER-002');
    }

    #[Test]
    public function it_filters_online_logs_by_query(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        foreach (['LOGMATCH-001', 'LOGOTHER-002'] as $number) {
            $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => $number]);
            $this->databaseInsert('ip_merchant_responses', [
                'invoice_id' => $invoiceId, 'merchant_response_successful' => 1, 'merchant_response_date' => date('Y-m-d'),
                'merchant_response_driver' => 'stripe', 'merchant_response' => 'ok', 'merchant_response_reference' => 'ref-' . $number,
            ]);
        }

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_online_logs', ['filter_query' => 'LOGMATCH-001']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'LOGMATCH-001');
        $this->assertResponseBodyNotContains($response, 'LOGOTHER-002');
    }

    #[Test]
    public function it_filters_archives_by_query(): void
    {
        /* Arrange */
        $folder = rtrim(UPLOADS_ARCHIVE_FOLDER, '/') . '/';
        $files  = [$folder . '2026-10-05_FILTERARCH-001.pdf', $folder . '2026-10-05_OTHERARCH-002.pdf'];
        foreach ($files as $file) {
            file_put_contents($file, '%PDF-1.4');
        }

        try {
            /* Act */
            $response = $this->ajax('POST', '/filter/ajax/filter_archives', ['filter_query' => 'FILTERARCH-001']);

            /* Assert */
            $this->assertResponseBodyContains($response, 'FILTERARCH-001');
            $this->assertResponseBodyNotContains($response, 'OTHERARCH-002');
        } finally {
            array_map('unlink', array_filter($files, 'is_file'));
        }
    }

    #[Test]
    public function it_filters_payments_by_query(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId, ['payment_note' => 'FilterPaymentMatch']);
        $this->seedPayment($invoiceId, ['payment_note' => 'UnrelatedPaymentNote']);

        /* Act */
        $response = $this->ajax('POST', '/filter/ajax/filter_payments', ['filter_query' => 'FilterPaymentMatch']);

        /* Assert */
        $this->assertResponseBodyContains($response, 'FilterPaymentMatch');
        $this->assertResponseBodyNotContains($response, 'UnrelatedPaymentNote');
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->post('/filter/ajax/filter_invoices', ['filter_query' => 'x']);

        /* Assert */
        self::assertSame('', $response->body());
    }
}
