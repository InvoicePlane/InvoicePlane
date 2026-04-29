<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Security tests for the Sumex PDF template validation pipeline.
 *
 * The Sumex::pdf() method passes the invoice template name through
 * validate_template_name() (via select_pdf_invoice_template() when null)
 * and through validate_safe_filename() in file_security_helper.php.
 * These tests verify that each security layer rejects malicious inputs
 * and returns the safe default.
 */
class SumexTemplateSecurityTest extends CiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->CI->load->helper(['file_security', 'template']);
        $this->CI->load->model('invoices/mdl_templates');
    }

    // region path traversal

    #[Group('security')]
    #[Test]
    public function it_rejects_path_traversal_in_sumex_template(): void
    {
        // Arrange
        $malicious_template = '../../../../composer.json';

        // Act — validate_template_name is the security layer called by the Sumex pdf pipeline
        $result = validate_template_name($malicious_template, 'invoice', 'pdf');

        // Assert — path traversal must be rejected; pipeline falls back to 'InvoicePlane'
        $this->assertFalse($result, 'Path traversal template must be rejected by validate_template_name()');
    }

    #[Group('security')]
    #[Test]
    public function it_rejects_windows_style_path_traversal_in_sumex_template(): void
    {
        // Arrange
        $malicious_template = '..\\..\\..\\composer.json';

        // Act
        $result = validate_template_name($malicious_template, 'invoice', 'pdf');

        // Assert
        $this->assertFalse($result, 'Windows-style path traversal template must be rejected');
    }

    // endregion

    // region null byte injection

    #[Group('security')]
    #[Test]
    public function it_rejects_null_byte_injection_in_sumex_template(): void
    {
        // Arrange
        $malicious_template = "InvoicePlane\0/../../etc/passwd";

        // Act — validate_safe_filename (called inside validate_template_name) detects null bytes
        $result = validate_template_name($malicious_template, 'invoice', 'pdf');

        // Assert
        $this->assertFalse($result, 'Null-byte-injected template name must be rejected');
    }

    #[Group('security')]
    #[Test]
    public function it_rejects_null_byte_at_start_of_sumex_template(): void
    {
        // Arrange
        $malicious_template = "\0InvoicePlane";

        // Act
        $result = validate_template_name($malicious_template, 'invoice', 'pdf');

        // Assert
        $this->assertFalse($result, 'Template starting with null byte must be rejected');
    }

    // endregion

    // region whitelist enforcement

    #[Group('security')]
    #[Test]
    public function it_rejects_template_not_in_whitelist(): void
    {
        // Arrange
        $unlisted_template = 'EvilTemplate';

        // Act
        $result = validate_template_name($unlisted_template, 'invoice', 'pdf');

        // Assert — only templates in ALLOWED_INVOICE_TEMPLATES pass
        $this->assertFalse($result, 'Template not in static whitelist must be rejected');
    }

    #[Group('security')]
    #[Test]
    public function it_rejects_empty_template_name(): void
    {
        // Arrange
        $empty_template = '';

        // Act
        $result = validate_template_name($empty_template, 'invoice', 'pdf');

        // Assert
        $this->assertFalse($result, 'Empty template name must be rejected');
    }

    #[Group('security')]
    #[Test]
    public function it_rejects_absolute_path_as_sumex_template(): void
    {
        // Arrange
        $absolute_path = '/etc/passwd';

        // Act
        $result = validate_template_name($absolute_path, 'invoice', 'pdf');

        // Assert
        $this->assertFalse($result, 'Absolute path as template name must be rejected');
    }

    // endregion

    // region valid template acceptance

    #[Group('smoke')]
    #[Test]
    public function it_accepts_valid_sumex_template(): void
    {
        // Arrange — 'InvoicePlane' is the canonical PDF invoice template in the static whitelist
        $valid_template = 'InvoicePlane';

        // Act
        $result = validate_template_name($valid_template, 'invoice', 'pdf');

        // Assert — validation returns the template name unchanged when it passes all security layers
        $this->assertSame($valid_template, $result, 'A valid whitelisted template name must be returned as-is');
    }

    #[Group('smoke')]
    #[Test]
    public function it_accepts_all_built_in_invoice_pdf_templates(): void
    {
        // Arrange — these are the built-in templates defined in Mdl_Templates::ALLOWED_INVOICE_TEMPLATES
        $built_in_templates = ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'];

        foreach ($built_in_templates as $template) {
            // Act
            $result = validate_template_name($template, 'invoice', 'pdf');

            // Assert
            $this->assertSame($template, $result, "Built-in template '{$template}' must be accepted");
        }
    }

    // endregion

    // region default fallback when template is null

    #[Group('smoke')]
    #[Test]
    public function it_uses_default_when_sumex_template_is_null(): void
    {
        $this->skipWithoutDatabase();

        // Arrange — build a minimal invoice stub that select_pdf_invoice_template() needs.
        // is_overdue=false and invoice_status_id != 4 routes to the plain 'pdf_invoice_template' setting.
        $invoice              = new \stdClass();
        $invoice->is_overdue  = false;
        $invoice->invoice_status_id = 1;

        // Act — select_pdf_invoice_template() is called by Sumex::pdf() when $invoice_template is null.
        // It internally calls validate_template_name() and falls back to 'InvoicePlane' on failure.
        $this->CI->load->helper('template');
        $resolved = select_pdf_invoice_template($invoice);

        // Assert — the result must be a non-empty string that passes whitelist validation,
        // confirming the null-template path always yields a safe, validated default.
        $this->assertIsString($resolved, 'select_pdf_invoice_template() must return a string');
        $this->assertNotEmpty($resolved, 'select_pdf_invoice_template() must not return an empty string');
        $this->assertNotFalse(
            validate_template_name($resolved, 'invoice', 'pdf'),
            'The resolved default template must itself pass validate_template_name()'
        );
    }

    // endregion

    // region file_security_helper unit checks (used internally by validate_template_name)

    #[Group('security')]
    #[Test]
    public function it_detects_path_traversal_via_file_security_helper(): void
    {
        // Arrange
        $traversal_inputs = [
            '../etc/passwd',
            '..\\Windows\\System32',
            'foo/../../bar',
            '/absolute/path',
        ];

        foreach ($traversal_inputs as $input) {
            // Act
            $validation = validate_safe_filename($input);

            // Assert — each unsafe input must be flagged
            $this->assertFalse(
                $validation['valid'],
                "validate_safe_filename() must reject unsafe input: {$input}"
            );
            $this->assertArrayHasKey('error', $validation);
        }
    }

    #[Group('security')]
    #[Test]
    public function it_detects_null_byte_via_file_security_helper(): void
    {
        // Arrange
        $null_byte_inputs = [
            "InvoicePlane\0",
            "\0InvoicePlane",
            "Invoice\0Plane",
        ];

        foreach ($null_byte_inputs as $input) {
            // Act
            $validation = validate_safe_filename($input);

            // Assert
            $this->assertFalse($validation['valid'], "validate_safe_filename() must reject null-byte input");
            $this->assertSame('null_byte', $validation['error']);
        }
    }

    #[Group('smoke')]
    #[Test]
    public function it_accepts_safe_template_name_via_file_security_helper(): void
    {
        // Arrange — a safe name that would survive path/null-byte checks
        $safe_inputs = [
            'InvoicePlane',
            'InvoicePlane - paid',
            'InvoicePlane - overdue',
            'InvoicePlane_Web',
        ];

        foreach ($safe_inputs as $input) {
            // Act
            $validation = validate_safe_filename($input);

            // Assert
            $this->assertTrue($validation['valid'], "validate_safe_filename() must accept safe name: {$input}");
        }
    }

    // endregion
}
