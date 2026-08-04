<?php

declare(strict_types=1);

namespace Tests\Scripts;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/Support/ClassCoverageInventory.php';

final class ClassCoverageInventoryTest extends TestCase
{
    #[Test]
    public function it_builds_an_inventory_for_testable_application_classes(): void
    {
        /* Arrange */
        $repoRoot = dirname(__DIR__, 2);

        /* Act */
        $inventory = \ClassCoverageInventory::build($repoRoot);
        $classes   = array_column($inventory, 'class');

        /* Assert */
        self::assertNotEmpty($inventory);
        self::assertContains('Mdl_Templates', $classes);
        self::assertContains('Cryptor', $classes);
        self::assertContains('Settings', $classes);
    }

    #[Test]
    public function it_keeps_the_committed_markdown_inventory_in_sync(): void
    {
        /* Arrange */
        $repoRoot = dirname(__DIR__, 2);
        $target   = $repoRoot . '/tests/Support/class-coverage-inventory.md';

        /* Act */
        $expected = \ClassCoverageInventory::toMarkdown(\ClassCoverageInventory::build($repoRoot));

        /* Assert */
        self::assertFileExists($target);
        self::assertSame($expected, (string) file_get_contents($target));
    }
}
