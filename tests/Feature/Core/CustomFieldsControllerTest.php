<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Custom_Fields controller — application/modules/custom_fields/controllers/Custom_fields.php.
 *
 * Required fields (Mdl_Custom_Fields::validation_rules): custom_field_table
 * (must be one of the ip_*_custom tables), custom_field_label, custom_field_type.
 * Absorbs CustomFieldsServiceTest and Issue1694CustomFieldsDeleteCsrfTest.
 */
#[Group('custom_fields')]
#[CoversClass(\Custom_Fields::class)]
class CustomFieldsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const REQUIRED = [
        'custom_field_table' => 'ip_client_custom',
        'custom_field_label' => 'Payload Field',
        'custom_field_type'  => 'TEXT',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_custom_field(): void
    {
        /* Arrange */
        $this->seedField(['custom_field_label' => 'Client Loyalty Tier']);
        $this->seedField(['custom_field_label' => 'Client Referral Source']);

        /* Act */
        $response = $this->get('/custom_fields/table/all');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Client Loyalty Tier');
        $this->assertResponseBodyContains($response, 'Client Referral Source');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_custom_field(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/custom_fields/form', array_merge(self::REQUIRED, [
            'custom_field_label' => 'Client Industry',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'custom_fields');
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_label' => 'Client Industry', 'custom_field_table' => 'ip_client_custom']);
        $this->assertDatabaseCount('ip_custom_fields', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_custom_field_table(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/custom_fields/form', array_merge(self::REQUIRED, [
            'custom_field_table' => '',
            'custom_field_label' => 'Tableless Field',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_custom_fields', 0);
    }

    #[Test]
    public function it_fails_to_create_without_custom_field_label(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/custom_fields/form', array_merge(self::REQUIRED, [
            'custom_field_label' => '',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_custom_fields', 0);
    }

    #[Test]
    public function it_fails_to_create_without_custom_field_type(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/custom_fields/form', array_merge(self::REQUIRED, [
            'custom_field_label' => 'Typeless Field',
            'custom_field_type'  => '',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_label' => 'Typeless Field']);
    }

    #[Test]
    public function it_rejects_a_custom_field_table_that_is_not_allow_listed(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/custom_fields/form', array_merge(self::REQUIRED, [
            'custom_field_table' => 'ip_users',
            'custom_field_label' => 'Injected Field',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A non-allow-listed table must be rejected by validation.');
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_label' => 'Injected Field']);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_custom_field_only(): void
    {
        /* Arrange */
        $target = $this->seedField(['custom_field_label' => 'Editable Custom Field']);
        $this->seedField(['custom_field_label' => 'Other Custom Field']);

        /* Act */
        $response = $this->get('/custom_fields/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Custom Field');
        $this->assertResponseBodyNotContains($response, 'Other Custom Field');
    }

    #[Test]
    public function it_updates_a_custom_field(): void
    {
        /* Arrange */
        $id = $this->seedField(['custom_field_label' => 'Original Custom Field']);

        /* Act */
        $response = $this->post('/custom_fields/form/' . $id, array_merge(self::REQUIRED, [
            'custom_field_label' => 'Renamed Custom Field',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'custom_fields');
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $id, 'custom_field_label' => 'Renamed Custom Field']);
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_label' => 'Original Custom Field']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_custom_field_label(): void
    {
        /* Arrange */
        $id = $this->seedField(['custom_field_label' => 'Keep This Field']);

        /* Act */
        $response = $this->post('/custom_fields/form/' . $id, array_merge(self::REQUIRED, [
            'custom_field_label' => '',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $id, 'custom_field_label' => 'Keep This Field']);
    }

    #[Test]
    public function it_fails_to_update_without_custom_field_type(): void
    {
        /* Arrange */
        $id = $this->seedField(['custom_field_label' => 'Type Kept Field', 'custom_field_type' => 'TEXT']);

        /* Act */
        $response = $this->post('/custom_fields/form/' . $id, array_merge(self::REQUIRED, [
            'custom_field_label' => 'Type Kept Field',
            'custom_field_type'  => '',
            'btn_submit'         => '1',
        ]));

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $id, 'custom_field_type' => 'TEXT']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_custom_field(): void
    {
        /* Arrange */
        $id   = $this->seedField(['custom_field_label' => 'Deletable Field']);
        $keep = $this->seedField(['custom_field_label' => 'Kept Field']);

        /* Act */
        $response = $this->post('/custom_fields/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful delete redirects to the referring list.');
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_id' => $id]);
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_custom_field_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedField(['custom_field_label' => 'CSRF Field']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/custom_fields/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_custom_fields', ['custom_field_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_custom_field_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedField(['custom_field_label' => 'CSRF Field Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/custom_fields/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_custom_fields', ['custom_field_id' => $id, 'custom_field_label' => 'CSRF Field Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_custom_field(): void
    {
        /* Arrange */
        $this->seedField(['custom_field_label' => 'Secret Custom Field']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/custom_fields/table/all');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Custom Field');
    }

    /** @param array<string,mixed> $overrides */
    private function seedField(array $overrides = []): int
    {
        return $this->databaseInsert('ip_custom_fields', array_merge([
            'custom_field_table' => 'ip_client_custom',
            'custom_field_label' => 'Seeded Field ' . bin2hex(random_bytes(3)),
            'custom_field_type'  => 'TEXT',
        ], $overrides));
    }
}
