<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Resources\Rector\AddCoversClassRector;

require_once __DIR__
    . '/resources/rector/AddCoversClassRector.php';

return static function (
    RectorConfig $rectorConfig
): void {
    $rectorConfig->paths([
        __DIR__ . '/tests',
    ]);

    $rectorConfig->parallel(maxNumberOfProcess: 1);

    $rectorConfig->rule(
        AddCoversClassRector::class
    );
};
