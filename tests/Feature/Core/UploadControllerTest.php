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

class UploadControllerTest extends FeatureTestCase
{
    /**
     * Test index displays upload page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_upload_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('upload.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::upload_index');
    }
}
