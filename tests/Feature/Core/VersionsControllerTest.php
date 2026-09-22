<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * VersionsController Feature Tests.
 *
 * Tests the settings page (which includes version info).
 */
#[CoversClass(\Versions::class)]
class VersionsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/settings');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/settings] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
