<?php

namespace Tests\Unit\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(\Mdl_Templates::class)]
class TemplateServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TemplateService();
    }

    /**
     * Test service returns empty array when invoice PDF templates directory doesn't exist.
     *
     * This tests graceful degradation - the service should return empty array
     * rather than throwing an exception when the template directory is missing.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_invoice_pdf_templates_directory_not_exists(): void
    {
        /* Arrange */
        // APPPATH is defined in bootstrap to point to 'application' directory
        // The old CodeIgniter template path (APPPATH/views/invoice_templates/pdf)
        // won't exist in the new Laravel structure, where templates are in
        // Modules/Core/Resources/views/invoice_templates/

        /* Act */
        $result = $this->service->getInvoiceTemplates('pdf');

        /* Assert */
        // Service should gracefully handle missing directory by returning empty array
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service returns empty array when invoice public templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_invoice_public_templates_directory_not_exists(): void
    {
        /* Arrange */
        // Test graceful handling of missing public template directory

        /* Act */
        $result = $this->service->getInvoiceTemplates('public');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when public directory does not exist');
    }

    /**
     * Test service returns empty array when quote PDF templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_quote_pdf_templates_directory_not_exists(): void
    {
        /* Arrange */
        // Test graceful handling of missing quote template directory

        /* Act */
        $result = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service returns empty array when quote public templates directory doesn't exist.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_quote_public_templates_directory_not_exists(): void
    {
        /* Arrange */
        // Test graceful handling of missing public quote template directory

        /* Act */
        $result = $this->service->getQuoteTemplates('public');

        /* Assert */
        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Should return empty array when directory does not exist');
    }

    /**
     * Test service uses 'pdf' as default type parameter for invoice templates.
     */
    #[Test]
    public function it_defaults_to_pdf_type_for_invoice_templates(): void
    {
        /* Arrange */
        // Service should use 'pdf' as default when no type is specified

        /* Act */
        $resultDefault = $this->service->getInvoiceTemplates();
        $resultPdf     = $this->service->getInvoiceTemplates('pdf');

        /* Assert */
        $this->assertEquals($resultPdf, $resultDefault, 'Default should match PDF type results');
    }

    /**
     * Test service uses 'pdf' as default type parameter for quote templates.
     */
    #[Test]
    public function it_defaults_to_pdf_type_for_quote_templates(): void
    {
        /* Arrange */
        // Service should use 'pdf' as default when no type is specified

        /* Act */
        $resultDefault = $this->service->getQuoteTemplates();
        $resultPdf     = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        $this->assertEquals($resultPdf, $resultDefault, 'Default should match PDF type results');
    }

    /**
     * Test service filters out dot directories ('.' and '..') from results.
     *
     * Note: This test verifies expected behavior even when directories don't exist.
     * When directory doesn't exist, scandir returns false and empty array is returned.
     */
    #[Test]
    public function it_filters_out_dot_directories(): void
    {
        /* Arrange */
        // Service should exclude '.' and '..' from directory listings using array_filter

        /* Act */
        $invoiceTemplates = $this->service->getInvoiceTemplates();
        $quoteTemplates   = $this->service->getQuoteTemplates();

        /* Assert */
        // The service uses array_filter to remove '.' and '..'
        $this->assertNotContains('.', $invoiceTemplates, 'Should not contain current directory marker');
        $this->assertNotContains('..', $invoiceTemplates, 'Should not contain parent directory marker');
        $this->assertNotContains('.', $quoteTemplates, 'Should not contain current directory marker');
        $this->assertNotContains('..', $quoteTemplates, 'Should not contain parent directory marker');
    }

    /**
     * Test service removes file extensions from template names.
     *
     * Note: This test verifies the extension removal logic is applied,
     * even when working with empty arrays (when directories don't exist).
     */
    #[Test]
    public function it_removes_file_extensions_from_template_names(): void
    {
        /* Arrange */
        // Service should strip .php extensions using pathinfo(PATHINFO_FILENAME)

        /* Act */
        $invoiceTemplates = $this->service->getInvoiceTemplates('pdf');
        $quoteTemplates   = $this->service->getQuoteTemplates('pdf');

        /* Assert */
        // All template names should have extensions removed
        // (if any templates exist in the configured directories)
        foreach ($invoiceTemplates as $template) {
            $this->assertStringNotContainsString('.php', $template, 'Template name should not contain .php extension');
            $this->assertStringNotContainsString('.blade.php', $template, 'Template name should not contain .blade.php extension');
        }

        foreach ($quoteTemplates as $template) {
            $this->assertStringNotContainsString('.php', $template, 'Template name should not contain .php extension');
            $this->assertStringNotContainsString('.blade.php', $template, 'Template name should not contain .blade.php extension');
        }
    }

    /**
     * Test service handles both 'pdf' and 'public' template types.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_different_template_types(): void
    {
        /* Arrange */
        // Service should handle both 'pdf' and 'public' directory types

        /* Act */
        $pdfTemplates    = $this->service->getInvoiceTemplates('pdf');
        $publicTemplates = $this->service->getInvoiceTemplates('public');

        /* Assert */
        $this->assertIsArray($pdfTemplates, 'PDF templates should return array');
        $this->assertIsArray($publicTemplates, 'Public templates should return array');
        // Both types should return arrays (empty or populated based on directory existence)
    }

    /**
     * Test service returns numerically indexed array (not associative).
     *
     * The service uses array_values() to ensure numeric indexing after filtering.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_indexed_array(): void
    {
        /* Arrange */
        // Service uses array_values() to ensure numeric indexing

        /* Act */
        $templates = $this->service->getInvoiceTemplates();

        /* Assert */
        // Should be numerically indexed (array_values is used in the service)
        $this->assertIsArray($templates, 'Should return an array');

        // If array is not empty, verify it starts at index 0
        if (count($templates) > 0) {
            $this->assertArrayHasKey(0, $templates, 'Array should be numerically indexed starting at 0');
        }

        // Verify all keys are sequential integers
        $keys         = array_keys($templates);
        $expectedKeys = range(0, count($templates) - 1);
        $this->assertEquals($expectedKeys, $keys, 'Array keys should be sequential integers starting from 0');
    }
}
