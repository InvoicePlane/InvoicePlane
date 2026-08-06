<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the CustomFieldsServiceTest module via CI3 HTTP harness.
 */
class CustomFieldsServiceTest extends AbstractTestCase
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
        $response = $this->get('/custom_fields');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(200),
                self::equalTo(301),
                self::equalTo(302),
                self::equalTo(303),
                self::equalTo(307),
                self::equalTo(308),
            ),
            sprintf('[GET /custom_fields] returned unexpected status [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_does_not_expose_php_errors(): void
    {
        /* Arrange */
        /* (authenticated admin via setUp) */

        /* Act */
        $response = $this->get('/custom_fields');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/custom_fields');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/custom_fields] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_creates_a_custom_field_for_an_allowed_table(): void
    {
        /* Act */
        $response = $this->post('/custom_fields/form', [
            'custom_field_table'    => 'ip_client_custom',
            'custom_field_label'    => 'Client Reference',
            'custom_field_type'     => 'TEXT',
            'custom_field_order'    => '1',
            'custom_field_location' => '0',
            'btn_submit'            => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful custom field create must redirect.');
        $this->assertDatabaseHas('ip_custom_fields', [
            'custom_field_table' => 'ip_client_custom',
            'custom_field_label' => 'Client Reference',
            'custom_field_type'  => 'TEXT',
        ]);
    }

    #[Test]
    public function it_rejects_custom_field_table_names_outside_the_allowlist(): void
    {
        /* Act */
        $response = $this->post('/custom_fields/form', [
            'custom_field_table'    => 'ip_client_custom; DROP TABLE ip_users; --',
            'custom_field_label'    => 'Injected Table',
            'custom_field_type'     => 'TEXT',
            'custom_field_order'    => '1',
            'custom_field_location' => '0',
            'btn_submit'            => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_label' => 'Injected Table']);
        $this->assertDatabaseHas('ip_users', ['user_email' => 'admin@test.local']);
    }
}
