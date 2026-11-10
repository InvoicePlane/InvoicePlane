<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression coverage for two fixes already shipped on prep/v180 but never carried to any
 * upstream-facing security PR of their own: ip_users.user_passwordreset_token stores a
 * SHA-256 digest of the reset token (never the raw value), and the new-password POST
 * enforces the same 8-character minimum as Mdl_Users::validation_rules().
 *
 * The digest-storage property is pinned from the read side: Sessions::passwordreset()'s
 * token-link (GET) flow hashes its input before comparing against the stored column, so a
 * column holding the raw token (the pre-fix bug, or a future regression that reverts the
 * write side) can never satisfy a legitimate reset link — the only value that can ever
 * match is the token's own SHA-256 digest. Combined with IpSecurityHelperTest's direct
 * pin of hash_password_reset_token() itself, this closes the loop: the write and read
 * sides must agree on the digest, not the raw value, for the feature to work at all.
 */
#[Group('feature')]
#[Group('security')]
#[Group('sessions')]
class PasswordResetTokenStorageSecurityTest extends AbstractTestCase
{
    private const TOKEN = 'ef260948cd51e1728a24ee672433e12757465c964269fd24d692b8980ecc2cf3';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_does_not_accept_a_raw_token_stored_in_place_of_its_digest(): void
    {
        /* Arrange: simulate the pre-fix bug — the raw token stored as-is, not its digest. */
        $this->seedUserWithResetToken(self::TOKEN, gmdate('Y-m-d H:i:s', time() + 600));

        /* Act: visit the reset link with that same value. */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert: the GET flow hashes the input before comparing, so a raw-stored value
         * can never match — proving the column must hold a digest, not the raw token. */
        self::assertTrue($response->isRedirect(), 'A raw-stored token must not be accepted as a valid reset link.');
        $this->assertResponseBodyNotContains($response, 'btn_new_password');
    }

    #[Test]
    public function it_accepts_the_real_token_when_the_column_holds_its_digest(): void
    {
        /* Arrange: the column holds hash('sha256', TOKEN), as the generation path writes. */
        $this->seedUserWithResetToken(hash('sha256', self::TOKEN), gmdate('Y-m-d H:i:s', time() + 600));

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert: the legitimate digest-backed flow still works (positive control). */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'btn_new_password');
    }

    #[Test]
    public function it_rejects_a_new_password_shorter_than_eight_characters(): void
    {
        /* Arrange: a valid, unexpired token. */
        $userId = $this->seedUserWithResetToken(hash('sha256', self::TOKEN), gmdate('Y-m-d H:i:s', time() + 600));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'Short1!',
        ]);

        /* Assert: rejected, and the password is unchanged. */
        self::assertTrue($response->isRedirect(), 'A too-short new password must redirect, not change the password.');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(
            $before['user_password'],
            $after['user_password'],
            'A new password under 8 characters must never be saved.'
        );
    }

    #[Test]
    public function it_accepts_a_new_password_of_exactly_eight_characters(): void
    {
        /* Arrange: a valid, unexpired token (positive control for the boundary). */
        $userId = $this->seedUserWithResetToken(hash('sha256', self::TOKEN), gmdate('Y-m-d H:i:s', time() + 600));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'Exactly8',
        ]);

        /* Assert: an 8-character password is accepted and actually changes the password. */
        self::assertTrue($response->isRedirect(), 'A valid new-password submission must redirect.');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame(
            $before['user_password'],
            $after['user_password'],
            'An 8-character new password must be accepted and saved.'
        );
    }

    private function seedUserWithResetToken(string $storedTokenColumnValue, string $expiry): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'resettarget_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'reset+' . bin2hex(random_bytes(3)) . '@example.com',
            'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(10)),
            'user_type'                       => 1,
            'user_active'                     => 1,
            'user_passwordreset_token'        => $storedTokenColumnValue,
            'user_passwordreset_token_expiry' => $expiry,
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }
}
