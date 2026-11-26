<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Git does not track empty directories, so deleting the last file in a directory
 * that composer.json still lists makes every fresh `composer install` fail with
 * "Could not scan for classes inside ...". A pre-installed vendor/ hides it locally.
 */
final class ComposerAutoloadPathsTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function autoloadPaths(): array
    {
        $root     = dirname(__DIR__, 3);
        $composer = json_decode((string) file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
        $paths    = [];

        foreach (['autoload', 'autoload-dev'] as $section) {
            foreach (($composer[$section]['classmap'] ?? []) as $path) {
                $paths[$section . ' classmap: ' . $path] = [$path];
            }
            foreach (($composer[$section]['psr-4'] ?? []) as $dirs) {
                foreach ((array) $dirs as $path) {
                    $paths[$section . ' psr-4: ' . $path] = [$path];
                }
            }
            foreach (($composer[$section]['files'] ?? []) as $path) {
                $paths[$section . ' files: ' . $path] = [$path];
            }
        }

        return $paths;
    }

    #[Test]
    #[DataProvider('autoloadPaths')]
    public function it_only_lists_autoload_paths_that_exist_in_the_repository(string $path): void
    {
        /* Arrange */
        $absolute = dirname(__DIR__, 3) . '/' . ltrim($path, '/');

        /* Assert */
        self::assertFileExists($absolute, "composer.json lists \"{$path}\" but it is missing; composer install will fail.");
    }
}
