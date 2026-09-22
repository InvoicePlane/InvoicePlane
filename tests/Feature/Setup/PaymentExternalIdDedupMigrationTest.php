<?php

namespace Tests\Feature\Setup;

use PDOException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Migration 044_1.7.3.sql — payment_external_id de-duplication.
 *
 * Before ADD UNIQUE INDEX idx_payment_external_id can be applied, any duplicate
 * gateway references that were recorded while the index was still absent (or
 * non-unique, as an earlier revision of this file / upstream develop shipped)
 * have to be resolved. The migration must do that WITHOUT deleting rows: the
 * earliest row keeps its reference and the later ones are suffixed
 * "-dup<payment_id>" so the payment history stays intact and greppable.
 */
#[Group('setup')]
class PaymentExternalIdDedupMigrationTest extends AbstractTestCase
{
    private const MIGRATION = '044_1.7.3.sql';

    protected function tearDown(): void
    {
        // This test drops idx_payment_external_id to reproduce a pre-unique
        // database; resetMysqlDatabase() only re-seeds rows, not schema, so make
        // sure the unique index is restored for every following test.
        $index = $this->databaseSelect(
            'SELECT `NON_UNIQUE` FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \'ip_payments\'
               AND INDEX_NAME = \'idx_payment_external_id\''
        );
        $present = $index !== [];
        $unique  = $present && (int) $index[0]['NON_UNIQUE'] === 0;

        if ($present && ! $unique) {
            $this->databaseRunScript('ALTER TABLE `ip_payments` DROP INDEX `idx_payment_external_id`');
        }
        if ( ! $unique) {
            $this->databaseRunScript(
                'ALTER TABLE `ip_payments` ADD UNIQUE INDEX `idx_payment_external_id` (`payment_external_id`)'
            );
        }

        parent::tearDown();
    }

    #[Test]
    public function it_dedupes_existing_references_and_makes_the_index_unique_without_dropping_rows(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/application/helpers/sql_helper.php';

        $statements = split_sql_statements($this->migrationFile());

        // Statements keep any leading "-- ..." comment lines attached (the
        // splitter only trims whitespace), so match past an optional comment
        // prologue rather than anchoring hard at offset 0.
        $dedupStatements = array_values(array_filter(
            $statements,
            static fn (string $s): bool => preg_match('/^\s*(?:--[^\n]*\n\s*)*UPDATE\s+`ip_payments`/i', $s) === 1
        ));
        $this->assertNotEmpty(
            $dedupStatements,
            self::MIGRATION . ' must de-duplicate payment_external_id before ADD UNIQUE INDEX'
        );

        $uniqueIndexStatements = array_values(array_filter(
            $statements,
            static fn (string $s): bool => stripos($s, 'ADD UNIQUE INDEX') !== false
                && stripos($s, 'idx_payment_external_id') !== false
        ));
        $this->assertNotEmpty(
            $uniqueIndexStatements,
            self::MIGRATION . ' must add a UNIQUE index on payment_external_id'
        );

        // Reproduce a pre-unique-index database: drop the index, then record
        // two payments that share an external reference plus one unique and one
        // NULL.
        $this->databaseRunScript('ALTER TABLE `ip_payments` DROP INDEX `idx_payment_external_id`');

        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $keepId  = $this->seedPayment($invoiceId, ['payment_amount' => '10.00']);
        $dupId   = $this->seedPayment($invoiceId, ['payment_amount' => '10.00']);
        $otherId = $this->seedPayment($invoiceId, ['payment_amount' => '10.00']);
        $nullId  = $this->seedPayment($invoiceId, ['payment_amount' => '10.00']);

        $this->databaseUpdate('ip_payments', ['payment_external_id' => 'pi_DUP'], ['payment_id' => $keepId]);
        $this->databaseUpdate('ip_payments', ['payment_external_id' => 'pi_DUP'], ['payment_id' => $dupId]);
        $this->databaseUpdate('ip_payments', ['payment_external_id' => 'pi_UNIQUE'], ['payment_id' => $otherId]);

        /* Act — run the migration's dedup + unique-index statements */
        foreach ([...$dedupStatements, ...$uniqueIndexStatements] as $statement) {
            $this->databaseRunScript($statement);
        }

        /* Assert */
        $rows = $this->databaseSelect(
            'SELECT `payment_id`, `payment_external_id` FROM `ip_payments` WHERE `invoice_id` = :i ORDER BY `payment_id`',
            [':i' => $invoiceId]
        );
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['payment_id']] = $row['payment_external_id'];
        }

        // No rows were deleted.
        $this->assertCount(4, $rows);

        // Earliest keeps its reference; the later duplicate is suffixed, not removed.
        $this->assertSame('pi_DUP', $byId[$keepId]);
        $this->assertSame('pi_DUP-dup' . $dupId, $byId[$dupId]);

        // Unrelated references are untouched.
        $this->assertSame('pi_UNIQUE', $byId[$otherId]);
        $this->assertNull($byId[$nullId]);

        // The index is present and UNIQUE.
        $index = $this->databaseSelect(
            'SELECT `NON_UNIQUE` FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \'ip_payments\'
               AND INDEX_NAME = \'idx_payment_external_id\''
        );
        $this->assertNotEmpty($index, 'idx_payment_external_id must exist after the migration');
        $this->assertSame(0, (int) $index[0]['NON_UNIQUE'], 'idx_payment_external_id must be UNIQUE');

        // And it now actually enforces uniqueness on new writes.
        $this->expectException(PDOException::class);
        $this->databaseUpdate('ip_payments', ['payment_external_id' => 'pi_UNIQUE'], ['payment_id' => $nullId]);
    }

    private function migrationFile(): string
    {
        return (string) file_get_contents(
            dirname(__DIR__, 3) . '/application/modules/setup/sql/' . self::MIGRATION
        );
    }
}
