<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/index.php',
        __DIR__ . '/application',
    ])
    ->withSkip([
        __DIR__ . '/application/logs/*',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(15)
    ->withCodingStyleLevel(12);
