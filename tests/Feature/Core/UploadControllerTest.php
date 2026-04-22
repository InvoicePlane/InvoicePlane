<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Upload;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Upload::class)]
#[CoversClass(Tests\Feature\Core\UploadController::class)]

class UploadControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays upload page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_upload_page(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('upload.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::upload_index');
    }
}
