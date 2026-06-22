<?php

namespace Tests\Unit\Integration\Endpoints;

use LetsPeppolDocumentEndpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolDocumentEndpointTest extends TestCase
{
    private function makeEndpoint(array $responses = []): array
    {
        $client = new FakeLetsPeppolApiClient($responses);
        $client->configure([
            'api_base_url'      => 'https://api.letspeppol.eu',
            'documents_endpoint' => '/v1/documents',
            'document_endpoint'  => '/v1/documents/{id}',
        ]);
        $endpoint = new LetsPeppolDocumentEndpoint($client);

        return [$endpoint, $client];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_a_document_by_id_with_a_get_request(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->get('doc-33');

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('doc-33', $client->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_document_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'document_endpoint' => '']);
        $endpoint = new LetsPeppolDocumentEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->get('doc-1');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_get_request_to_list_documents(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->list(['type' => 'invoice']);

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('documents', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_documents_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'documents_endpoint' => '']);
        $endpoint = new LetsPeppolDocumentEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->list();
    }
}
