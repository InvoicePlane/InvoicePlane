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
    /**
     * @param array<string, string> $env
     */
    private function runKernelProbe(array $env, string $constant): string
    {
        $repoRoot = dirname(__DIR__, 3);
        $envCode  = '';

        foreach ($env as $key => $value) {
            $envCode .= sprintf('$_ENV[%s] = %s;', var_export($key, true), var_export($value, true));
        }

        $code = $envCode
            . 'require ' . var_export($repoRoot . '/bootstrap/kernel.php', true) . ';'
            . 'echo defined(' . var_export($constant, true) . ') ? constant(' . var_export($constant, true) . ') : "__missing__";';

        $output = [];
        $status = 0;
        exec(PHP_BINARY . ' -r ' . escapeshellarg($code), $output, $status);

        self::assertSame(0, $status, 'Kernel probe subprocess must exit cleanly.');

        return implode("\n", $output);
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_populates_the_invoice_pdf_allowlist_constant_through_the_real_kernel(): void
    {
        /* Arrange */
        $env = ['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Kernel Template'];

        /* Act */
        $value = $this->runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF');

        /* Assert */
        self::assertSame(
            'My Kernel Template',
            $value,
            'The ipconfig value in $_ENV must reach the constant through the real kernel boot path.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_defines_all_four_allowlist_constants_after_boot(): void
    {
        $env = [
            'CUSTOM_INVOICE_TEMPLATES_PDF'    => 'Inv PDF',
            'CUSTOM_INVOICE_TEMPLATES_PUBLIC' => 'Inv Web',
            'CUSTOM_QUOTE_TEMPLATES_PDF'      => 'Quote PDF',
            'CUSTOM_QUOTE_TEMPLATES_PUBLIC'   => 'Quote Web',
        ];

        self::assertSame('Inv PDF', $this->runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF'));
        self::assertSame('Inv Web', $this->runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PUBLIC'));
        self::assertSame('Quote PDF', $this->runKernelProbe($env, 'CUSTOM_QUOTE_TEMPLATES_PDF'));
        self::assertSame('Quote Web', $this->runKernelProbe($env, 'CUSTOM_QUOTE_TEMPLATES_PUBLIC'));
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_defines_the_env_helper_before_wiring_the_constants(): void
    {
        /* Arrange */
        $env = ['CUSTOM_INVOICE_TEMPLATES_PDF' => ''];

        /* Act */
        $value = $this->runKernelProbe($env, 'CUSTOM_INVOICE_TEMPLATES_PDF');

        /* Assert */
        self::assertEmpty(
            $value,
            'An unset/empty ipconfig key must yield an empty constant so Mdl_Templates treats it as "no custom templates".'
        );
    }
}
