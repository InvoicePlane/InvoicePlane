<?php

namespace tests\Scripts;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Tests for .github/scripts/parse-phpstan-results.php.
 *
 * The script defines four helper functions used for formatting PHPStan JSON
 * output into a readable Markdown report:
 *   - categorizeError(string): string
 *   - getCategoryLabel(string): string
 *   - getShortPath(string): string
 *   - trimMessage(string, int): string
 *
 * Because the script is a CLI entry-point, it contains top-level bootstrap code
 * that runs immediately on inclusion.  The tests below load the script with a
 * minimal valid PHPStan JSON fixture so the bootstrap code completes without
 * calling exit(), and then exercise each helper function in isolation.
 */
#[CoversNothing]
class ParsePhpstanResultsTest extends AbstractTestCase
{
    private static bool $scriptLoaded = false;

    /**
     * Bootstrap: load the script once per process so that all helper
     * functions are defined before any test method runs.
     *
     * The script is a CLI tool that reads a file named in $argv[1], so we
     * create a temporary PHPStan JSON fixture with at least one error to
     * avoid the early exit(0) branch that fires when totalErrors === 0.
     */
    public static function setUpBeforeClass(): void
    {
        if (self::$scriptLoaded) {
            return;
        }

        // Build a minimal PHPStan JSON payload with one error so the script
        // takes the "produce report" path rather than the "no errors" exit(0) path.
        $fixture = json_encode([
            'totals' => ['file_errors' => 1, 'errors' => 1],
            'files'  => [
                '/project/src/Foo.php' => [
                    'errors'   => 1,
                    'messages' => [
                        ['message' => 'Method Foo::bar() should return int but returns string.', 'line' => 10],
                    ],
                ],
            ],
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'phpstan_test_') . '.json';
        file_put_contents($tmpFile, $fixture);

        // Simulate CLI invocation: the script reads $argc and $argv at the
        // global scope, so we set them before including the file.
        $GLOBALS['argc'] = 2;
        $GLOBALS['argv'] = ['parse-phpstan-results.php', $tmpFile];

        // Suppress the Markdown output the bootstrap code echoes to stdout.
        ob_start();
        require_once dirname(__DIR__, 3) . '/.github/scripts/parse-phpstan-results.php';
        ob_end_clean();

        @unlink($tmpFile);

        self::$scriptLoaded = true;
    }

    // -------------------------------------------------------------------------
    // getCategoryLabel()
    // -------------------------------------------------------------------------

    /** @return array<string, array{string, string}> */
    public static function categoryLabelProvider(): array
    {
        return [
            'type_errors'        => ['type_errors', 'Type Errors'],
            'method_errors'      => ['method_errors', 'Method Errors'],
            'property_errors'    => ['property_errors', 'Property Errors'],
            'return_type_errors' => ['return_type_errors', 'Return Type Errors'],
            'other_errors'       => ['other_errors', 'Other Errors'],
        ];
    }

    // -------------------------------------------------------------------------
    // categorizeError()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_categorizes_return_type_errors(): void
    {
        /* Arrange */
        $message = 'Method Foo::bar() should return int but returns string.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('return_type_errors', $result);
    }

    #[Test]
    public function it_categorizes_return_type_errors_case_insensitively(): void
    {
        /* Arrange */
        $message = 'METHOD FOO::BAR() SHOULD RETURN INT BUT RETURNS NULL.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('return_type_errors', $result);
    }

    #[Test]
    public function it_categorizes_method_errors_for_undefined_method(): void
    {
        /* Arrange */
        $message = 'Call to an undefined method Foo::missingMethod().';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('method_errors', $result);
    }

    #[Test]
    public function it_categorizes_method_errors_when_message_contains_method_keyword(): void
    {
        /* Arrange */
        $message = 'Method Foo::bar() has no return type specified.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('method_errors', $result);
    }

    #[Test]
    public function it_categorizes_property_errors(): void
    {
        /* Arrange */
        $message = 'Access to an undefined property Foo::$bar.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('property_errors', $result);
    }

    #[Test]
    public function it_categorizes_type_errors_for_type_mismatch(): void
    {
        /* Arrange */
        $message = 'Parameter #1 $id of function doSomething expects int, string given.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('type_errors', $result);
    }

    #[Test]
    public function it_categorizes_type_errors_when_only_type_keyword_present(): void
    {
        /* Arrange */
        $message = 'Variable $x with type int cannot be assigned to.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('type_errors', $result);
    }

    #[Test]
    public function it_falls_back_to_other_errors_for_unrecognized_messages(): void
    {
        /* Arrange */
        $message = 'Syntax error, unexpected token.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('other_errors', $result);
    }

    #[Test]
    public function it_falls_back_to_other_errors_for_empty_message(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $result = categorizeError('');

        /* Assert */
        $this->assertSame('other_errors', $result);
    }

    /**
     * "should return" takes priority over "method" so that a message like
     * "Method Foo::bar() should return int" is classified as a return-type
     * error rather than a method error.
     */
    #[Test]
    public function it_prioritizes_return_type_over_method_when_both_keywords_present(): void
    {
        /* Arrange */
        $message = 'Method Baz::qux() should return array but returns void.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('return_type_errors', $result);
    }

    /**
     * A message containing both "property" and "method" must be classified
     * as a method error because the method/call-to guard takes precedence.
     */
    #[Test]
    public function it_classifies_message_with_property_and_method_as_method_error(): void
    {
        /* Arrange */
        $message = 'Method setProperty() was called with wrong argument type.';

        /* Act */
        $result = categorizeError($message);

        /* Assert */
        $this->assertSame('method_errors', $result);
    }

    #[Test]
    #[DataProvider('categoryLabelProvider')]
    public function it_returns_human_readable_label_for_known_category(string $category, string $expectedLabel): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $result = getCategoryLabel($category);

        /* Assert */
        $this->assertSame($expectedLabel, $result);
    }

