<?php

namespace Tests\Unit\Setup;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for application/helpers/sql_helper.php::split_sql_statements().
 *
 * Mdl_setup::execute_contents() runs every migration file through this splitter,
 * one returned statement per db->query(). A statement it drops or mangles is a
 * schema change that silently never happens on a real upgrade.
 */
#[Group('setup')]
class SqlHelperTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/sql_helper.php';
    }

    #[Test]
    public function it_splits_on_top_level_semicolons_only(): void
    {
        /* Arrange */
        $sql = "CREATE TABLE `a` (`id` INT);\nINSERT INTO `a` VALUES (1);\n";

        /* Act */
        $statements = split_sql_statements($sql);

        /* Assert */
        $this->assertSame(
            ['CREATE TABLE `a` (`id` INT)', 'INSERT INTO `a` VALUES (1)'],
            $statements
        );
    }

    #[Test]
    public function it_does_not_split_on_semicolons_inside_strings_or_comments(): void
    {
        /* Arrange */
        $sql = <<<'SQL'
            ALTER TABLE `x` ADD COLUMN `c` INT NULL COMMENT 'FK to y; NULL for legacy rows';
            -- a trailing comment; with a semicolon
            INSERT INTO `x` (`c`) VALUES (1);
            SQL;

        /* Act */
        $statements = split_sql_statements($sql);

        /* Assert — the ';' in the string literal and the one in the line
           comment are not treated as terminators, so there are exactly two
           statements. Leading comment lines stay attached to the statement
           that follows them, which the db->query() runner handles fine. */
        $this->assertCount(2, $statements);
        $this->assertStringContainsString("COMMENT 'FK to y; NULL for legacy rows'", $statements[0]);
        $this->assertStringContainsString('INSERT INTO `x` (`c`) VALUES (1)', $statements[1]);
    }

    #[Test]
    public function it_keeps_the_final_statement_when_earlier_comments_contain_multibyte_characters(): void
    {
        /* Arrange — the em dash is 3 bytes / 1 codepoint; a mb_strlen() bound
           would stop the walker 2 bytes before end and truncate the last stmt. */
        $sql = <<<'SQL'
            -- 1.8.0 — consolidated migration — folds several files
            ALTER TABLE `ip_clients` ADD COLUMN `client_peppol_id` VARCHAR(100) NULL;
            ALTER TABLE `ip_quotes` ADD COLUMN `service_id` INT(11) DEFAULT 0 AFTER `notes`;
            SQL;

        /* Act */
        $statements = split_sql_statements($sql);

        /* Assert */
        $this->assertCount(2, $statements);
        $this->assertSame(
            'ALTER TABLE `ip_quotes` ADD COLUMN `service_id` INT(11) DEFAULT 0 AFTER `notes`',
            $statements[1]
        );
    }

    #[Test]
    public function it_preserves_every_statement_of_the_real_consolidated_1_8_0_migration(): void
    {
        /* Arrange */
        $file = dirname(__DIR__, 3) . '/application/modules/setup/sql/045_1.8.0.sql';
        $sql  = (string) file_get_contents($file);
        $this->assertNotSame('', $sql);

        /* Act */
        $statements = split_sql_statements($sql);

        /* Assert — the file ends on the ip_quotes.service_id ALTER; it must
           survive the split intact (this file has em dashes in its comments). */
        $last = $statements[count($statements) - 1];
        $this->assertStringContainsString('`ip_quotes`', $last);
        $this->assertStringContainsString('`service_id`', $last);
        $this->assertStringEndsWith('`notes`', rtrim($last, "; \n"));

        // Nothing after the last real ';' is silently dropped.
        $reassembled = implode(";\n", $statements) . ';';
        $this->assertStringContainsString('ADD COLUMN IF NOT EXISTS `service_id` INT(11) DEFAULT 0 AFTER `notes`', $reassembled);
    }
}
