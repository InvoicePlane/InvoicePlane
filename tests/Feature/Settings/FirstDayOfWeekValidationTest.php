<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class FirstDayOfWeekValidationTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_accepts_valid_first_day_of_week_values(): void
    {
        for ($day = 0; $day <= 6; $day++) {
            $response = $this->post('/settings', [
                'settings' => [
                    'first_day_of_week' => (string) $day,
                ],
            ]);

            $this->assertResponseRedirectsToRoute($response, 'settings');
            $this->assertDatabaseHas('ip_settings', [
                'setting_key'   => 'first_day_of_week',
                'setting_value' => (string) $day,
            ]);
        }
    }

    #[Test]
    public function it_rejects_invalid_first_day_of_week_values(): void
    {
        $original_setting = $this->getSettingFromDatabase('first_day_of_week');

        $invalid_values = ['-1', '7', '8', '99', 'invalid', 'null'];

        foreach ($invalid_values as $invalid) {
            $response = $this->post('/settings', [
                'settings' => [
                    'first_day_of_week' => $invalid,
                ],
            ]);

            $current_setting = $this->getSettingFromDatabase('first_day_of_week');
            $this->assertEquals(
                $original_setting,
                $current_setting,
                "Invalid value '{$invalid}' should not change setting"
            );
        }
    }

    #[Test]
    public function it_rejects_xss_injection_via_quote_breakout(): void
    {
        $xss_payload = "1' + window['ale'+'rt']('XSS') + '";
        $original_setting = $this->getSettingFromDatabase('first_day_of_week');

        $response = $this->post('/settings', [
            'settings' => [
                'first_day_of_week' => $xss_payload,
            ],
        ]);

        $current_setting = $this->getSettingFromDatabase('first_day_of_week');
        $this->assertEquals($original_setting, $current_setting, 'XSS payload should not change setting');
    }

    #[Test]
    public function it_rejects_xss_injection_via_newline(): void
    {
        $xss_payload = "1\nalert('XSS')//";
        $original_setting = $this->getSettingFromDatabase('first_day_of_week');

        $response = $this->post('/settings', [
            'settings' => [
                'first_day_of_week' => $xss_payload,
            ],
        ]);

        $current_setting = $this->getSettingFromDatabase('first_day_of_week');
        $this->assertEquals($original_setting, $current_setting, 'Newline injection should not change setting');
    }

    #[Test]
    public function it_rejects_xss_injection_via_unicode_separators(): void
    {
        $xss_payload = "1\u{2028}alert('XSS')";
        $original_setting = $this->getSettingFromDatabase('first_day_of_week');

        $response = $this->post('/settings', [
            'settings' => [
                'first_day_of_week' => $xss_payload,
            ],
        ]);

        $current_setting = $this->getSettingFromDatabase('first_day_of_week');
        $this->assertEquals($original_setting, $current_setting, 'Unicode separator injection should not change setting');
    }

    private function getSettingFromDatabase(string $key): string
    {
        $CI = &get_instance();
        $CI->load->model('mdl_settings');
        return $CI->mdl_settings->get_setting($key) ?? '';
    }
}
