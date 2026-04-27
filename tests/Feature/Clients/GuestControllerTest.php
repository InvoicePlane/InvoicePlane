<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Support\TestRoutes;

class GuestControllerTest extends AbstractTestCase
{
    #[Group('smoke')]
    #[Test]
    public function it_handles_guest_portal_home_page_request_without_server_errors(): void
    {
        $response = $this->get(TestRoutes::GUEST_INDEX);

        self::assertNotSame(500, $response->statusCode());
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_handles_guest_portal_home_page_request_for_authenticated_admin_without_server_errors(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(TestRoutes::GUEST_INDEX);

        self::assertNotSame(500, $response->statusCode());
        $this->assertResponseHasNoPhpErrors($response);
    }
}
