<?php

namespace Tests\Kernel;

abstract class InvoicePlaneRegressionKernel extends MxCiTestCase
{
    use DatabaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCoreState();
    }

    protected function tearDown(): void
    {
        $this->rollback();

        parent::tearDown();
    }

    protected function seedCoreState(): void
    {
        $ci = &get_instance();

        if ( ! isset($ci->db)) {
            return;
        }

        // Minimal deterministic state
        $ci->db->query('SET FOREIGN_KEY_CHECKS=0');
    }
}
