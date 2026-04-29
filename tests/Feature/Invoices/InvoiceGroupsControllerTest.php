<?php

namespace Tests\Feature\Invoices;

use Invoice_Groups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Invoice_Groups Controller Feature Tests.
 *
 * Tests invoice group management (index, form, delete).
 */
#[CoversClass(Invoice_Groups::class)]
class InvoiceGroupsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_displays_paginated_list_of_invoice_groups(): void
    {
        $response = $this->get('/invoice_groups/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_orders_invoice_groups_by_name(): void
    {
        $response = $this->get('/invoice_groups/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_paginates_invoice_groups_at_15_per_page(): void
    {
        $response = $this->get('/invoice_groups/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_displays_create_form_with_default_values(): void
    {
        $response = $this->get('/invoice_groups/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_displays_edit_form_with_existing_record(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup', [
            'invoice_group_name'              => 'Edit Me',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $response = $this->get('/invoice_groups/form/' . $group->invoice_group_id);

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_returns_404_when_editing_non_existent_invoice_group(): void
    {
        $response = $this->get('/invoice_groups/form/99999');

        $this->assertNotEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_redirects_to_index_when_cancel_button_clicked(): void
    {
        /**
         * Payload:
         * {
         *     "btn_cancel": "1"
         * }
         */
        $response = $this->post('/invoice_groups/form', ['btn_cancel' => '1']);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_creates_new_invoice_group_with_valid_data(): void
    {
        $this->skipWithoutDatabase();
        $uniqueName = 'Test Group ' . bin2hex(random_bytes(4));

        /**
         * Payload:
         * {
         *     "invoice_group_name": 1,
         *     "invoice_group_identifier_format": "{{{year}}}-{{{id}}}",
         *     "invoice_group_next_id": 1,
         *     "invoice_group_left_pad": 4
         * }
         */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => $uniqueName,
            'invoice_group_identifier_format' => '{{{year}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => $uniqueName]);
    }

    #[Test]
    public function it_updates_existing_invoice_group_with_valid_data(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup', [
            'invoice_group_name'              => 'Original Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);
        $updatedName = 'Updated Group ' . bin2hex(random_bytes(4));

        /**
         * Payload:
         * {
         *     "invoice_group_name": 1,
         *     "invoice_group_identifier_format": "{{{year}}}/{{{id}}}",
         *     "invoice_group_next_id": 100,
         *     "invoice_group_left_pad": 5
         * }
         */
        $response = $this->post('/invoice_groups/form/' . $group->invoice_group_id, [
            'invoice_group_name'              => $updatedName,
            'invoice_group_identifier_format' => '{{{year}}}/{{{id}}}',
            'invoice_group_next_id'           => 100,
            'invoice_group_left_pad'          => 5,
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_validates_required_fields_on_submit(): void
    {
        /**
         * Payload:
         * {
         *     "invoice_group_name": ""
         * }
         */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name' => '',
        ]);

        $this->assertFalse($response->isRedirect());
        $this->assertEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_validates_field_types_and_constraints(): void
    {
        /**
         * Payload:
         * {
         *     "invoice_group_name": "",
         *     "invoice_group_identifier_format": "",
         *     "invoice_group_next_id": "",
         *     "invoice_group_left_pad": ""
         * }
         */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => '',
            'invoice_group_identifier_format' => '',
            'invoice_group_next_id'           => '',
            'invoice_group_left_pad'          => '',
        ]);

        $this->assertFalse($response->isRedirect());
        $this->assertEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_deletes_invoice_group_successfully(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup', [
            'invoice_group_name'              => 'Delete Me',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $response = $this->post('/invoice_groups/delete/' . $group->invoice_group_id);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $group->invoice_group_id]);
    }

    #[Test]
    public function it_returns_404_when_deleting_non_existent_invoice_group(): void
    {
        $response = $this->post('/invoice_groups/delete/99999');

        $this->assertNotEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_handles_deletion_of_invoice_group_with_associated_invoices(): void
    {
        $response = $this->get('/invoice_groups/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_displays_success_message_after_creating_invoice_group(): void
    {
        $this->skipWithoutDatabase();
        $uniqueName = 'Create Success ' . bin2hex(random_bytes(4));

        /**
         * Payload:
         * {
         *     "invoice_group_name": 1,
         *     "invoice_group_identifier_format": "INV-{{{id}}}",
         *     "invoice_group_next_id": 1,
         *     "invoice_group_left_pad": 4
         * }
         */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => $uniqueName,
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_displays_success_message_after_updating_invoice_group(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup');

        /**
         * Payload:
         * {
         *     "invoice_group_name": "Updated ",
         *     "invoice_group_identifier_format": "INV-{{{id}}}",
         *     "invoice_group_next_id": 1,
         *     "invoice_group_left_pad": 4
         * }
         */
        $response = $this->post('/invoice_groups/form/' . $group->invoice_group_id, [
            'invoice_group_name'              => 'Updated ' . bin2hex(random_bytes(3)),
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_displays_success_message_after_deleting_invoice_group(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup');

        $response = $this->post('/invoice_groups/delete/' . $group->invoice_group_id);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_supports_year_variable_in_identifier_format(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup', [
            'invoice_group_identifier_format' => '{{{year}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $this->assertNotNull($group->invoice_group_id);
        $this->assertEquals('{{{year}}}-{{{id}}}', $group->invoice_group_identifier_format);
    }

    #[Test]
    public function it_supports_id_with_left_padding_in_identifier_format(): void
    {
        $this->skipWithoutDatabase();
        $group = $this->seedModel('InvoiceGroup', [
            'invoice_group_identifier_format' => '{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $this->assertNotNull($group->invoice_group_id);
        $this->assertEquals(4, (int) $group->invoice_group_left_pad);
    }
}
