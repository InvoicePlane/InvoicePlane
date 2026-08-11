<?php

declare(strict_types=1);

require_once __DIR__ . '/ClassCoverageInventory.php';

$repoRoot = dirname(__DIR__, 2);
$target   = $argv[1] ?? $repoRoot . '/tests/Support/class-coverage-inventory.md';

$inventory = ClassCoverageInventory::build($repoRoot);
$markdown  = ClassCoverageInventory::toMarkdown($inventory);

if (file_put_contents($target, $markdown) === false) {
    fwrite(STDERR, 'Unable to write class coverage inventory: ' . $target . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, sprintf(
    'Wrote %s (%d classes)%s',
    $target,
    count($inventory),
    PHP_EOL
));
