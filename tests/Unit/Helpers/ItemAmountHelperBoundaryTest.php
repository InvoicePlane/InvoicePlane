<?php

namespace Tests\Unit\Helpers;

use ArrayIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-jvcw-w8pr-j92c: boundaries of amount_sign_errors() that the first unit test leaves open: row order,
 * credit-document edge values and the quote-only percent cap.
 */
#[Group('unit')]
#[Group('security')]
final class ItemAmountHelperBoundaryTest extends TestCase
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

    /** @return array<string, array{string, string, string}> */
    public static function creditRows(): array
    {
        return [
            'zero quantity'     => ['0', '10', ''],
            'negative quantity' => ['-0,01', '10', ''],
            'zero price'        => ['-1', '0', ''],
        ];
    }

    #[Test]
    public function it_keeps_checking_the_rows_after_an_unnamed_row(): void
    {
        /* Arrange */
        $items = [$this->row('', '1', '1'), $this->row('Bad', '-1', '1')];

        /* Act */
        $errors = amount_sign_errors($items, '', '');

        /* Assert */
        self::assertSame(['item_quantity' => 'T:item_quantity_must_not_be_negative'], $errors);
    }

    #[Test]
    public function it_keeps_checking_the_rows_after_a_valid_row(): void
    {
        /* Arrange */
        $items = [$this->row('Good', '1', '1'), $this->row('Bad', '1', '-1', '-1')];

        /* Act */
        $errors = amount_sign_errors($items, '', '');

        /* Assert */
        self::assertSame(['item_price', 'item_discount_amount'], array_keys($errors));
    }

    #[Test]
    public function it_reports_a_negative_row_even_when_an_earlier_row_was_clean_in_the_same_field(): void
    {
        /* Arrange */
        $items = [$this->row('A', '1', '1'), $this->row('B', '-5', '1'), $this->row('C', '2', '1')];

        /* Act */
        $errors = amount_sign_errors($items, '', '');

        /* Assert: a later clean row must not erase the error of an earlier bad one */
        self::assertSame(['item_quantity'], array_keys($errors));
    }

    #[Test]
    #[DataProvider('creditRows')]
    public function it_accepts_zero_and_negative_quantities_and_a_zero_price_on_a_credit_document(string $quantity, string $price, string $discount): void
    {
        /* Arrange */
        $items = [$this->row('X', $quantity, $price, $discount)];

        /* Act */
        $errors = amount_sign_errors($items, '', '', true);

        /* Assert */
        self::assertSame([], $errors);
    }

    #[Test]
    public function it_still_rejects_a_negative_item_discount_and_negative_global_discounts_on_a_credit_document(): void
    {
        /* Arrange */
        $items = [$this->row('X', '-1', '10', '-2')];

        /* Act */
        $errors = amount_sign_errors($items, '-5', '-1', true);

        /* Assert */
        self::assertSame(['item_discount_amount', 'invoice_discount_amount', 'invoice_discount_percent'], array_keys($errors));
        self::assertArrayNotHasKey('item_quantity', $errors);
    }

    #[Test]
    public function it_uses_the_credit_message_only_for_credit_documents(): void
    {
        /* Arrange */
        $regular = amount_sign_errors([$this->row('X', '-1', '1')], '', '');
        $credit  = amount_sign_errors([$this->row('X', '1', '1')], '', '', true);

        /* Assert */
        self::assertSame('T:item_quantity_must_not_be_negative', $regular['item_quantity']);
        self::assertSame('T:item_quantity_must_not_be_positive_on_credit', $credit['item_quantity']);
    }

    #[Test]
    public function it_reports_a_negative_quote_percent_as_negative_not_as_over_one_hundred(): void
    {
        /* Act */
        $errors = amount_sign_errors([], '', '-1', false, 'quote');

        /* Assert */
        self::assertSame(['quote_discount_percent' => 'T:discount_must_not_be_negative'], $errors);
    }

    #[Test]
    public function it_applies_the_one_hundred_cap_to_a_localised_quote_percent_and_not_to_the_amount(): void
    {
        /* Arrange */
        $over  = amount_sign_errors([], '', '100,5', false, 'quote');
        $exact = amount_sign_errors([], '100000', '100,0', false, 'quote');

        /* Assert */
        self::assertSame(['quote_discount_percent' => 'T:discount_percent_must_not_exceed_100'], $over);
        self::assertSame([], $exact);
    }

    #[Test]
    public function it_treats_missing_item_fields_as_zero(): void
    {
        /* Arrange */
        $items = [(object) ['item_name' => 'Bare']];

        /* Act */
        $errors = amount_sign_errors($items, null, null);

        /* Assert */
        self::assertSame([], $errors);
    }

    #[Test]
    public function it_accepts_a_traversable_items_payload(): void
    {
        /* Arrange */
        $items = new ArrayIterator([$this->row('Bad', '1', '-1')]);

        /* Act */
        $errors = amount_sign_errors($items, '', '');

        /* Assert */
        self::assertSame(['item_price'], array_keys($errors));
    }

    private function row(string $name, string $quantity, string $price, string $discount = ''): object
    {
        return (object) ['item_name' => $name, 'item_quantity' => $quantity, 'item_price' => $price, 'item_discount_amount' => $discount];
    }
}