    #[Test]
    public function it_returns_unknown_for_unrecognized_category(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        /* Act */
        $result = getCategoryLabel('nonexistent_category');

        /* Assert */
        $this->assertSame('Unknown', $result);
    }

    // -------------------------------------------------------------------------
    // trimMessage()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_short_message_unchanged(): void
    {
        /* Arrange */
        $message = 'Short error message.';

        /* Act */
        $result = trimMessage($message);

        /* Assert */
        $this->assertSame('Short error message.', $result);
    }

    #[Test]
    public function it_truncates_message_exceeding_default_max_length(): void
    {
        /* Arrange */
        $message = str_repeat('a', 200);

        /* Act */
        $result = trimMessage($message);

        /* Assert */
        $this->assertSame(150, mb_strlen($result, 'UTF-8'));
        $this->assertStringEndsWith('...', $result);
    }

    #[Test]
    public function it_truncates_message_to_custom_max_length(): void
    {
        /* Arrange */
        $message = str_repeat('x', 100);

        /* Act */
        $result = trimMessage($message, 50);

        /* Assert */
        $this->assertSame(50, mb_strlen($result, 'UTF-8'));
        $this->assertStringEndsWith('...', $result);
    }

    #[Test]
    public function it_does_not_truncate_message_at_exact_max_length(): void
    {
        /* Arrange */
        $message = str_repeat('z', 150);

        /* Act */
        $result = trimMessage($message, 150);

        /* Assert */
        $this->assertSame(150, mb_strlen($result, 'UTF-8'));
        $this->assertStringNotContainsString('...', $result);
    }

    #[Test]
    public function it_collapses_multiple_whitespace_characters(): void
    {
        /* Arrange */
        $message = "Error  with   extra\t\ttabs\nand newlines";

        /* Act */
        $result = trimMessage($message);

        /* Assert */
        $this->assertSame('Error with extra tabs and newlines', $result);
    }

    #[Test]
    public function it_trims_leading_and_trailing_whitespace(): void
    {
        /* Arrange */
        $message = '   padded message   ';

        /* Act */
        $result = trimMessage($message);

        /* Assert */
        $this->assertSame('padded message', $result);
    }

    #[Test]
    public function it_handles_multibyte_characters_correctly(): void
    {
        /* Arrange – 60 multibyte Japanese characters, exceeds custom limit of 20 */
        $message = str_repeat('あ', 60);

        /* Act */
        $result = trimMessage($message, 20);

        /* Assert */
        $this->assertSame(20, mb_strlen($result, 'UTF-8'));
        $this->assertStringEndsWith('...', $result);
    }

    // -------------------------------------------------------------------------
    // getShortPath()
    // -------------------------------------------------------------------------

    #[Test]
    public function it_strips_project_root_prefix_from_absolute_path(): void
    {
        /* Arrange – derive the project root the same way the script does:
           .github/scripts/parse-phpstan-results.php => dirname(__DIR__, 2) from the
           script file's __DIR__ (.github/scripts) gives the project root.          */
        $scriptDir    = dirname(__DIR__, 3) . '/.github/scripts';
        $projectRoot  = dirname($scriptDir, 2);
        $absolutePath = $projectRoot . '/application/modules/invoices/models/Mdl_invoices.php';

        /* Act */
        $result = getShortPath($absolutePath);

        /* Assert */
        $this->assertSame('application/modules/invoices/models/Mdl_invoices.php', $result);
        $this->assertStringNotContainsString($projectRoot, $result);
    }

    #[Test]
    public function it_normalizes_windows_backslashes_to_forward_slashes(): void
    {
        /* Arrange */
        $path = 'C:\\project\\src\\Foo.php';

        /* Act */
        $result = getShortPath($path);

        /* Assert – the result must not contain any backslashes */
        $this->assertStringNotContainsString('\\', $result);
    }

    #[Test]
    public function it_returns_path_unchanged_when_no_prefix_matches(): void
    {
        /* Arrange – a path that does not start with the project root or cwd */
        $path = '/some/completely/different/directory/file.php';

        /* Act */
        $result = getShortPath($path);

        /* Assert */
        $this->assertSame('/some/completely/different/directory/file.php', $result);
    }

    #[Test]
    public function it_strips_cwd_prefix_as_fallback(): void
    {
        /* Arrange */
        $cwd      = (string) getcwd();
        $fullPath = $cwd . '/application/helpers/security_helper.php';

        /* Act */
        $result = getShortPath($fullPath);

        /* Assert */
        $this->assertSame('application/helpers/security_helper.php', $result);
    }
}
