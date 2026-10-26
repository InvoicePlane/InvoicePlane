<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * InvoicePlane/InvoicePlane#1780: render_template_view()'s custom-templates-folder branch
 * (application/helpers/template_helper.php) has two outcomes depending on $return -- return
 * the rendered string (the PDF callers in pdf_helper.php / Sumex.php, which embed it into a
 * PDF rather than emit it directly) or append it to $CI->output (the guest-view public-template
 * caller). GuestViewCustomTemplateTest exercises only the $return = false path through the full
 * HTTP stack; no test reaches the $return = true path, so a mutation that forces that branch
 * survived undetected. This runs the real helper directly, in a clean process, against both
 * $return values.
 *
 * Also covers the CodeRabbit-flagged follow-up on PR #1781: the closure that includes the
 * custom file reads $data via extract(), which defaults to EXTR_OVERWRITE -- a $data array
 * carrying a key named after whatever local variable holds the include path can hijack it.
 * render_template_view() reads the path via func_get_arg() (never a named local before
 * extract() runs) and calls extract(..., EXTR_SKIP), so the one pre-existing local can't be
 * overwritten by any key, while ordinary keys like 'file_path' or 'data' still reach the
 * template untouched.
 */
final class RenderTemplateViewRealHelperTest extends TestCase
{
    #[Test]
    public function it_returns_the_rendered_string_instead_of_appending_it_when_return_is_true(): void
    {
        /* Act */
        [$returned, $appended] = $this->runRenderTemplateView(true);

        /* Assert: the output comes back as the function's return value, and never reaches
         * $CI->output->append_output() -- the mutant (if ($return) -> if (false)) would route
         * it through append_output and return null instead. */
        self::assertSame('MARKER-return-true', $returned);
        self::assertSame('NONE', $appended, '$CI->output->append_output() must not be called when $return is true.');
    }

    #[Test]
    public function it_appends_the_rendered_string_to_ci_output_instead_of_returning_it_when_return_is_false(): void
    {
        /* Act */
        [$returned, $appended] = $this->runRenderTemplateView(false);

        /* Assert: the inverse of the above -- proves the two branches are not just both taken,
         * but each one exclusively. */
        self::assertSame('NONE', $returned, 'render_template_view() must not return a value when $return is false.');
        self::assertSame('MARKER-return-false', $appended);
    }

    #[Test]
    public function it_does_not_let_caller_data_hijack_the_include_target_via_a_ci_path_key(): void
    {
        /* Arrange: a $data array a real caller would never build on purpose, but nothing stops
         * one from carrying a '_ci_path' key -- e.g. a custom field or merged array literally
         * named that. If it clobbers the include target, render_template_view() includes
         * whatever file the caller's data pointed at instead of the one Settings selected. */
        $fixtureDir = sys_get_temp_dir() . '/render_template_view_hijack_test_' . bin2hex(random_bytes(6));
        mkdir($fixtureDir . '/invoice_templates/pdf', 0777, true);
        mkdir($fixtureDir . '/evil');
        file_put_contents(
            $fixtureDir . '/invoice_templates/pdf/Custom.php',
            '<?php echo $marker . "|FP:" . ($file_path ?? "MISSING") . "|D:" . ($data ?? "MISSING");'
        );
        file_put_contents($fixtureDir . '/evil/Evil.php', '<?php echo "HIJACKED";');

        $maliciousData = [
            'marker'    => 'MARKER-hijack-test',
            '_ci_path'  => $fixtureDir . '/evil/Evil.php',
            'file_path' => 'caller-file-path-value',
            'data'      => 'caller-data-value',
        ];

        /* Act */
        $output = $this->runRenderTemplateViewWithData($fixtureDir, $maliciousData);

        /* Assert: the selected template ran (not evil/Evil.php), and the caller's own
         * 'file_path' and 'data' keys still reached it -- the fix protects the include target
         * specifically, not caller data in general. */
        self::assertSame('MARKER-hijack-test|FP:caller-file-path-value|D:caller-data-value', $output);
    }

    private function runRenderTemplateViewWithData(string $fixtureDir, array $data): string
    {
        $script = sprintf(
            <<<'PHP'
                define('BASEPATH', '/');
                define('CUSTOM_TEMPLATES_FOLDER', %s . DIRECTORY_SEPARATOR);
                class StubOutput { public function append_output($s) {} }
                class StubCI { public $output; public function __construct() { $this->output = new StubOutput(); } }
                function &get_instance() { static $ci; if ($ci === null) { $ci = new StubCI(); } return $ci; }
                require %s;
                echo render_template_view('invoice_templates/pdf/Custom', %s, true);
                PHP,
            var_export($fixtureDir, true),
            var_export(dirname(__DIR__, 3) . '/application/helpers/template_helper.php', true),
            var_export($data, true)
        );

        $output = shell_exec(escapeshellarg(PHP_BINARY) . ' -d error_reporting=-1 -d display_errors=1 -r ' . escapeshellarg($script) . ' 2>&1');

        $this->removeDirectory($fixtureDir);

        return (string) $output;
    }

    private function removeDirectory(string $dir): void
    {
        foreach (glob($dir . '/*') ?: [] as $path) {
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * @return array{0: string, 1: string} [return value echoed as 'NONE' when null, $CI->output->append_output() argument echoed as 'NONE' when never called]
     */
    private function runRenderTemplateView(bool $return): array
    {
        $fixtureDir = sys_get_temp_dir() . '/render_template_view_test_' . bin2hex(random_bytes(6));
        mkdir($fixtureDir . '/invoice_templates/pdf', 0777, true);
        file_put_contents(
            $fixtureDir . '/invoice_templates/pdf/Custom.php',
            '<?php echo $marker;'
        );

        $script = sprintf(
            <<<'PHP'
                define('BASEPATH', '/');
                define('CUSTOM_TEMPLATES_FOLDER', %s . DIRECTORY_SEPARATOR);
                $GLOBALS['appended'] = null;
                class StubOutput { public function append_output($s) { $GLOBALS['appended'] = $s; } }
                class StubCI { public $output; public function __construct() { $this->output = new StubOutput(); } }
                function &get_instance() { static $ci; if ($ci === null) { $ci = new StubCI(); } return $ci; }
                require %s;
                $returned = render_template_view('invoice_templates/pdf/Custom', ['marker' => %s], %s);
                echo ($returned === null ? 'NONE' : $returned) . '|' . ($GLOBALS['appended'] === null ? 'NONE' : $GLOBALS['appended']);
                PHP,
            var_export($fixtureDir, true),
            var_export(dirname(__DIR__, 3) . '/application/helpers/template_helper.php', true),
            var_export('MARKER-return-' . ($return ? 'true' : 'false'), true),
            var_export($return, true)
        );

        $output = shell_exec(escapeshellarg(PHP_BINARY) . ' -d error_reporting=-1 -d display_errors=1 -r ' . escapeshellarg($script) . ' 2>&1');

        $this->removeDirectory($fixtureDir);

        return explode('|', (string) $output, 2) + ['', ''];
    }
}
