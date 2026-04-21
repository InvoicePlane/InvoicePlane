<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]

class VersionsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays versions page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_versions_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('versions.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::versions_index');
    }
}
