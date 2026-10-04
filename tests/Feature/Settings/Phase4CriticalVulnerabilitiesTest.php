<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class Phase4CriticalVulnerabilitiesTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function custom_title_accepts_valid_text()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => 'My Company',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function custom_title_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_script_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '"><script>alert(1)</script><"',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '" onload="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_svg_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '<svg onload=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_invoice_terms_accepts_valid_text()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => 'Payment due within 30 days',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function default_invoice_terms_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_invoice_terms_rejects_script_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_invoice_footer_accepts_valid_text()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => 'Company Name - 123 Main St',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function pdf_invoice_footer_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => '<iframe src="javascript:alert(1)">',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_invoice_footer_rejects_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => '" onclick="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_quote_footer_accepts_valid_text()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_quote_footer' => 'Quote valid for 30 days',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function pdf_quote_footer_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_quote_footer' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public void pdf_quote_footer_rejects_javascript_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_quote_footer' => '"><script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function settings_not_updated_when_custom_title_validation_fails()
    {
        $originalTitle = setting('custom_title');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalTitle, setting('custom_title'));
    }

    #[Test]
    public function settings_not_updated_when_default_invoice_terms_validation_fails()
    {
        $originalTerms = setting('default_invoice_terms');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => '<iframe src="javascript:alert(1)">',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalTerms, setting('default_invoice_terms'));
    }

    #[Test]
    public function settings_not_updated_when_pdf_invoice_footer_validation_fails()
    {
        $originalFooter = setting('pdf_invoice_footer');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => '" onload="alert(1)',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalFooter, setting('pdf_invoice_footer'));
    }

    #[Test]
    public function settings_not_updated_when_pdf_quote_footer_validation_fails()
    {
        $originalFooter = setting('pdf_quote_footer');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_quote_footer' => '<svg onload=alert(1)>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalFooter, setting('pdf_quote_footer'));
    }
}
