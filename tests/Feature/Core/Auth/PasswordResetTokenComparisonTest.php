<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;

/**
 * GHSA-wcqc-qqv5-65ph: the password-change POST compares the stored token digest with
 * hash_equals(). A black-box test cannot distinguish hash_equals() from !==, so the
 * timing property itself is NOT asserted here. What is asserted is everything the
 * comparison must still get right once it is rewritten: only the digest of the exact raw
 * token for exactly this user succeeds, and every other input is rejected with one
 * indistinguishable response while leaving the stored token intact.
 */
#[Group('feature')]
#[Group('security')]
#[Group('sessions')]
final class PasswordResetTokenComparisonTest extends AbstractTestCase
{
    private const TOKEN = '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08';

    private const OTHER_TOKEN = '60303ae22b998861bce3b28f33eec1be758a213c86c93c076dbe9f558c11c752';

    private const GENERIC_MESSAGE = 'This password reset link has expired. Please request a new password reset.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_answers_an_unknown_user_id_exactly_like_a_wrong_token(): void
    {
        /* Arrange */
        $userId = $this->seedUser(self::TOKEN);

        /* Act */
        $unknownUser = $this->postNewPassword(987654, self::TOKEN);
        $wrongToken  = $this->postNewPassword($userId, self::OTHER_TOKEN);

        /* Assert: identical message and destination, so user ids cannot be enumerated */
        self::assertSame(self::GENERIC_MESSAGE, $unknownUser->sessionValue('alert_error'));
        self::assertSame(self::GENERIC_MESSAGE, $wrongToken->sessionValue('alert_error'));
        $this->assertResponseRedirectsToRoute($unknownUser, 'sessions/passwordreset');
        $this->assertResponseRedirectsToRoute($wrongToken, 'sessions/passwordreset');
    }

    #[Test]
    public function it_rejects_the_stored_digest_submitted_in_place_of_the_raw_token(): void
    {
        /* Arrange: an attacker who can only READ the column submits the digest he found */
        $userId = $this->seedUser(self::TOKEN);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->postNewPassword($userId, hash('sha256', self::TOKEN));

        /* Assert: rejected, password and stored digest untouched */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        self::assertSame(self::GENERIC_MESSAGE, $response->sessionValue('alert_error'));
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame($before['user_password'], $after['user_password']);
        self::assertSame($before['user_passwordreset_token'], $after['user_passwordreset_token']);

        /* Assert (positive control): the raw token itself still works afterwards */
        $control = $this->postNewPassword($userId, self::TOKEN, 'ControlNewPass123!');
        $this->assertResponseRedirectsToRoute($control, 'sessions/login');
        $final = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame($before['user_password'], $final['user_password']);
    }

    #[Test]
    public function it_rejects_every_near_miss_of_the_token(): void
    {
        /* Arrange */
        $userId = $this->seedUser(self::TOKEN);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        $misses = [
            'truncated by one char' => mb_substr(self::TOKEN, 0, -1),
            'one char appended'     => self::TOKEN . '0',
            'upper-cased'           => mb_strtoupper(self::TOKEN),
            'trailing newline'      => self::TOKEN . "\n",
            'leading space'         => ' ' . self::TOKEN,
            'empty'                 => '',
            'last char flipped'     => mb_substr(self::TOKEN, 0, -1) . (str_ends_with(self::TOKEN, '8') ? '9' : '8'),
        ];

        foreach ($misses as $label => $submitted) {
            /* Act */
            $response = $this->postNewPassword($userId, $submitted);

            /* Assert */
            $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
            self::assertSame(self::GENERIC_MESSAGE, $response->sessionValue('alert_error'), "Token variant [{$label}] must get the generic rejection.");
            $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
            self::assertSame($before['user_password'], $after['user_password'], "Token variant [{$label}] must not change the password.");
            self::assertSame($before['user_passwordreset_token'], $after['user_passwordreset_token'], "Token variant [{$label}] must not consume the real token.");
        }

        /* Assert (positive control): the exact token succeeds */
        $control = $this->postNewPassword($userId, self::TOKEN, 'ControlNewPass123!');
        $this->assertResponseRedirectsToRoute($control, 'sessions/login');
        $final = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame($before['user_password'], $final['user_password']);
    }

