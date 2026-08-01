<?php

namespace Tests\Unit\Settings;

use Mdl_Templates;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * End-to-end coverage for the "custom templates configured in ipconfig must
 * appear in the settings dropdown" fix (PR InvoicePlane/InvoicePlane#1648).
 *
 * The dropdown is populated straight from
 * Mdl_Templates::get_invoice_templates() / get_quote_templates()
 * (settings/controllers/Settings.php), which read the four allowlists via
 * defined()/constant(). Nothing used to define those constants, so custom
 * templates never surfaced.
 *
 * The fix defines them from their ipconfig.php keys in the bootstrap. There are
 * two independent bootstraps in this repo:
 *   - the legacy root index.php (shipped to production; nginx serves it), and
 *   - bootstrap/constants.php (used by public/index.php and the test harness).
 * Both must wire the constants or the fix is silently absent on one path.
 *
 * These tests drive the *real* wiring: they set the ipconfig value in $_ENV
 * (as Dotenv does at runtime), require the actual bootstrap/constants.php, and
 * assert the model surfaces the name. Each runs in a separate process because
 * PHP constants cannot be redefined within one process. Without the constants.php
 * fix they fail; with it they pass — so a revert of the fix breaks them.
 */
class CustomTemplateAllowlistTest extends TestCase
{
    /**
     * Boot the real bootstrap constants with the given ipconfig values in $_ENV,
     * then return a fresh Mdl_Templates. Mirrors the runtime order: env is
     * populated (Dotenv) -> bootstrap defines the constants -> the model reads them.
     *
     * @param array<string, string> $ipconfig
     */
    private function bootModelWith(array $ipconfig): Mdl_Templates
    {
        // Stubs: CI_Model, log_message(), and an env() that reads $_ENV exactly
        // like the real helper kernel.php defines before requiring constants.php.
        require_once dirname(__DIR__, 2) . '/Support/template_model_stubs.php';

        foreach ($ipconfig as $key => $value) {
            $_ENV[$key] = $value;
        }

        require_once dirname(__DIR__, 3) . '/bootstrap/constants.php';
        require_once dirname(__DIR__, 3) . '/application/modules/invoices/models/Mdl_templates.php';

        return new Mdl_Templates();
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_lists_a_custom_invoice_pdf_template_configured_in_ipconfig(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Custom Template']);

        self::assertContains(
            'My Custom Template',
            $model->get_invoice_templates('pdf'),
            'A custom invoice PDF template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_lists_a_custom_invoice_public_template_configured_in_ipconfig(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PUBLIC' => 'My Web Template']);

        self::assertContains(
            'My Web Template',
            $model->get_invoice_templates('public'),
            'A custom invoice public template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_lists_a_custom_quote_pdf_template_configured_in_ipconfig(): void
    {
        $model = $this->bootModelWith(['CUSTOM_QUOTE_TEMPLATES_PDF' => 'My Quote Template']);

        self::assertContains(
            'My Quote Template',
            $model->get_quote_templates('pdf'),
            'A custom quote PDF template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_lists_a_custom_quote_public_template_configured_in_ipconfig(): void
    {
        $model = $this->bootModelWith(['CUSTOM_QUOTE_TEMPLATES_PUBLIC' => 'My Quote Web Template']);

        self::assertContains(
            'My Quote Web Template',
            $model->get_quote_templates('public'),
            'A custom quote public template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_keeps_built_in_templates_alongside_a_custom_one(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'My Custom Template']);

        $templates = $model->get_invoice_templates('pdf');

        self::assertContains('My Custom Template', $templates, 'The custom template must be listed.');
        self::assertContains('InvoicePlane', $templates, 'Built-in templates must still be listed after merging in a custom one.');
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_lists_multiple_comma_separated_custom_templates(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'Corporate - Modern,Corporate - Classic']);

        $templates = $model->get_invoice_templates('pdf');

        self::assertContains('Corporate - Modern', $templates);
        self::assertContains('Corporate - Classic', $templates);
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_returns_only_built_ins_when_no_custom_templates_are_configured(): void
    {
        $model = $this->bootModelWith([]);

        self::assertSame(
            ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],
            $model->get_invoice_templates('pdf'),
            'With nothing configured the list must be exactly the built-in whitelist, unchanged.'
        );
    }

    // -- Deny path: the allowlist regex in _merge_custom() must reject unsafe names --

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_rejects_a_path_traversal_custom_template_name(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => '../../evil']);

        $templates = $model->get_invoice_templates('pdf');

        self::assertNotContains('../../evil', $templates, 'A path-traversal name must never enter the allowlist.');
        self::assertSame(
            ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],
            $templates,
            'A rejected name must leave the built-in list untouched.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_rejects_a_php_extension_custom_template_name(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'evil.php']);

        self::assertNotContains(
            'evil.php',
            $model->get_invoice_templates('pdf'),
            'A name containing a file extension (the "." is disallowed) must be rejected.'
        );
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_keeps_the_valid_name_and_drops_the_invalid_one_from_a_mixed_list(): void
    {
        $model = $this->bootModelWith(['CUSTOM_INVOICE_TEMPLATES_PDF' => 'Good Template,../evil']);

        $templates = $model->get_invoice_templates('pdf');

        self::assertContains('Good Template', $templates, 'The valid name must pass the allowlist.');
        self::assertNotContains('../evil', $templates, 'The invalid name must be dropped, not the whole list.');
    }

    // -- Bootstrap wiring guard: both production and harness paths must define all four --

    #[Test]
    public function both_bootstraps_wire_all_four_allowlist_constants_from_ipconfig(): void
    {
        $repoRoot   = dirname(__DIR__, 3);
        $bootstraps = [
            'index.php'              => (string) file_get_contents($repoRoot . '/index.php'),
            'bootstrap/constants.php' => (string) file_get_contents($repoRoot . '/bootstrap/constants.php'),
        ];

        $constants = [
            'CUSTOM_INVOICE_TEMPLATES_PDF',
            'CUSTOM_INVOICE_TEMPLATES_PUBLIC',
            'CUSTOM_QUOTE_TEMPLATES_PDF',
            'CUSTOM_QUOTE_TEMPLATES_PUBLIC',
        ];

        foreach ($bootstraps as $file => $source) {
            foreach ($constants as $constant) {
                $pattern = '/define\(\s*[\'"]' . preg_quote($constant, '/') . '[\'"]\s*,\s*env\(/';
                self::assertMatchesRegularExpression(
                    $pattern,
                    $source,
                    sprintf('%s must define %s from its ipconfig env key, or the fix is absent on that boot path.', $file, $constant)
                );
            }
        }
    }
}
