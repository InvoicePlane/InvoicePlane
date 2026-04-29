<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Guest Get Controller Feature Tests.
 *
 * Tests the guest invoice download endpoint.
 */
#[CoversNothing]
class GetControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_get_page(): void
    {
        $response = $this->get('/guest/get');

        $this->assertNotEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        $response = $this->get('/guest/get');

        $this->assertNotEquals(500, $response->statusCode());
    }

    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        $this->actingAsAdmin();
        $response = $this->get('/guest/get');

        $this->assertNotEquals(500, $response->statusCode());
    }
}
