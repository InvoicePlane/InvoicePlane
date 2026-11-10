<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

#[Group('unit')]
#[Group('security')]
final class ItemAmountHelperTest extends TestCase
{
    private mixed $ciBackup = null;

    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/trans_helper.php';
        require_once APPPATH . 'helpers/number_helper.php';
        require_once APPPATH . 'helpers/item_amount_helper.php';
        $this->ciBackup  = $GLOBALS['unitCiInstance'] ?? null;
        $settings        = new FakeCiSettings();
        $settings->_data = ['thousands_separator' => '.', 'decimal_point' => ','];
        $lang            = new class () {
            public function line(string $key): string
            {
                return 'T:' . $key;
            }
        };
        $GLOBALS['unitCiInstance'] = (object) ['mdl_settings' => $settings, 'lang' => $lang];
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->ciBackup;
    }

    /** @return array<string, array{array<string, string>, list<string>}> */
    public static function negativeItems(): array
    {
        return [
            'quantity'            => [['item_quantity' => '-1'], ['item_quantity']],
            'price'               => [['item_price' => '-1'], ['item_price']],
            'discount'            => [['item_discount_amount' => '-0,01'], ['item_discount_amount']],
            'localised thousands' => [['item_price' => '-1.234,50'], ['item_price']],
            'all three'           => [['item_quantity' => '-3', 'item_price' => '-150', 'item_discount_amount' => '-1'], ['item_quantity', 'item_price', 'item_discount_amount']],
        ];
    }

    /** @return array<string, array{string}> */
    public static function smallPositiveQuantities(): array
    {
        return ['one' => ['1'], 'a half' => ['0,5'], 'a cent' => ['0,01']];
    }

    #[Test]
    public function it_accepts_zero_and_positive_amounts_and_unnamed_rows(): void
    {
        $items = [
            (object) ['item_name' => 'A', 'item_quantity' => '0', 'item_price' => '0', 'item_discount_amount' => ''],
            (object) ['item_name' => 'B', 'item_quantity' => '2,5', 'item_price' => '1.234,50', 'item_discount_amount' => '1,00'],
            (object) ['item_name' => '', 'item_quantity' => '-9', 'item_price' => '-9'],
        ];

        self::assertSame([], amount_sign_errors($items, '', '0'));
    }

    #[Test]
    #[DataProvider('negativeItems')]
    public function it_names_the_field_of_each_negative_item_amount(array $item, array $expectedFields): void
    {
        $errors = amount_sign_errors([(object) array_replace(['item_name' => 'X', 'item_quantity' => '1', 'item_price' => '1', 'item_discount_amount' => ''], $item)], '', '');

        self::assertSame($expectedFields, array_keys($errors));
    }

    #[Test]
    public function it_does_not_treat_negative_zero_as_negative(): void
    {
        self::assertSame([], amount_sign_errors([(object) ['item_name' => 'X', 'item_quantity' => '-0', 'item_price' => '0', 'item_discount_amount' => '']], '', ''));
    }

    #[Test]
    public function it_allows_only_zero_or_negative_quantities_on_a_credit_document(): void
    {
        $credit   = amount_sign_errors([(object) ['item_name' => 'X', 'item_quantity' => '-2', 'item_price' => '150']], '', '', true);
        $positive = amount_sign_errors([(object) ['item_name' => 'X', 'item_quantity' => '2', 'item_price' => '150']], '', '', true);
        $badPrice = amount_sign_errors([(object) ['item_name' => 'X', 'item_quantity' => '-2', 'item_price' => '-150']], '', '', true);

        self::assertSame([], $credit);
        self::assertSame(['item_quantity' => 'T:item_quantity_must_not_be_positive_on_credit'], $positive);
        self::assertSame(['item_price' => 'T:item_price_must_not_be_negative'], $badPrice);
    }

    #[Test]
    #[DataProvider('smallPositiveQuantities')]
    public function it_rejects_any_positive_quantity_on_a_credit_document_however_small(string $quantity): void
    {
        $errors = amount_sign_errors([(object) ['item_name' => 'X', 'item_quantity' => $quantity, 'item_price' => '10']], '', '', true);

        self::assertSame(['item_quantity'], array_keys($errors));
    }

    #[Test]
    public function it_names_the_global_discount_fields_with_the_document_prefix(): void
    {
        $invoice = amount_sign_errors([], '-5', '-1');
        $quote   = amount_sign_errors([], '-5', '', false, 'quote');

        self::assertSame(['invoice_discount_amount', 'invoice_discount_percent'], array_keys($invoice));
        self::assertSame(['quote_discount_amount'], array_keys($quote));
        self::assertSame('T:discount_must_not_be_negative', $quote['quote_discount_amount']);
    }

    #[Test]
    public function it_rejects_a_quote_discount_percent_above_one_hundred(): void
    {
        $errors = amount_sign_errors([], '', '100.01', false, 'quote');

        self::assertSame(['quote_discount_percent'], array_keys($errors));
        self::assertSame('T:discount_percent_must_not_exceed_100', $errors['quote_discount_percent']);
    }

    #[Test]
    public function it_accepts_a_quote_discount_percent_of_exactly_one_hundred(): void
    {
        self::assertSame([], amount_sign_errors([], '', '100', false, 'quote'));
    }

    #[Test]
    public function it_leaves_the_invoice_discount_percent_cap_to_the_total_guard(): void
    {
        self::assertSame([], amount_sign_errors([], '', '150'));
    }

    #[Test]
    public function it_ignores_a_payload_that_is_not_a_list_of_items(): void
    {
        self::assertSame([], amount_sign_errors(null, '', ''));
        self::assertSame([], amount_sign_errors('oops', null, null));
    }
}
