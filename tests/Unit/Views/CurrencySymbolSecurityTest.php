<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-x4g3-fp78-5gjx: Stored XSS via unescaped currency_symbol on public guest invoice/quote pages.
 */
class CurrencySymbolSecurityTest extends TestCase
{
    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousCi          = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance'] = (object) ['mdl_settings' => new FakeCiSettings()];
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/settings_helper.php';
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_get_setting(): void
    {
        // When get_setting('currency_symbol') is called without escaping,
        // and the setting contains HTML, it should be escaped by the caller

        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<script>alert(1)</script>';

        // get_setting without escape flag returns raw value
        $raw = get_setting('currency_symbol', '', false);
        $this->assertStringContainsString('<script>', $raw);

        // get_setting with escape flag should return escaped
        $escaped = get_setting('currency_symbol', '', true);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
        $this->assertStringNotContainsString('<script>', $escaped);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_a_real_view_sink(): void
    {
        /*
         * Regression for GHSA-gpv9-p6gj-238h: 15 view sinks echo currency_symbol directly
         * via htmlsc(get_setting('currency_symbol')). The previous version of this test
         * called htmlsc() on a local variable instead of any of those 15 files, so it would
         * stay green even if every one of them were reverted to a raw echo. This renders
         * one of the actual sinks (shared by both the invoice and quote item-discount
         * partials) and asserts on its real output.
         */

        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<img src=x onerror=alert(1)>';

        /* Act */
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/layout/views/partial/itemlist_table_item_discount_input.php';
        $html = (string) ob_get_clean();

        /* Assert */
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_preserves_a_benign_currency_symbol_in_a_real_view_sink(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '€';

        /* Act */
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/layout/views/partial/itemlist_table_item_discount_input.php';
        $html = (string) ob_get_clean();

        /* Assert */
        $this->assertStringContainsString('€', $html);
    }

    /**
     * GHSA-gpv9-p6gj-238h touched 15 sinks across 10 files, all the identical
     * `echo htmlsc(get_setting('currency_symbol'))` pattern. Rendering each of the
     * remaining 9 files faithfully would need full invoice/quote/product/task fixtures
     * for no extra defect-detection value, since the fix is mechanically identical in
     * every sink. These three render the small, self-contained discount partials
     * directly (real sinks, real assertions) to extend beyond the one partial already
     * covered above.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_the_invoice_discount_partial(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<img src=x onerror=alert(1)>';
        $invoice                                    = (object) [
            'is_read_only'             => 0,
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
        ];

        /* Act */
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/invoices/views/partial_itemlist_table_invoice_discount.php';
        $html = (string) ob_get_clean();

        /* Assert */
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_the_quote_discount_partial(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<img src=x onerror=alert(1)>';
        $quote                                      = (object) [
            'quote_discount_amount'  => 0,
            'quote_discount_percent' => 0,
        ];

        /* Act */
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/quotes/views/partial_itemlist_table_quote_discount.php';
        $html = (string) ob_get_clean();

        /* Assert */
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_the_item_discount_input_partial(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<img src=x onerror=alert(1)>';
        $item                                       = (object) ['item_id' => 1];

        /* Act */
        ob_start();
        include dirname(__DIR__, 3) . '/application/modules/layout/views/partial/itemlist_responsive_item_discount_input.php';
        $html = (string) ob_get_clean();

        /* Assert */
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
    }

    /**
     * Backstop for the 6 remaining sinks (invoice/quote table + responsive partials,
     * plus products/form.php and tasks/form.php) that take a full invoice/quote/product/
     * task fixture to render faithfully. This asserts the literal escaped call survives
     * at each known line, so a revert to a raw `echo get_setting('currency_symbol')` in
     * any of them fails this test even without a full render.
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_keeps_the_escaped_currency_symbol_call_at_every_known_sink(): void
    {
        /* Arrange */
        $root  = dirname(__DIR__, 3);
        $sinks = [
            'application/modules/invoices/views/partial_itemlist_responsive.php' => [81, 258],
            'application/modules/invoices/views/partial_itemlist_table.php'      => [60, 208],
            'application/modules/quotes/views/partial_itemlist_responsive.php'   => [56, 189],
            'application/modules/quotes/views/partial_itemlist_table.php'        => [49, 157],
            'application/modules/products/views/form.php'                        => [80, 143],
            'application/modules/tasks/views/form.php'                           => [71],
        ];

        foreach ($sinks as $relativePath => $lines) {
            /* Act */
            $source = file_get_contents($root . '/' . $relativePath);
            $this->assertNotFalse($source, "Could not read {$relativePath}.");
            $fileLines = explode("\n", $source);

            foreach ($lines as $lineNumber) {
                $line = $fileLines[$lineNumber - 1] ?? '';

                /* Assert */
                $this->assertMatchesRegularExpression(
                    '/htmlsc\(\s*get_setting\(\s*[\'"]currency_symbol[\'"]/',
                    $line,
                    "{$relativePath}:{$lineNumber} no longer wraps get_setting('currency_symbol') in htmlsc() — GHSA-gpv9-p6gj-238h regression."
                );
            }
        }
    }
}
