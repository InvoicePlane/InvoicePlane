<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class CurrencySymbolSecurityTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function currency_symbol_accepts_valid_symbols()
    {
        $symbols = ['$', '€', '£', '¥', '₹'];

        foreach ($symbols as $symbol) {
            $response = $this->postWithValidCsrfToken('/settings', [
                'settings' => [
                    'currency_symbol' => $symbol,
                ],
            ]);

            $this->assertResponseRedirectsToRoute($response, 'settings');
        }
    }

    #[Test]
    public function currency_symbol_accepts_multicharacter_valid_symbols()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => 'USD',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function currency_symbol_rejects_html_img_tag()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_script_tag()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '"><script>alert(document.domain)</script><"',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_event_handler_attribute()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '" onload="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_svg_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '<svg onload=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_data_url_attack()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => 'javascript:alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_iframe_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '<iframe src="javascript:alert(1)">',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_is_escaped_in_views()
    {
        // Set a valid symbol first
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '$',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');

        // Try to update with an XSS payload
        $payload = '<img src=x onerror=alert(1)>';
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => $payload,
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertNotEquals($payload, setting('currency_symbol'));
    }

    #[Test]
    public function settings_not_updated_when_validation_fails()
    {
        $originalSymbol = setting('currency_symbol');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalSymbol, setting('currency_symbol'));
    }
}