    #[Test]
    public function it_rejects_another_users_valid_token_for_this_user_id(): void
    {
        /* Arrange: two users, each with a live token of their own */
        $victim         = $this->seedUser(self::TOKEN);
        $attacker       = $this->seedUser(self::OTHER_TOKEN);
        $victimBefore   = $this->databaseFetchOne('ip_users', ['user_id' => $victim]);
        $attackerBefore = $this->databaseFetchOne('ip_users', ['user_id' => $attacker]);

        /* Act: the attacker's own valid token, aimed at the victim's user_id */
        $response = $this->postNewPassword($victim, self::OTHER_TOKEN, 'AttackerPass123!');

        /* Assert: nobody's password or token moves */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        self::assertSame($victimBefore['user_password'], $this->databaseFetchOne('ip_users', ['user_id' => $victim])['user_password']);
        self::assertSame($attackerBefore['user_passwordreset_token'], $this->databaseFetchOne('ip_users', ['user_id' => $attacker])['user_passwordreset_token']);

        /* Assert (positive control): the same token for its own user_id works */
        $control = $this->postNewPassword($attacker, self::OTHER_TOKEN, 'OwnNewPass123!');
        $this->assertResponseRedirectsToRoute($control, 'sessions/login');
        self::assertNotSame($attackerBefore['user_password'], $this->databaseFetchOne('ip_users', ['user_id' => $attacker])['user_password']);
    }

    #[Test]
    public function it_rejects_an_empty_submitted_token_against_a_user_with_no_pending_reset(): void
    {
        /* Arrange: a cleared token column ('') and a request with no token at all */
        $userId = $this->seedUser('');
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $emptyToken = $this->postNewPassword($userId, '');
        $noToken    = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => (string) $userId,
            'new_password'     => 'HackedPass123!',
            'new_passwordv'    => 'HackedPass123!',
        ]);

        /* Assert */
        foreach ([$emptyToken, $noToken] as $response) {
            $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
            self::assertSame(self::GENERIC_MESSAGE, $response->sessionValue('alert_error'));
        }
        self::assertSame($before['user_password'], $this->databaseFetchOne('ip_users', ['user_id' => $userId])['user_password']);

        /* Assert (positive control): once a real token is issued, the same user can reset */
        $this->databaseUpdate('ip_users', [
            'user_passwordreset_token'        => hash('sha256', self::TOKEN),
            'user_passwordreset_token_expiry' => gmdate('Y-m-d H:i:s', time() + 600),
        ], ['user_id' => $userId]);
        $control = $this->postNewPassword($userId, self::TOKEN, 'ControlNewPass123!');
        $this->assertResponseRedirectsToRoute($control, 'sessions/login');
        self::assertNotSame($before['user_password'], $this->databaseFetchOne('ip_users', ['user_id' => $userId])['user_password']);
    }

    private function postNewPassword(int $userId, string $token, string $newPassword = 'HackedPass123!'): HttpResponse
    {
        return $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => (string) $userId,
            'token'            => $token,
            'new_password'     => $newPassword,
            'new_passwordv'    => $newPassword,
        ]);
    }

    /**
     * @param string $rawToken raw token whose sha256 digest is stored, or '' for a cleared column
     */
    private function seedUser(string $rawToken): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'cmpgap_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'cmpgap+' . bin2hex(random_bytes(3)) . '@example.com',
            'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(10)),
            'user_type'                       => 1,
            'user_active'                     => 1,
            'user_passwordreset_token'        => $rawToken === '' ? '' : hash('sha256', $rawToken),
            'user_passwordreset_token_expiry' => $rawToken === '' ? null : gmdate('Y-m-d H:i:s', time() + 600),
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }
}
