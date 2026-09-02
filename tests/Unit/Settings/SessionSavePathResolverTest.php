<?php

namespace Tests\Unit\Settings;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for the empty-SESS_SAVE_PATH session bug (shipped through
 * 1.7.x).
 *
 * ipconfig.php.example ships a bare `SESS_SAVE_PATH=` line. phpdotenv parses that
 * as a *defined* variable whose value is "" — so env('SESS_SAVE_PATH', <default>)
 * returned "" and the default was never applied. config.php then set
 * $config['sess_save_path'] = "", CodeIgniter's Session_files_driver ran
 * ini_set('session.save_path', '') (clobbering php.ini), and session open did
 * mkdir('') and failed: login broke and the installer stuck on setup/language.
 *
 * The fix is resolve_session_save_path() in bootstrap/session_path.php, wired
 * into config.php. These tests exercise the pure resolver directly (no index.php
 * bootstrap needed) plus one end-to-end probe that boots the real config.php.
 *
 * Revert bootstrap/session_path.php + the config.php line and every test here
 * fails (fatal "undefined function" for the resolver tests, wrong value for the
 * boot probe).
 */
class SessionSavePathResolverTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/bootstrap/session_path.php';
    }

    #[Test]
    public function it_falls_back_to_the_system_temp_dir_when_the_value_is_an_empty_string(): void
    {
        self::assertSame($this->tempDir(), resolve_session_save_path(''));
    }

    #[Test]
    public function it_falls_back_to_the_system_temp_dir_when_the_value_is_whitespace_only(): void
    {
        self::assertSame($this->tempDir(), resolve_session_save_path('   '));
        self::assertSame($this->tempDir(), resolve_session_save_path("\t\n"));
    }

    #[Test]
    public function it_falls_back_to_the_system_temp_dir_when_the_value_is_null(): void
    {
        self::assertSame($this->tempDir(), resolve_session_save_path(null));
    }

    #[Test]
    public function it_returns_an_explicit_path_unchanged(): void
    {
        self::assertSame(
            '/srv/invoiceplane/sessions',
            resolve_session_save_path('/srv/invoiceplane/sessions')
        );
    }

    #[Test]
    public function it_trims_a_trailing_slash_from_an_explicit_path(): void
    {
        self::assertSame(
            '/srv/invoiceplane/sessions',
            resolve_session_save_path('/srv/invoiceplane/sessions/')
        );
    }

    #[Test]
    public function it_honours_a_caller_supplied_fallback_for_a_blank_value(): void
    {
        self::assertSame('/var/lib/ip', resolve_session_save_path('', '/var/lib/ip'));
        self::assertSame('/var/lib/ip', resolve_session_save_path('  ', '/var/lib/ip/'));
    }

    #[Test]
    public function it_never_returns_an_empty_string_or_a_non_directory_for_a_blank_value(): void
    {
        foreach (['', '   ', "\t", null] as $blank) {
            $resolved = resolve_session_save_path($blank);

            self::assertNotSame('', $resolved, 'resolver must never return an empty save path');
            self::assertTrue(is_dir($resolved), 'resolver must return an existing directory for a blank value');
            self::assertTrue(is_writable($resolved), 'the fallback session directory must be writable');
        }
    }

    // -- Wiring guards: the fix must stay connected to the real boot path --

    #[Test]
    public function it_routes_the_sess_save_path_config_through_the_resolver(): void
    {
        $source = (string) file_get_contents(
            dirname(__DIR__, 3) . '/application/config/config.php'
        );

        self::assertMatchesRegularExpression(
            '/\$config\[\x27sess_save_path\x27\]\s*=\s*resolve_session_save_path\(/',
            $source,
            'config.php must route sess_save_path through resolve_session_save_path() or the fix is absent.'
        );
        self::assertStringNotContainsString(
            "env('SESS_SAVE_PATH', sys_get_temp_dir())",
            $source,
            'the original buggy expression must be gone from config.php.'
        );
    }

    #[Test]
    public function it_loads_the_resolver_from_the_single_kernel_boot_path(): void
    {
        $source = (string) file_get_contents(
            dirname(__DIR__, 3) . '/bootstrap/kernel.php'
        );

        self::assertStringContainsString(
            "require_once __DIR__ . '/session_path.php';",
            $source,
            'kernel.php (the sole boot path) must load bootstrap/session_path.php.'
        );
    }

    // -- Packaging guard: the shipped example config must not be able to break --

    #[Test]
    public function it_keeps_the_shipped_example_config_from_resolving_to_a_broken_session_path(): void
    {
        $example = (string) file_get_contents(
            dirname(__DIR__, 3) . '/ipconfig.php.example'
        );

        // Read SESS_SAVE_PATH exactly as phpdotenv would: the last active
        // (non-comment) assignment wins.
        $value = null;
        foreach (preg_split('/\R/', $example) ?: [] as $line) {
            if (preg_match('/^\s*SESS_SAVE_PATH\s*=(.*)$/', $line, $m)) {
                $value = trim($m[1], " \t\"'");
            }
        }

        self::assertNotNull($value, 'ipconfig.php.example must document SESS_SAVE_PATH.');

        $resolved = resolve_session_save_path($value, sys_get_temp_dir());

        self::assertNotSame('', $resolved, 'the example config must never yield an empty session save path.');
        self::assertTrue(is_dir($resolved), 'the example config must resolve to a real directory.');
    }

    // -- End-to-end: boot the real config.php with an empty SESS_SAVE_PATH --

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_resolves_a_real_directory_when_config_php_boots_with_an_empty_sess_save_path(): void
    {
        $repoRoot = dirname(__DIR__, 3);

        $code = '$_ENV["SESS_SAVE_PATH"] = "";'
            . 'require ' . var_export($repoRoot . '/bootstrap/kernel.php', true) . ';'
            . '$config = [];'
            . 'require ' . var_export($repoRoot . '/application/config/config.php', true) . ';'
            . 'echo $config["sess_save_path"];';

        $output = [];
        $status = 1;
        exec(PHP_BINARY . ' -r ' . escapeshellarg($code), $output, $status);

        self::assertSame(0, $status, 'config boot probe must exit cleanly.');

        $path = implode("\n", $output);

        self::assertNotSame('', $path, 'an empty SESS_SAVE_PATH must not survive into $config.');
        self::assertDirectoryExists($path, 'the booted config must expose a real session directory.');
        self::assertSame(rtrim(sys_get_temp_dir(), '/\\'), $path);
    }

    private function tempDir(): string
    {
        return rtrim(sys_get_temp_dir(), '/\\');
    }
}
