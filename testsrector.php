<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

require_once __DIR__
    . '/resources/rector/AddCoversClassRector.php';

return static function (
    RectorConfig $rectorConfig
): void {
    $rectorConfig->paths([
        __DIR__ . '/tests',
    ]);

    $rectorConfig->sets([
        SetList::TYPE_DECLARATION,
    ]);
};
