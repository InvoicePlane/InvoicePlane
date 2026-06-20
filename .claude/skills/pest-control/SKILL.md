---
name: pest-control
description: >
  Enforces PHPUnit-only testing in this project. Activates when writing tests, reviewing test
  files, or when any Pest syntax appears (it(), test(), describe(), uses(), expect() chains,
  beforeEach/afterEach hooks). Scans for and eliminates all Pest references from code,
  config, and documentation.
license: MIT
metadata:
  author: project
---

# Pest Control

## Rule 0 — Hard Stop

**Pest is NOT installed in this project and must never be used.**

This project uses **PHPUnit 12+** exclusively.

Never write, suggest, or accept:
- `it('description', fn () => ...)`
- `test('description', fn () => ...)`
- `describe('group', fn () => ...)`
- `uses(SomeClass::class)`
- `expect($value)->toBe(...)`
- `beforeEach(fn () => ...)`
- `afterEach(fn () => ...)`
- `pest()` configuration

---

## Rule 1 — Correct Test Class Pattern

Every test MUST be a class extending one of the three base classes:

```php
// Company panel tests
class FooTest extends AbstractCompanyPanelTestCase
{
    #[Test]
    public function it_does_something(): void
    {
        // ...
    }
}

// Admin panel tests
class BarTest extends AbstractAdminPanelTestCase
{
    #[Test]
    public function it_does_something(): void
    {
        // ...
    }
}

// Pure unit tests (no DB, no framework boot)
class BazTest extends AbstractTestCase
{
    #[Test]
    public function it_does_something(): void
    {
        // ...
    }
}
```

Base class locations: `Modules/Core/Tests/`

---

## Rule 2 — Attribute Syntax

Use PHP 8.1+ attributes for test metadata:

```php
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;

#[Test]
public function it_creates_an_invoice(): void {}

#[Test]
#[DataProvider('invoiceDataProvider')]
public function it_validates_invoice_fields(array $data, string $error): void {}
```

Never use `/** @test */` docblock annotations — use `#[Test]` attributes.

---

## Rule 3 — Assertion Style

Use PHPUnit assertions, not Pest chains:

```php
// Correct
$this->assertSame('expected', $actual);
$this->assertDatabaseHas('invoices', ['status' => 'paid']);
$this->assertCount(3, $results);

// Wrong — Pest chain
expect($actual)->toBe('expected');
expect($results)->toHaveCount(3);
```

---

## Rule 4 — Livewire Testing

Filament/Livewire tests use the Livewire facade directly:

```php
use Livewire\Livewire;

Livewire::actingAs($this->user)
    ->test(ListInvoices::class, ['tenant' => 'ivplv2'])
    ->assertSuccessful();
```

Or the base class helper:
```php
$this->testLivewire(ListInvoices::class)->assertSuccessful();
```

---

## Rule 5 — File Placement

```
Modules/<Name>/Tests/Unit/    ← AbstractTestCase, no DB
Modules/<Name>/Tests/Feature/ ← AbstractCompanyPanelTestCase or AbstractAdminPanelTestCase
```

PHPUnit discovers tests via `phpunit.xml`:
```xml
<testsuite name="Unit">   <directory>Modules/*/Tests/Unit</directory>   </testsuite>
<testsuite name="Feature"><directory>Modules/*/Tests/Feature</directory></testsuite>
```

---

## Rule 6 — Pest Elimination Checklist

When asked to eliminate Pest from a codebase, check and fix all of the following:

### composer.json
- [ ] Remove `"pestphp/pest-plugin": true` from `config.allow-plugins`
- [ ] Remove any `pestphp/pest*` entries from `require-dev`

### Test files
- [ ] Convert `it('...', fn () => ...)` → class method with `#[Test]` attribute
- [ ] Convert `test('...', fn () => ...)` → class method with `#[Test]` attribute
- [ ] Remove all `uses(...)` declarations
- [ ] Replace `expect(...)->toBe(...)` chains with `$this->assertSame(...)`
- [ ] Replace `beforeEach` → `setUp()`, `afterEach` → `tearDown()`
- [ ] Remove `describe()` wrappers; flatten into separate methods or classes

### Config / tooling
- [ ] Delete `pest.php` or `tests/Pest.php` if present
- [ ] Remove any `--pest` flag from CI workflow commands
- [ ] Update `.gitignore` comments: `# PHPUnit / Pest` → `# PHPUnit`
- [ ] Update Makefile comments that mention Pest

### Documentation
- [ ] Update `CLAUDE.md` testing section to state PHPUnit-only
- [ ] Update any README or CONTRIBUTING docs that mention Pest

---

## Rule 7 — Test Method Naming

Test methods MUST follow the `it_{verb}_{object}` convention. The name must read
as a sentence describing observable behavior.

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

## Rule 8 — Arrange / Act / Assert

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

## Rule 8 — Conversion Reference

| Pest | PHPUnit equivalent |
|------|--------------------|
| `it('desc', fn() => ...)` | `#[Test] public function it_desc(): void` |
| `test('desc', fn() => ...)` | `#[Test] public function test_desc(): void` |
| `expect($x)->toBe($y)` | `$this->assertSame($y, $x)` |
| `expect($x)->toEqual($y)` | `$this->assertEquals($y, $x)` |
| `expect($x)->toBeTrue()` | `$this->assertTrue($x)` |
| `expect($x)->toBeFalse()` | `$this->assertFalse($x)` |
| `expect($x)->toBeNull()` | `$this->assertNull($x)` |
| `expect($x)->toBeEmpty()` | `$this->assertEmpty($x)` |
| `expect($x)->toHaveCount(n)` | `$this->assertCount(n, $x)` |
| `expect($x)->toContain($y)` | `$this->assertContains($y, $x)` |
| `expect($x)->toMatchArray([...])` | `$this->assertEquals([...], $x)` |
| `expect($x)->toBeInstanceOf(Cls::class)` | `$this->assertInstanceOf(Cls::class, $x)` |
| `beforeEach(fn() => ...)` | `protected function setUp(): void` |
| `afterEach(fn() => ...)` | `protected function tearDown(): void` |
| `uses(RefreshDatabase::class)` | `use RefreshDatabase;` inside the class |
| `dataset(...)` | `public static function provider(): array` + `#[DataProvider('provider')]` |
