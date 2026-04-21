<?php

namespace Tests\Feature\Core;

use Tests\Concerns\InteractsWithDatabase;

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

class ImportControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays import page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_import_page(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');

        /** Act */
        $response = $this->actingAs($user)->get(route('import.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
    }
}
