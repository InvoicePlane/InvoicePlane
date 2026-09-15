<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Clients controller — application/modules/clients/controllers/Clients.php.
 *
 * Required fields (Mdl_Clients::validation_rules): client_name.
 * Absorbs Issue1694ClientsDeleteCsrfTest. AJAX endpoints (client notes,
 * preferences) live in ClientsAjaxControllerTest.
 */
#[Group('clients')]
#[CoversClass(\Clients::class)]
class ClientsControllerTest extends AbstractTestCase
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
    public function it_lists_every_active_client(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Northwind Traders', 'client_active' => 1]);
        $this->seedClient(['client_name' => 'Acme Industries', 'client_active' => 1]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Northwind Traders');
        $this->assertResponseBodyContains($response, 'Acme Industries');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_client(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name' => 'Globex Corporation',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful create redirects to the new client.');
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Globex Corporation']);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_client_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name'    => '',
            'client_surname' => 'Nameless',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_clients', ['client_surname' => 'Nameless']);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_client_only(): void
    {
        /* Arrange */
        $target = $this->seedClient(['client_name' => 'Editable Client Ltd']);
        $this->seedClient(['client_name' => 'Other Client Ltd']);

        /* Act */
        $response = $this->get('/clients/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Client Ltd');
        $this->assertResponseBodyNotContains($response, 'Other Client Ltd');
    }

    #[Test]
    public function it_updates_a_client(): void
    {
        /* Arrange */
        $id = $this->seedClient(['client_name' => 'Original Client']);

        /* Act */
        $response = $this->post('/clients/form/' . $id, [
            'client_name' => 'Renamed Client',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful update redirects.');
        $this->assertDatabaseHas('ip_clients', ['client_id' => $id, 'client_name' => 'Renamed Client']);
        $this->assertDatabaseMissing('ip_clients', ['client_name' => 'Original Client']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_client_name(): void
    {
        /* Arrange */
        $id = $this->seedClient(['client_name' => 'Keep This Client']);

        /* Act */
        $response = $this->post('/clients/form/' . $id, [
            'client_name' => '',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_clients', ['client_id' => $id, 'client_name' => 'Keep This Client']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_client(): void
    {
        /* Arrange */
        $id   = $this->seedClient(['client_name' => 'Deletable Client']);
        $keep = $this->seedClient(['client_name' => 'Kept Client']);

        /* Act */
        $response = $this->post('/clients/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'clients');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $id]);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_client_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedClient(['client_name' => 'CSRF Client']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/clients/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'clients');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_client_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedClient(['client_name' => 'CSRF Client Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/clients/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_clients', ['client_id' => $id, 'client_name' => 'CSRF Client Kept']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_client_name_and_surname_on_create(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Duplicate Co', 'client_surname' => 'Dup']);

        /* Act */
        $response = $this->post('/clients/form', [
            'client_name'    => 'Duplicate Co',
            'client_surname' => 'Dup',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A duplicate create redirects back to the form with a flash error.');
        $this->assertDatabaseCount('ip_clients', 1, ['client_name' => 'Duplicate Co']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_client(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Secret Client']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Client');
    }
}
