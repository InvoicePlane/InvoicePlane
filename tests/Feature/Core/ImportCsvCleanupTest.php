<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * GHSA-wv2c-c285-9hrq (PR #1623): import CSVs carry bulk client and invoice data, so besides the web-server
 * deny rules the importer must not leave them behind. ImportControllerTest only proves that clients.csv is
 * gone; the other three entries of Mdl_import::cleanup_import_files() and the failure path were never checked.
 */
#[Group('security')]
final class ImportCsvCleanupTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const CSVS = ['clients.csv', 'invoices.csv', 'invoice_items.csv', 'payments.csv'];

    private string $importDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();

        $this->importDir = ROOT_PATH . '/uploads/import';
        if ( ! is_dir($this->importDir)) {
            mkdir($this->importDir, 0777, true);
        }
        $this->removeCsvs();
    }

    protected function tearDown(): void
    {
        $this->removeCsvs();
        parent::tearDown();
    }

    #[Test]
    public function it_leaves_no_csv_behind_after_importing_all_four_files_and_keeps_the_folder_guards(): void
    {
        /* Arrange */
        $guardsBefore = $this->residentsOtherThanCsvs();
        $this->writeCsv('clients.csv', $this->clientHeaders(), [$this->clientRow('Cleanup Co', 'cleanup@client.test')]);
        $this->writeCsv('invoices.csv', ['user_email', 'client_name', 'invoice_date_created', 'invoice_date_due', 'invoice_number', 'invoice_terms'], [
            ['admin@test.local', 'Cleanup Co', '2026-01-10', '2026-02-10', 'CLEAN-1', 'Net 30'],
        ]);
        $this->writeCsv('invoice_items.csv', ['invoice_number', 'item_tax_rate', 'item_date_added', 'item_name', 'item_description', 'item_quantity', 'item_price'], [
            ['CLEAN-1', '0', '2026-01-10', 'Cleanup line', 'desc', '1', '10'],
        ]);
        $this->writeCsv('payments.csv', ['invoice_number', 'payment_method', 'payment_date', 'payment_amount', 'payment_note'], [
            ['CLEAN-1', 'Cash', '2026-01-15', '5.00', 'cleanup'],
        ]);
        foreach (self::CSVS as $csv) {
            self::assertFileExists($this->importDir . '/' . $csv, 'Arrange: ' . $csv . ' must exist before the import.');
        }

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => self::CSVS]);

        /* Assert: the import really ran (positive control) and every CSV is gone */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseHas('ip_clients', ['client_email' => 'cleanup@client.test']);
        $this->assertDatabaseHas('ip_invoices', ['invoice_number' => 'CLEAN-1']);
        $this->assertDatabaseHas('ip_invoice_items', ['item_name' => 'Cleanup line']);
        $this->assertDatabaseHas('ip_payments', ['payment_note' => 'cleanup']);
        foreach (self::CSVS as $csv) {
            self::assertFileDoesNotExist($this->importDir . '/' . $csv, $csv . ' must be deleted once imported.');
        }
        self::assertSame($guardsBefore, $this->residentsOtherThanCsvs(), 'Cleanup must not touch .htaccess / index.html / .gitignore.');
        self::assertContains('.htaccess', $this->residentsOtherThanCsvs(), 'The folder keeps the .htaccess that denies web access.');
    }

    #[Test]
    public function it_deletes_a_csv_whose_headers_were_rejected(): void
    {
        /* Arrange: nothing is imported from this file, but it still holds client data */
        $this->writeCsv('clients.csv', ['name', 'email'], [['Wrong Shape', 'rejected@client.test']]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseMissing('ip_clients', ['client_email' => 'rejected@client.test']);
        self::assertFileDoesNotExist($this->importDir . '/clients.csv', 'A rejected CSV must not stay in the import folder.');
    }

    #[Test]
    public function it_also_deletes_staged_csvs_that_were_not_selected_in_the_submitted_import(): void
    {
        /* Arrange: payments.csv is staged but not ticked */
        $this->writeCsv('clients.csv', $this->clientHeaders(), [$this->clientRow('Only Me', 'onlyme@client.test')]);
        $this->writeCsv('payments.csv', ['invoice_number', 'payment_method', 'payment_date', 'payment_amount', 'payment_note'], [
            ['NO-SUCH', 'Cash', '2026-01-15', '5.00', 'unticked'],
        ]);

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert: current behaviour of cleanup_import_files(): the whole staging area is emptied */
        $this->assertDatabaseHas('ip_clients', ['client_email' => 'onlyme@client.test']);
        self::assertFileDoesNotExist($this->importDir . '/payments.csv', 'An unselected staged CSV must not linger either.');
    }

    #[Test]
    public function it_keeps_the_csv_when_the_submission_is_rejected_for_a_missing_csrf_token(): void
    {
        /* Arrange */
        $this->writeCsv('clients.csv', $this->clientHeaders(), [$this->clientRow('No Token', 'notoken@client.test')]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert: control for the tests above, the deletion is caused by a processed import, not by any request */
        self::assertFalse($response->isRedirect(), 'A request without a CSRF token must be refused, not processed and redirected.');
        $this->assertDatabaseMissing('ip_clients', ['client_email' => 'notoken@client.test']);
        self::assertFileExists($this->importDir . '/clients.csv');
    }

    /** @return list<string> */
    private function residentsOtherThanCsvs(): array
    {
        $entries = array_values(array_diff(scandir($this->importDir) ?: [], ['.', '..'], self::CSVS));
        sort($entries);

        return $entries;
    }

    private function removeCsvs(): void
    {
        foreach (self::CSVS as $csv) {
            $path = $this->importDir . '/' . $csv;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /** @return list<string> */
    private function clientHeaders(): array
    {
        return ['client_name', 'client_address_1', 'client_address_2', 'client_city', 'client_state', 'client_zip', 'client_country', 'client_phone', 'client_fax', 'client_mobile', 'client_email', 'client_web', 'client_vat_id', 'client_tax_code', 'client_active'];
    }

    /** @return list<string> */
    private function clientRow(string $name, string $email): array
    {
        return [$name, '1 Main St', '', 'Springfield', 'ST', '12345', 'US', '555-0100', '', '', $email, 'https://client.test', 'VAT123', 'TAX123', '1'];
    }

    /**
     * @param list<string>       $headers
     * @param list<list<string>> $rows
     */
    private function writeCsv(string $file, array $headers, array $rows): void
    {
        $handle = fopen($this->importDir . '/' . $file, 'w');
        fputcsv($handle, $headers, ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($handle, $row, ',', '"', '\\');
        }
        fclose($handle);
    }
}
