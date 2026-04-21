<?php

namespace Tests\Kernel;

class TestKernel
{
    public static function boot(): void
    {
        self::bootGlobals();
        self::bootContainer();
    }

    private static function bootGlobals(): void
    {
        $GLOBALS['CI'] = null;
    }

    private static function bootContainer(): void
    {
// load only what tests need (models/controllers/services)
    }
}
