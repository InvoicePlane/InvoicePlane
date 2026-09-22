<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Custom_Values controller — application/modules/custom_values/controllers/Custom_values.php.
 *
 * A custom value is one option of a choice-type custom field. Required field
 * (Mdl_Custom_Values::validation_rules): custom_values_value. Routes:
 *   list   GET  /custom_values/field/{field_id}
 *   create POST /custom_values/create/{field_id}   -> /custom_values/field/{field_id}
 *   edit   POST /custom_values/edit/{id}           -> /custom_values/field/{field_id}
 *   delete POST /custom_values/delete/{id}         -> /custom_values/field/{field_id}
 * Absorbs Issue1694CustomValuesDeleteCsrfTest.
 */
#[Group('custom_values')]
#[CoversClass(\Custom_Values::class)]
class CustomValuesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_value_for_a_field(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();
        $this->seedValue($fieldId, 'Enterprise');
        $this->seedValue($fieldId, 'Small Business');

        /* Act */
        $response = $this->get('/custom_values/field/' . $fieldId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Enterprise');
        $this->assertResponseBodyContains($response, 'Small Business');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_value_for_a_field(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();

        /* Act */
        $response = $this->post('/custom_values/create/' . $fieldId, [
            'custom_field_id'     => (string) $fieldId,
            'custom_values_value' => 'Non Profit',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful create redirects back to the field value list.');
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_field' => $fieldId, 'custom_values_value' => 'Non Profit']);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_a_value_without_custom_values_value(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();

        /* Act */
        $response = $this->post('/custom_values/create/' . $fieldId, [
            'custom_field_id'     => (string) $fieldId,
            'custom_values_value' => '',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_custom_values', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_updates_a_value(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'Original Segment');

        /* Act */
        $response = $this->post('/custom_values/edit/' . $id, [
            'custom_field_id'     => (string) $fieldId,
            'custom_values_value' => 'Renamed Segment',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful update redirects back to the field value list.');
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $id, 'custom_values_value' => 'Renamed Segment']);
        $this->assertDatabaseMissing('ip_custom_values', ['custom_values_value' => 'Original Segment']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_a_value_without_custom_values_value(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'Keep This Segment');

        /* Act */
        $response = $this->post('/custom_values/edit/' . $id, [
            'custom_field_id'     => (string) $fieldId,
            'custom_values_value' => '',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $id, 'custom_values_value' => 'Keep This Segment']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_value(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'Deletable Segment');
        $keep    = $this->seedValue($fieldId, 'Kept Segment');

        /* Act */
        $response = $this->post('/custom_values/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful delete redirects back to the field value list.');
        $this->assertDatabaseMissing('ip_custom_values', ['custom_values_id' => $id]);
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_value_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'CSRF Segment');

        /* Act */
        $response = $this->postWithValidCsrfToken('/custom_values/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_custom_values', ['custom_values_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_value_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'CSRF Segment Kept');

        /* Act */
        $response = $this->postWithoutCsrfToken('/custom_values/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $id, 'custom_values_value' => 'CSRF Segment Kept']);
    }

    #[Test]
    public function it_does_not_delete_a_value_on_a_plain_get_request(): void
    {
        /* Arrange */
        // Base_Controller::__construct() 404s any GET whose URL contains
        // "delete" before the controller (and ensure_valid_post_request())
        // ever runs — a distinct, earlier guard than the CSRF-token checks
        // above, and one no test here currently exercises.
        $fieldId = $this->seedChoiceField();
        $id      = $this->seedValue($fieldId, 'GET Segment Kept');

        /* Act */
        $response = $this->get('/custom_values/delete/' . $id);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'The global GET-to-delete gate in Base_Controller must reject this before it is acted on.');
        $this->assertDatabaseHas('ip_custom_values', ['custom_values_id' => $id, 'custom_values_value' => 'GET Segment Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_value(): void
    {
        /* Arrange */
        $fieldId = $this->seedChoiceField();
        $this->seedValue($fieldId, 'Secret Segment');
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/custom_values/field/' . $fieldId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Segment');
    }

    private function seedChoiceField(): int
    {
        return $this->databaseInsert('ip_custom_fields', [
            'custom_field_table' => 'ip_client_custom',
            'custom_field_label' => 'Client Segment ' . bin2hex(random_bytes(3)),
            'custom_field_type'  => 'MULTIPLE-CHOICE',
        ]);
    }

    private function seedValue(int $fieldId, string $value): int
    {
        return $this->databaseInsert('ip_custom_values', [
            'custom_values_field' => $fieldId,
            'custom_values_value' => $value,
        ]);
    }
}
