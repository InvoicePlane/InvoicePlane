<?php

namespace Tests\Unit\Integration\Endpoints;

use LetsPeppolParticipantEndpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolParticipantEndpointTest extends TestCase
{
    private function makeEndpoint(array $responses = []): array
    {
        $client = new FakeLetsPeppolApiClient($responses);
        $client->configure([
            'api_base_url'                => 'https://api.letspeppol.eu',
            'participants_endpoint'        => '/v1/participants',
            'participant_lookup_endpoint'  => '/v1/participants/{id}',
        ]);
        $endpoint = new LetsPeppolParticipantEndpoint($client);

        return [$endpoint, $client];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_looks_up_a_participant_by_id(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->lookup('0088:1234567890');

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('participants', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_participant_id_in_the_lookup_url(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->lookup('0088:9999');

        /* Assert */
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
        $this->assertStringContainsString('0088', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_participant_lookup_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'participant_lookup_endpoint' => '']);
        $endpoint = new LetsPeppolParticipantEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->lookup('0088:1234');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_get_request_to_list_participants(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->list(['page' => 1]);

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_participants_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'participants_endpoint' => '']);
        $endpoint = new LetsPeppolParticipantEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->list();
    }
}
