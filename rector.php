<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return RectorConfig::configure()
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withPaths([
        __DIR__ . '/index.php',
        __DIR__ . '/application',
    ])
    ->withSkip([
        __DIR__ . '/application/logs/*',
        ExplicitReturnNullRector::class, // No conflict with pint
        ExplicitBoolCompareRector::class,
        UseIdenticalOverEqualWithSameTypeRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class, // TypeCoverageLevel(20)
        ReturnNeverTypeRector::class, // TypeCoverageLevel(45) php 8.1
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
    );
