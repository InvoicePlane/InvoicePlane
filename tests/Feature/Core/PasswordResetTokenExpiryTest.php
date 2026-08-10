<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use Tests\AbstractTestCase;

/**
 * Regression coverage for password-reset token expiry enforcement.
 *
 * The token-link (GET) flow already rejected an expired token, but the
 * password-change (POST / btn_new_password) flow only checked that the
 * submitted token matched the stored token — never its expiry — so an
 * expired-but-still-stored token could still change the password.
 *
 * These tests pin both the security guarantee (an expired token cannot change
 * the password on either flow) and the backward-compatible behaviour (a token
 * with no stored expiry, and an unexpired token, still work).
 */
#[Group('feature')]
#[Group('security')]
#[Group('sessions')]
class PasswordResetTokenExpiryTest extends AbstractTestCase
{
    private const TOKEN = 'ef260948cd51e1728a24ee672433e12757465c964269fd24d692b8980ecc2cf3';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    /**
     * Seed an active user holding a password-reset token with the given expiry.
     *
     * @param string|null $expiry UTC 'Y-m-d H:i:s', or null for a legacy token with no expiry
     */
    private function seedUserWithResetToken(?string $expiry): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'resettarget_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'reset+' . bin2hex(random_bytes(3)) . '@example.com',
            'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(10)),
            'user_type'                       => 1,
            'user_active'                     => 1,
            'user_passwordreset_token'        => self::TOKEN,
            'user_passwordreset_token_expiry' => $expiry,
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_a_password_change_when_the_reset_token_has_expired(): void
    {
        /* Arrange: a reset token whose 15-minute lifetime elapsed 5 minutes ago. */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act: submit the password-change POST with the correct (but expired) token. */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'HackedPass123!',
            'new_passwordv'    => 'HackedPass123!',
        ]);

        /* Assert: the request is rejected and the stored password is unchanged. */
        self::assertTrue(
            $response->isRedirect(),
            'An expired-token password change must redirect, not render the form.'
        );

        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(
            $before['user_password'],
            $after['user_password'],
            'An expired reset token must NOT be able to change the password (POST-side expiry bypass).'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_clears_the_expired_token_after_a_rejected_password_change(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

        /* Act */
        $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'HackedPass123!',
            'new_passwordv'    => 'HackedPass123!',
        ]);

        /* Assert: the burnt, expired token is cleared so it cannot be retried. */
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(
            '',
            (string) $after['user_passwordreset_token'],
            'An expired reset token must be cleared from the user row after a rejected attempt.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_a_password_change_with_a_valid_unexpired_token(): void
    {
        /* Arrange: a token that is valid for another 10 minutes. */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'BrandNewPass123!',
            'new_passwordv'    => 'BrandNewPass123!',
        ]);

        /* Assert: the happy path still works — the password is changed. */
        self::assertTrue($response->isRedirect(), 'A valid-token password change must redirect.');

        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame(
            $before['user_password'],
            $after['user_password'],
            'A valid, unexpired reset token must still be able to change the password.'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_a_password_change_when_no_expiry_is_stored(): void
    {
        /* Arrange: a legacy token issued before the expiry column existed (NULL expiry). */
        $userId = $this->seedUserWithResetToken(null);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'BrandNewPass123!',
            'new_passwordv'    => 'BrandNewPass123!',
        ]);

        /* Assert: with no stored expiry there is nothing to enforce, so it succeeds. */
        self::assertTrue($response->isRedirect(), 'A no-expiry token password change must redirect.');

        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame(
            $before['user_password'],
            $after['user_password'],
            'A token with no stored expiry must still be able to change the password (backward compatibility).'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_the_reset_link_when_the_token_has_expired(): void
    {
        /* Arrange */
        $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

        /* Act: open the reset link (GET) carrying the expired token. */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert: the GET flow must not render the new-password form for an expired token. */
        self::assertTrue(
            $response->isRedirect(),
            'The reset link for an expired token must redirect, not render the new-password form.'
        );
        $this->assertResponseBodyNotContains($response, 'btn_new_password');
    }
}
