<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Users controller — application/modules/users/controllers/Users.php.
 *
 * Create rules (Mdl_Users::validation_rules): user_type, user_email (unique),
 * user_name, user_password (min 8), user_passwordv (matches), user_language.
 * Update rules (validation_rules_existing): user_type, user_email, user_name,
 * user_language. The baseline seed always creates the primary admin (id 1),
 * which Users::delete() refuses to remove. Absorbs UsersServiceTest and
 * Issue1694UsersDeleteCsrfTest. user-client assignment lives in
 * UserClientsControllerTest.
 */
#[Group('users')]
#[CoversClass(\Users::class)]
class UsersControllerTest extends AbstractTestCase
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
    public function it_lists_every_user(): void
    {
        /* Arrange */
        $this->seedUser(['user_name' => 'Dana Accountant']);
        $this->seedUser(['user_name' => 'Eli Bookkeeper']);

        /* Act */
        $response = $this->get('/users');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Dana Accountant');
        $this->assertResponseBodyContains($response, 'Eli Bookkeeper');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_user(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => 'Frank Clerk',
            'user_email'     => 'frank.clerk@test.local',
            'user_password'  => 'sup3rsecret',
            'user_passwordv' => 'sup3rsecret',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_email' => 'frank.clerk@test.local', 'user_name' => 'Frank Clerk']);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_user_email(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => 'No Email User',
            'user_email'     => '',
            'user_password'  => 'sup3rsecret',
            'user_passwordv' => 'sup3rsecret',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_users', ['user_name' => 'No Email User']);
    }

    #[Test]
    public function it_fails_to_create_without_user_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => '',
            'user_email'     => 'no.name@test.local',
            'user_password'  => 'sup3rsecret',
            'user_passwordv' => 'sup3rsecret',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_users', ['user_email' => 'no.name@test.local']);
    }

    #[Test]
    public function it_fails_to_create_without_user_password(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => 'No Password User',
            'user_email'     => 'no.password@test.local',
            'user_password'  => '',
            'user_passwordv' => '',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_users', ['user_email' => 'no.password@test.local']);
    }

    #[Test]
    public function it_rejects_a_mismatched_password_confirmation_on_create(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => 'Mismatch User',
            'user_email'     => 'mismatch@test.local',
            'user_password'  => 'sup3rsecret',
            'user_passwordv' => 'differentpass',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A mismatched confirmation must be rejected.');
        $this->assertDatabaseMissing('ip_users', ['user_email' => 'mismatch@test.local']);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_user_only(): void
    {
        /* Arrange */
        $target = $this->seedUser(['user_name' => 'Editable User Person']);
        $this->seedUser(['user_name' => 'Other User Person']);

        /* Act */
        $response = $this->get('/users/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable User Person');
        $this->assertResponseBodyNotContains($response, 'Other User Person');
    }

    #[Test]
    public function it_updates_a_user_without_touching_the_password(): void
    {
        /**
         * POST /users/form/{id}
         * {
         *   "user_type": "2",
         *   "user_name": "Renamed User Name",
         *   "user_email": "update.me@test.local",
         *   "user_language": "system",
         *   "btn_submit": "1"
         * }.
         */
        /* Arrange */
        $id             = $this->seedUser(['user_name' => 'Original User Name', 'user_email' => 'update.me@test.local']);
        $passwordBefore = $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'];

        /* Act */
        $response = $this->post('/users/form/' . $id, [
            'user_type'     => '2',
            'user_name'     => 'Renamed User Name',
            'user_email'    => 'update.me@test.local',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_id' => $id, 'user_name' => 'Renamed User Name']);
        $this->assertDatabaseMissing('ip_users', ['user_name' => 'Original User Name']);
        self::assertSame(
            $passwordBefore,
            $this->databaseFetchOne('ip_users', ['user_id' => $id])['user_password'],
            'An update that carries no new password must leave the stored hash untouched.'
        );
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_user_email(): void
    {
        /* Arrange */
        $id = $this->seedUser(['user_name' => 'Keep This User', 'user_email' => 'keep.me@test.local']);

        /* Act */
        $response = $this->post('/users/form/' . $id, [
            'user_type'     => '2',
            'user_name'     => 'Keep This User',
            'user_email'    => '',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_users', ['user_id' => $id, 'user_email' => 'keep.me@test.local']);
    }

    #[Test]
    public function it_fails_to_update_without_user_name(): void
    {
        /* Arrange */
        $id = $this->seedUser(['user_name' => 'Name Kept User']);

        /* Act */
        $response = $this->post('/users/form/' . $id, [
            'user_type'     => '2',
            'user_name'     => '',
            'user_email'    => 'name.kept@test.local',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_users', ['user_id' => $id, 'user_name' => 'Name Kept User']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_secondary_user(): void
    {
        /* Arrange */
        $id   = $this->seedUser(['user_name' => 'Deletable User']);
        $keep = $this->seedUser(['user_name' => 'Kept User']);

        /* Act */
        $response = $this->post('/users/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseMissing('ip_users', ['user_id' => $id]);
        $this->assertDatabaseHas('ip_users', ['user_id' => $keep]);
    }

    #[Test]
    public function it_never_deletes_the_primary_admin(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/users/delete/1', []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_id' => 1]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_user_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedUser(['user_name' => 'CSRF User']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseMissing('ip_users', ['user_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_user_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedUser(['user_name' => 'CSRF User Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/users/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_users', ['user_id' => $id, 'user_name' => 'CSRF User Kept']);
    }

    #[Test]
    public function it_does_not_delete_a_user_on_a_plain_get_request(): void
    {
        /* Arrange */
        // Base_Controller::__construct() 404s any GET whose URL contains
        // "delete" before the controller (and ensure_valid_post_request())
        // ever runs — a distinct, earlier guard than the CSRF-token tests
        // above.
        $id = $this->seedUser(['user_name' => 'GET User Kept']);

        /* Act */
        $response = $this->get('/users/delete/' . $id);

        /* Assert */
        self::assertSame(404, $response->statusCode(), 'The global GET-to-delete gate in Base_Controller must reject this before it is acted on.');
        $this->assertDatabaseHas('ip_users', ['user_id' => $id, 'user_name' => 'GET User Kept']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_duplicate_user_email_on_create(): void
    {
        /* Arrange */
        $this->seedUser(['user_email' => 'taken@test.local']);

        /* Act */
        $response = $this->post('/users/form', [
            'user_type'      => '2',
            'user_name'      => 'Second Taken',
            'user_email'     => 'taken@test.local',
            'user_password'  => 'sup3rsecret',
            'user_passwordv' => 'sup3rsecret',
            'user_language'  => 'system',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A duplicate email must be rejected by is_unique.');
        $this->assertDatabaseCount('ip_users', 1, ['user_email' => 'taken@test.local']);
        $this->assertDatabaseMissing('ip_users', ['user_name' => 'Second Taken']);
    }

    #[Test]
    public function it_prevents_a_non_primary_admin_from_changing_another_users_password(): void
    {
        /**
         * POST /users/change_password/{victim_id}
         * {
         *   "user_password": "attacker-chosen-password",
         *   "user_password_confirm": "attacker-chosen-password",
         *   "btn_submit": "1"
         * }.
         */
        /* Arrange */
        $attackerId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Attacker Admin',
            'user_password'      => password_hash('attacker-secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'attacker-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $victimHash = password_hash('victim-secret', PASSWORD_DEFAULT);
        $victimId   = $this->databaseInsert('ip_users', [
            'user_name'          => 'Victim Admin',
            'user_password'      => $victimHash,
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'victim-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $this->actingAsAdmin($attackerId);

        /* Act */
        $response = $this->post('/users/change_password/' . $victimId, [
            'user_password'         => 'attacker-chosen-password',
            'user_password_confirm' => 'attacker-chosen-password',
            'btn_submit'            => '1',
        ]);

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'Only the primary admin (user_id 1) may change another user\'s password.');
        $victim = $this->databaseFetchOne('ip_users', ['user_id' => $victimId]);
        self::assertSame(
            $victimHash,
            $victim['user_password'],
            'A non-primary admin must not be able to mutate another user\'s password hash.'
        );
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_user(): void
    {
        /* Arrange */
        $this->seedUser(['user_name' => 'Secret User Person']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/users');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret User Person');
    }

    /** @param array<string,mixed> $overrides */
    private function seedUser(array $overrides = []): int
    {
        return $this->databaseInsert('ip_users', array_merge([
            'user_type'          => 2,
            'user_name'          => 'Seeded User ' . bin2hex(random_bytes(3)),
            'user_email'         => 'seed+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('secret123', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));
    }
}
