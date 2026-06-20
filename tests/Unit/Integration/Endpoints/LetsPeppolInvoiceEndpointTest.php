<?php

namespace Tests\Unit\Integration\Endpoints;

use LetsPeppolInvoiceEndpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolInvoiceEndpointTest extends TestCase
{
    private function makeEndpoint(array $responses = []): array
    {
        $client   = new FakeLetsPeppolApiClient($responses);
        $client->configure([
            'api_base_url'               => 'https://api.letspeppol.eu',
            'invoice_endpoint'           => '/v1/invoices',
            'invoice_status_endpoint'    => '/v1/invoices/{id}',
            'incoming_invoices_endpoint' => '/v1/incoming-invoices',
            'invoice_events_endpoint'    => '/v1/invoice-events',
        ]);
        $endpoint = new LetsPeppolInvoiceEndpoint($client);

        return [$endpoint, $client];
    }

    // --- send ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_posts_multipart_when_sending_an_invoice(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $endpoint->send($tmp, ['ref' => 'INV-001']);
        unlink($tmp);

        /* Assert */
        $this->assertSame(\RequestMethod::POST, $client->requestLog[0]['method']);
        $this->assertTrue($client->requestLog[0]['multipart']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_metadata_as_json_encoded_field(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');
        $metadata = ['ref' => 'INV-001', 'buyer_reference' => 'PO-42'];

        /* Act */
        $endpoint->send($tmp, $metadata);
        unlink($tmp);

        /* Assert */
        $this->assertArrayHasKey('metadata', $client->requestLog[0]['payload']);
        $this->assertSame(json_encode($metadata), $client->requestLog[0]['payload']['metadata']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_omits_metadata_field_when_metadata_is_empty(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $endpoint->send($tmp, []);
        unlink($tmp);

        /* Assert */
        $this->assertArrayNotHasKey('metadata', $client->requestLog[0]['payload']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_invoice_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'invoice_endpoint' => '']);
        $endpoint = new LetsPeppolInvoiceEndpoint($client);
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
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

    // --- status ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_invoice_id_in_the_status_url(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $result = $endpoint->status('inv-42');

        /* Assert */
        $this->assertStringContainsString('inv-42', $client->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
        $this->assertSame('inv-42', $result['external_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_invoice_status_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'invoice_status_endpoint' => '']);
        $endpoint = new LetsPeppolInvoiceEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->status('inv-1');
    }

    // --- incoming ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_get_request_to_fetch_incoming_invoices(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->incoming(['status' => 'new']);

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('incoming-invoices', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_incoming_invoices_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'incoming_invoices_endpoint' => '']);
        $endpoint = new LetsPeppolInvoiceEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->incoming();
    }

    // --- events ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_invoice_events_with_a_get_request(): void
    {
        /* Arrange */
        [$endpoint, $client] = $this->makeEndpoint();

        /* Act */
        $endpoint->events();

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
        $this->assertStringContainsString('invoice-events', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_when_invoice_events_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $client = new FakeLetsPeppolApiClient();
        $client->configure(['api_base_url' => 'https://api.letspeppol.eu', 'invoice_events_endpoint' => '']);
        $endpoint = new LetsPeppolInvoiceEndpoint($client);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $endpoint->events();
    }
}
