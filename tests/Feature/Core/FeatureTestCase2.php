<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

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
        $user ??= \Modules\Core\Models\User::factory()->create();

        return $this->actingAs($user);
    }
}
