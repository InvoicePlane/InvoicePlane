<?php

namespace Tests\Feature\Quotes;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ClientHelper::class)]

class StorageStructureTest extends TestCase
{
    /**
     * Test that required storage directories exist.
     */
    public function test_required_storage_directories_exist(): void
    {
        $basePath = dirname(__DIR__, 2);

        $requiredDirectories = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/app/uploads/temp/mpdf',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($requiredDirectories as $directory) {
            $fullPath = $basePath . '/' . $directory;
            $this->assertDirectoryExists(
                $fullPath,
                "Required storage directory does not exist: {$directory}"
            );
        }
    }

    /**
     * Test that storage directories are writable.
     */
    public function test_storage_directories_are_writable(): void
    {
        $basePath = dirname(__DIR__, 2);

        $writableDirectories = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($writableDirectories as $directory) {
            $fullPath = $basePath . '/' . $directory;
            $this->assertTrue(
                is_writable($fullPath),
                "Storage directory is not writable: {$directory}"
            );
        }
    }

    /**
     * Test that .gitignore files exist in storage directories.
     */
    public function test_gitignore_files_exist_in_storage_directories(): void
    {
        $basePath = dirname(__DIR__, 2);

        $directoriesWithGitignore = [
            'storage/app',
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($directoriesWithGitignore as $directory) {
            $gitignorePath = $basePath . '/' . $directory . '/.gitignore';
            $this->assertFileExists(
                $gitignorePath,
                ".gitignore file does not exist in: {$directory}"
            );
        }
    }

    /**
     * Test that .gitignore files have correct content.
     */
    public function test_gitignore_files_have_correct_content(): void
    {
        $basePath = dirname(__DIR__, 2);

        // Test storage/app/.gitignore
        $appGitignore = file_get_contents($basePath . '/storage/app/.gitignore');
        $this->assertStringContainsString('*', $appGitignore);
        $this->assertStringContainsString('!public/', $appGitignore);
        $this->assertStringContainsString('!uploads/', $appGitignore);
        $this->assertStringContainsString('!.gitignore', $appGitignore);

        // Test storage/framework/cache/.gitignore
        $cacheGitignore = file_get_contents($basePath . '/storage/framework/cache/.gitignore');
        $this->assertStringContainsString('*', $cacheGitignore);
        $this->assertStringContainsString('!data/', $cacheGitignore);
        $this->assertStringContainsString('!.gitignore', $cacheGitignore);

        // Test other directories have standard .gitignore
        $standardGitignoreContent = "*\n!.gitignore\n";
        $standardDirs             = [
            'storage/app/public',
            'storage/app/uploads',
            'storage/app/uploads/archive',
            'storage/app/uploads/customer_files',
            'storage/app/uploads/import',
            'storage/app/uploads/temp',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($standardDirs as $directory) {
            $gitignorePath = $basePath . '/' . $directory . '/.gitignore';
            $content       = file_get_contents($gitignorePath);
            $this->assertEquals(
                $standardGitignoreContent,
                $content,
                "Incorrect .gitignore content in: {$directory}"
            );
        }
    }

    /**
     * Test upload helper functions.
     */
    public function test_upload_helper_functions(): void
    {
        require_once __DIR__ . '/../../bootstrap/helpers.php';

        $basePath = dirname(__DIR__, 2);

        // Test uploads_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads',
            mb_rtrim(uploads_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_archive_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/archive',
            mb_rtrim(uploads_archive_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_customer_files_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/customer_files',
            mb_rtrim(uploads_customer_files_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_temp_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/temp',
            mb_rtrim(uploads_temp_path(), DIRECTORY_SEPARATOR)
        );

        // Test uploads_temp_mpdf_path()
        $this->assertEquals(
            $basePath . '/storage/app/uploads/temp/mpdf',
            mb_rtrim(uploads_temp_mpdf_path(), DIRECTORY_SEPARATOR)
        );
    }

    /**
     * Test that UPLOADS constants point to storage location.
     */
    public function test_upload_constants_point_to_storage(): void
    {
        require_once __DIR__ . '/../../bootstrap/paths.php';

        $basePath = dirname(__DIR__, 2);

        $this->assertStringContainsString(
            'storage/app/uploads',
            UPLOADS_FOLDER,
            'UPLOADS_FOLDER should point to storage/app/uploads'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/archive',
            UPLOADS_ARCHIVE_FOLDER,
            'UPLOADS_ARCHIVE_FOLDER should point to storage/app/uploads/archive'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/customer_files',
            UPLOADS_CFILES_FOLDER,
            'UPLOADS_CFILES_FOLDER should point to storage/app/uploads/customer_files'
        );

        $this->assertStringContainsString(
            'storage/app/uploads/temp',
            UPLOADS_TEMP_FOLDER,
            'UPLOADS_TEMP_FOLDER should point to storage/app/uploads/temp'
        );
    }
}
