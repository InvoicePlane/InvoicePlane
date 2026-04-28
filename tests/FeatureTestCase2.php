<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace tests;

use Tests\Concerns\InteractsWithDatabase;

abstract class FeatureTestCase2 extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Additional feature test setup can go here
        // For example: seed common data, set up authentication, etc.
    }

    /**
     * Helper method to authenticate as a user for tests.
     *
     * @param \Modules\Core\Models\User|null $user
     *
     * @return \Modules\Core\Models\User
     */
    protected function actingAsUser($user = null)
    {
        $user ??= $this->seedModel('User');

        return $this->actingAs($user);
    }
}
