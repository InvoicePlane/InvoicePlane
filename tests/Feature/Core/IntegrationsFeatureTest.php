<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke tests for the integrations module via CI3 HTTP harness.
 */
class IntegrationsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_settings_page(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'My SuperPDP',
            'enabled'       => 0,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'My SuperPDP');
    }

    #[Test]
    public function it_returns_a_successful_response_for_the_integrations_providers_endpoint(): void
    {
        /* Arrange */
        /* (providers discovered from filesystem — no seeding needed) */

        /* Act */
        $response = $this->get('/integrations/providers');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'superpdp');
        $this->assertResponseBodyContains($response, 'qonto');
        $this->assertResponseBodyContains($response, 'letspeppol');
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_settings_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/settings');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/settings] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_incoming_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/incoming] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_events_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/events] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_events_page(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'My Events Provider',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/events');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'My Events Provider');
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_for_the_integrations_incoming_page(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'My Incoming Provider',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/incoming');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'My Incoming Provider');
    }

    #[Test]
    #[Group('smoke')]
    public function it_handles_the_history_endpoint_gracefully_for_a_missing_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'History Test Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $merchantClientId = $this->databaseInsert('ip_merchant_clients', [
            'merchant_type' => 'superpdp',
            'label'         => 'History Provider',
            'enabled'       => 1,
            'auth_type'     => 'oauth2',
            'settings_json' => '{}',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $this->databaseInsert('ip_merchant_responses', [
            'invoice_id'                  => $invoiceId,
            'merchant_client_id'          => $merchantClientId,
            'merchant_response_date'      => date('Y-m-d'),
            'merchant_response_driver'    => 'superpdp',
            'merchant_response'           => 'accepted',
            'merchant_response_reference' => 'REF-001',
            'direction'                   => 'out',
            'record_type'                 => 'outbound_status',
            'status'                      => 'accepted',
            'created_at'                  => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/integrations/history/' . $invoiceId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'REF-001');
    }

    #[Test]
    public function it_redirects_a_guest_away_from_the_integrations_history_page(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/integrations/history/1');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/integrations/history/1] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
