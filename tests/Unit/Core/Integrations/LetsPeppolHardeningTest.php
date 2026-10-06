<?php

namespace Tests\Unit\Core\Integrations;

use ApiClientInterface;
use CURLFile;
use LetsPeppolApiClient;
use LetsPeppolClient;
use LetsPeppolCreditNoteEndpoint;
use LetsPeppolDocumentEndpoint;
use LetsPeppolParticipantEndpoint;
use LetsPeppolTransmissionEndpoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;
use Tests\Fakes\Integration\ApiClientFake;

/**
 * LetsPeppol paths the scenario tests do not exercise, driven through the real
 * LetsPeppolApiClient and endpoint classes over a fake HTTP adapter: authentication,
 * request building, multipart uploads, normalisation, document download.
 */
#[Group('unit')]
final class LetsPeppolHardeningTest extends TestCase
{
    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
        $this->tempFiles = [];
    }

    /** @return array<string, array{string}> */
    public static function requiredSettings(): array
    {
        return ['client_id' => ['client_id'], 'client_secret' => ['client_secret'], 'token_url' => ['token_url'], 'api_base_url' => ['api_base_url']];
    }

    /** @return array<string, array{string, string, string}> */
    public static function listEndpointSettings(): array
    {
        return [
            'incoming'      => ['incoming_invoices_endpoint', 'Missing LetsPeppol incoming invoices endpoint configuration.', 'incoming'],
            'events'        => ['invoice_events_endpoint', 'Missing LetsPeppol invoice events endpoint configuration.', 'events'],
            'participants'  => ['participants_endpoint', 'Missing LetsPeppol participants endpoint configuration.', 'participants'],
            'documents'     => ['documents_endpoint', 'Missing LetsPeppol documents endpoint configuration.', 'documents'],
            'transmissions' => ['transmissions_endpoint', 'Missing LetsPeppol transmissions endpoint configuration.', 'transmissions'],
        ];
    }

    /** @return array<string, array{array<string, mixed>}> */
    public static function invalidDocumentIds(): array
    {
        return ['absent' => [[]], 'empty' => [['id' => '']], 'numeric' => [['id' => 5]]];
    }

    /** @return array<string, array{string, string, string}> */
    public static function lookupEndpointSettings(): array
    {
        return [
            'document'     => ['document_endpoint', 'Missing LetsPeppol document endpoint configuration.', 'document'],
            'participant'  => ['participant_lookup_endpoint', 'Missing LetsPeppol participant lookup endpoint configuration.', 'participant'],
            'transmission' => ['transmission_status_endpoint', 'Missing LetsPeppol transmission status endpoint configuration.', 'transmission'],
        ];
    }

    // ---- authentication ------------------------------------------------------------------------

    #[Test]
    #[DataProvider('requiredSettings')]
    public function it_names_the_missing_setting_when_authentication_is_incomplete(string $missing): void
    {
        [$client, $http] = $this->client();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol setting: ' . $missing);

        try {
            $client->authenticate(array_replace($this->settings(), [$missing => '']));
        } finally {
            self::assertSame([], $http->tokenLog, 'No token request is made with incomplete settings.');
        }
    }

    #[Test]
    public function it_requests_a_token_with_the_client_credentials_and_openid_scope_and_uses_it_as_bearer(): void
    {
        $http   = new ApiClientFake([$this->envelope()], ['access_token' => 'lp-token']);
        $client = new LetsPeppolClient(new LetsPeppolApiClient($http));

        self::assertTrue($client->authenticate($this->settings()));
        $client->getInvoiceStatus('x');

        self::assertSame([['tokenUrl' => 'https://auth.lp.test/token', 'clientId' => 'cid', 'clientSecret' => 'sec']], $http->tokenLog);
        self::assertSame('lp-token', $http->requestLog[0]['bearerToken']);
    }

    #[Test]
    public function it_sends_the_openid_scope_in_the_token_request(): void
    {
        $captured = null;
        $http     = new class ($captured) implements ApiClientInterface {
            public function __construct(public mixed &$captured) {}

            public function request(RequestMethod $method, string $url, array $options = []): array
            {
                $this->captured = [$method, $options['form_params'] ?? null];

                return ['success' => true, 'response' => ['access_token' => 't']];
            }
        };
        (new LetsPeppolApiClient($http))->configure($this->settings());
        $api = new LetsPeppolApiClient($http);
        $api->configure($this->settings());

        $api->authenticate();

        self::assertSame(RequestMethod::POST, $captured[0]);
        self::assertSame(['grant_type' => 'client_credentials', 'client_id' => 'cid', 'client_secret' => 'sec', 'scope' => 'openid'], $captured[1]);
        self::assertSame('t', $api->getAccessToken());
    }

    #[Test]
    public function it_reports_a_failed_token_request_with_the_transport_message(): void
    {
        $api = new LetsPeppolApiClient($this->failingHttp(['success' => false, 'message' => 'bad credentials']));
        $api->configure($this->settings());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('LetsPeppol OAuth request failed: bad credentials');

        $api->authenticate();
    }

    #[Test]
    public function it_reports_a_token_response_without_an_access_token(): void
    {
        $api = new LetsPeppolApiClient($this->failingHttp(['success' => true, 'response' => ['token_type' => 'Bearer']]));
        $api->configure($this->settings());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('LetsPeppol OAuth failed: no access_token in response.');

        $api->authenticate();
    }

    #[Test]
    public function it_fetches_a_token_on_demand_and_returns_an_empty_string_when_there_is_none(): void
    {
        $withToken = new LetsPeppolClient(new LetsPeppolApiClient(new ApiClientFake([], ['access_token' => 'on-demand'])));
        self::assertSame('on-demand', $withToken->fetchToken($this->settings()));

        $api = new LetsPeppolApiClient(new ApiClientFake());
        self::assertNull($api->getAccessToken(), 'Before authentication there is no token.');
        $api->configure($this->settings());
        self::assertSame($this->settings(), $api->getSettings());
    }

    // ---- request building ----------------------------------------------------------------------

    #[Test]
    public function it_joins_base_url_endpoint_and_an_encoded_id_with_single_slashes(): void
    {
        $api = $this->api();

        self::assertSame('https://api.lp.test/v1/invoices', $api->buildUrl('/v1/invoices'));
        self::assertSame('https://api.lp.test/v1/invoices', $api->buildUrl('v1/invoices'));
        self::assertSame('https://api.lp.test/v1/invoices/a%2Fb+c', $api->buildUrl('/v1/invoices/{id}', 'a/b c'));
        self::assertSame('https://api.lp.test/v1/invoices/{id}', $api->buildUrl('/v1/invoices/{id}'), 'Without an id the template is left alone.');
    }

    #[Test]
    public function it_builds_the_request_body_from_the_payload_kind(): void
    {
        $http = new ApiClientFake();
        $api  = $this->api($http);

        $api->request(RequestMethod::POST, 'https://api.lp.test/a', ['k' => 'v']);
        $api->request(RequestMethod::POST, 'https://api.lp.test/b', [['name' => 'file']], true);
        $api->request(RequestMethod::GET, 'https://api.lp.test/c');
        $api->request(RequestMethod::GET, 'https://api.lp.test/d', query: ['limit' => 2, 'q' => 'a b']);

        self::assertSame(['k' => 'v'], $http->requestLog[0]['options']['json']);
        self::assertSame([['name' => 'file']], $http->requestLog[1]['options']['multipart']);
        self::assertArrayNotHasKey('json', $http->requestLog[1]['options']);
        self::assertArrayNotHasKey('json', $http->requestLog[2]['options']);
        self::assertArrayNotHasKey('multipart', $http->requestLog[2]['options']);
        self::assertSame('https://api.lp.test/d?limit=2&q=a+b', $http->requestLog[3]['url']);
        self::assertSame('https://api.lp.test/c', $http->requestLog[2]['url'], 'No query, no question mark.');
    }

    // ---- sending -------------------------------------------------------------------------------

    #[Test]
    public function it_refuses_a_path_that_does_not_exist_and_one_that_is_not_a_file(): void
    {
        [$client, $http] = $this->authenticatedClient();
        $directory       = sys_get_temp_dir() . '/lp-dir-' . bin2hex(random_bytes(4));
        mkdir($directory);

        foreach (['/nonexistent/x.xml', $directory] as $path) {
            try {
                $client->sendInvoice($path, []);
                self::fail('An unusable path must be refused.');
            } catch (RuntimeException $e) {
                self::assertSame('Invoice document not found: ' . $path, $e->getMessage());
            }
        }
        rmdir($directory);
        self::assertSame([], $http->requestLog);
    }

    #[Test]
    public function it_uploads_the_document_as_multipart_with_its_mime_type_and_json_metadata(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['invoice' => ['id' => 'lp-1']]])]);
        $path            = $this->tempFile('inv.xml', '<Invoice/>');

        $result = $client->sendInvoice($path, ['mime_type' => 'application/xml', 'invoice_id' => 7]);

        $call = $http->requestLog[0];
        self::assertSame(RequestMethod::POST, $call['method']);
        self::assertSame('https://api.lp.test/v1/invoices', $call['url']);
        self::assertTrue($call['multipart']);
        $file = $call['options']['multipart']['file'];
        self::assertInstanceOf(CURLFile::class, $file);
        self::assertSame($path, $file->getFilename());
        self::assertSame('application/xml', $file->getMimeType());
        self::assertSame('inv.xml', $file->getPostFilename());
        self::assertSame('{"mime_type":"application\/xml","invoice_id":7}', $call['options']['multipart']['metadata']);
        self::assertSame('lp-1', $result['external_id']);
        self::assertTrue($result['success'], 'An upload with an id stays successful.');
    }

    #[Test]
    public function it_defaults_the_mime_type_and_omits_metadata_when_there_is_none(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['id' => 'lp-2']])]);

        $client->sendInvoice($this->tempFile('inv.xml', '<Invoice/>'), []);

        $multipart = $http->requestLog[0]['options']['multipart'];
        self::assertSame('application/octet-stream', $multipart['file']->getMimeType());
        self::assertArrayNotHasKey('metadata', $multipart);
    }

    #[Test]
    public function it_requires_the_invoice_endpoint_and_turns_an_id_less_acceptance_into_an_error(): void
    {
        [$unconfigured, $http] = $this->authenticatedClient([], ['invoice_endpoint' => '']);
        try {
            $unconfigured->sendInvoice($this->tempFile('a.xml', '<a/>'), []);
            self::fail('The invoice endpoint is required.');
        } catch (RuntimeException $e) {
            self::assertSame('Missing LetsPeppol invoice endpoint configuration.', $e->getMessage());
        }
        self::assertSame([], $http->requestLog);

        [$client] = $this->authenticatedClient([$this->envelope(['response' => ['invoice' => []]])]);
        $result   = $client->sendInvoice($this->tempFile('a.xml', '<a/>'), []);

        self::assertFalse($result['success']);
        self::assertSame('error', $result['status']);
        self::assertSame('LetsPeppol accepted the invoice but returned no external ID.', $result['message']);
    }

    // ---- status, incoming, events --------------------------------------------------------------

    #[Test]
    public function it_looks_up_a_status_by_encoded_id_and_reports_the_callers_id(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['status' => 'sent']])]);

        $result = $client->getInvoiceStatus('a/b');

        self::assertSame('https://api.lp.test/v1/invoices/a%2Fb', $http->requestLog[0]['url']);
        self::assertSame('a/b', $result['external_id']);
        self::assertSame('sent', $result['status']);

        [$unconfigured] = $this->authenticatedClient([], ['invoice_status_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol invoice status endpoint configuration.');
        $unconfigured->getInvoiceStatus('x');
    }

    #[Test]
    public function it_lists_incoming_invoices_and_events_with_filters_in_the_query(): void
    {
        [$client, $http] = $this->authenticatedClient([
            $this->envelope(['response' => ['data' => [['id' => 'i1']]]]),
            $this->envelope(['response' => ['events' => [['id' => 'e1']]]]),
        ]);

        $incoming = $client->receiveInvoices(['limit' => 5]);
        $events   = $client->getInvoiceEvents(['since' => 'yesterday']);

        self::assertSame('https://api.lp.test/v1/incoming-invoices?limit=5', $http->requestLog[0]['url']);
        self::assertSame([['id' => 'i1']], $incoming['response']['invoices']);
        self::assertSame('https://api.lp.test/v1/invoice-events?since=yesterday', $http->requestLog[1]['url']);
        self::assertSame([['id' => 'e1']], $events['response']['events']);
    }

    #[Test]
    #[DataProvider('listEndpointSettings')]
    public function it_requires_each_list_endpoint(string $setting, string $message, string $method): void
    {
        [$client] = $this->authenticatedClient([], [$setting => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        match ($method) {
            'incoming'      => $client->receiveInvoices(),
            'events'        => $client->getInvoiceEvents(),
            'participants'  => $client->participants()->list(),
            'documents'     => $client->documents()->list(),
            'transmissions' => $client->transmissions()->list(),
        };
    }

    // ---- document download ---------------------------------------------------------------------

    #[Test]
    public function it_decodes_an_inline_document_using_the_first_non_empty_content_key(): void
    {
        [$client, $http] = $this->authenticatedClient();

        $first  = $client->downloadInvoiceDocument(['content_base64' => base64_encode('one'), 'document_content' => base64_encode('two'), 'content' => base64_encode('three'), 'filename' => 'a.xml', 'mime_type' => 'text/xml']);
        $second = $client->downloadInvoiceDocument(['content_base64' => '', 'document_content' => base64_encode('two'), 'file_name' => 'b.xml', 'content_type' => 'application/x-b']);
        $third  = $client->downloadInvoiceDocument(['content' => base64_encode('three')]);

        self::assertSame([], $http->requestLog, 'An inline document needs no request.');
        self::assertSame(['one', 'a.xml', 'text/xml'], [$first['content'], $first['filename'], $first['mime_type']]);
        self::assertSame(['two', 'b.xml', 'application/x-b'], [$second['content'], $second['filename'], $second['mime_type']]);
        self::assertSame(['three', 'letspeppol-invoice.xml', 'application/xml'], [$third['content'], $third['filename'], $third['mime_type']]);
        self::assertTrue($first['success']);
        self::assertSame('LetsPeppol document decoded.', $first['message']);
        self::assertSame(200, $first['http_code']);
    }

    #[Test]
    public function it_fetches_a_referenced_document_by_encoded_id_and_decodes_it(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['document' => ['content' => base64_encode('<Invoice/>'), 'filename' => 'remote.xml']]])]);

        $result = $client->downloadInvoiceDocument(['id' => 'doc/1']);

        self::assertSame('https://api.lp.test/v1/documents/doc%2F1', $http->requestLog[0]['url']);
        self::assertSame('<Invoice/>', $result['content']);
        self::assertSame('remote.xml', $result['filename']);
    }

    #[Test]
    public function it_takes_the_document_id_from_document_id_before_id(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['data' => ['content' => base64_encode('x')]]])]);

        $client->downloadInvoiceDocument(['document_id' => 'd9', 'id' => 'i9']);

        self::assertStringEndsWith('/v1/documents/d9', $http->requestLog[0]['url']);
    }

    #[Test]
    #[DataProvider('invalidDocumentIds')]
    public function it_refuses_an_incoming_invoice_without_a_usable_document_id(array $invoice): void
    {
        [$client, $http] = $this->authenticatedClient();

        try {
            $client->downloadInvoiceDocument($invoice);
            self::fail('A document id is required.');
        } catch (RuntimeException $e) {
            self::assertSame('LetsPeppol incoming invoice has no document ID.', $e->getMessage());
        }
        self::assertSame([], $http->requestLog);
    }

    #[Test]
    public function it_returns_the_failure_with_empty_content_when_the_document_lookup_fails(): void
    {
        [$client] = $this->authenticatedClient([$this->envelope(['success' => false, 'http_code' => 404, 'message' => 'gone'])]);

        $result = $client->downloadInvoiceDocument(['id' => 'x']);

        self::assertFalse($result['success']);
        self::assertSame(404, $result['http_code']);
        self::assertNull($result['content']);
        self::assertNull($result['filename']);
        self::assertNull($result['mime_type']);
    }

    #[Test]
    public function it_rejects_a_document_without_content_or_with_invalid_base64(): void
    {
        [$empty] = $this->authenticatedClient([$this->envelope(['response' => ['document' => ['filename' => 'a.xml']]])]);
        try {
            $empty->downloadInvoiceDocument(['id' => 'x']);
            self::fail('A document without content must be refused.');
        } catch (RuntimeException $e) {
            self::assertSame('LetsPeppol document response has no base64 content.', $e->getMessage());
        }

        [$client] = $this->authenticatedClient();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('LetsPeppol incoming document is not valid base64.');
        $client->downloadInvoiceDocument(['content' => '***']);
    }

    // ---- credit notes, documents, participants, transmissions ----------------------------------

    #[Test]
    public function it_exposes_a_distinct_endpoint_object_per_resource(): void
    {
        [$client] = $this->client();

        self::assertInstanceOf(LetsPeppolParticipantEndpoint::class, $client->participants());
        self::assertInstanceOf(LetsPeppolCreditNoteEndpoint::class, $client->creditNotes());
        self::assertInstanceOf(LetsPeppolTransmissionEndpoint::class, $client->transmissions());
        self::assertInstanceOf(LetsPeppolDocumentEndpoint::class, $client->documents());
        self::assertSame($client->participants(), $client->participants());
    }

    #[Test]
    public function it_uploads_a_credit_note_as_a_pdf_and_reports_a_missing_id_as_an_error(): void
    {
        [$client, $http] = $this->authenticatedClient([
            $this->envelope(['response' => ['credit_note' => ['id' => 'cn-1']]]),
            $this->envelope(['response' => ['credit_note' => []]]),
        ]);
        $path = $this->tempFile('cn.pdf', '%PDF');

        $ok  = $client->creditNotes()->send($path, ['credit_note_id' => 3]);
        $bad = $client->creditNotes()->send($path, []);

        $multipart = $http->requestLog[0]['options']['multipart'];
        self::assertSame('https://api.lp.test/v1/credit-notes', $http->requestLog[0]['url']);
        self::assertSame('application/pdf', $multipart['file']->getMimeType());
        self::assertSame('{"credit_note_id":3}', $multipart['metadata']);
        self::assertSame('cn-1', $ok['external_id']);
        self::assertTrue($ok['success'], 'A credit note with an id stays successful.');
        self::assertNotSame('error', $ok['status']);
        self::assertArrayNotHasKey('metadata', $http->requestLog[1]['options']['multipart']);
        self::assertFalse($bad['success']);
        self::assertSame('LetsPeppol accepted the credit note but returned no external ID.', $bad['message']);
    }

    #[Test]
    public function it_guards_the_credit_note_upload_and_status_lookup(): void
    {
        [$client, $http] = $this->authenticatedClient([$this->envelope(['response' => ['status' => 'sent']])], ['credit_note_endpoint' => '', 'credit_note_status_endpoint' => '/v1/credit-notes/{id}']);

        $directory = sys_get_temp_dir() . '/lp-cn-dir-' . bin2hex(random_bytes(4));
        mkdir($directory);
        foreach (['/nonexistent.pdf', $directory] as $path) {
            try {
                $client->creditNotes()->send($path, []);
                self::fail('An unusable credit note path must be refused.');
            } catch (RuntimeException $e) {
                self::assertSame('Credit note document not found: ' . $path, $e->getMessage());
            }
        }
        rmdir($directory);
        try {
            $client->creditNotes()->send($this->tempFile('cn.pdf', '%PDF'), []);
            self::fail('The credit note endpoint is required.');
        } catch (RuntimeException $e) {
            self::assertSame('Missing LetsPeppol credit note endpoint configuration.', $e->getMessage());
        }

        $status = $client->creditNotes()->status('c/n');
        self::assertSame('https://api.lp.test/v1/credit-notes/c%2Fn', $http->requestLog[0]['url']);
        self::assertSame('c/n', $status['external_id']);

        [$unconfigured] = $this->authenticatedClient([], ['credit_note_status_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing LetsPeppol credit note status endpoint configuration.');
        $unconfigured->creditNotes()->status('x');
    }

    #[Test]
    public function it_looks_up_documents_participants_and_transmissions_by_encoded_id(): void
    {
        [$client, $http] = $this->authenticatedClient([
            $this->envelope(['response' => ['document' => ['id' => 'd']]]),
            $this->envelope(['response' => ['participant' => ['name' => 'ACME']]]),
            $this->envelope(['response' => ['transmission' => ['status' => 'delivered']]]),
        ]);

        $document     = $client->documents()->get('d/1');
        $participant  = $client->participants()->lookup('0088:123');
        $transmission = $client->transmissions()->status('t/1');

        self::assertSame('https://api.lp.test/v1/documents/d%2F1', $http->requestLog[0]['url']);
        self::assertSame(['id' => 'd'], $document['response']['entity']);
        self::assertSame('https://api.lp.test/v1/participants/0088%3A123', $http->requestLog[1]['url']);
        self::assertSame('ACME', $participant['response']['entity']['name']);
        self::assertSame('https://api.lp.test/v1/transmissions/t%2F1', $http->requestLog[2]['url']);
        self::assertSame('t/1', $transmission['external_id']);
        self::assertSame('delivered', $transmission['status']);
    }

    #[Test]
    #[DataProvider('lookupEndpointSettings')]
    public function it_requires_each_lookup_endpoint(string $setting, string $message, string $method): void
    {
        [$client] = $this->authenticatedClient([], [$setting => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        match ($method) {
            'document'     => $client->documents()->get('x'),
            'participant'  => $client->participants()->lookup('x'),
            'transmission' => $client->transmissions()->status('x'),
        };
    }

    #[Test]
    public function it_lists_documents_participants_and_transmissions_with_their_collection_keys(): void
    {
        [$client, $http] = $this->authenticatedClient([
            $this->envelope(['response' => ['documents' => [['id' => 'd']]]]),
            $this->envelope(['response' => ['items' => [['id' => 'p']]]]),
            $this->envelope(['response' => ['data' => [['id' => 't']]]]),
        ]);

        $documents     = $client->documents()->list(['page' => 2]);
        $participants  = $client->participants()->list(['q' => 'acme']);
        $transmissions = $client->transmissions()->list();

        self::assertSame('https://api.lp.test/v1/documents?page=2', $http->requestLog[0]['url']);
        self::assertSame([['id' => 'd']], $documents['response']['documents']);
        self::assertSame('https://api.lp.test/v1/participants?q=acme', $http->requestLog[1]['url']);
        self::assertSame([['id' => 'p']], $participants['response']['participants']);
        self::assertSame('https://api.lp.test/v1/transmissions', $http->requestLog[2]['url']);
        self::assertSame([['id' => 't']], $transmissions['response']['transmissions']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    /**
     * @param array<int, array<string, mixed>> $responses
     *
     * @return array{0: LetsPeppolClient, 1: ApiClientFake}
     */
    private function client(array $responses = []): array
    {
        $http = new ApiClientFake($responses);

        return [new LetsPeppolClient(new LetsPeppolApiClient($http)), $http];
    }

    /**
     * @param array<int, array<string, mixed>> $responses
     * @param array<string, mixed>             $overrides
     *
     * @return array{0: LetsPeppolClient, 1: ApiClientFake}
     */
    private function authenticatedClient(array $responses = [], array $overrides = []): array
    {
        [$client, $http] = $this->client($responses);
        $client->authenticate($this->settings($overrides));

        return [$client, $http];
    }

    private function api(?ApiClientFake $http = null): LetsPeppolApiClient
    {
        $api = new LetsPeppolApiClient($http ?? new ApiClientFake());
        $api->configure($this->settings());

        return $api;
    }

    /**
     * @param array<string, mixed> $response
     */
    private function failingHttp(array $response): ApiClientInterface
    {
        return new class ($response) implements ApiClientInterface {
            /** @param array<string, mixed> $response */
            public function __construct(private array $response) {}

            public function request(RequestMethod $method, string $url, array $options = []): array
            {
                return $this->response;
            }
        };
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function settings(array $overrides = []): array
    {
        return array_replace(LetsPeppolClient::defaultSettings(), [
            'client_id'     => 'cid',
            'client_secret' => 'sec',
            'token_url'     => 'https://auth.lp.test/token',
            'api_base_url'  => 'https://api.lp.test',
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function envelope(array $overrides = []): array
    {
        return array_merge([
            'success'     => true,
            'external_id' => null,
            'status'      => 'ok',
            'message'     => 'ok',
            'http_code'   => 200,
            'request'     => [],
            'response'    => [],
        ], $overrides);
    }

    private function tempFile(string $name, string $content): string
    {
        $dir = sys_get_temp_dir() . '/lp-hardening-' . bin2hex(random_bytes(4));
        mkdir($dir);
        $path = $dir . '/' . $name;
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return $path;
    }
}
