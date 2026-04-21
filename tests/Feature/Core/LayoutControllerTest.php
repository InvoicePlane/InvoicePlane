<?php

namespace Tests\Feature\Core;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\FeatureTestCase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]

class LayoutControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays layout configuration page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_layout_configuration_page(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');

        /** Act */
        $response = $this->actingAs($user)->get(route('layout.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::layout_index');
    }
}
