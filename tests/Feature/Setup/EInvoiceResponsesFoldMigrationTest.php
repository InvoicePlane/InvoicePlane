<?php

namespace Tests\Feature\Setup;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Migration 045_1.8.0.sql — fold ip_einvoice_responses into ip_merchant_responses.
 *
 * The consolidated 1.8.0 migration drops ip_einvoice_responses (the old
 * standalone `einvoice` module's response log). Before the DROP it must copy
 * every row into ip_merchant_responses so a development install that ran that
 * module keeps its provider audit trail — request/response JSON included.
 * The block is guarded so the migration stays safely re-runnable.
 */
#[Group('setup')]
class EInvoiceResponsesFoldMigrationTest extends AbstractTestCase
{
    private const MIGRATION = '045_1.8.0.sql';

    private const LEGACY_TABLE_DDL = <<<'SQL'
        CREATE TABLE IF NOT EXISTS `ip_einvoice_responses` (
          `id`                 INT AUTO_INCREMENT PRIMARY KEY,
          `merchant_client_id` INT NOT NULL,
          `direction`          ENUM('out','in') NOT NULL DEFAULT 'out',
          `record_type`        VARCHAR(50) NOT NULL DEFAULT 'outbound_status',
          `invoice_id`         INT NULL,
          `external_id`        VARCHAR(255) NULL,
          `status`             VARCHAR(50) DEFAULT 'draft',
          `message`            TEXT NULL,
          `http_code`          INT NULL,
          `request_json`       LONGTEXT NULL,
          `response_json`      LONGTEXT NULL,
          `created_at`         DATETIME NULL,
          `updated_at`         DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL;

    protected function tearDown(): void
    {
        if ($this->databaseTableExists('ip_einvoice_responses')) {
            $this->databaseRunScript('DROP TABLE `ip_einvoice_responses`');
        }

        parent::tearDown();
    }

    #[Test]
    public function it_folds_legacy_rows_into_ip_merchant_responses_before_dropping_the_table(): void
    {
        /* Arrange */
        $foldStatements = $this->foldStatements();

        $insertIndex = null;
        $dropIndex   = null;
        foreach ($foldStatements as $i => $s) {
            if (preg_match('/\bINSERT\s+INTO\s+`ip_merchant_responses`.*\bFROM\s+`ip_einvoice_responses`/is', $s) === 1) {
                $insertIndex = $i;
            }
            if (preg_match('/\bDROP\s+TABLE\b.*`ip_einvoice_responses`/is', $s) === 1) {
                $dropIndex = $i;
            }
        }

        $this->assertNotNull(
            $insertIndex,
            self::MIGRATION . ' must fold ip_einvoice_responses rows into ip_merchant_responses before the DROP'
        );
        $this->assertNotNull($dropIndex, self::MIGRATION . ' must still drop ip_einvoice_responses');
        $this->assertLessThan($dropIndex, $insertIndex, 'the fold must run before the DROP');

        $this->databaseRunScript('DELETE FROM `ip_merchant_responses`');
        $this->databaseRunScript(self::LEGACY_TABLE_DDL);

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $merchantClientId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'Fold Test ' . bin2hex(random_bytes(3)),
            'enabled'       => 0,
            'auth_type'     => 'oauth2',
        ]);

        // An outbound row tied to a known merchant client and invoice.
        $this->databaseInsert('ip_einvoice_responses', [
            'merchant_client_id' => $merchantClientId,
            'direction'          => 'out',
            'record_type'        => 'outbound_status',
            'invoice_id'         => $invoiceId,
            'external_id'        => 'sp_12345',
            'status'             => 'sent',
            'message'            => 'Accepted by SuperPDP',
            'http_code'          => 200,
            'request_json'       => '{"foo":1}',
            'response_json'      => '{"id":"sp_12345"}',
            'created_at'         => '2026-02-01 10:00:00',
            'updated_at'         => '2026-02-01 10:05:00',
        ]);

        // An inbound row with an orphan merchant client, no invoice, all-NULL
        // optionals — exercises the LEFT JOIN driver fallback and nullable cols.
        $this->databaseInsert('ip_einvoice_responses', [
            'merchant_client_id' => 999999,
            'direction'          => 'in',
            'record_type'        => 'incoming_invoice',
            'invoice_id'         => null,
            'external_id'        => null,
            'status'             => 'error',
            'message'            => null,
            'http_code'          => 502,
            'request_json'       => null,
            'response_json'      => null,
            'created_at'         => null,
            'updated_at'         => null,
        ]);

        /* Act */
        foreach ($foldStatements as $statement) {
            $this->databaseRunScript($statement);
        }

        /* Assert */
        $this->assertFalse(
            $this->databaseTableExists('ip_einvoice_responses'),
            'ip_einvoice_responses must be dropped after the fold'
        );

        $rows = $this->databaseSelect(
            'SELECT * FROM `ip_merchant_responses` ORDER BY `merchant_response_id`'
        );
        $this->assertCount(2, $rows, 'both legacy rows must survive as ip_merchant_responses rows');

        [$outbound, $inbound] = $rows;

        $this->assertSame($invoiceId, (int) $outbound['invoice_id']);
        $this->assertSame($merchantClientId, (int) $outbound['merchant_client_id']);
        $this->assertSame('out', $outbound['direction']);
        $this->assertSame('outbound_status', $outbound['record_type']);
        $this->assertSame('sent', $outbound['status']);
        $this->assertSame(200, (int) $outbound['http_code']);
        $this->assertSame('2026-02-01', $outbound['merchant_response_date']);
        $this->assertSame('2026-02-01 10:00:00', $outbound['created_at']);
        $this->assertSame('superpdp', $outbound['merchant_response_driver']);
        $this->assertSame('Accepted by SuperPDP', $outbound['merchant_response']);
        $this->assertSame('sp_12345', $outbound['merchant_response_reference']);
        $this->assertSame(1, (int) $outbound['merchant_response_successful']);
        $this->assertStringContainsString('"request":{"foo":1}', $outbound['raw_payload']);
        $this->assertStringContainsString('"response":{"id":"sp_12345"}', $outbound['raw_payload']);
        $this->assertStringContainsString('"updated_at":"2026-02-01 10:05:00"', $outbound['raw_payload']);

        $this->assertNull($inbound['invoice_id']);
        $this->assertSame('in', $inbound['direction']);
        $this->assertSame('incoming_invoice', $inbound['record_type']);
        $this->assertSame('error', $inbound['status']);
        $this->assertSame(502, (int) $inbound['http_code']);
        $this->assertSame('einvoice', $inbound['merchant_response_driver'], 'orphan merchant client falls back to the "einvoice" driver');
        $this->assertSame('', $inbound['merchant_response']);
        $this->assertSame('', $inbound['merchant_response_reference']);
        $this->assertSame(0, (int) $inbound['merchant_response_successful']);
        $this->assertNull($inbound['created_at']);
        $this->assertStringContainsString('"updated_at":null', $inbound['raw_payload']);
    }

    #[Test]
    public function it_is_safe_to_run_a_second_time(): void
    {
        /* Arrange */
        $foldStatements = $this->foldStatements();
        $this->databaseRunScript('DELETE FROM `ip_merchant_responses`');
        $this->databaseRunScript(self::LEGACY_TABLE_DDL);

        $merchantClientId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'Rerun ' . bin2hex(random_bytes(3)),
            'enabled'       => 0,
            'auth_type'     => 'oauth2',
        ]);
        $this->databaseInsert('ip_einvoice_responses', [
            'merchant_client_id' => $merchantClientId,
            'direction'          => 'out',
            'record_type'        => 'outbound_status',
            'invoice_id'         => null,
            'external_id'        => 'sp_once',
            'status'             => 'sent',
            'message'            => 'ok',
            'http_code'          => 200,
            'created_at'         => '2026-03-03 09:00:00',
        ]);

        /* Act — run the fold block twice */
        foreach ($foldStatements as $statement) {
            $this->databaseRunScript($statement);
        }
        foreach ($foldStatements as $statement) {
            $this->databaseRunScript($statement);
        }

        /* Assert — the row was folded exactly once, no error on the re-run */
        $this->assertFalse($this->databaseTableExists('ip_einvoice_responses'));
        $this->assertSame(
            1,
            (int) $this->databaseSelect('SELECT COUNT(*) AS c FROM `ip_merchant_responses`')[0]['c']
        );
    }

    /** @return string[] the migration's ip_einvoice_responses fold + drop statements */
    private function foldStatements(): array
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/sql_helper.php';

        $sql = (string) file_get_contents(
            dirname(__DIR__, 3) . '/application/modules/setup/sql/' . self::MIGRATION
        );

        return array_values(array_filter(
            split_sql_statements($sql),
            static fn (string $s): bool => stripos($s, 'ip_einvoice_responses') !== false
        ));
    }
}
