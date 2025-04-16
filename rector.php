<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/index.php',
        __DIR__ . '/application',
    ])
    ->withSkip([
        ExplicitBoolCompareRector::class,
    ])
    ->withSkip([
        __DIR__ . '/application/logs/*',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(21)
    ->withCodingStyleLevel(12);
