<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use UserAuthorizationService;

/**
 * UserAuthorizationService Unit Tests.
 *
 * Tests pure authorization business logic without Feature test overhead.
 * No database, no sessions, no HTTP — just business rules.
 */
class UserAuthorizationServiceTest extends TestCase
{
    private UserAuthorizationService $auth;

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/modules/users/services/UserAuthorizationService.php';
        $this->auth = new UserAuthorizationService();
    }

    /**
     * Data provider: comprehensive authorization matrix.
     */
    public static function authorizationMatrixProvider(): array
    {
        return [
            'primary_admin_self'              => [1, 1, true],
            'primary_admin_secondary'         => [1, 2, true],
            'primary_admin_guest'             => [1, 99, true],
            'secondary_admin_self'            => [2, 2, true],
            'secondary_admin_primary'         => [2, 1, false],
            'secondary_admin_other_secondary' => [2, 3, false],
            'secondary_admin_guest'           => [2, 99, false],
            'guest_self'                      => [5, 5, true],
            'guest_primary'                   => [5, 1, false],
            'guest_secondary'                 => [5, 2, false],
            'guest_other_guest'               => [5, 6, false],
        ];
    }

    #[Test]
    #[Group('security')]
    public function it_allows_primary_admin_to_edit_any_user(): void
    {
        /* Arrange: primary admin (user_id=1) editing anyone */
        $primary_admin_id = 1;
        $target_user_ids  = [1, 2, 100, 999];

        /* Act & Assert */
        foreach ($target_user_ids as $target_id) {
            self::assertTrue(
                $this->auth->can_edit_user($primary_admin_id, $target_id),
                "Primary admin must be able to edit user {$target_id}"
            );
        }
    }

    #[Test]
    #[Group('security')]
    public function it_allows_any_user_to_edit_themselves(): void
    {
        /* Arrange */
        $user_ids = [1, 2, 5, 100];

        /* Act & Assert */
        foreach ($user_ids as $user_id) {
            self::assertTrue(
                $this->auth->can_edit_user($user_id, $user_id),
                "User {$user_id} must be able to edit themselves"
            );
        }
    }

    #[Test]
    #[Group('security')]
    public function it_prevents_secondary_admin_from_editing_other_users(): void
    {
        /* Arrange: secondary admin (user_id != 1) editing someone else */
        $secondary_admin_id = 2;
        $other_user_ids     = [1, 3, 5, 100];

        /* Act & Assert */
        foreach ($other_user_ids as $target_id) {
            self::assertFalse(
                $this->auth->can_edit_user($secondary_admin_id, $target_id),
                "Secondary admin must NOT be able to edit user {$target_id}"
            );
        }
    }

    #[Test]
    #[Group('security')]
    public function it_prevents_guests_from_editing_others(): void
    {
        /* Arrange: non-admin user (user_id > 1) trying to edit another */
        $guest_user_id = 5;
        $other_users   = [1, 2, 3, 6];

        /* Act & Assert */
        foreach ($other_users as $target_id) {
            self::assertFalse(
                $this->auth->can_edit_user($guest_user_id, $target_id),
                "Guest user {$guest_user_id} must NOT be able to edit user {$target_id}"
            );
        }
    }

    /**
     * Scenario: Secondary admin tries to change user roles.
     */
    #[Test]
    #[Group('security')]
    public function it_prevents_secondary_admin_from_changing_user_types(): void
    {
        /* Arrange: secondary admin, not editing themselves */
        $secondary_admin_id = 2;
        $target_user_id     = 3;
        $is_self_edit       = false;

        /* Act */
        $result = $this->auth->can_change_user_type($secondary_admin_id, $target_user_id, $is_self_edit);

        /* Assert */
        self::assertFalse($result, 'Secondary admin must NOT be able to change user types');
    }

    /**
     * Scenario: Primary admin changes user role.
     */
    #[Test]
    #[Group('security')]
    public function it_allows_primary_admin_to_change_user_types(): void
    {
        /* Arrange: primary admin editing another user */
        $primary_admin_id = 1;
        $target_user_id   = 5;
        $is_self_edit     = false;

        /* Act */
        $result = $this->auth->can_change_user_type($primary_admin_id, $target_user_id, $is_self_edit);

        /* Assert */
        self::assertTrue($result, 'Primary admin must be able to change user types');
    }

    /**
     * Scenario: User editing themselves cannot escalate their type.
     */
    #[Test]
    #[Group('security')]
    public function it_prevents_self_escalation_during_self_edit(): void
    {
        /* Arrange: secondary admin editing themselves */
        $secondary_admin_id = 2;
        $is_self_edit       = true;

        /* Act */
        $result = $this->auth->can_change_user_type($secondary_admin_id, $secondary_admin_id, $is_self_edit);

        /* Assert */
        self::assertFalse($result, 'Users cannot change their own type during self-edit');
    }

    /**
     * Scenario: Primary admin can change their own type (edge case, but allowed).
     */
    #[Test]
    #[Group('security')]
    public function it_prevents_primary_admin_type_change_during_self_edit(): void
    {
        /* Arrange: primary admin editing themselves */
        $primary_admin_id = 1;
        $is_self_edit     = true;

        /* Act */
        $result = $this->auth->can_change_user_type($primary_admin_id, $primary_admin_id, $is_self_edit);

        /* Assert */
        self::assertFalse($result, 'Type changes are blocked during self-edit, even for primary admin');
    }

    #[Test]
    #[Group('security')]
    public function it_delegates_form_view_to_edit_authorization(): void
    {
        /* Arrange: test a few scenarios */
        $test_cases = [
            [1, 1, true],   // Primary admin viewing themselves
            [1, 2, true],   // Primary admin viewing other
            [2, 2, true],   // Secondary admin viewing themselves
            [2, 1, false],  // Secondary admin viewing primary admin
            [5, 3, false],  // Guest viewing another guest
        ];

        /* Act & Assert */
        foreach ($test_cases as [$acting_id, $target_id, $expected]) {
            self::assertSame(
                $expected,
                $this->auth->can_view_user_form($acting_id, $target_id),
                "View authorization for user {$acting_id} viewing {$target_id} should be {$expected}"
            );
        }
    }

    #[Test]
    #[DataProvider('authorizationMatrixProvider')]
    #[Group('security')]
    public function it_enforces_complete_authorization_matrix(
        int $acting_id,
        int $target_id,
        bool $expected_result
    ): void {
        /* Act */
        $result = $this->auth->can_edit_user($acting_id, $target_id);

        /* Assert */
        self::assertSame(
            $expected_result,
            $result,
            "Authorization for {$acting_id} editing {$target_id} should be {$expected_result}"
        );
    }
}
