<?php

namespace Tests\Feature\Core;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Core\FeatureTestCase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]

class SetupControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays setup wizard page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_setup_wizard_page(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('setup.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup_index');
    }

    /**
     * Test setup wizard is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /* Arrange */
        // No authentication for initial setup

        /* Act */
        $response = $this->get(route('setup.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::setup_index');
    }
}
