<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/index.php',
        __DIR__ . '/application',
    ])
    ->withSkip([
        ExplicitReturnNullRector::class,
        ExplicitBoolCompareRector::class,
        UseIdenticalOverEqualWithSameTypeRector::class,
        RemoveUselessParamTagRector::class, // DeadCodeLevel(19)
    ])
    ->withSkip([
        __DIR__ . '/application/logs/*',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(48) // 0 to 48
    ->withCodeQualityLevel(70) // 0 to 70
    ->withCodingStyleLevel(12);
