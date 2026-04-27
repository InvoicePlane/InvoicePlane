<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Support\TestRoutes;

class ClientsControllerTest extends AbstractTestCase
{
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_active_status_view_from_index(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(TestRoutes::CLIENTS_INDEX);

        self::assertTrue($response->isRedirect());
        self::assertStringEndsWith('/clients/status/active', (string) $response->redirectUrl());
    }

    #[Group('smoke')]
    #[Test]
    public function it_renders_active_status_page(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(TestRoutes::clientsStatus('active'));

        $this->assertOk($response);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, 'clients');
    }

    #[Group('smoke')]
    #[Test]
    public function it_renders_create_form_page(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(TestRoutes::CLIENTS_FORM);

        $this->assertOk($response);
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseBodyContains($response, 'client_name');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_not_found_for_missing_client_view(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(TestRoutes::clientsView(999999));

        $this->assertResponseStatusCode($response, 404);
    }
}
