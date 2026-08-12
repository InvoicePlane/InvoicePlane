<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression tests for the security fixes merged from InvoicePlane/InvoicePlane's
 * `develop` branch (v1.8.0-era). Each test proves the fix cannot silently regress.
 *
 * Covered:
 *   - SUMEX invoice XML information disclosure: the XML is written to a
 *     non-web-accessible storage directory, never under the public web root.
 *   - Access control on uploads/import: the directory that holds in-progress
 *     CSV imports (invoices.csv, payments.csv, ...) denies direct web access.
 *
 * Note: this suite's HTTP harness (tests/Integration/bin/request.php) invokes
 * public/index.php directly in-process and never goes through Apache/nginx, so
 * .htaccess directives cannot be exercised end-to-end. Where the actual control
 * is a web-server rule, these tests assert on the file/constant that
 * implements it instead of on an HTTP response.
 */
#[Group('security')]
class DevelopMergeSecurityTest extends AbstractTestCase
{
    // -----------------------------------------------------------------------
    // SUMEX XML information disclosure (99c52db)
    // -----------------------------------------------------------------------

    #[Test]
    public function it_defines_the_sumex_storage_folder_outside_the_public_web_root(): void
    {
        /* Arrange */
        require_once dirname(__DIR__, 3) . '/bootstrap/kernel.php';

        /* Act */
        $storageTempFolder = STORAGE_TEMP_FOLDER;

        /* Assert */
        self::assertTrue(defined('STORAGE_TEMP_FOLDER'), 'STORAGE_TEMP_FOLDER must be defined by bootstrap/kernel.php.');
        self::assertTrue(defined('FCPATH'), 'FCPATH must be defined by bootstrap/kernel.php.');

        self::assertStringStartsNotWith(
            FCPATH,
            $storageTempFolder,
            'STORAGE_TEMP_FOLDER must live outside the public web root (FCPATH), '
            . 'otherwise SUMEX XML files are directly downloadable by an unauthenticated attacker.'
        );
    }

    #[Test]
    public function it_writes_the_sumex_xml_to_the_non_web_accessible_storage_folder(): void
    {
        /* Arrange */
        $sumexFile = APPPATH . 'libraries/Sumex.php';

        /* Act */
        $content = file_get_contents($sumexFile);

        /* Assert */
        self::assertStringContainsString(
            'STORAGE_TEMP_FOLDER . $filename',
            $content,
            'Sumex::pdf() must write the invoice XML into STORAGE_TEMP_FOLDER. '
            . 'Writing it back under UPLOADS_TEMP_FOLDER (or any path under the public '
            . 'web root) would reintroduce the unauthenticated information-disclosure vulnerability.'
        );
        self::assertStringNotContainsString(
            'UPLOADS_TEMP_FOLDER . $filename',
            $content,
            'Sumex::pdf() must not write the invoice XML under the web-accessible uploads/temp folder.'
        );
    }

    // -----------------------------------------------------------------------
    // uploads/import access control (4add6c1)
    // -----------------------------------------------------------------------

    #[Test]
    public function it_denies_direct_web_access_to_the_uploads_import_directory(): void
    {
        /* Arrange */
        $htaccessPath = dirname(APPPATH) . '/uploads/import/.htaccess';

        /* Act */
        $rules = file_get_contents($htaccessPath);

        /* Assert */
        self::assertFileExists(
            $htaccessPath,
            'uploads/import/.htaccess must exist — it is the only barrier between the '
            . 'internet and in-progress CSV imports (invoices.csv, payments.csv, ...) '
            . 'which are plaintext and contain client/invoice financial data.'
        );
        self::assertStringContainsString(
            'Deny from all',
            $rules,
            'uploads/import/.htaccess must deny all direct web access.'
        );
    }
}
