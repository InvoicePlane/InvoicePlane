# Testing Conventions

## Rule — Test Method Naming

Test methods MUST follow the `it_{verb}_{object}` convention. The name must read
as a sentence describing observable behavior. Because PHPUnit does not pick up
methods that don't start with `test`, every `it_` method MUST carry the
`#[\PHPUnit\Framework\Attributes\Test]` attribute.

```php
// Correct
it_creates_an_invoice
it_rejects_a_duplicate_email
it_returns_404_for_missing_resource
it_assigns_company_id_to_new_invoices

// Wrong — noun before verb
it_invoice_creates

// Wrong — no verb
it_invoice
```

Never describe implementation. Describe what the system does from the outside.

---

## Rule — Arrange / Act / Assert

Every test method MUST be structured in three named phases, each preceded by its
own `/* Arrange */`, `/* Act */`, or `/* Assert */` comment. No exceptions.

```php
#[Test]
public function it_creates_an_invoice(): void
{
    /* Arrange */
    $client  = Relation::factory()->for($this->company)->create();
    $payload = ['customer_id' => $client->getKey(), 'invoice_date' => '2026-01-01'];

    /* Act */
    app(InvoiceService::class)->createInvoice($payload);

    /* Assert */
    $this->assertDatabaseHas('invoices', [
        'customer_id' => $client->getKey(),
        'company_id'  => $this->company->id,
    ]);
}
```

A test with no `/* Arrange */` / `/* Act */` / `/* Assert */` comments is rejected on
review, no matter how correct the assertions are.

If a phase is genuinely empty (e.g. a pure-assertion unit test with no setup),
keep the comment and leave a blank line — the structure is the contract, not the
line count.

---

## Rule — Test Honesty

A test is **honest** when the data it seeds in `/* Arrange */` is the same data
it asserts in `/* Assert */`. Seed a client named `'Acme Corp'`, then prove
`'Acme Corp'` appears in the response. Anything less is theatre.

```
Arrange → seed something specific
Act     → call the endpoint
Assert  → prove that specific thing is visible
```

If you cannot assert that the seeded data appears in the response, you are not
testing the endpoint — you are testing that it doesn't crash.

---

## Rule — No Shallow Assertions

A test that only checks a status code or the absence of PHP errors is **not a
test** — it is a false sense of coverage. The following patterns are banned:

**Banned: status-code-only smoke test**
```php
// Wrong — the logicalOr catches 200, 301, 302, 500 equally
$this->assertThat(
    $response->statusCode(),
    $this->logicalOr($this->equalTo(200), $this->equalTo(301))
);
```

**Banned: `assertResponseHasNoPhpErrors` as the sole assertion**
```php
// Wrong — this does not test the route, it tests that PHP didn't crash
$this->assertResponseHasNoPhpErrors($response);
```

**Banned: standalone `it_does_not_expose_php_errors` test method** — delete it.

### What to do instead

Seed real data in `/* Arrange */` and assert that data is observable:

```php
#[Test]
#[Group('smoke')]
public function it_lists_active_clients(): void
{
    /* Arrange */
    $clientId = $this->seedClient(['client_name' => 'Acme Corp']);

    /* Act */
    $response = $this->get('/clients/view/' . $clientId);

    /* Assert */
    $this->assertResponseStatusCode($response, 200);
    $this->assertResponseBodyContains($response, 'Acme Corp');
}
```

### When the list view does not render seeded rows

CI3 paginated list views (e.g. `/products`, `/tax_rates`, `/clients/status/active`)
use complex subqueries that do not return rows inserted via PDO in the test
harness. In that case:

1. Use `assertDatabaseHas` to prove the seed succeeded.
2. Use `assertResponseBodyContains($response, '<html')` to prove the page rendered.
3. Where a dedicated view endpoint exists (e.g. `/invoices/view/{id}`,
   `/quotes/view/{id}`), prefer it — the seeded data will appear in the response.

```php
/* Assert */
$this->assertResponseStatusCode($response, 200);
$this->assertDatabaseHas('ip_products', ['product_name' => 'Acme Widget']);
$this->assertResponseBodyContains($response, '<html');
```

Do NOT replace a seeded-data assertion with `assertResponseBodyContains($response, '<html')`
alone — `assertDatabaseHas` must accompany it.
