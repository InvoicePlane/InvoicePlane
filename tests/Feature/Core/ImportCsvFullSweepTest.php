<?php

namespace Tests\Feature\Core;

use Import;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * GHSA-qq8q-gf24-576m (PR #1716): import CSVs hold bulk client/invoice/payment data and must not
 * linger in uploads/import after an import.
 *
 * ImportControllerTest only checks that clients.csv disappears. Mutation targets:
 * Mdl_import::cleanup_import_files() (application/modules/import/models/Mdl_import.php ~461-475,
 * each entry of $files_to_delete, the is_file guard) and the call in Import::form() (~107).
 */
#[Group('security')]
#[CoversClass(Import::class)]
final class ImportCsvFullSweepTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const ALL = ['clients.csv', 'invoices.csv', 'invoice_items.csv', 'payments.csv'];

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
        $this->dir = ROOT_PATH . '/uploads/import';
        if ( ! is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (array_merge(self::ALL, ['keep.txt']) as $file) {
            if (is_file($this->dir . '/' . $file)) {
                unlink($this->dir . '/' . $file);
            }
        }
        parent::tearDown();
    }

    #[Test]
    public function it_removes_all_four_csv_files_after_a_full_import(): void
    {
        /* Arrange */
        $this->csv('clients.csv', ['client_name', 'client_address_1', 'client_address_2', 'client_city', 'client_state', 'client_zip', 'client_country', 'client_phone', 'client_fax', 'client_mobile', 'client_email', 'client_web', 'client_vat_id', 'client_tax_code', 'client_active'], [['Wipe Co', '1 St', '', 'X', '', '1', 'NL', '', '', '', 'wipe@c.test', '', '', '', '1']]);
        $this->csv('invoices.csv', ['user_email', 'client_name', 'invoice_date_created', 'invoice_date_due', 'invoice_number', 'invoice_terms'], [['admin@test.local', 'Wipe Co', '2026-01-01', '2026-02-01', 'WIPE-1', '']]);
        $this->csv('invoice_items.csv', ['invoice_number', 'item_tax_rate', 'item_date_added', 'item_name', 'item_description', 'item_quantity', 'item_price'], [['WIPE-1', '', '2026-01-01', 'Thing', '', '1', '10.00']]);
        $this->csv('payments.csv', ['invoice_number', 'payment_method', 'payment_date', 'payment_amount', 'payment_note'], [['WIPE-1', 'Cash', '2026-01-02', '5.00', '']]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => self::ALL]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Wipe Co']);
        foreach (self::ALL as $file) {
            self::assertFileDoesNotExist($this->dir . '/' . $file, "{$file} must be deleted after the import.");
        }
    }

    #[Test]
    public function it_removes_staged_csv_files_even_when_the_import_form_is_submitted_with_nothing_selected(): void
    {
        /* Arrange: uploaded, then the form is submitted without ticking any file */
        $this->csv('clients.csv', ['client_name'], [['Never Imported Co']]);
        $this->csv('payments.csv', ['invoice_number'], [['X-1']]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseMissing('ip_clients', ['client_name' => 'Never Imported Co']);
        self::assertFileDoesNotExist($this->dir . '/clients.csv');
        self::assertFileDoesNotExist($this->dir . '/payments.csv');
    }

    #[Test]
    public function it_removes_a_csv_with_the_wrong_headers_and_unselected_csvs_too_but_leaves_other_files(): void
    {
        /* Arrange: clients.csv selected but malformed, payments.csv present yet unselected */
        $this->csv('clients.csv', ['wrong', 'headers'], [['a', 'b']]);
        $this->csv('payments.csv', ['invoice_number', 'payment_method', 'payment_date', 'payment_amount', 'payment_note'], [['X-1', 'Cash', '2026-01-02', '5.00', '']]);
        file_put_contents($this->dir . '/keep.txt', 'not an import file');

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $this->assertDatabaseCount('ip_clients', 0, ['client_name' => 'a']);
        self::assertFileDoesNotExist($this->dir . '/clients.csv', 'Sensitive data must not linger even when the import rejected it.');
        self::assertFileDoesNotExist($this->dir . '/payments.csv', 'Unselected CSVs are swept as well.');
        self::assertFileExists($this->dir . '/keep.txt', 'Only the four known CSV names are deleted.');
    }

    #[Test]
    public function it_keeps_the_csvs_when_the_submit_is_rejected_for_a_missing_csrf_token(): void
    {
        /* Arrange: control, a request that never reaches the importer deletes nothing */
        $this->csv('clients.csv', ['client_name'], [['Untouched']]);

        /* Act */
        $this->postWithoutCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        self::assertFileExists($this->dir . '/clients.csv');
        $this->assertDatabaseCount('ip_clients', 0, ['client_name' => 'Untouched']);
    }

    private function csv(string $file, array $headers, array $rows): void
    {
        $h = fopen($this->dir . '/' . $file, 'w');
        fputcsv($h, $headers, ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($h, $row, ',', '"', '\\');
        }
        fclose($h);
    }
}
