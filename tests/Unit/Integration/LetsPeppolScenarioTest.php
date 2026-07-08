<?php

namespace Tests\Unit\Integration;

use LetsPeppolClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fakes\Integration\FakeLetsPeppolApiClient;

/**
 * Scenario-level tests for the LetsPeppol integration flow.
 *
 * Each test exercises a realistic sequence of calls (authenticate → do something)
 * using FakeLetsPeppolApiClient so no real HTTP is made. The goal is to verify
 * that the correct endpoints are called in the correct order with the correct
 * payload, and that the provider returns well-formed result arrays.
 */
class LetsPeppolScenarioTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Participant lookup — green-checkmark scenario
    // -----------------------------------------------------------------------

    #[Test]
    public function it_looks_up_a_reachable_participant_after_authentication(): void
    {
        /* Arrange */
        $participantResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => [
                'id'        => '0088:1234567890',
                'name'      => 'ACME Corp',
                'country'   => 'NL',
                'reachable' => true,
            ],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$participantResponse]);

        /* Act */
        $result = $provider->participants()->lookup('0088:1234567890');

        /* Assert */
        $this->assertTrue($result['response']['reachable']);
        $this->assertSame('ACME Corp', $result['response']['name']);
        $this->assertSame('NL', $result['response']['country']);

        $this->assertCount(1, $fake->requestLog);
        $this->assertStringContainsString('0088', $fake->requestLog[0]['url']);
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
    }

    #[Test]
    public function it_returns_reachable_false_when_participant_is_not_on_the_peppol_network(): void
    {
        /* Arrange */
        $participantResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => [
                'id'        => '0088:9999999999',
                'name'      => 'Unknown Ltd',
                'country'   => 'DE',
                'reachable' => false,
            ],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$participantResponse]);

        /* Act */
        $result = $provider->participants()->lookup('0088:9999999999');

        /* Assert */
        $this->assertFalse($result['response']['reachable']);
        $this->assertCount(1, $fake->requestLog);
    }

    #[Test]
    public function it_propagates_a_not_found_response_when_participant_id_does_not_exist(): void
    {
        /* Arrange */
        $notFoundResponse = [
            'success'   => false,
            'http_code' => 404,
            'message'   => 'Participant not found',
            'response'  => [],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$notFoundResponse]);

        /* Act */
        $result = $provider->participants()->lookup('0088:0000000000');

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertSame(404, $result['http_code']);
        $this->assertSame('Participant not found', $result['message']);
        $this->assertCount(1, $fake->requestLog);
    }

    #[Test]
    public function it_sends_the_bearer_token_on_the_participant_lookup_request(): void
    {
        /* Arrange */
        [$provider, $fake] = $this->makeProvider([['success' => true, 'http_code' => 200, 'response' => ['reachable' => true]]]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->participants()->lookup('0088:1111111111');

        /* Assert */
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
    }

    // -----------------------------------------------------------------------
    // fetchToken helper
    // -----------------------------------------------------------------------

    #[Test]
    public function it_returns_the_access_token_string_via_fetch_token(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider([], ['access_token' => 'token-xyz']);

        /* Act */
        $token = $provider->fetchToken($this->defaultSettings());

        /* Assert */
        $this->assertSame('token-xyz', $token);
    }

    #[Test]
    public function it_throws_when_fetch_token_receives_no_access_token(): void
    {
        /* Arrange */
        [$provider] = $this->makeProvider([], ['error' => 'invalid_client']);

        /* Act & Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no access_token');

        $provider->fetchToken($this->defaultSettings());
    }

    // -----------------------------------------------------------------------
    // Send invoice — happy path
    // -----------------------------------------------------------------------

    #[Test]
    public function it_sends_an_invoice_and_returns_the_external_id(): void
    {
        /* Arrange */
        $sendResponse = [
            'success'     => true,
            'http_code'   => 201,
            'external_id' => 'inv-ext-001',
            'response'    => ['id' => 'inv-ext-001', 'status' => 'processing'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$sendResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $result = $provider->sendInvoice($path, ['peppol_id' => '0088:1234567890']);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame('inv-ext-001', $result['response']['id']);
        $this->assertSame('processing', $result['response']['status']);
        $this->assertTrue($fake->requestLog[0]['multipart']);

        unlink($path);
    }

    #[Test]
    public function it_sends_the_bearer_token_on_the_invoice_send_request(): void
    {
        /* Arrange */
        $sendResponse      = ['success' => true, 'http_code' => 201, 'response' => ['id' => 'inv-ext-002', 'status' => 'processing']];
        [$provider, $fake] = $this->authenticatedProvider([$sendResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $provider->sendInvoice($path, []);

        /* Assert */
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);

        unlink($path);
    }

    #[Test]
    public function it_throws_when_the_invoice_document_file_does_not_exist(): void
    {
        /* Arrange */
        [$provider] = $this->authenticatedProvider();

        /* Act & Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        $provider->sendInvoice('/tmp/does-not-exist.pdf', []);
    }

    // -----------------------------------------------------------------------
    // Send invoice — error gates
    // -----------------------------------------------------------------------

    #[Test]
    public function it_returns_a_validation_error_when_the_api_rejects_the_invoice(): void
    {
        /* Arrange */
        $rejectedResponse = [
            'success'   => false,
            'http_code' => 422,
            'message'   => 'Invalid UBL document',
            'response'  => ['errors' => ['ubl_schema' => 'XSD validation failed']],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$rejectedResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $result = $provider->sendInvoice($path, []);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertSame(422, $result['http_code']);
        $this->assertSame('Invalid UBL document', $result['message']);
        $this->assertCount(1, $fake->requestLog);

        unlink($path);
    }

    #[Test]
    public function it_returns_an_auth_error_when_the_token_is_rejected_during_send(): void
    {
        /* Arrange */
        $unauthorizedResponse = [
            'success'   => false,
            'http_code' => 401,
            'message'   => 'Unauthorized',
            'response'  => [],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$unauthorizedResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $result = $provider->sendInvoice($path, []);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertSame(401, $result['http_code']);

        unlink($path);
    }

    // -----------------------------------------------------------------------
    // Invoice status
    // -----------------------------------------------------------------------

    #[Test]
    public function it_returns_delivered_status_after_a_successful_send(): void
    {
        /* Arrange */
        $statusResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => 'inv-ext-001', 'status' => 'delivered'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$statusResponse]);

        /* Act */
        $result = $provider->getInvoiceStatus('inv-ext-001');

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame('delivered', $result['response']['status']);
        $this->assertStringContainsString('inv-ext-001', $fake->requestLog[0]['url']);
    }

    #[Test]
    public function it_returns_rejected_status_when_the_recipient_rejects_the_invoice(): void
    {
        /* Arrange */
        $statusResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => 'inv-ext-007', 'status' => 'rejected', 'reason' => 'Duplicate invoice number'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$statusResponse]);

        /* Act */
        $result = $provider->getInvoiceStatus('inv-ext-007');

        /* Assert */
        $this->assertSame('rejected', $result['response']['status']);
        $this->assertSame('Duplicate invoice number', $result['response']['reason']);
    }

    #[Test]
    public function it_sends_the_bearer_token_on_the_status_check_request(): void
    {
        /* Arrange */
        $statusResponse    = ['success' => true, 'http_code' => 200, 'response' => ['id' => 'x', 'status' => 'processing']];
        [$provider, $fake] = $this->authenticatedProvider([$statusResponse]);

        /* Act */
        $provider->getInvoiceStatus('x');

        /* Assert */
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
    }

    // -----------------------------------------------------------------------
    // Full flow: authenticate → lookup → send → status
    // -----------------------------------------------------------------------

    #[Test]
    public function it_completes_the_full_peppol_flow_in_order(): void
    {
        /* Arrange */
        $lookupResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => '0088:1234567890', 'reachable' => true],
        ];
        $sendResponse = [
            'success'     => true,
            'http_code'   => 201,
            'external_id' => 'inv-flow-1',
            'response'    => ['id' => 'inv-flow-1', 'status' => 'processing'],
        ];
        $statusResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => 'inv-flow-1', 'status' => 'delivered'],
        ];

        [$provider, $fake] = $this->makeProvider([$lookupResponse, $sendResponse, $statusResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $provider->authenticate($this->defaultSettings());

        $lookup = $provider->participants()->lookup('0088:1234567890');
        $send   = $provider->sendInvoice($path, ['peppol_id' => '0088:1234567890']);
        $status = $provider->getInvoiceStatus('inv-flow-1');

        /* Assert */
        $this->assertTrue($lookup['response']['reachable']);
        $this->assertTrue($send['success']);
        $this->assertSame('processing', $send['response']['status']);
        $this->assertSame('delivered', $status['response']['status']);

        $this->assertCount(3, $fake->requestLog);
        $this->assertStringContainsString('participants', $fake->requestLog[0]['url']);
        $this->assertStringContainsString('invoices', $fake->requestLog[1]['url']);
        $this->assertStringContainsString('invoices', $fake->requestLog[2]['url']);

        $this->assertCount(1, $fake->tokenLog);
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
        $this->assertSame('tok-abc', $fake->requestLog[1]['bearerToken']);
        $this->assertSame('tok-abc', $fake->requestLog[2]['bearerToken']);

        unlink($path);
    }

    // -----------------------------------------------------------------------
    // Incoming invoices
    // -----------------------------------------------------------------------

    #[Test]
    public function it_retrieves_incoming_invoices_from_the_peppol_network(): void
    {
        /* Arrange */
        $incomingResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => [
                'data' => [
                    ['id' => 'inc-001', 'sender' => '0088:9876543210', 'status' => 'received'],
                    ['id' => 'inc-002', 'sender' => '0088:1111111111', 'status' => 'received'],
                ],
                'meta' => ['total' => 2],
            ],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$incomingResponse]);

        /* Act */
        $result = $provider->receiveInvoices(['page' => 1]);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['response']['data']);
        $this->assertSame('inc-001', $result['response']['data'][0]['id']);
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
    }

    // -----------------------------------------------------------------------
    // Token isolation — each operation uses the same token within one session
    // -----------------------------------------------------------------------

    #[Test]
    public function it_uses_the_same_token_for_all_requests_within_one_authentication_session(): void
    {
        /* Arrange */
        $responses = [
            ['success' => true, 'http_code' => 200, 'response' => ['reachable' => true]],
            ['success' => true, 'http_code' => 200, 'response' => ['id' => 'x', 'status' => 'processing']],
        ];
        [$provider, $fake] = $this->authenticatedProvider($responses);
        $path              = $this->tempDocumentPath();

        /* Act */
        $provider->participants()->lookup('0088:1234567890');
        $provider->getInvoiceStatus('x');

        /* Assert */
        $this->assertCount(1, $fake->tokenLog, 'authenticate() should only fetch one token per session');
        $this->assertSame('tok-abc', $fake->requestLog[0]['bearerToken']);
        $this->assertSame('tok-abc', $fake->requestLog[1]['bearerToken']);

        unlink($path);
    }

    // -----------------------------------------------------------------------
    // Transmission status
    // -----------------------------------------------------------------------

    #[Test]
    public function it_retrieves_transmission_status_and_attaches_the_external_id(): void
    {
        /* Arrange */
        $transmissionResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => 'tx-99', 'status' => 'delivered', 'invoice_id' => 'inv-flow-1'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$transmissionResponse]);

        /* Act */
        $result = $provider->transmissions()->status('tx-99');

        /* Assert */
        $this->assertSame('tx-99', $result['external_id']);
        $this->assertSame('delivered', $result['response']['status']);
        $this->assertCount(1, $fake->requestLog);
    }

    // -----------------------------------------------------------------------
    // Credit note
    // -----------------------------------------------------------------------

    #[Test]
    public function it_sends_a_credit_note_and_returns_the_external_id(): void
    {
        /* Arrange */
        $sendResponse = [
            'success'   => true,
            'http_code' => 201,
            'response'  => ['id' => 'cn-ext-01', 'status' => 'processing'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$sendResponse]);
        $path              = $this->tempDocumentPath();

        /* Act */
        $result = $provider->creditNotes()->send($path, ['peppol_id' => '0088:1234567890']);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame('cn-ext-01', $result['response']['id']);
        $this->assertTrue($fake->requestLog[0]['multipart']);

        unlink($path);
    }

    #[Test]
    public function it_retrieves_credit_note_status_and_attaches_the_external_id(): void
    {
        /* Arrange */
        $statusResponse = [
            'success'   => true,
            'http_code' => 200,
            'response'  => ['id' => 'cn-ext-01', 'status' => 'sent'],
        ];
        [$provider, $fake] = $this->authenticatedProvider([$statusResponse]);

        /* Act */
        $result = $provider->creditNotes()->status('cn-ext-01');

        /* Assert */
        $this->assertSame('cn-ext-01', $result['external_id']);
        $this->assertSame('sent', $result['response']['status']);
    }
    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function defaultSettings(): array
    {
        return [
            'client_id'                    => 'my-client-id',
            'client_secret'                => 'my-client-secret',
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

    /** Build a provider + fake HTTP client pre-loaded with $responses. */
    private function makeProvider(
        array $responses = [],
        array $tokenResponse = ['access_token' => 'tok-abc'],
        ?string $tokenError = null
    ): array {
        $fake     = new FakeLetsPeppolApiClient($responses, $tokenResponse, $tokenError);
        $provider = new LetsPeppolClient($fake);

        return [$provider, $fake];
    }

    /** Authenticate the provider against defaultSettings() and return it. */
    private function authenticatedProvider(array $responses = []): array
    {
        [$provider, $fake] = $this->makeProvider($responses);
        $provider->authenticate($this->defaultSettings());

        return [$provider, $fake];
    }

    /** Create a real temp PDF-like file so file_exists() passes. */
    private function tempDocumentPath(): string
    {
        $path = sys_get_temp_dir() . '/letspeppol_test_' . uniqid() . '.pdf';
        file_put_contents($path, '%PDF-1.4 fake content');

        return $path;
    }
}
