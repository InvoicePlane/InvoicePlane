<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-9jmq-q7m8-vjm3: the report views are rendered to HTML and handed to mPDF, which fetches any <img src> it
 * finds. ReportViewsEscapingTest covers the from/to date headings; this covers the values read from the database
 * for each row (client name, VAT id, invoice number, payment method, payment note), which reach the same renderer.
 */
final class ReportRowEscapingTest extends TestCase
{
    private const HOSTILE = '<img src="http://169.254.169.254/latest/meta-data/">';

    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $root = dirname(__DIR__, 3);
        require_once $root . '/application/helpers/echo_helper.php';
        require_once $root . '/application/helpers/number_helper.php';
        require_once $root . '/application/helpers/trans_helper.php';
        require_once $root . '/application/helpers/settings_helper.php';
        require_once $root . '/application/helpers/client_helper.php';
        require_once $root . '/application/helpers/date_helper.php';

        $this->previousCi                = $GLOBALS['unitCiInstance'] ?? null;
        $settings                        = new FakeCiSettings();
        $GLOBALS['unitCiInstance']       = (object) ['mdl_settings' => $settings];
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

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function hostileRows(): array
    {
        $client = ['client_id' => 7, 'client_name' => self::HOSTILE, 'client_surname' => '', 'client_title' => ''];

        return [
            'sales by year: name'         => ['sales_by_year', ['Name' => self::HOSTILE, 'VAT_ID' => 'V1', 'payment_ab_2026' => '1', 'total_payment' => '1']],
            'sales by year: vat id'       => ['sales_by_year', ['Name' => 'N', 'VAT_ID' => self::HOSTILE, 'payment_ab_2026' => '1', 'total_payment' => '1']],
            'sales by client: client'     => ['sales_by_client', $client + ['invoice_count' => 1, 'sales' => '1', 'sales_with_tax' => '1']],
            'invoices per client: client' => ['invoices_per_client', $client + ['invoice_date_created' => '2026-01-02', 'invoice_number' => 'INV-1', 'invoice_total' => '1']],
            'invoices per client: number' => ['invoices_per_client', ['client_id' => 7, 'client_name' => 'C', 'client_surname' => '', 'client_title' => '', 'invoice_date_created' => '2026-01-02', 'invoice_number' => self::HOSTILE, 'invoice_total' => '1']],
            'payment history: client'     => ['payment_history', $client + ['payment_date' => '2026-01-02', 'invoice_number' => 'INV-1', 'payment_method_name' => 'Cash', 'payment_note' => 'n', 'payment_amount' => '1']],
            'payment history: invoice'    => ['payment_history', ['client_name' => 'C', 'client_surname' => '', 'client_title' => '', 'payment_date' => '2026-01-02', 'invoice_number' => self::HOSTILE, 'payment_method_name' => 'Cash', 'payment_note' => 'n', 'payment_amount' => '1']],
            'payment history: method'     => ['payment_history', ['client_name' => 'C', 'client_surname' => '', 'client_title' => '', 'payment_date' => '2026-01-02', 'invoice_number' => 'INV-1', 'payment_method_name' => self::HOSTILE, 'payment_note' => 'n', 'payment_amount' => '1']],
            'payment history: note'       => ['payment_history', ['client_name' => 'C', 'client_surname' => '', 'client_title' => '', 'payment_date' => '2026-01-02', 'invoice_number' => 'INV-1', 'payment_method_name' => 'Cash', 'payment_note' => self::HOSTILE, 'payment_amount' => '1']],
        ];
    }

    /**
     * @param array<string, mixed> $row
     */
    #[Test]
    #[DataProvider('hostileRows')]
    public function it_never_emits_a_hostile_row_value_as_a_tag(string $view, array $row): void
    {
        /* Act */
        $html = $this->render($view, ['from_date' => '01/02/2026', 'to_date' => '28/02/2026', 'results' => [(object) $row]]);

        /* Assert: shown as text, never an <img> that mPDF would fetch */
        self::assertStringNotContainsString('<img', $html, 'A database value must never reach mPDF as an <img> tag.');
        self::assertStringContainsString('&lt;img src=&quot;http://169.254.169.254', $html);
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $view, array $vars): string
    {
        extract($vars);

        ob_start();
        include dirname(__DIR__, 3) . '/application/views/reports/' . $view . '.php';

        return (string) ob_get_clean();
    }
}
