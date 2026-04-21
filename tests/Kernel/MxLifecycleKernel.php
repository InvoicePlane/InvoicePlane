<?php

namespace Tests\Kernel;

abstract class MxLifecycleKernel extends CiLifecycleKernel
{
    protected function runIndexLifecycle(): void
    {
        /*
         * MX modules require full CI boot lifecycle.
         * We simply rely on index.php execution,
         * but ensure isolation per request.
         */

        parent::runIndexLifecycle();
    }
}
