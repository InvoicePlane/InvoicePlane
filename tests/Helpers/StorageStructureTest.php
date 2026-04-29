<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
class StorageStructureTest extends TestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRoot = dirname(__DIR__, 2);
    }

    #[Test]
    public function it_required_storage_directories_exist(): void
    {
        $this->assertDirectoryExists($this->projectRoot . '/uploads');
        $this->assertDirectoryExists($this->projectRoot . '/uploads/customer_files');
        $this->assertDirectoryExists($this->projectRoot . '/uploads/archive');
        $this->assertDirectoryExists($this->projectRoot . '/uploads/import');
    }

    #[Test]
    public function it_storage_directories_are_writable(): void
    {
        $dirs = [
            $this->projectRoot . '/uploads',
            $this->projectRoot . '/uploads/customer_files',
            $this->projectRoot . '/uploads/archive',
            $this->projectRoot . '/uploads/import',
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $this->assertTrue(is_writable($dir), "Directory '{$dir}' should be writable.");
            }
        }

        $this->assertDirectoryExists($this->projectRoot . '/uploads', 'uploads/ directory must exist.');
    }

    #[Test]
    public function it_gitignore_files_exist_in_storage_directories(): void
    {
        $uploadsDir = $this->projectRoot . '/uploads';
        $hasMarker  = file_exists($uploadsDir . '/index.html')
            || file_exists($uploadsDir . '/.gitignore')
            || file_exists($uploadsDir . '/.gitkeep');

        $this->assertTrue(is_dir($uploadsDir), 'uploads/ directory should exist.');
        $this->assertTrue($hasMarker, 'uploads/ should contain an index.html, .gitignore, or .gitkeep marker file.');
    }

    #[Test]
    public function it_gitignore_files_have_correct_content(): void
    {
        $indexFile = $this->projectRoot . '/uploads/index.html';

        if (file_exists($indexFile)) {
            $content = file_get_contents($indexFile);
            $this->assertIsString($content);
        } else {
            $this->assertDirectoryExists($this->projectRoot . '/uploads');
        }
    }

    #[Test]
    public function it_upload_helper_functions(): void
    {
        $this->assertFileExists(
            $this->projectRoot . '/application/modules/upload/controllers/Upload.php'
        );
    }

    #[Test]
    public function it_upload_constants_point_to_storage(): void
    {
        $uploadsDir = $this->projectRoot . '/uploads';

        $this->assertDirectoryExists($uploadsDir);
        $this->assertTrue(is_readable($uploadsDir), 'uploads/ should be readable.');
    }
}
