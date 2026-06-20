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
