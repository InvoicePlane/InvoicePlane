<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The shapes FileSecurityHelperTest does not send: a traversal segment that is not at the start, a backslash-absolute
 * path, a DEL character in a download name, a bare carriage return in a log line, and the port range limits.
 */
#[Group('security')]
final class FileSecurityHelperBoundariesTest extends TestCase
{
    protected function setUp(): void
    {
        require_once ROOT_PATH . '/application/helpers/file_security_helper.php';
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function unsafeNames(): array
    {
        return [
            'trailing slash dot-dot'     => ['a/..', 'path_traversal'],
            'trailing backslash dot-dot' => ['a\\..', 'path_traversal'],
            'backslash absolute'         => ['\\share\\file.pdf', 'absolute_path'],
        ];
    }

    #[Test]
    #[DataProvider('unsafeNames')]
    public function it_refuses_the_name_with_the_matching_reason(string $name, string $error): void
    {
        /* Act */
        $result = validate_safe_filename($name);

        /* Assert */
        self::assertFalse($result['valid']);
        self::assertSame($error, $result['error']);
    }

    #[Test]
    public function it_accepts_a_plain_name_and_a_name_with_dots_inside(): void
    {
        /* Act and assert: positive control, the refusals above are not "everything fails" */
        self::assertTrue(validate_safe_filename('invoice.2026.pdf')['valid']);
        self::assertTrue(validate_safe_filename('a..b.pdf')['valid']);
    }

    #[Test]
    public function it_strips_the_del_character_and_other_controls_from_a_download_name(): void
    {
        /* Act and assert */
        self::assertSame('ab', sanitize_filename_for_header("a\x7Fb"));
        self::assertSame('ab', sanitize_filename_for_header("a\x00\r\nb"));
        self::assertSame('ab.pdf', sanitize_filename_for_header('a"b\\.pdf'));
    }

    #[Test]
    public function it_strips_a_bare_carriage_return_and_a_bare_line_feed_from_a_log_value(): void
    {
        /* Act and assert: either one alone can start a forged log line */
        self::assertSame('ab', sanitize_for_logging("a\rb"));
        self::assertSame('ab', sanitize_for_logging("a\nb"));
        self::assertSame('ab', sanitize_for_logging("a\r\nb"));
    }

    #[Test]
    #[DataProvider('ports')]
    public function it_accepts_ports_one_to_65535_only(string $port, bool $valid): void
    {
        /* Act */
        $result = validate_db_config_parameter($port, 'port');

        /* Assert */
        self::assertSame($valid, $result['valid']);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function ports(): array
    {
        return [
            'zero'          => ['0', false],
            'one'           => ['1', true],
            'default mysql' => ['3306', true],
            'top'           => ['65535', true],
            'just above'    => ['65536', false],
            'huge'          => ['999999', false],
            'not a number'  => ['33x6', false],
        ];
    }
}
