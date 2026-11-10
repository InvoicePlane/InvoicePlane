<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;
use Throwable;

/**
 * GHSA-fwj7-c84x-jvjq: Sessions::_reject_expired_password_reset_token() is shared by the
 * reset-link (GET) and password-change (POST) flows.
 *
 * PasswordResetTokenExpiryTest pins "expired token cannot change the password". These tests
 * pin the rest of the contract that a mutation of the helper would otherwise slip past: where
 * the rejection redirects, what the user is told, that both the token AND its expiry are
 * burnt, how close to the boundary the clock is read, that a successful change consumes the
 * token, and that an unparseable stored expiry fails closed.
 */
#[Group('feature')]
#[Group('security')]
#[Group('sessions')]
final class PasswordResetExpiryEnforcementTest extends AbstractTestCase
{
    private const TOKEN = 'ab12cd34ef56ab12cd34ef56ab12cd34ef56ab12cd34ef56ab12cd34ef56ab12';

    private const EXPIRED_MESSAGE = 'This password reset link has expired. Please request a new password reset.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_sends_an_expired_password_change_back_to_the_reset_page_with_the_generic_message(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

        /* Act */
        $response = $this->postNewPassword($userId, 'HackedPass123!');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        self::assertSame(self::EXPIRED_MESSAGE, $response->sessionValue('alert_error'));
    }

    #[Test]
    public function it_burns_both_the_token_and_its_expiry_when_an_expired_password_change_is_rejected(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

        /* Act */
        $this->postNewPassword($userId, 'HackedPass123!');

        /* Assert */
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame('', (string) $row['user_passwordreset_token']);
        self::assertNull($row['user_passwordreset_token_expiry'], 'The expiry column must be cleared together with the token.');
    }

    #[Test]
    public function it_sends_an_expired_reset_link_to_the_reset_page_and_burns_the_token(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 300));

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        self::assertSame(self::EXPIRED_MESSAGE, $response->sessionValue('alert_error'));
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame('', (string) $row['user_passwordreset_token']);
        self::assertNull($row['user_passwordreset_token_expiry']);
    }

    #[Test]
    public function it_leaves_the_token_untouched_when_an_unexpired_reset_link_is_opened(): void
    {
        /* Arrange: positive control for the burn tests above */
        $expiry = gmdate('Y-m-d H:i:s', time() + 600);
        $userId = $this->seedUserWithResetToken($expiry);

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert: the form renders and opening the link does not consume the token */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'btn_new_password');
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(hash('sha256', self::TOKEN), $row['user_passwordreset_token']);
        self::assertSame($expiry, $row['user_passwordreset_token_expiry']);
    }

    #[Test]
    public function it_rejects_a_token_that_expired_one_minute_ago(): void
    {
        /* Arrange: close to the boundary, so a wrong timezone or offset would flip the result */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() - 60));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->postNewPassword($userId, 'HackedPass123!');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame($before['user_password'], $after['user_password']);
    }

    #[Test]
    public function it_accepts_a_token_that_expires_in_one_minute(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 60));
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->postNewPassword($userId, 'BrandNewPass123!');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame($before['user_password'], $after['user_password']);
    }

    #[Test]
    public function it_consumes_the_token_on_a_successful_change_so_it_cannot_be_replayed(): void
    {
        /* Arrange */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));

        /* Act: first use succeeds */
        $first      = $this->postNewPassword($userId, 'FirstNewPass123!');
        $afterFirst = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Assert: token and expiry are gone */
        $this->assertResponseRedirectsToRoute($first, 'sessions/login');
        self::assertSame('', (string) $afterFirst['user_passwordreset_token']);
        self::assertNull($afterFirst['user_passwordreset_token_expiry']);

        /* Act: replaying the same token with a different password */
        $replay      = $this->postNewPassword($userId, 'AttackerPass123!');
        $afterReplay = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Assert: the replay is refused and the first password stands */
        $this->assertResponseRedirectsToRoute($replay, 'sessions/passwordreset');
        self::assertSame(self::EXPIRED_MESSAGE, $replay->sessionValue('alert_error'));
        self::assertSame($afterFirst['user_password'], $afterReplay['user_password']);
    }

    #[Test]
    public function it_fails_closed_when_the_stored_expiry_is_an_unparseable_zero_date(): void
    {
        /* Arrange: the only malformed value a DATETIME column can still hold. It is only storable
         * outside strict sql_mode, so lift that for this test's own connection while writing it. */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));

        try {
            $this->databaseRunScript("SET SESSION sql_mode = ''");
            $this->databaseUpdate('ip_users', ['user_passwordreset_token_expiry' => '0000-00-00 00:00:00'], ['user_id' => $userId]);
        } catch (Throwable) {
            self::markTestSkipped('PARITY-OK: the test database refuses to store a zero-date expiry even with sql_mode lifted.');
        } finally {
            $this->databaseRunScript('SET SESSION sql_mode = DEFAULT');
        }

        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        if (empty($before['user_passwordreset_token_expiry'])) {
            self::markTestSkipped('PARITY-OK: the database normalised the zero-date expiry to NULL.');
        }

        /* Act */
        $response = $this->postNewPassword($userId, 'HackedPass123!');

        /* Assert: a token whose lifetime cannot be established is not honoured, and is burnt */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        self::assertSame(self::EXPIRED_MESSAGE, $response->sessionValue('alert_error'));
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame($before['user_password'], $after['user_password']);
        self::assertSame('', (string) $after['user_passwordreset_token']);
    }

    private function postNewPassword(int $userId, string $newPassword): HttpResponse
    {
        return $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => (string) $userId,
            'token'            => self::TOKEN,
            'new_password'     => $newPassword,
            'new_passwordv'    => $newPassword,
        ]);
    }

    private function seedUserWithResetToken(?string $expiry): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'resetgap_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'resetgap+' . bin2hex(random_bytes(3)) . '@example.com',
            'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(10)),
            'user_type'                       => 1,
            'user_active'                     => 1,
            'user_passwordreset_token'        => hash('sha256', self::TOKEN),
            'user_passwordreset_token_expiry' => $expiry,
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }
}
