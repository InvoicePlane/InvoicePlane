<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class CustomValuesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/custom_values');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/custom_values');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/custom_values] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_creates_a_custom_value_for_an_allowed_custom_field(): void
    {
        /* Arrange */
        $fieldId = $this->databaseInsert('ip_custom_fields', [
            'custom_field_table'    => 'ip_client_custom',
            'custom_field_label'    => 'Client Tier',
            'custom_field_type'     => 'SINGLE-CHOICE',
            'custom_field_order'    => 1,
            'custom_field_location' => 0,
        ]);

        /* Act */
        $response = $this->post('/custom_values/create/' . $fieldId, [
            'custom_values_value' => 'Gold',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful custom value create must redirect.');
        $this->assertDatabaseHas('ip_custom_values', [
            'custom_values_field' => $fieldId,
            'custom_values_value' => 'Gold',
        ]);
    }

    #[Test]
    public function it_does_not_create_orphan_custom_values_for_missing_fields(): void
    {
        /* Act */
        $response = $this->post('/custom_values/create/999999', [
            'custom_values_value' => 'Orphan',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Missing custom field create must return without saving.');
        $this->assertDatabaseMissing('ip_custom_values', ['custom_values_value' => 'Orphan']);
    }
}
