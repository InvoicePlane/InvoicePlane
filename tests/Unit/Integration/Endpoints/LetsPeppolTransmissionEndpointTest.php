<?php

namespace Tests\Unit\Integration\Endpoints;

use LetsPeppolTransmissionEndpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolTransmissionEndpointTest extends TestCase
{
    private function makeEndpoint(array $responses = []): array
    {
        $client = new FakeLetsPeppolApiClient($responses);
        $client->configure([
            'api_base_url'                 => 'https://api.letspeppol.eu',
            'transmissions_endpoint'       => '/v1/transmissions',
            'transmission_status_endpoint' => '/v1/transmissions/{id}',
        ]);
        $endpoint = new LetsPeppolTransmissionEndpoint($client);

        return [$endpoint, $client];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_transmission_id_in_the_status_url(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $result = $endpoint->status('tx-55');

        /* Assert */
        $this->assertStringContainsString('tx-55', $client->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
        $this->assertSame('tx-55', $result['external_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_transmission_status_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'transmission_status_endpoint' => '']);
        $endpoint = new LetsPeppolTransmissionEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->status('tx-1');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_get_request_to_list_transmissions(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->list(['status' => 'delivered']);

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('transmissions', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_transmissions_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'transmissions_endpoint' => '']);
        $endpoint = new LetsPeppolTransmissionEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->list();
    }
}
