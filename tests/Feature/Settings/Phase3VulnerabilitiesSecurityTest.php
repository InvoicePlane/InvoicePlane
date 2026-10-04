<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class Phase3VulnerabilitiesSecurityTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function default_language_accepts_valid_language()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => 'english',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function default_language_rejects_invalid_language()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => 'nonexistent_lang',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_language_rejects_path_traversal()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => '../../../etc/passwd',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_language_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => '<img src=x onerror=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_language_rejects_javascript_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => 'english" onclick="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_country_accepts_valid_country()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => 'US',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function default_country_rejects_invalid_country()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => 'XX',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_country_rejects_path_traversal()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => '../../config.php',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_country_rejects_xss_payload()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => '<script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_country_rejects_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => 'US" onload="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function settings_not_updated_when_default_language_validation_fails()
    {
        $originalLang = setting('default_language');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => '<iframe src="javascript:alert(1)">',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalLang, setting('default_language'));
    }

    #[Test]
    public function settings_not_updated_when_default_country_validation_fails()
    {
        $originalCtry = setting('default_country');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => '../../sensitive/file',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalCtry, setting('default_country'));
    }
}
