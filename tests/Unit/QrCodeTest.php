<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QrCode;

class QrCodeTest extends TestCase
{
    protected function setUp(): void
    {
        $settings = [
            'qr_code_recipient'       => 'Configured Recipient',
            'qr_code_iban'            => 'CH5604835012345678009',
            'qr_code_bic'             => 'CRESCHZZ80A',
            'currency_code'           => 'CHF',
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

        require_once dirname(__DIR__, 2) . '/application/libraries/QrCode.php';
    }

    #[Test]
    public function it_prefers_configured_recipient_and_invoice_bank_details(): void
    {
        /* Arrange */
        $invoice = (object) [
            'user_company'         => 'Invoice Company',
            'user_name'            => 'Invoice User',
            'user_iban'            => 'CH9300762011623852957',
            'user_bic'             => 'POFICHBEXXX',
            'user_remittance_text' => 'Invoice INV-42',
            'invoice_balance'      => '12.50',
            'invoice_number'       => 'INV-42',
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
        /* Arrange */
        $invoice = (object) [
            'user_company'         => '',
            'user_name'            => 'Invoice User',
            'user_iban'            => '',
            'user_bic'             => '',
            'user_remittance_text' => '',
            'invoice_balance'      => '12.50',
            'invoice_number'       => 'INV-42',
        ];

        /* Act */
        $qrCode = new QrCode(['invoice' => $invoice]);

        /* Assert */
        self::assertSame('Configured Recipient', $qrCode->recipient);
        self::assertSame('CH5604835012345678009', $qrCode->iban);
        self::assertSame('CRESCHZZ80A', $qrCode->bic);
        self::assertSame('Invoice {invoice_number}', $qrCode->remittance_text);
    }
}
