<?php

namespace Tests\Unit\Integration;

use LetsPeppolClient;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

class LetsPeppolClientTest extends TestCase
{
    private function defaultSettings(): array
    {
        return [
            'client_id'                    => 'cid',
            'client_secret'                => 'csecret',
            'token_url'                    => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url'                 => 'https://api.letspeppol.eu',
            'invoice_endpoint'             => '/v1/invoices',
            'invoice_status_endpoint'      => '/v1/invoices/{id}',
            'incoming_invoices_endpoint'   => '/v1/incoming-invoices',
            'invoice_events_endpoint'      => '/v1/invoice-events',
            'credit_note_endpoint'         => '/v1/credit-notes',
            'credit_note_status_endpoint'  => '/v1/credit-notes/{id}',
            'participants_endpoint'        => '/v1/participants',
            'participant_lookup_endpoint'  => '/v1/participants/{id}',
            'transmissions_endpoint'       => '/v1/transmissions',
            'transmission_status_endpoint' => '/v1/transmissions/{id}',
            'documents_endpoint'           => '/v1/documents',
            'document_endpoint'            => '/v1/documents/{id}',
        ];
    }

    private function makeProvider(array $responses = [], array $tokenResponse = ['access_token' => 'fake-token']): array
    {
        $client   = new FakeLetsPeppolApiClient($responses, $tokenResponse);
        $provider = new LetsPeppolClient($client);

        return [$provider, $client];
    }

