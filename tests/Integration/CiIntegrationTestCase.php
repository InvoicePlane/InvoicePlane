<?php

namespace Tests\Integration;

use Tests\AbstractTestCase;

abstract class CiIntegrationTestCase extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires live CI3 environment with database — not available in CI');
    }
}
