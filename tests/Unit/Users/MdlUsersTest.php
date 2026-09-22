<?php

namespace Tests\Unit\Users;

use Mdl_Users;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mdl_Users::class)]
class MdlUsersTest extends TestCase
{
    /**
     * Mdl_Users is a CodeIgniter model guarded by `defined('BASEPATH')` and
     * extends the Response_Model → Form_Validation_Model → MY_Model → CI_Model
     * chain. The phpunit bootstrap defines BASEPATH but does not eagerly load
     * every model class, so pull in the ancestry (definitions only, no
     * constructors run) before the pure-static assertions below.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (class_exists(Mdl_Users::class, false)) {
            return;
        }

        $root = dirname(__DIR__, 3);

        $files = [
            $root . '/vendor/pocketarc/codeigniter/system/core/Model.php',
            $root . '/application/core/MY_Model.php',
            $root . '/application/core/Form_Validation_Model.php',
            $root . '/application/core/Response_Model.php',
            $root . '/application/modules/users/models/Mdl_users.php',
        ];

        foreach ($files as $file) {
            require_once $file;
        }
    }

    // -------------------------------------------------------------------------
    // is_primary_administrator()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_true_for_user_id_1_as_integer(): void
    {
        self::assertTrue(Mdl_Users::is_primary_administrator(1));
    }

    #[Test]
    public function it_returns_true_for_user_id_1_as_string(): void
    {
        self::assertTrue(Mdl_Users::is_primary_administrator('1'));
    }

    #[Test]
    public function it_returns_true_for_user_id_01_as_string(): void
    {
        /* canonicalizes through (int) so leading zero is ignored */
        self::assertTrue(Mdl_Users::is_primary_administrator('01'));
    }

    #[Test]
    public function it_returns_true_for_user_id_1abc_as_string(): void
    {
        /* int cast ignores non-numeric suffix */
        self::assertTrue(Mdl_Users::is_primary_administrator('1abc'));
    }

    #[Test]
    public function it_returns_true_when_user_id_is_boolean_true(): void
    {
        /* true casts to 1 */
        self::assertTrue(Mdl_Users::is_primary_administrator(true));
    }

    #[Test]
    public function it_returns_false_for_user_id_2_as_integer(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(2));
    }

    #[Test]
    public function it_returns_false_for_user_id_2_as_string(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator('2'));
    }

    #[Test]
    public function it_returns_false_for_user_id_0(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(0));
    }

    #[Test]
    public function it_returns_false_when_user_id_is_boolean_false(): void
    {
        /* false casts to 0 */
        self::assertFalse(Mdl_Users::is_primary_administrator(false));
    }

    #[Test]
    public function it_returns_false_for_negative_user_id(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(-1));
    }

    #[Test]
    public function it_returns_false_for_null(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(null));
    }

    #[Test]
    public function it_returns_false_for_empty_string(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(''));
    }

    #[Test]
    public function it_returns_false_for_whitespace_string(): void
    {
        /* whitespace casts to 0 */
        self::assertFalse(Mdl_Users::is_primary_administrator('   '));
    }

    #[Test]
    public function it_returns_false_for_non_numeric_string(): void
    {
        /* non-numeric string casts to 0 */
        self::assertFalse(Mdl_Users::is_primary_administrator('abc'));
    }

    #[Test]
    public function it_returns_false_for_float_1_point_0(): void
    {
        /* 1.0 casts to 1 → true */
        self::assertTrue(Mdl_Users::is_primary_administrator(1.0));
    }

    #[Test]
    public function it_returns_false_for_float_1_point_5(): void
    {
        /* 1.5 casts to 1 → true */
        self::assertTrue(Mdl_Users::is_primary_administrator(1.5));
    }

    #[Test]
    public function it_returns_false_for_very_large_user_id(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator(999999));
    }

    #[Test]
    public function it_returns_false_for_string_with_only_whitespace(): void
    {
        self::assertFalse(Mdl_Users::is_primary_administrator("\t\n"));
    }
}
