<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * Found via mutation-check while re-scrutinizing PR #1744's `currency_symbol` fix: these
 * discount-field partials (rendered alongside the escaped currency symbol) have their own,
 * unrelated display logic — a read-only "disabled" attribute toggle and a blank-vs-formatted
 * amount toggle — that had no coverage at all. Pre-existing gaps, not part of #1744 itself,
 * closed here rather than left parked.
 */
class DiscountFieldDisplayTest extends TestCase
{
    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $root = dirname(__DIR__, 3);
        require_once $root . '/application/helpers/echo_helper.php';
        require_once $root . '/application/helpers/number_helper.php';
        require_once $root . '/application/helpers/trans_helper.php';
        require_once $root . '/application/helpers/settings_helper.php';

        $this->previousCi                = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance']       = (object) ['mdl_settings' => new FakeCiSettings()];
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[Test]
    public function it_disables_the_item_discount_input_when_the_invoice_is_read_only(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 1];
        $item    = (object) ['item_id' => 1, 'item_discount_amount' => 0];

        /* Act */
        $html = $this->render('application/modules/layout/views/partial/itemlist_table_item_discount_input.php', [
            'invoice' => $invoice, 'item' => $item,
        ]);

        /* Assert */
        $this->assertStringContainsString('disabled="disabled"', $html);
    }

    #[Test]
    public function it_does_not_disable_the_item_discount_input_when_the_invoice_is_editable(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 0];
        $item    = (object) ['item_id' => 1, 'item_discount_amount' => 0];

        /* Act */
        $html = $this->render('application/modules/layout/views/partial/itemlist_table_item_discount_input.php', [
            'invoice' => $invoice, 'item' => $item,
        ]);

        /* Assert */
        $this->assertStringNotContainsString('disabled="disabled"', $html);
    }

    #[Test]
    public function it_disables_the_responsive_item_discount_input_when_the_invoice_is_read_only(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 1];
        $item    = (object) ['item_id' => 1, 'item_discount_amount' => 0];

        /* Act */
        $html = $this->render('application/modules/layout/views/partial/itemlist_responsive_item_discount_input.php', [
            'invoice' => $invoice, 'item' => $item,
        ]);

        /* Assert */
        $this->assertStringContainsString('disabled="disabled"', $html);
    }

    #[Test]
    public function it_does_not_disable_the_responsive_item_discount_input_when_the_invoice_is_editable(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 0];
        $item    = (object) ['item_id' => 1, 'item_discount_amount' => 0];

        /* Act */
        $html = $this->render('application/modules/layout/views/partial/itemlist_responsive_item_discount_input.php', [
            'invoice' => $invoice, 'item' => $item,
        ]);

        /* Assert */
        $this->assertStringNotContainsString('disabled="disabled"', $html);
    }

    #[Test]
    public function it_disables_the_invoice_discount_inputs_when_the_invoice_is_read_only(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 1, 'invoice_discount_amount' => 0, 'invoice_discount_percent' => 0];

        /* Act */
        $html = $this->render('application/modules/invoices/views/partial_itemlist_table_invoice_discount.php', [
            'invoice' => $invoice,
        ]);

        /* Assert */
        $this->assertSame(2, substr_count($html, 'disabled="disabled"'));
    }

    #[Test]
    public function it_does_not_disable_the_invoice_discount_inputs_when_the_invoice_is_editable(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 0, 'invoice_discount_amount' => 0, 'invoice_discount_percent' => 0];

        /* Act */
        $html = $this->render('application/modules/invoices/views/partial_itemlist_table_invoice_discount.php', [
            'invoice' => $invoice,
        ]);

        /* Assert */
        $this->assertStringNotContainsString('disabled="disabled"', $html);
    }

    #[Test]
    public function it_shows_a_blank_invoice_discount_value_when_zero(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 0, 'invoice_discount_amount' => 0, 'invoice_discount_percent' => 0];

        /* Act */
        $html = $this->render('application/modules/invoices/views/partial_itemlist_table_invoice_discount.php', [
            'invoice' => $invoice,
        ]);

        /* Assert */
        $this->assertMatchesRegularExpression('/id="invoice_discount_amount"[^>]*value=""/s', $html);
        $this->assertMatchesRegularExpression('/id="invoice_discount_percent"[^>]*value=""/s', $html);
    }

    #[Test]
    public function it_shows_the_formatted_invoice_discount_value_when_nonzero(): void
    {
        /* Arrange */
        $invoice = (object) ['is_read_only' => 0, 'invoice_discount_amount' => 15, 'invoice_discount_percent' => 10];

        /* Act */
        $html = $this->render('application/modules/invoices/views/partial_itemlist_table_invoice_discount.php', [
            'invoice' => $invoice,
        ]);

        /* Assert */
        $this->assertStringContainsString('value="15"', $html);
        $this->assertStringContainsString('value="10"', $html);
    }

    #[Test]
    public function it_shows_the_formatted_invoice_discount_value_when_it_equals_one(): void
    {
        /*
         * Distinguishes the real `!= 0` check from a `!= 1` mutant: both pass a nonzero
         * value like 15 through unchanged, but only the real check also shows a discount
         * of exactly 1 instead of blanking it out.
         */

        /* Arrange */
        $invoice = (object) ['is_read_only' => 0, 'invoice_discount_amount' => 1, 'invoice_discount_percent' => 1];

        /* Act */
        $html = $this->render('application/modules/invoices/views/partial_itemlist_table_invoice_discount.php', [
            'invoice' => $invoice,
        ]);

        /* Assert */
        $this->assertMatchesRegularExpression('/id="invoice_discount_amount"[^>]*value="1"/s', $html);
        $this->assertMatchesRegularExpression('/id="invoice_discount_percent"[^>]*value="1"/s', $html);
    }

    #[Test]
    public function it_shows_a_blank_quote_discount_value_when_zero(): void
    {
        /* Arrange */
        $quote = (object) ['quote_discount_amount' => 0, 'quote_discount_percent' => 0];

        /* Act */
        $html = $this->render('application/modules/quotes/views/partial_itemlist_table_quote_discount.php', [
            'quote' => $quote,
        ]);

        /* Assert */
        $this->assertStringContainsString('id="quote_discount_amount" name="quote_discount_amount"', $html);
        $this->assertMatchesRegularExpression('/id="quote_discount_amount"[^>]*value=""/', $html);
        $this->assertMatchesRegularExpression('/id="quote_discount_percent"[^>]*value=""/', $html);
    }

    #[Test]
    public function it_shows_the_formatted_quote_discount_value_when_nonzero(): void
    {
        /* Arrange */
        $quote = (object) ['quote_discount_amount' => 25, 'quote_discount_percent' => 5];

        /* Act */
        $html = $this->render('application/modules/quotes/views/partial_itemlist_table_quote_discount.php', [
            'quote' => $quote,
        ]);

        /* Assert */
        $this->assertMatchesRegularExpression('/id="quote_discount_amount"[^>]*value="25"/', $html);
        $this->assertMatchesRegularExpression('/id="quote_discount_percent"[^>]*value="5"/', $html);
    }

    #[Test]
    public function it_shows_the_formatted_quote_discount_value_when_it_equals_one(): void
    {
        /*
         * Distinguishes the real `!= 0` check from a `!= 1` mutant: both pass a nonzero
         * value like 25 through unchanged, but only the real check also shows a discount
         * of exactly 1 instead of blanking it out.
         */

        /* Arrange */
        $quote = (object) ['quote_discount_amount' => 1, 'quote_discount_percent' => 1];

        /* Act */
        $html = $this->render('application/modules/quotes/views/partial_itemlist_table_quote_discount.php', [
            'quote' => $quote,
        ]);

        /* Assert */
        $this->assertMatchesRegularExpression('/id="quote_discount_amount"[^>]*value="1"/s', $html);
        $this->assertMatchesRegularExpression('/id="quote_discount_percent"[^>]*value="1"/s', $html);
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $relativePath, array $vars): string
    {
        extract($vars);

        ob_start();
        include dirname(__DIR__, 3) . '/' . $relativePath;

        return (string) ob_get_clean();
    }
}
