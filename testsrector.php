<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Resources\Rector\AddCoversClassAndMoveTestRector;

require_once __DIR__
    . '/resources/rector/AddCoversClassAndMoveTestRector.php';

return static function (
    RectorConfig $rectorConfig
): void {

    $rectorConfig->paths([
        __DIR__ . '/tests',
    ]);

    $rectorConfig->rule(
        AddCoversClassAndMoveTestRector::class
    );
};
