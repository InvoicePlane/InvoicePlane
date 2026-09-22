<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Units controller — application/modules/units/controllers/Units.php.
 *
 * Required fields (Mdl_Units::validation_rules): unit_name, unit_name_plrl.
 * Absorbs Issue1694UnitsDeleteCsrfTest.
 */
#[Group('units')]
#[CoversClass(\Units::class)]
class UnitsControllerTest extends AbstractTestCase
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
    public function it_lists_every_unit(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_units', ['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);
        $this->databaseInsert('ip_units', ['unit_name' => 'Kilogram', 'unit_name_plrl' => 'Kilograms']);

        /* Act */
        $response = $this->get('/units');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Hour');
        $this->assertResponseBodyContains($response, 'Kilogram');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_unit(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Litre',
            'unit_name_plrl' => 'Litres',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'units');
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Litre', 'unit_name_plrl' => 'Litres']);
        $this->assertDatabaseCount('ip_units', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_unit_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => '',
            'unit_name_plrl' => 'Nameless Plural',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_units', ['unit_name_plrl' => 'Nameless Plural']);
        $this->assertDatabaseCount('ip_units', 0);
    }

    #[Test]
    public function it_fails_to_create_without_unit_name_plrl(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Singular Only',
            'unit_name_plrl' => '',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Singular Only']);
        $this->assertDatabaseCount('ip_units', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_unit_only(): void
    {
        /* Arrange */
        $target = $this->databaseInsert('ip_units', ['unit_name' => 'Editable Metre', 'unit_name_plrl' => 'Editable Metres']);
        $this->databaseInsert('ip_units', ['unit_name' => 'Other Metre', 'unit_name_plrl' => 'Other Metres']);

        /* Act */
        $response = $this->get('/units/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Metres');
        $this->assertResponseBodyNotContains($response, 'Other Metre');
    }

    #[Test]
    public function it_updates_a_unit(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_units', ['unit_name' => 'Original Litre', 'unit_name_plrl' => 'Original Litres']);

        /* Act */
        $response = $this->post('/units/form/' . $id, [
            'unit_name'      => 'Renamed Litre',
            'unit_name_plrl' => 'Renamed Litres',
            'is_update'      => '1',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'units');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $id, 'unit_name' => 'Renamed Litre', 'unit_name_plrl' => 'Renamed Litres']);
        $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Original Litre']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_unit_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_units', ['unit_name' => 'Keep This Unit', 'unit_name_plrl' => 'Keep These Units']);

        /* Act */
        $response = $this->post('/units/form/' . $id, [
            'unit_name'      => '',
            'unit_name_plrl' => 'Keep These Units',
            'is_update'      => '1',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $id, 'unit_name' => 'Keep This Unit']);
    }

    #[Test]
    public function it_fails_to_update_without_unit_name_plrl(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_units', ['unit_name' => 'Plural Kept Unit', 'unit_name_plrl' => 'Plural Kept Units']);

        /* Act */
        $response = $this->post('/units/form/' . $id, [
            'unit_name'      => 'Plural Kept Unit',
            'unit_name_plrl' => '',
            'is_update'      => '1',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $id, 'unit_name_plrl' => 'Plural Kept Units']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_unit(): void
    {
        /* Arrange */
        $id   = $this->databaseInsert('ip_units', ['unit_name' => 'Deletable Gram', 'unit_name_plrl' => 'Deletable Grams']);
        $keep = $this->databaseInsert('ip_units', ['unit_name' => 'Kept Gram', 'unit_name_plrl' => 'Kept Grams']);

        /* Act */
        $response = $this->post('/units/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'units');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $id]);
        $this->assertDatabaseHas('ip_units', ['unit_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_unit_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_units', ['unit_name' => 'CSRF Unit', 'unit_name_plrl' => 'CSRF Units']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/units/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'units');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_unit_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_units', ['unit_name' => 'CSRF Unit Kept', 'unit_name_plrl' => 'CSRF Units Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/units/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $id, 'unit_name' => 'CSRF Unit Kept']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_unit_name_on_create(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_units', ['unit_name' => 'Duplicate Unit', 'unit_name_plrl' => 'Duplicate Units']);

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Duplicate Unit',
            'unit_name_plrl' => 'Duplicate Units Again',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertDatabaseCount('ip_units', 1);
        $this->assertDatabaseMissing('ip_units', ['unit_name_plrl' => 'Duplicate Units Again']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_unit(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_units', ['unit_name' => 'Secret Unit', 'unit_name_plrl' => 'Secret Units']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/units');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Unit');
    }
}
