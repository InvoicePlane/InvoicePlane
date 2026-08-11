<?php

declare(strict_types=1);

namespace Tests\Scripts;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/Support/AssertionQualityInventory.php';

final class AssertionQualityInventoryTest extends TestCase
{
    #[Test]
    public function it_inventories_every_attributed_test_and_marks_shallow_shapes(): void
    {
        $root = dirname(__DIR__, 2);
        $inventory = \AssertionQualityInventory::build($root);

        self::assertGreaterThan(0, count($inventory));
        self::assertContains('status-only', array_column($inventory, 'shape'));
        self::assertContains('healthy', array_column($inventory, 'shape'));
        self::assertFileExists($root . '/tests/Support/assertion-quality-inventory.md');
    }
}
