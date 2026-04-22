<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Rules\MarkWeakTestIncompleteRector;

require_once __DIR__ . '/resources/rector/MarkWeakTestIncompleteRector.php';

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/tests',
    ]);

    $rectorConfig->rule(
        MarkWeakTestIncompleteRector::class
    );
};
