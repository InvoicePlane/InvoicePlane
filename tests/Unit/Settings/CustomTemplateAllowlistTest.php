<?php

namespace Tests\Unit\Settings;

use Mdl_Templates;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Failing regression tests for the "custom templates never appear in the
 * settings dropdown" bug.
 *
 * Reproduction (from the bug report):
 *   1. create a template
 *   2. put it in a custom template dir (CUSTOM_TEMPLATES_FOLDER)
 *   3. add it to the allowlist (CUSTOM_INVOICE_TEMPLATES_PDF etc. in ipconfig.php)
 *   4. check out the template dropdown -> the custom template is missing
 *
 * The settings dropdown is populated straight from
 * Mdl_Templates::get_invoice_templates() / get_quote_templates()
 * (see settings/controllers/Settings.php), so those methods are the exact
 * data source under test here.
 *
 * Root cause: ipconfig.php values are loaded into $_ENV by
 * Dotenv::createImmutable() and are readable through the env() helper (the same
 * way CUSTOM_TEMPLATES_FOLDER is read in index.php). But index.php never
 * define()s the four CUSTOM_*_TEMPLATES_* constants, while
 * Mdl_Templates::_merge_custom() reads the allowlist exclusively through
 * defined()/constant(). The configured value therefore never reaches the model
 * and the built-in list is returned unchanged.
 *
 * These tests express the expected behaviour (the configured custom template
 * shows up) and FAIL against the current code. They are intentionally left
 * unresolved.
 */
class CustomTemplateAllowlistTest extends TestCase
{
    /**
     * The four ipconfig keys this suite drives, snapshotted so each test runs
     * in isolation regardless of the real environment.
     *
     * @var array<string, string|null>
     */
    private array $envSnapshot = [];

    private const KEYS = [
        'CUSTOM_INVOICE_TEMPLATES_PDF',
        'CUSTOM_INVOICE_TEMPLATES_PUBLIC',
        'CUSTOM_QUOTE_TEMPLATES_PDF',
        'CUSTOM_QUOTE_TEMPLATES_PUBLIC',
    ];

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/Support/template_model_stubs.php';
        require_once dirname(__DIR__, 3) . '/application/modules/invoices/models/Mdl_templates.php';

        foreach (self::KEYS as $key) {
            $this->envSnapshot[$key] = $_ENV[$key] ?? null;
            unset($_ENV[$key]);
        }
    }

    protected function tearDown(): void
    {
        foreach (self::KEYS as $key) {
            if ($this->envSnapshot[$key] === null) {
                unset($_ENV[$key]);
            } else {
                $_ENV[$key] = $this->envSnapshot[$key];
            }
        }
    }

    /**
     * Set an ipconfig value the way Dotenv::createImmutable() does at runtime.
     */
    private function configureIpconfig(string $key, string $value): void
    {
        $_ENV[$key] = $value;
    }

    #[Test]
    public function it_lists_a_custom_invoice_pdf_template_configured_in_ipconfig(): void
    {
        $this->configureIpconfig('CUSTOM_INVOICE_TEMPLATES_PDF', 'My Custom Template');

        $templates = (new Mdl_Templates())->get_invoice_templates('pdf');

        self::assertContains(
            'My Custom Template',
            $templates,
            'A custom invoice PDF template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    public function it_lists_a_custom_invoice_public_template_configured_in_ipconfig(): void
    {
        $this->configureIpconfig('CUSTOM_INVOICE_TEMPLATES_PUBLIC', 'My Web Template');

        $templates = (new Mdl_Templates())->get_invoice_templates('public');

        self::assertContains(
            'My Web Template',
            $templates,
            'A custom invoice public template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    public function it_lists_a_custom_quote_pdf_template_configured_in_ipconfig(): void
    {
        $this->configureIpconfig('CUSTOM_QUOTE_TEMPLATES_PDF', 'My Quote Template');

        $templates = (new Mdl_Templates())->get_quote_templates('pdf');

        self::assertContains(
            'My Quote Template',
            $templates,
            'A custom quote PDF template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    public function it_lists_a_custom_quote_public_template_configured_in_ipconfig(): void
    {
        $this->configureIpconfig('CUSTOM_QUOTE_TEMPLATES_PUBLIC', 'My Quote Web Template');

        $templates = (new Mdl_Templates())->get_quote_templates('public');

        self::assertContains(
            'My Quote Web Template',
            $templates,
            'A custom quote public template configured in ipconfig.php must appear in the settings dropdown.'
        );
    }

    #[Test]
    public function it_keeps_built_in_templates_alongside_a_custom_one(): void
    {
        $this->configureIpconfig('CUSTOM_INVOICE_TEMPLATES_PDF', 'My Custom Template');

        $templates = (new Mdl_Templates())->get_invoice_templates('pdf');

        self::assertContains('My Custom Template', $templates, 'The custom template must be listed.');
        self::assertContains('InvoicePlane', $templates, 'Built-in templates must still be listed after merging in a custom one.');
    }

    #[Test]
    public function it_lists_multiple_comma_separated_custom_templates(): void
    {
        $this->configureIpconfig('CUSTOM_INVOICE_TEMPLATES_PDF', 'Corporate - Modern,Corporate - Classic');

        $templates = (new Mdl_Templates())->get_invoice_templates('pdf');

        self::assertContains('Corporate - Modern', $templates);
        self::assertContains('Corporate - Classic', $templates);
    }
}
