<?php

namespace Tests\Feature\Settings;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

class AdvancedSecurityEdgeCasesTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function custom_title_rejects_mixed_case_script_tag()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => '<Script>alert(1)</Script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_mixed_case_javascript_uri()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => 'JavaScript:alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_mixed_case_event_handler()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => 'Title" OnError="alert(1)',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_mixed_case_svg()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => '<SvG oNlOaD=alert(1)>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_invoice_terms_rejects_whitespace_padding_xss()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => '   <script>alert(1)</script>   ',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_invoice_footer_rejects_tab_padded_iframe()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => "\t<iframe src='javascript:alert(1)'>\t",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_newline_embedded_script()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => "Title\n<script>alert(1)</script>",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_invoice_terms_rejects_carriage_return_injection()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => "Terms\r\n<script>alert(1)</script>",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function currency_symbol_rejects_null_byte_prefix()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => "\x00<script>",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_rejects_null_byte_suffix()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => "<script>alert(1)\x00",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_quote_footer_rejects_angle_bracket_variants()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_quote_footer' => '＜script＞alert(1)＜/script＞',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_language_rejects_double_dot_dot_slash()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_language' => '../../etc/passwd',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function default_country_rejects_backslash_path_traversal()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_country' => '..\\..\\windows\\system32',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function date_format_rejects_xss_after_valid_format()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'date_format' => 'd/m/Y<script>alert(1)</script>',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function number_format_rejects_xss_payload_between_valid_chars()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'number_format' => 'format<script>alert(1)</script>_us_uk',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function pdf_watermark_rejects_null_byte_in_value()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_watermark' => "1\x00",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function custom_title_accepts_legitimate_symbols()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => 'MyCompany™ (USA) - 2026 © All Rights Reserved',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function default_invoice_terms_accepts_legitimate_punctuation()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'default_invoice_terms' => 'Payment due within 30 days. Late payments subject to 1.5% monthly interest (18% APY).',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function pdf_invoice_footer_accepts_url_like_text()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'pdf_invoice_footer' => 'Contact: support@company.com or visit company.com',
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
    }

    #[Test]
    public function currency_symbol_rejects_multiple_null_bytes()
    {
        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'currency_symbol' => "\x00\x00\x00",
            ],
        ]);

        $this->assertResponseRedirectsToRoute($response, 'settings');
        $this->assertSessionHasErrors();
    }

    #[Test]
    public function settings_not_updated_when_advanced_xss_bypasses_attempted()
    {
        $originalTitle = setting('custom_title');

        $response = $this->postWithValidCsrfToken('/settings', [
            'settings' => [
                'custom_title' => 'Title<ScRiPt>alert(1)</sCrIpT>',
            ],
        ]);

        $this->assertSessionHasErrors();
        $this->assertEquals($originalTitle, setting('custom_title'));
    }
}
