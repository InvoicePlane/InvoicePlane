<?php

/**
 * Unit tests for file_security_helper.php functions.
 *
 * Note: These tests require PHPUnit to be installed.
 * To run: vendor/bin/phpunit tests/Unit/Helpers/FileSecurityHelperTest.php
 *
 * To add PHPUnit to the project:
 * composer require --dev phpunit/phpunit
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FileSecurityHelperTest extends TestCase
{
    private string $testBaseDir;

    private string $testFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Load CodeIgniter helpers
        require_once __DIR__ . '/../../../application/helpers/file_security_helper.php';

        // Create temporary test directory
        $this->testBaseDir = sys_get_temp_dir() . '/ip_test_' . uniqid();
        mkdir($this->testBaseDir);

        // Create a test file
        $this->testFile = 'test_file.txt';
        file_put_contents($this->testBaseDir . '/' . $this->testFile, 'test content');
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (file_exists($this->testBaseDir . '/' . $this->testFile)) {
            unlink($this->testBaseDir . '/' . $this->testFile);
        }
        if (is_dir($this->testBaseDir)) {
            rmdir($this->testBaseDir);
        }

        parent::tearDown();
    }

    /**
     * Test that valid filenames are accepted and normalized.
     */
    #[Test]
    public function it_accepts_valid_filename(): void
    {
        $result = validate_db_filename($this->testFile, $this->testBaseDir);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('basename', $result);
        $this->assertArrayHasKey('hash', $result);
        $this->assertEquals($this->testFile, $result['basename']);
        $this->assertEquals($this->testBaseDir . '/' . $this->testFile, $result['path']);
    }

    /**
     * Test that path traversal attempts are rejected.
     */
    #[Test]
    public function it_rejects_path_traversal(): void
    {
        $traversalAttempts = [
            '../../../etc/passwd',
            '../../config/database.php',
            'dir/../../../secret.txt',
            './../../sensitive.conf',
        ];

        foreach ($traversalAttempts as $attempt) {
            $result = validate_db_filename($attempt, $this->testBaseDir);
            $this->assertNull($result, "Should reject path traversal: {$attempt}");
        }
    }

    /**
     * Test that absolute paths are rejected.
     */
    #[Test]
    public function it_rejects_absolute_paths(): void
    {
        $absolutePaths = [
            '/etc/passwd',
            '/var/www/config.php',
            '/home/user/secret.txt',
        ];

        foreach ($absolutePaths as $path) {
            $result = validate_db_filename($path, $this->testBaseDir);
            $this->assertNull($result, "Should reject absolute path: {$path}");
        }
    }

    /**
     * Test that null bytes are rejected.
     */
    #[Test]
    public function it_rejects_null_bytes(): void
    {
        $nullByteAttempts = [
            "file\x00.png",
            "test\x00.txt",
            "upload\x00../../../etc/passwd",
        ];

        foreach ($nullByteAttempts as $attempt) {
            $result = validate_db_filename($attempt, $this->testBaseDir);
            $this->assertNull($result, 'Should reject null byte: ' . bin2hex($attempt));
        }
    }

    /**
     * Test that basename normalization works correctly.
     */
    #[Test]
    public function it_normalizes_basename(): void
    {
        // Create test file with complex name
        $complexName      = 'dir/subdir/actual_file.txt';
        $expectedBasename = 'actual_file.txt';

        file_put_contents($this->testBaseDir . '/' . $expectedBasename, 'test');

        $result = validate_db_filename($complexName, $this->testBaseDir);

        $this->assertIsArray($result);
        $this->assertEquals($expectedBasename, $result['basename']);

        // Clean up
        unlink($this->testBaseDir . '/' . $expectedBasename);
    }

    /**
     * Test handling of multiple directory separators.
     */
    #[Test]
    public function it_handles_multiple_separators(): void
    {
        $multiSeparatorNames = [
            'dir//subdir///file.txt',
            'dir\\\\subdir\\\\file.txt',
            'dir/./subdir/./file.txt',
        ];

        foreach ($multiSeparatorNames as $name) {
            $expectedBasename = 'file.txt';
            file_put_contents($this->testBaseDir . '/' . $expectedBasename, 'test');

            $result = validate_db_filename($name, $this->testBaseDir);

            if ($result !== null) {
                $this->assertEquals($expectedBasename, $result['basename']);
            }

            if (file_exists($this->testBaseDir . '/' . $expectedBasename)) {
                unlink($this->testBaseDir . '/' . $expectedBasename);
            }
        }
    }

    /**
     * Test base_dir with trailing slash.
     */
    #[Test]
    public function it_handles_trailing_slash(): void
    {
        $baseDirWithSlash    = $this->testBaseDir . '/';
        $baseDirWithoutSlash = $this->testBaseDir;

        $result1 = validate_db_filename($this->testFile, $baseDirWithSlash);
        $result2 = validate_db_filename($this->testFile, $baseDirWithoutSlash);

        $this->assertIsArray($result1);
        $this->assertIsArray($result2);
        $this->assertEquals($result1['path'], $result2['path']);
    }

    /**
     * Test that symlinks outside base directory are rejected.
     */
    #[Test]
    public function it_rejects_symlink_escape(): void
    {
        // Skip test on Windows as symlink behavior differs
        if (mb_strtoupper(mb_substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Symlink test not supported on Windows');
        }

        // Create a temporary file outside the test directory to use as symlink target
        $outsideDir = sys_get_temp_dir() . '/ip_test_outside_' . uniqid();
        mkdir($outsideDir);
        $targetFile = $outsideDir . '/sensitive.txt';
        file_put_contents($targetFile, 'sensitive content');

        try {
            // Create a symlink inside test directory pointing to file outside
            $symlinkName = 'evil_symlink.txt';
            $symlinkPath = $this->testBaseDir . '/' . $symlinkName;

            if (symlink($targetFile, $symlinkPath)) {
                $result = validate_db_filename($symlinkName, $this->testBaseDir);

                // Should reject because resolved path is outside base directory
                $this->assertNull($result, 'Should reject symlink pointing outside base directory');

                // Clean up symlink
                unlink($symlinkPath);
            }
        } finally {
            // Clean up temp file and directory
            if (file_exists($targetFile)) {
                unlink($targetFile);
            }
            if (is_dir($outsideDir)) {
                rmdir($outsideDir);
            }
        }
    }

    /**
     * Test that validation hash is preserved.
     */
    #[Test]
    public function it_preserves_hash(): void
    {
        $result = validate_db_filename($this->testFile, $this->testBaseDir);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('hash', $result);
        $this->assertIsString($result['hash']);
        $this->assertEquals(64, mb_strlen($result['hash'])); // SHA256 hash length
    }

    /**
     * Test that non-existent files are handled (path still returned for creation).
     */
    #[Test]
    public function it_handles_nonexistent_file(): void
    {
        $nonExistentFile = 'nonexistent_' . uniqid() . '.txt';

        $result = validate_db_filename($nonExistentFile, $this->testBaseDir);

        // Should still return a result (for file creation scenarios)
        $this->assertIsArray($result);
        $this->assertEquals($nonExistentFile, $result['basename']);
    }
}
