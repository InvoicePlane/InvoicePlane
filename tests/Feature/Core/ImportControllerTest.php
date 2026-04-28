<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use Import;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Core Import Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Import::class)]

class ImportControllerTest extends AbstractTestCase
{
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
        $response = $this->actingAs($user)->get('/import/index');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('core::import_index');
    }
}
