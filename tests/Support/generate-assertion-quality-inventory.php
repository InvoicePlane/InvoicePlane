<?php

declare(strict_types=1);

require_once __DIR__ . '/AssertionQualityInventory.php';

$root      = dirname(__DIR__, 2);
$inventory = AssertionQualityInventory::build($root);
$target    = $root . '/tests/Support/assertion-quality-inventory.md';
file_put_contents($target, AssertionQualityInventory::toMarkdown($inventory));
printf("Audited %d PHPUnit test methods; report written to %s\n", count($inventory), $target);
