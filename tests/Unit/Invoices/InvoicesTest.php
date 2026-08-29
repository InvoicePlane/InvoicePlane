<?php

declare(strict_types=1);

namespace Tests\Unit\Invoices;

use Error;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QrCode;

class InvoicesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_calculates_recursive_mod10_checksums(): void
    {
        $this->setUpInvoiceHelper();

        /* Arrange */

        $inputs = ['1234567890', '0000000000', '12345'];

        /* Act */

        $checksums = array_map('invoice_recMod10', $inputs);

        /* Assert */

        self::assertSame([3, 0, 7], $checksums);
    }

    #[Test]
    public function it_builds_a_valid_isr_code_line(): void
    {
        $this->setUpInvoiceHelper();

        /* Arrange */

        $slipType = '11';

        /* Act */

        $codeLine = invoice_genCodeline($slipType, '12.50', '123456', '12-345678-9');

        /* Assert */

        self::assertStringStartsWith('11', $codeLine);

        self::assertStringEndsWith('>123456+ 123456789>', $codeLine);

        self::assertMatchesRegularExpression('/\A\d{13}>123456\+ 123456789>\z/', $codeLine);
    }

    #[Test]
    public function it_rejects_an_invalid_subscriber_number(): void
    {
        $this->setUpInvoiceHelper();

        /* Arrange */

        $subscriberNumber = 'invalid';

        /* Act */

        try {
            invoice_genCodeline('11', '12.50', '123456', $subscriberNumber);

            $exception = null;
        } catch (Error $error) {
            $exception = $error;
        }

        /* Assert */

        self::assertInstanceOf(Error::class, $exception);

        self::assertSame('Invalid subscriber number', $exception->getMessage());
    }

    #[Test]
    public function it_rejects_an_amount_above_the_isr_limit(): void
    {
        $this->setUpInvoiceHelper();

        /* Arrange */

        $amount = '100000000.00';

        /* Act */

        try {
            invoice_genCodeline('11', $amount, '123456', '12-345678-9');

            $exception = null;
        } catch (Error $error) {
            $exception = $error;
        }

        /* Assert */

        self::assertInstanceOf(Error::class, $exception);

        self::assertSame('Invalid amount', $exception->getMessage());
    }

    #[Test]
    public function it_prefers_configured_recipient_and_invoice_bank_details(): void
    {
        $this->setUpQrCode();

        /* Arrange */

        $invoice = (object) [
            'user_company' => 'Invoice Company',

            'user_name' => 'Invoice User',

            'user_iban' => 'CH9300762011623852957',

            'user_bic' => 'POFICHBEXXX',

            'user_remittance_text' => 'Invoice INV-42',

            'invoice_balance' => '12.50',

            'invoice_number' => 'INV-42',
        ];

        /* Act */

        $qrCode = new QrCode(['invoice' => $invoice]);

        /* Assert */

        self::assertSame('Configured Recipient', $qrCode->recipient);

        self::assertSame('CH9300762011623852957', $qrCode->iban);

        self::assertSame('POFICHBEXXX', $qrCode->bic);

        self::assertSame('CHF', $qrCode->currencyCode);

        self::assertSame('Invoice INV-42', $qrCode->remittance_text);
    }

    #[Test]
    public function it_falls_back_to_invoice_identity_and_settings_for_missing_bank_details(): void
    {
        $this->setUpQrCode();

        /* Arrange */

        $invoice = (object) [
            'user_company' => '',

            'user_name' => 'Invoice User',

            'user_iban' => '',

            'user_bic' => '',

            'user_remittance_text' => '',

            'invoice_balance' => '12.50',

            'invoice_number' => 'INV-42',
        ];

        /* Act */

        $qrCode = new QrCode(['invoice' => $invoice]);

        /* Assert */

        self::assertSame('Configured Recipient', $qrCode->recipient);

        self::assertSame('CH5604835012345678009', $qrCode->iban);

        self::assertSame('CRESCHZZ80A', $qrCode->bic);

        self::assertSame('Invoice {invoice_number}', $qrCode->remittance_text);
    }

    protected function setUpInvoiceHelper(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/invoice_helper.php';
    }

    protected function setUpQrCode(): void
    {
        $settings = [
            'qr_code_recipient' => 'Configured Recipient',

            'qr_code_iban' => 'CH5604835012345678009',

            'qr_code_bic' => 'CRESCHZZ80A',

            'currency_code' => 'CHF',

            'qr_code_remittance_text' => 'Invoice {invoice_number}',
        ];

        $GLOBALS['unitCiInstance'] = new class ($settings) {
            public object $load;

            public object $mdl_settings;

            public function __construct(private array $settings)
            {
                $this->load = new class () {
                    public function helper(string $helper): void {}
                };

                $this->mdl_settings = new class ($settings) {
                    public function __construct(private array $settings) {}

                    public function setting(string $key): string
                    {
                        return (string) ($this->settings[$key] ?? '');
                    }
                };
            }
        };

        require_once dirname(__DIR__, 3) . '/application/libraries/QrCode.php';
    }
}
