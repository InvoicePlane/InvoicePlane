<?php

namespace Tests\Feature\Core;

use Import;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Core Import Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Import::class)]
#[CoversClass(Tests\Feature\Core\ImportController::class)]

class ImportControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

    /**
     * Test index displays import page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_import_page(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('import.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
    }
}
