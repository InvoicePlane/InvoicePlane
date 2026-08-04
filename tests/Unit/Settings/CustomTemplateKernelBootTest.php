<?php

namespace Tests\Unit\Settings;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * End-to-end boot-path coverage for the custom-template env wiring
 * (companion to CustomTemplateAllowlistTest).
 *
 * CustomTemplateAllowlistTest requires bootstrap/constants.php *directly* with a
 * stubbed env() and a hand-populated $_ENV, so it proves constants.php maps the
 * four keys correctly — but it never exercises bootstrap/kernel.php, the real
 * boot path that public/index.php uses. If kernel.php's Dotenv load or its env()
 * helper regressed, the ipconfig values would silently fail to reach the
 * constants at runtime ("env not loading -> custom templates not showing") and
 * the direct-require tests would still pass.
 *
 * These tests close that gap: they set an ipconfig value in $_ENV (exactly what
 * Dotenv's createImmutable leaves behind), boot the *actual* kernel.php, and
 * assert the constant is populated from it. createImmutable is immutable, so a
 * key already present in $_ENV is preserved rather than overwritten by any
 * ipconfig.php on disk — the assertion is deterministic. Each test runs in its
 * own process because PHP constants (and the kernel's boot guard) cannot be
 * redefined within one process.
 */
class CustomTemplateKernelBootTest extends TestCase
{
    private function boot_kernel_with(array $env): void
    {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }

        require_once dirname(__DIR__, 3) . '/bootstrap/kernel.php';
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_populates_the_invoice_pdf_allowlist_constant_through_the_real_kernel(): void
    {
        $this->boot_kernel_with(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Kernel Template']);

        self::assertTrue(
            defined('CUSTOM_INVOICE_TEMPLATES_PDF'),
            'kernel.php must define the custom-template allowlist constants (via constants.php).'
        );
        self::assertSame(
            'My Kernel Template',
            constant('CUSTOM_INVOICE_TEMPLATES_PDF'),
            'The ipconfig value in $_ENV must reach the constant through the real kernel boot path.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_defines_all_four_allowlist_constants_after_boot(): void
    {
        $this->boot_kernel_with([
            'CUSTOM_INVOICE_TEMPLATES_PDF'    => 'Inv PDF',
            'CUSTOM_INVOICE_TEMPLATES_PUBLIC' => 'Inv Web',
            'CUSTOM_QUOTE_TEMPLATES_PDF'      => 'Quote PDF',
            'CUSTOM_QUOTE_TEMPLATES_PUBLIC'   => 'Quote Web',
        ]);

        self::assertSame('Inv PDF', constant('CUSTOM_INVOICE_TEMPLATES_PDF'));
        self::assertSame('Inv Web', constant('CUSTOM_INVOICE_TEMPLATES_PUBLIC'));
        self::assertSame('Quote PDF', constant('CUSTOM_QUOTE_TEMPLATES_PDF'));
        self::assertSame('Quote Web', constant('CUSTOM_QUOTE_TEMPLATES_PUBLIC'));
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_defines_the_env_helper_before_wiring_the_constants(): void
    {
        $this->boot_kernel_with(['CUSTOM_INVOICE_TEMPLATES_PDF' => '']);

        // The custom-template constants are defined from env(); if kernel.php did
        // not provide env() before requiring constants.php, boot would fatal.
        self::assertTrue(function_exists('env'), 'kernel.php must define the env() helper.');
        self::assertTrue(
            defined('CUSTOM_INVOICE_TEMPLATES_PDF'),
            'With no custom templates configured the constant must still be defined (null), not missing.'
        );
        // Depending on boot context the value is null (no ipconfig.php) or ''
        // (ipconfig.php present with the key empty); both mean "no custom
        // templates" to Mdl_Templates. What matters is it is defined and empty.
        self::assertEmpty(
            constant('CUSTOM_INVOICE_TEMPLATES_PDF'),
            'An unset/empty ipconfig key must yield an empty constant so Mdl_Templates treats it as "no custom templates".'
        );
    }
}
