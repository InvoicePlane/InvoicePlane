<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Catches the hollow-assertion pattern this repo has shipped by accident before:
 * `assertTrue(function_exists('some_function'))` (or class_exists()/method_exists())
 * standing in for a real assertion on behavior. Checking that a dependency merely
 * exists can never fail for the reason the test's name claims, and it is exactly
 * what tests/Unit/Helpers/PdfSecurityTest.php::it_escapes_filename_in_pdf_footer
 * did before being fixed to call the real escaping function and assert on its
 * output. A diff review can miss this because the surrounding comments often
 * still read as if the test does something real; this test reads every tracked
 * test file fresh from disk instead of trusting the diff.
 *
 * See CLAUDE.md "Test conventions": "a test must be able to fail for the reason
 * it names... no re-implementation of production logic inside a test."
 */
final class TestHonestyGuardTest extends TestCase
{
    private const FORBIDDEN_PATTERN = '/assertTrue\s*\(\s*(function_exists|class_exists|method_exists)\s*\(/';

    /**
     * @return array<string, array{0: string}>
     */
    public static function trackedTestFiles(): array
    {
        $root = dirname(__DIR__, 3);
        $self = __FILE__;

        $output   = [];
        $exitCode = 0;
        exec('git -C ' . escapeshellarg($root) . ' ls-files -- tests 2>/dev/null', $output, $exitCode);

        $cases = [];

        foreach ($output as $relativePath) {
            if (strtolower((string) pathinfo($relativePath, PATHINFO_EXTENSION)) !== 'php') {
                continue;
            }

            $absolute = $root . '/' . $relativePath;

            // This test's own source necessarily documents the forbidden pattern; it can
            // never check itself.
            if ($absolute === $self) {
                continue;
            }

            if ( ! is_file($absolute)) {
                continue;
            }

            $cases[$relativePath] = [$absolute];
        }

        self::assertNotEmpty($cases, 'git ls-files returned nothing; are we running outside a git checkout?');

        return $cases;
    }

    #[Test]
    #[DataProvider('trackedTestFiles')]
    public function it_has_no_existence_only_assertion_standing_in_for_real_behavior(string $path): void
    {
        $content = file_get_contents($path);
        self::assertIsString($content, "Could not read {$path}.");

        self::assertDoesNotMatchRegularExpression(
            self::FORBIDDEN_PATTERN,
            $content,
            "{$path} asserts only that a function/class/method exists, never that it behaves "
            . 'correctly. This can never fail for the reason the test names — assert on the '
            . "real return value or side effect instead. See CLAUDE.md's \"Test conventions\"."
        );
    }
}