    // --- static metadata ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_letspeppol_as_the_provider_code(): void
    {
        /* Arrange */

        /* Act */
        $code = LetsPeppolClient::clientCode();

        /* Assert */
        $this->assertSame('letspeppol', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_LetsPeppol_as_the_provider_name(): void
    {
        /* Arrange */

        /* Act */
        $name = LetsPeppolClient::clientName();

        /* Assert */
        $this->assertSame('LetsPeppol', $name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_all_required_oauth_keys_in_default_settings(): void
    {
        /* Arrange */

        /* Act */
        $settings = LetsPeppolClient::defaultSettings();

        /* Assert */
        foreach (['client_id', 'client_secret', 'token_url', 'api_base_url'] as $key) {
            $this->assertArrayHasKey($key, $settings);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_all_endpoint_keys_in_default_settings(): void
    {
        /* Arrange */

        /* Act */
        $settings = LetsPeppolClient::defaultSettings();

        /* Assert */
        $expected = [
            'invoice_endpoint',
            'invoice_status_endpoint',
            'incoming_invoices_endpoint',
            'invoice_events_endpoint',
            'credit_note_endpoint',
            'participants_endpoint',
            'transmissions_endpoint',
            'documents_endpoint',
        ];
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $settings);
        }
    }

    // --- authenticate ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calls_fetch_token_with_the_configured_credentials(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider();

        /* Act */
        $result = $provider->authenticate($this->defaultSettings());

        /* Assert */
        $this->assertTrue($result);
        $this->assertCount(1, $client->tokenLog);
        $this->assertSame('https://api.letspeppol.eu/oauth2/token', $client->tokenLog[0]['tokenUrl']);
        $this->assertSame('cid', $client->tokenLog[0]['clientId']);
        $this->assertSame('csecret', $client->tokenLog[0]['clientSecret']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_client_id_is_missing(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $settings = $this->defaultSettings();
        $settings['client_id'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol setting: client_id');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_client_secret_is_missing(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $settings = $this->defaultSettings();
        $settings['client_secret'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol setting: client_secret');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_token_url_is_missing(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $settings = $this->defaultSettings();
        $settings['token_url'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_api_base_url_is_missing(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $settings = $this->defaultSettings();
        $settings['api_base_url'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol setting: api_base_url');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_the_token_response_contains_no_access_token(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider([], ['error' => 'invalid_client']);

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no access_token');

        /* Assert */
        $provider->authenticate($this->defaultSettings());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_propagates_a_fetch_token_network_exception(): void
    {
        /* Arrange */
        $client   = new FakeLetsPeppolApiClient([], ['access_token' => 'tok'], 'LetsPeppol OAuth error: connection refused');
        $provider = new LetsPeppolClient($client);

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('connection refused');

        /* Assert */
        $provider->authenticate($this->defaultSettings());
    }

    // --- sendInvoice ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_send_when_the_invoice_file_does_not_exist(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        /* Assert */
        $provider->sendInvoice('/nonexistent/invoice.pdf', []);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_an_invoice_as_a_multipart_post(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider([
            ['success' => true, 'external_id' => 'inv-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 201, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmp, ['ref' => 'INV-001']);
        unlink($tmp);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame(\RequestMethod::POST, $client->requestLog[0]['method']);
        $this->assertTrue($client->requestLog[0]['multipart']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_an_error_when_the_send_step_fails(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider([
            ['success' => false, 'external_id' => null, 'status' => 'error', 'message' => 'Forbidden', 'http_code' => 403, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmp, []);
        unlink($tmp);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertSame(403, $result['http_code']);
    }

    // --- getInvoiceStatus ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_invoice_id_into_the_status_url(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider([
            ['success' => true, 'external_id' => 'inv-99', 'status' => 'delivered', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $result = $provider->getInvoiceStatus('inv-99');

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame('inv-99', $result['external_id']);
        $this->assertStringContainsString('inv-99', $client->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_get_status_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings = $this->defaultSettings();
        $settings['invoice_status_endpoint'] = '';
        [$provider] = $this->makeProvider();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->getInvoiceStatus('inv-1');
    }

    // --- receiveInvoices ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_appends_filters_as_a_query_string_when_receiving_invoices(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->receiveInvoices(['status' => 'new', 'page' => 3]);

        /* Assert */
        $this->assertStringContainsString('status=new', $client->requestLog[0]['url']);
        $this->assertStringContainsString('page=3', $client->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_receive_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings = $this->defaultSettings();
        $settings['incoming_invoices_endpoint'] = '';
        [$provider] = $this->makeProvider();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->receiveInvoices();
    }

    // --- getInvoiceEvents ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_invoice_events_with_a_get_request(): void
    {
        /* Arrange */
        [$provider, $client] = $this->makeProvider();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->getInvoiceEvents();

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $client->requestLog[0]['method']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_get_events_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings = $this->defaultSettings();
        $settings['invoice_events_endpoint'] = '';
        [$provider] = $this->makeProvider();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->getInvoiceEvents();
    }

    // --- buildInvoicePayload ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_metadata_unchanged_from_build_invoice_payload(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();
        $metadata = ['ref' => 'INV-001', 'buyer_reference' => 'PO-42'];

        /* Act */
        $result = $provider->buildInvoicePayload((object)[], [], $metadata);

        /* Assert */
        $this->assertSame($metadata, $result);
    }

    // --- endpoint accessors ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_exposes_the_participants_endpoint(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();

        /* Act */
        $participants = $provider->participants();

        /* Assert */
        $this->assertInstanceOf(\LetsPeppolParticipantEndpoint::class, $participants);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_exposes_the_credit_notes_endpoint(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();

        /* Act */
        $creditNotes = $provider->creditNotes();

        /* Assert */
        $this->assertInstanceOf(\LetsPeppolCreditNoteEndpoint::class, $creditNotes);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_exposes_the_transmissions_endpoint(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();

        /* Act */
        $transmissions = $provider->transmissions();

        /* Assert */
        $this->assertInstanceOf(\LetsPeppolTransmissionEndpoint::class, $transmissions);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_exposes_the_documents_endpoint(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider();

        /* Act */
        $documents = $provider->documents();

        /* Assert */
        $this->assertInstanceOf(\LetsPeppolDocumentEndpoint::class, $documents);
    }
}
