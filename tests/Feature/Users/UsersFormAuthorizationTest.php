<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Users::form object-level rules: only the primary administrator edits another user's record, and nobody changes
 * their own role through the form. These tests remove each of the two guards in turn and must go red.
 */
#[Group('security')]
#[Group('users')]
final class UsersFormAuthorizationTest extends AbstractTestCase
{
    #[Test]
    public function it_refuses_a_peer_administrator_posting_to_the_primary_administrators_form_and_changes_nothing(): void
    {
        /* Arrange */
        $peerId = $this->seedUser(['user_type' => 1, 'user_name' => 'Peer Admin', 'user_email' => 'peer@test.local']);
        $before = $this->databaseFetchOne('ip_users', ['user_id' => 1]);
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->post('/users/form/1', [
            'user_type'     => '2',
            'user_name'     => 'Hijacked',
            'user_email'    => 'hijacked@test.local',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertSame(403, $response->statusCode());
        $after = $this->databaseFetchOne('ip_users', ['user_id' => 1]);
        self::assertSame($before['user_name'], $after['user_name']);
        self::assertSame($before['user_email'], $after['user_email']);
        self::assertSame($before['user_type'], $after['user_type']);
    }

    #[Test]
    public function it_refuses_a_peer_administrator_opening_the_primary_administrators_form_without_leaking_the_record(): void
    {
        /* Arrange */
        $peerId = $this->seedUser(['user_type' => 1, 'user_name' => 'Peer Admin', 'user_email' => 'peer@test.local']);
        $primary = $this->databaseFetchOne('ip_users', ['user_id' => 1]);
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->get('/users/form/1');

        /* Assert */
        self::assertSame(403, $response->statusCode());
        $this->assertResponseBodyNotContains($response, (string) $primary['user_email']);
    }

    #[Test]
    public function it_does_not_let_an_administrator_change_their_own_role_through_the_form(): void
    {
        /* Arrange */
        $selfId = $this->seedUser(['user_type' => 1, 'user_name' => 'Self Admin', 'user_email' => 'self@test.local']);
        $this->actingAsAdmin($selfId);

        /* Act */
        $response = $this->post('/users/form/' . $selfId, [
            'user_type'     => '2',
            'user_name'     => 'Self Admin',
            'user_email'    => 'self@test.local',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert: the save goes through, the role does not move */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_id' => $selfId, 'user_type' => 1]);
    }

    #[Test]
    public function it_lets_the_primary_administrator_change_another_users_role(): void
    {
        /* Arrange: positive control, so the refusals above are not simply "everything fails" */
        $otherId = $this->seedUser(['user_type' => 2, 'user_name' => 'Other User', 'user_email' => 'other@test.local']);
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->post('/users/form/' . $otherId, [
            'user_type'     => '1',
            'user_name'     => 'Other User',
            'user_email'    => 'other@test.local',
            'user_language' => 'system',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_id' => $otherId, 'user_type' => 1]);
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
