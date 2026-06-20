# API Client Conventions

## Rule — Use `RequestMethod` enum on `request()`, no named HTTP wrappers

All HTTP calls from endpoint classes MUST go through the single public
`request(RequestMethod $method, ...)` method on the API client. Named wrapper
methods such as `get()`, `post()`, or `postMultipart()` are **forbidden** — they
duplicate the enum's job and hide intent behind an extra layer.

```php
// Correct
$this->client->request(RequestMethod::GET, $url);
$this->client->request(RequestMethod::GET, $url, query: $filters);
$this->client->request(RequestMethod::POST, $url, $payload);
$this->client->request(RequestMethod::POST, $url, $payload, multipart: true);

// Wrong — named wrappers
$this->client->get($url, $filters);
$this->client->post($url, $payload);
$this->client->postMultipart($url, $payload);
```

### Signature

```php
public function request(
    RequestMethod $method,
    string $url,
    array $payload = [],
    bool $multipart = false,
    array $query = []
): array
```

- `$query` — appended as a URL query string (GET filters, pagination, etc.)
- `$multipart` — set `true` for file uploads; use named argument at the call site

### Where the enum lives

`application/modules/einvoice/libraries/RequestMethod.php`

```php
enum RequestMethod: string
{
    case GET  = 'GET';
    case POST = 'POST';
}
```

Add cases here (e.g. `PUT`, `DELETE`, `PATCH`) only when a provider actually needs them.

### Fake override

Test fakes override the **protected** `send()` method (the actual curl layer),
not `request()`. This lets the `query` → URL-append logic in `request()` run
for real during tests, so query-string assertions work without any extra wiring.

```php
protected function send(
    RequestMethod $method,
    string $url,
    array $payload = [],
    bool $multipart = false
): array {
    $this->requestLog[] = compact('method', 'url', 'payload', 'multipart');
    // ...
}
```

Test assertions check the enum value directly:

```php
$this->assertSame(RequestMethod::GET,  $client->requestLog[0]['method']);
$this->assertSame(RequestMethod::POST, $client->requestLog[0]['method']);
```
