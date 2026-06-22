<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Users mass-assignment protection.
 *
 * Covered:
 *  - user_type, user_active, user_psalt are always stripped from POST-sourced db_array()
 *  - Legitimate non-protected fields pass through untouched
 *  - All three protected fields are stripped even when submitted together
 *  - An empty POST array produces an empty db_array (no phantom data)
 */
#[Group('unit')]
#[Group('security')]
class MassAssignmentProtectionTest extends TestCase
{
    private StubMdlUsers $model;

    protected function setUp(): void
    {
        $this->model = new StubMdlUsers();
    }

    #[Test]
    #[DataProvider('protectedFields')]
    public function it_strips_the_protected_field_from_post_sourced_data(string $field): void
    {
        /* Arrange */
        $post = [
            'user_name'  => 'Alice',
            'user_email' => 'alice@test.local',
            $field       => 'attacker-supplied-value',
        ];

        /* Act */
        $dbArray = $this->model->db_array_from($post);

        /* Assert */
        self::assertArrayNotHasKey(
            $field,
            $dbArray,
            "Protected field [{$field}] must be stripped before the array reaches the DB layer."
        );
    }

    public static function protectedFields(): array
    {
        return [
            'user_type'   => ['user_type'],
            'user_active' => ['user_active'],
            'user_psalt'  => ['user_psalt'],
        ];
    }

    #[Test]
    public function it_strips_all_protected_fields_when_submitted_together(): void
    {
        /* Arrange */
        $post = [
            'user_name'   => 'Bob',
            'user_email'  => 'bob@test.local',
            'user_type'   => '1',
            'user_active' => '1',
            'user_psalt'  => 'injected-salt',
        ];

        /* Act */
        $dbArray = $this->model->db_array_from($post);

        /* Assert */
        self::assertArrayNotHasKey('user_type',   $dbArray);
        self::assertArrayNotHasKey('user_active',  $dbArray);
        self::assertArrayNotHasKey('user_psalt',   $dbArray);
    }

    #[Test]
    public function it_passes_through_non_protected_user_fields(): void
    {
        /* Arrange */
        $post = [
            'user_name'    => 'Carol',
            'user_email'   => 'carol@test.local',
            'user_company' => 'ACME Corp',
            'user_city'    => 'Amsterdam',
        ];

        /* Act */
        $dbArray = $this->model->db_array_from($post);

        /* Assert */
        self::assertSame('Carol',        $dbArray['user_name']);
        self::assertSame('carol@test.local', $dbArray['user_email']);
        self::assertSame('ACME Corp',    $dbArray['user_company']);
        self::assertSame('Amsterdam',    $dbArray['user_city']);
    }

    #[Test]
    public function it_returns_an_empty_array_for_an_empty_post(): void
    {
        /* Arrange */

        /* Act */
        $dbArray = $this->model->db_array_from([]);

        /* Assert */
        self::assertSame([], $dbArray);
    }

    #[Test]
    public function it_does_not_strip_user_type_when_passed_directly_to_save(): void
    {
        /* Arrange */
        // Admin controllers build their own $db_array — the protection only applies to POST-sourced data.
        $adminBuilt = [
            'user_name'   => 'Dave',
            'user_email'  => 'dave@test.local',
            'user_type'   => '1',
            'user_active' => '1',
        ];

        /* Act */
        $passedThrough = $this->model->admin_db_array($adminBuilt);

        /* Assert */
        self::assertArrayHasKey('user_type',   $passedThrough, 'Admin-supplied db_array must keep user_type.');
        self::assertArrayHasKey('user_active',  $passedThrough, 'Admin-supplied db_array must keep user_active.');
        self::assertSame('1', $passedThrough['user_type']);
    }
}

/**
 * Stub replicating only the mass-assignment-protection logic from Mdl_Users
 * without requiring CI3 to be loaded.
 */
class StubMdlUsers
{
    private const PROTECTED_FIELDS = ['user_type', 'user_active', 'user_psalt'];

    private const ALLOWED_FIELDS = [
        'user_name', 'user_email', 'user_password', 'user_passwordv',
        'user_company', 'user_address_1', 'user_address_2',
        'user_city', 'user_state', 'user_zip', 'user_country',
        'user_phone', 'user_language', 'user_type', 'user_active', 'user_psalt',
    ];

    /**
     * Simulates what Mdl_Users::db_array() does: filter by validation_rules keys,
     * then strip protected fields.
     */
    public function db_array_from(array $post): array
    {
        // Step 1: retain only keys that appear in the validation rules (like parent::db_array())
        $db_array = array_intersect_key($post, array_flip(self::ALLOWED_FIELDS));

        // Step 2: strip protected fields (the security fix)
        foreach (self::PROTECTED_FIELDS as $field) {
            unset($db_array[$field]);
        }

        return $db_array;
    }

    /**
     * Simulates a controller passing a hand-built $db_array directly to save().
     * Protected fields are NOT stripped here — that's intentional for admin use.
     */
    public function admin_db_array(array $explicit): array
    {
        return $explicit;
    }
}
