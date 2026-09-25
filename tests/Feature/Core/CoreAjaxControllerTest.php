<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Clients Status Controller Feature Tests.
 *
 * Tests client status filtering (/clients/status/active and /clients/status/inactive).
 */
class CoreAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_displays_active_clients_list(): void
    {
        /* Arrange */
        $activeClient = $this->seedClient(['client_name' => 'Active Ajax Client', 'client_active' => 1]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert: Response is successful */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');

        /* Assert: Active client is displayed */
        $this->assertResponseBodyContains($response, 'Active Ajax Client');
    }

    #[Test]
    public function it_filters_active_clients_correctly(): void
    {
        /* Arrange */
        $activeClient1  = $this->seedClient(['client_name' => 'Active One', 'client_active' => 1]);
        $activeClient2  = $this->seedClient(['client_name' => 'Active Two', 'client_active' => 1]);
        $inactiveClient = $this->seedClient(['client_name' => 'Inactive Client', 'client_active' => 0]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert: Only active clients appear in response */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Active One');
        $this->assertResponseBodyContains($response, 'Active Two');
        /* Inactive client should NOT be displayed in active list */
        $body = $response->body();
        self::assertFalse(
            str_contains($body, 'Inactive Client'),
            'Inactive client must not appear in active clients list'
        );
    }

    #[Test]
    public function it_displays_inactive_clients_correctly(): void
    {
        /* Arrange */
        $activeClient   = $this->seedClient(['client_name' => 'Active Client', 'client_active' => 1]);
        $inactiveClient = $this->seedClient(['client_name' => 'Inactive Test', 'client_active' => 0]);

        /* Act */
        $response = $this->get('/clients/status/inactive');

        /* Assert: Response is successful */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Only inactive client appears in response */
        $this->assertResponseBodyContains($response, 'Inactive Test');
        /* Active client should NOT appear */
        $body = $response->body();
        self::assertFalse(
            str_contains($body, 'Active Client'),
            'Active client must not appear in inactive clients list'
        );
    }

    #[Test]
    public function it_handles_empty_active_clients_list(): void
    {
        /* Arrange: seed only inactive clients */
        $this->seedClient(['client_name' => 'Only Inactive', 'client_active' => 0]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert: Response is successful even with no active clients */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_displays_client_with_total_balance(): void
    {
        /* Arrange */
        $expectedBalance = '150.00';
        $clientId        = $this->seedClient(['client_name' => 'Balance Client', 'client_active' => 1]);
        $invoiceId       = $this->seedInvoice($clientId, [], ['invoice_balance' => $expectedBalance]);

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert: Client is displayed with balance info loaded */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Balance Client');
        /* The endpoint loads with_total_balance(); it renders currency-formatted
         * ('$150'), not the raw stored value ('150.00'). */
        $this->assertResponseBodyContains($response, '$150');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/clients/status/active] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
