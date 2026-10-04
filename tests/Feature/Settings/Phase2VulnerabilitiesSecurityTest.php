<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class Phase2VulnerabilitiesSecurityTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function date_format_accepts_valid_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => 'd/m/Y',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function date_format_rejects_invalid_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => 'invalid_format',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function date_format_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function date_format_rejects_javascript_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => '"><script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function number_format_accepts_valid_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => 'number_format_us_uk',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function number_format_accepts_european_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => 'number_format_european',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function number_format_rejects_invalid_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => 'nonexistent_format',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function number_format_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => '<svg onload=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function number_format_rejects_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => 'number_format_us_uk" onmouseover="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_accepts_enabled()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '1',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function pdf_watermark_accepts_disabled()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '0',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function pdf_watermark_rejects_invalid_value()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '2',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_script_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '1"><script>alert(1)</script><"',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function settings_not_updated_when_date_format_validation_fails()
    {
        $originalFormat = setting('date_format');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalFormat, setting('date_format'));
    }

    #[Test]
    public function settings_not_updated_when_number_format_validation_fails()
    {
        $originalFormat = setting('number_format');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => '<iframe src="javascript:alert(1)">',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalFormat, setting('number_format'));
    }

    #[Test]
    public function settings_not_updated_when_pdf_watermark_validation_fails()
    {
        $originalValue = setting('pdf_watermark');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '" onload="alert(1)',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalValue, setting('pdf_watermark'));
    }

    #[Test]
    public function date_format_rejects_empty_string()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => '',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function number_format_rejects_empty_string()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => '',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_empty_string()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_non_boolean_number()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => '2',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_string_true()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => 'true',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }
}
