<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Families controller — application/modules/families/controllers/Families.php.
 *
 * Required fields (Mdl_Families::validation_rules): family_name.
 * Absorbs Issue1694FamiliesDeleteCsrfTest.
 */
#[Group('families')]
#[CoversClass(\Families::class)]
class FamiliesControllerTest extends AbstractTestCase
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
    public function it_lists_every_family(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_families', ['family_name' => 'Beverages']);
        $this->databaseInsert('ip_families', ['family_name' => 'Hardware']);

        /* Act */
        $response = $this->get('/families');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Beverages');
        $this->assertResponseBodyContains($response, 'Hardware');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_family(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => 'Stationery',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'families');
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Stationery']);
        $this->assertDatabaseCount('ip_families', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_family_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => '',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_families', 0);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_family_only(): void
    {
        /* Arrange */
        $target = $this->databaseInsert('ip_families', ['family_name' => 'Editable Family']);
        $this->databaseInsert('ip_families', ['family_name' => 'Other Family']);

        /* Act */
        $response = $this->get('/families/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Family');
        $this->assertResponseBodyNotContains($response, 'Other Family');
    }

    #[Test]
    public function it_updates_a_family(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Original Family']);

        /* Act */
        $response = $this->post('/families/form/' . $id, [
            'family_name' => 'Renamed Family',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'families');
        $this->assertDatabaseHas('ip_families', ['family_id' => $id, 'family_name' => 'Renamed Family']);
        $this->assertDatabaseMissing('ip_families', ['family_name' => 'Original Family']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_family_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Keep This Family']);

        /* Act */
        $response = $this->post('/families/form/' . $id, [
            'family_name' => '',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_families', ['family_id' => $id, 'family_name' => 'Keep This Family']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_family(): void
    {
        /* Arrange */
        $id   = $this->databaseInsert('ip_families', ['family_name' => 'Deletable Family']);
        $keep = $this->databaseInsert('ip_families', ['family_name' => 'Kept Family']);

        /* Act */
        $response = $this->post('/families/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'families');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $id]);
        $this->assertDatabaseHas('ip_families', ['family_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_family_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_families', ['family_name' => 'CSRF Family']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/families/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'families');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_family_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->databaseInsert('ip_families', ['family_name' => 'CSRF Family Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/families/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_families', ['family_id' => $id, 'family_name' => 'CSRF Family Kept']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_family_name_on_create(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_families', ['family_name' => 'Duplicate Family']);

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => 'Duplicate Family',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        $this->assertDatabaseCount('ip_families', 1);
        $this->assertDatabaseCount('ip_families', 1, ['family_name' => 'Duplicate Family']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_family(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_families', ['family_name' => 'Secret Family']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/families');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Family');
    }
}
