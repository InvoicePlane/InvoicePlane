<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Versions;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Versions::class)]

class VersionsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays versions page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_versions_page(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('versions.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::versions_index');
    }
}
