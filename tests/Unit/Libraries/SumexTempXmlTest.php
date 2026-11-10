<?php

namespace Tests\Unit\Libraries;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Sumex;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-65qw-2ppj-gjvm / GHSA-583r-4pw9-6rc9: Sumex wrote the invoice XML (patient and billing data) to
 * storage/temp/<invoice>_<number>.xml, a name derived only from the invoice number, readable by anyone who could reach
 * the directory. The name now carries random bytes and the file is owner-only. The previous tests only searched the
 * source text for STORAGE_TEMP_FOLDER, so they proved nothing about the file that is actually created.
 */
final class SumexTempXmlTest extends TestCase
{
    private mixed $ciBackup = null;

    /** @var list<string> */
    private array $created = [];

    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/invoice_helper.php';
        require_once APPPATH . 'libraries/Sumex.php';

        $this->ciBackup            = $GLOBALS['unitCiInstance'] ?? null;
        $settings                  = new FakeCiSettings();
        $settings->_data           = ['sumex_sliptype' => '0', 'currency_code' => 'CHF', 'sumex_role' => '1', 'sumex_place' => '0', 'sumex_canton' => '24'];
        $GLOBALS['unitCiInstance'] = (object) [
            'mdl_settings' => $settings,
            'load'         => new class () {
                public function helper(mixed $h): void {}
            },
        ];
    }

    protected function tearDown(): void
    {
        foreach ($this->created as $file) {
            @unlink($file);
        }
        $GLOBALS['unitCiInstance'] = $this->ciBackup;
    }

    #[Test]
    public function it_writes_the_xml_under_a_random_name_inside_the_storage_temp_folder(): void
    {
        /* Act */
        $first  = $this->write('Invoice_INV-7');
        $second = $this->write('Invoice_INV-7');

        /* Assert: inside the folder, readable XML, and the two names differ although the invoice is the same */
        self::assertStringStartsWith(STORAGE_TEMP_FOLDER, $first);
        self::assertMatchesRegularExpression('/Invoice_INV-7_[0-9a-f]{16}\.xml$/', $first);
        self::assertNotSame($first, $second, 'The file name must not be derivable from the invoice number alone.');
        self::assertStringContainsString('<invoice:request', (string) file_get_contents($first));
    }

    #[Test]
    public function it_makes_the_temporary_xml_readable_only_by_its_owner(): void
    {
        /* Act */
        $path = $this->write('Invoice_INV-7');

        /* Assert */
        self::assertSame('0600', substr(sprintf('%o', fileperms($path)), -4));
    }

    #[Test]
    public function it_creates_the_file_owner_only_even_when_the_process_umask_is_open(): void
    {
        /* Arrange: a permissive umask would make a plain create world-readable */
        $previous = umask(0);

        try {
            /* Act */
            $path = $this->write('Invoice_INV-7');
        } finally {
            umask($previous);
        }

        /* Assert */
        self::assertIsString($path);
        self::assertSame('0600', substr(sprintf('%o', fileperms($path)), -4));
    }

    #[Test]
    public function it_reports_failure_and_leaves_no_file_when_the_temp_folder_is_not_writable(): void
    {
        /* Arrange */
        if ( ! is_dir(STORAGE_TEMP_FOLDER)) {
            mkdir(STORAGE_TEMP_FOLDER, 0755, true);
        }
        $before = scandir(STORAGE_TEMP_FOLDER);
        $mode   = fileperms(STORAGE_TEMP_FOLDER) & 0777;
        chmod(STORAGE_TEMP_FOLDER, 0500);

        if (is_writable(STORAGE_TEMP_FOLDER)) {
            chmod(STORAGE_TEMP_FOLDER, $mode);
            self::markTestSkipped('The process can write to a 0500 directory (running as root); the failure path cannot be exercised.');
        }

        try {
            /* Act */
            $path = $this->write('Invoice_INV-7');
        } finally {
            chmod(STORAGE_TEMP_FOLDER, $mode);
        }

        /* Assert: no path is handed out for a file that does not exist, and nothing was left behind */
        self::assertNull($path);
        self::assertSame($before, scandir(STORAGE_TEMP_FOLDER));
    }

    private function write(string $filename): ?string
    {
        if ( ! is_dir(STORAGE_TEMP_FOLDER)) {
            mkdir(STORAGE_TEMP_FOLDER, 0755, true);
        }

        $invoice = (object) [
            'invoice_id'            => 7, 'invoice_number' => 'INV-7', 'invoice_total' => '150.00', 'invoice_date_modified' => '2026-03-02 10:00:00',
            'client_id'             => 3, 'client_name' => 'Anna', 'client_surname' => 'Muster', 'client_birthdate' => '1980-05-06', 'client_gender' => '1',
            'client_address_1'      => 'Hauptstrasse 1', 'client_zip' => '8000', 'client_city' => 'Zurich', 'client_phone' => '',
            'client_avs'            => '7561234567890', 'client_insurednumber' => '999', 'user_company' => 'Praxis AG', 'user_address_1' => 'Via Cantonale 5',
            'user_zip'              => '6900', 'user_city' => 'Lugano', 'user_phone' => '091', 'user_gln' => '7601000000001', 'user_rcc' => 'C000002',
            'user_subscribernumber' => '01-123456-7', 'sumex_casedate' => '2026-02-01', 'sumex_casenumber' => '42',
            'sumex_treatmentstart'  => '2026-02-01', 'sumex_treatmentend' => '2026-02-10', 'sumex_reason' => 0, 'sumex_diagnosis' => 'Flu', 'sumex_observations' => '',
        ];
        $item = (object) ['item_name' => 'Consult', 'product_sku' => '120', 'item_quantity' => '1.00', 'item_price' => '50.00', 'item_total' => '50.00', 'item_date' => '2026-02-03'];

        $sumex  = new Sumex(['invoice' => $invoice, 'items' => [$item], 'options' => []]);
        $method = new ReflectionMethod(Sumex::class, 'writeTempXml');
        $method->setAccessible(true);
        $path = $method->invoke($sumex, $filename);
        if (is_string($path)) {
            $this->created[] = $path;
        }

        return $path;
    }
}
