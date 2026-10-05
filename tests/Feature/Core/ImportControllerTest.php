<?php

namespace Tests\Feature\Core;

use Import;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Core Import Feature Tests.
 *
 * Tests the import page for authenticated admins.
 */
#[CoversClass(Import::class)]
class ImportControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private string $importDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        $this->importDir = ROOT_PATH . '/uploads/import';
        if ( ! is_dir($this->importDir)) {
            mkdir($this->importDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (['clients.csv', 'evil.php', 'invoice_items.csv', 'invoices.csv', 'payments.csv'] as $file) {
            $path = $this->importDir . '/' . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    private const CLIENT_HEADERS = ['client_name', 'client_address_1', 'client_address_2', 'client_city', 'client_state', 'client_zip', 'client_country', 'client_phone', 'client_fax', 'client_mobile', 'client_email', 'client_web', 'client_vat_id', 'client_tax_code', 'client_active'];

    private const INVOICE_HEADERS = ['user_email', 'client_name', 'invoice_date_created', 'invoice_date_due', 'invoice_number', 'invoice_terms'];

    private const ITEM_HEADERS = ['invoice_number', 'item_tax_rate', 'item_date_added', 'item_name', 'item_description', 'item_quantity', 'item_price'];

    private const PAYMENT_HEADERS = ['invoice_number', 'payment_method', 'payment_date', 'payment_amount', 'payment_note'];

    /**
     * @param list<string>             $headers
     * @param list<list<string>>       $rows
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

    /** @return list<string> */
    private function clientRow(string $name, string $email): array
    {
        return [$name, '1 Main St', '', 'Springfield', 'ST', '12345', 'US', '555-0100', '', '', $email, 'https://client.test', 'VAT123', 'TAX123', '1'];
    }

    #[Test]
    public function it_lists_past_imports(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_imports', ['import_date' => '2025-03-04 10:11:12']);

        /* Act */
        $response = $this->get('/import');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '2025-03-04 10:11:12');
    }

    #[Test]
    public function it_deletes_a_past_import(): void
    {
        /* Arrange */
        $keepId   = $this->databaseInsert('ip_imports', ['import_date' => '2025-03-05 08:00:00']);
        $deleteId = $this->databaseInsert('ip_imports', ['import_date' => '2025-03-04 08:00:00']);
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/delete/' . $deleteId);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseMissing('ip_imports', ['import_id' => $deleteId]);
        $this->assertDatabaseHas('ip_imports', ['import_id' => $keepId]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/import');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/import] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_lists_only_allowed_import_files(): void
    {
        /* Arrange */
        file_put_contents($this->importDir . '/clients.csv', "client_name\nAllowed Client\n");
        file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');

        /* Act */
        $response = $this->get('/import/form');

        /* Assert */
        $this->assertResponseBodyContains($response, 'clients.csv');
        $this->assertResponseBodyNotContains($response, 'evil.php');
    }

    // -------------------------------------------------------------------------
    // import pipeline (clients -> invoices -> items -> payments)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_imports_clients_records_the_batch_and_removes_the_source_file(): void
    {
        /* Arrange */
        $this->writeCsv('clients.csv', self::CLIENT_HEADERS, [
            $this->clientRow('Imported One', 'one@client.test'),
            $this->clientRow('Imported Two', 'two@client.test'),
        ]);
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Imported One', 'client_email' => 'one@client.test']);
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Imported Two']);
        $import = $this->databaseSelect('SELECT * FROM ip_imports ORDER BY import_id DESC LIMIT 1')[0] ?? null;
        self::assertNotNull($import, 'The batch must be recorded.');
        $this->assertDatabaseCount('ip_import_details', 2, ['import_id' => $import['import_id'], 'import_table_name' => 'ip_clients']);
        self::assertFileDoesNotExist($this->importDir . '/clients.csv', 'Source CSVs must not linger after an import.');
    }

    #[Test]
    public function it_strips_markup_from_imported_client_fields(): void
    {
        /* Arrange */
        $this->writeCsv('clients.csv', self::CLIENT_HEADERS, [$this->clientRow('<script>alert(1)</script>Evil <b>Corp</b>', 'evil@client.test')]);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $client = $this->databaseFetchOne('ip_clients', ['client_email' => 'evil@client.test']);
        self::assertNotNull($client);
        self::assertStringNotContainsString('<', $client['client_name']);
        self::assertStringNotContainsString('script', mb_strtolower($client['client_name']));
        self::assertStringContainsString('Evil', $client['client_name']);
    }

    #[Test]
    public function it_imports_nothing_from_a_file_with_the_wrong_headers(): void
    {
        /* Arrange */
        $this->writeCsv('clients.csv', ['name', 'email'], [['Wrong Shape', 'wrong@client.test']]);
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'import');
        $this->assertDatabaseMissing('ip_clients', ['client_email' => 'wrong@client.test']);
        $this->assertDatabaseCount('ip_import_details', 0);
    }

    #[Test]
    public function it_imports_only_the_files_that_were_selected(): void
    {
        /* Arrange */
        $this->writeCsv('clients.csv', self::CLIENT_HEADERS, [$this->clientRow('Selected Co', 'selected@client.test')]);
        $this->writeCsv('payments.csv', self::PAYMENT_HEADERS, [['NO-SUCH-INV', 'Cash', '2026-01-01', '5.00', 'x']]);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);

        /* Assert */
        $this->assertDatabaseHas('ip_clients', ['client_name' => 'Selected Co']);
        $this->assertDatabaseCount('ip_payment_methods', 0, ['payment_method_name' => 'Cash']);
    }

    #[Test]
    public function it_imports_invoices_resolving_users_and_clients_and_skipping_unknown_users(): void
    {
        /* Arrange: one existing client, one client that must be created, one row with an unknown user */
        $existing = $this->seedClient(['client_name' => 'Existing Client']);
        $this->writeCsv('invoices.csv', self::INVOICE_HEADERS, [
            ['admin@test.local', 'Existing Client', '2026-01-10', '2026-02-10', 'IMP-INV-1', 'Net 30'],
            ['admin@test.local', 'Brand New Client', '2026-01-11', '2026-02-11', 'IMP-INV-2', ''],
            ['nobody@test.local', 'Existing Client', '2026-01-12', '2026-02-12', 'IMP-INV-3', ''],
        ]);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['invoices.csv']]);

        /* Assert */
        $this->assertDatabaseHas('ip_invoices', ['invoice_number' => 'IMP-INV-1', 'client_id' => $existing, 'user_id' => 1]);
        $created = $this->databaseFetchOne('ip_clients', ['client_name' => 'Brand New Client']);
        self::assertNotNull($created, 'An unknown client name must create the client.');
        $this->assertDatabaseHas('ip_invoices', ['invoice_number' => 'IMP-INV-2', 'client_id' => $created['client_id']]);
        $this->assertDatabaseMissing('ip_invoices', ['invoice_number' => 'IMP-INV-3']);
    }

    #[Test]
    public function it_imports_invoice_items_and_payments_against_existing_invoices(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_number' => 'IMP-LINK-1']);
        $this->writeCsv('invoice_items.csv', self::ITEM_HEADERS, [
            ['IMP-LINK-1', '21', '2026-01-10', 'Imported line', 'desc', '2', '50'],
            ['IMP-MISSING', '0', '2026-01-10', 'Orphan line', 'desc', '1', '10'],
        ]);
        $this->writeCsv('payments.csv', self::PAYMENT_HEADERS, [
            ['IMP-LINK-1', 'Wire transfer', '2026-01-15', '40.00', 'first instalment'],
            ['IMP-MISSING', 'Wire transfer', '2026-01-15', '9.00', 'orphan'],
        ]);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['invoice_items.csv', 'payments.csv']]);

        /* Assert: linked rows are created (a new 21% tax rate and payment method on demand); orphans are skipped */
        $this->assertDatabaseHas('ip_invoice_items', ['invoice_id' => $invoiceId, 'item_name' => 'Imported line']);
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_percent' => '21.00']);
        $this->assertDatabaseMissing('ip_invoice_items', ['item_name' => 'Orphan line']);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '40.00', 'payment_note' => 'first instalment']);
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Wire transfer']);
        $this->assertDatabaseMissing('ip_payments', ['payment_note' => 'orphan']);
    }

    #[Test]
    public function it_undoes_an_import_without_touching_pre_existing_records(): void
    {
        /* Arrange: import one client, keep an unrelated one */
        $keep = $this->seedClient(['client_name' => 'Pre-existing Client']);
        $this->writeCsv('clients.csv', self::CLIENT_HEADERS, [$this->clientRow('Disposable Import', 'dispose@client.test')]);
        $this->enableCsrfProtection();
        $this->postWithValidCsrfToken('/import/form', ['btn_submit' => '1', 'files' => ['clients.csv']]);
        $import = $this->databaseSelect('SELECT * FROM ip_imports ORDER BY import_id DESC LIMIT 1')[0] ?? null;

        /* Act */
        $this->postWithValidCsrfToken('/import/delete/' . $import['import_id']);

        /* Assert */
        $this->assertDatabaseMissing('ip_clients', ['client_name' => 'Disposable Import']);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $keep]);
        $this->assertDatabaseMissing('ip_imports', ['import_id' => $import['import_id']]);
        $this->assertDatabaseCount('ip_import_details', 0);
    }

    #[Test]
    public function it_will_not_delete_arbitrary_tables_named_in_a_tampered_import_detail(): void
    {
        /* Arrange: a detail row pointing at a table the importer never writes to */
        $victim   = $this->seedClient(['client_name' => 'Must Survive']);
        $importId = $this->databaseInsert('ip_imports', ['import_date' => '2026-01-01 00:00:00']);
        $this->databaseInsert('ip_import_details', [
            'import_id' => $importId, 'import_table_name' => 'ip_users', 'import_lang_key' => 'users', 'import_record_id' => 1,
        ]);
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/import/delete/' . $importId);

        /* Assert: the allow-list skipped the tampered row, so the admin account is intact */
        $this->assertDatabaseHas('ip_users', ['user_id' => 1]);
        $this->assertDatabaseHas('ip_clients', ['client_id' => $victim]);
        $this->assertDatabaseMissing('ip_imports', ['import_id' => $importId]);
    }

    #[Test]
    public function it_ignores_unapproved_import_filenames_on_submit(): void
    {
        /* Arrange */
        file_put_contents($this->importDir . '/evil.php', '<?php echo "not allowed";');

        /* Act */
        $response = $this->post('/import/form', [
            'files'      => ['evil.php', '../../bootstrap/kernel.php'],
            'btn_submit' => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Import submit must redirect after processing.');
        $this->assertDatabaseCount('ip_imports', 1);
        $this->assertDatabaseCount('ip_import_details', 0);
    }
}
