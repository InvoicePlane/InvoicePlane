<?php

namespace Tests\Unit\Integration\Endpoints;

use LetsPeppolCreditNoteEndpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolCreditNoteEndpointTest extends TestCase
{
    private function makeEndpoint(array $responses = []): array
    {
        $client = new FakeLetsPeppolApiClient($responses);
        $client->configure([
            'api_base_url'                => 'https://api.letspeppol.eu',
            'credit_note_endpoint'        => '/v1/credit-notes',
            'credit_note_status_endpoint' => '/v1/credit-notes/{id}',
        ]);
        $endpoint = new LetsPeppolCreditNoteEndpoint($client);

        return [$endpoint, $client];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_posts_multipart_when_sending_a_credit_note(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();
        $tmp = tempnam(sys_get_temp_dir(), 'cn') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $endpoint->send($tmp, ['ref' => 'CN-001']);
        unlink($tmp);

        /* Assert */
        $this->assertSame(\RequestMethod::POST, $client->requestLog[0]['method']);
        $this->assertTrue($client->requestLog[0]['multipart']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_credit_note_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'credit_note_endpoint' => '']);
        $endpoint = new LetsPeppolCreditNoteEndpoint($client);
        $tmp = tempnam(sys_get_temp_dir(), 'cn') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        try {
            $endpoint->send($tmp, []);
        } finally {
            unlink($tmp);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_credit_note_id_in_the_status_url(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $result = $endpoint->status('cn-77');

        /* Assert */
        $this->assertStringContainsString('cn-77', $client->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
        $this->assertSame('cn-77', $result['external_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_credit_note_status_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'credit_note_status_endpoint' => '']);
        $endpoint = new LetsPeppolCreditNoteEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->status('cn-1');
    }
}
