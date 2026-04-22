<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

abstract class FeatureTestCase extends AbstractTestCase
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
        $user ??= $this->seedModel('\Modules\Core\Models\User');

        return $this->actingAs($user);
    }
}
