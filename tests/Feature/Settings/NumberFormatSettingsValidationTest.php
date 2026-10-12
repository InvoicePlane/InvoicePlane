<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class NumberFormatSettingsValidationTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function decimal_point_accepts_valid_single_character()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => '.',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function decimal_point_accepts_comma()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => ',',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function decimal_point_rejects_multiple_characters()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => '..',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function decimal_point_rejects_html_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function decimal_point_rejects_javascript_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => '"><script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function thousands_separator_accepts_valid_single_character()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'thousands_separator' => ',',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function thousands_separator_accepts_space()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'thousands_separator' => ' ',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function thousands_separator_rejects_multiple_characters()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'thousands_separator' => ',,',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function thousands_separator_rejects_html_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'thousands_separator' => '" onload="alert(1)"',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function thousands_separator_rejects_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'thousands_separator' => '\' onmouseover=\'alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function decimal_and_thousands_separator_must_not_be_identical()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point'       => '.',
                'thousands_separator' => '.',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function settings_not_updated_when_validation_fails()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'decimal_point' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertNotEquals('<script>alert(1)</script>', setting('decimal_point'));
    }
}
