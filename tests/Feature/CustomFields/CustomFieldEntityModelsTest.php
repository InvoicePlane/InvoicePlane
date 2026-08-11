<?php

namespace Tests\Feature\CustomFields;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class CustomFieldEntityModelsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_loads_allowed_custom_field_tables_and_positions_for_the_form(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->get('/custom_fields/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        foreach (['ip_client_custom', 'ip_invoice_custom', 'ip_payment_custom', 'ip_quote_custom', 'ip_user_custom'] as $table) {
            $this->assertResponseBodyContains($response, $table);
        }
        foreach (['Account Information', 'Contact Information', 'Properties', 'Taxes Information'] as $position) {
            $this->assertResponseBodyContains($response, $position);
        }
    }
}
