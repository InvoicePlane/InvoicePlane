# PHPUnit Test Organization

InvoicePlane is still a CodeIgniter 3 application, but tests should be grouped by the future InvoicePlane v2 product modules instead of mirroring every legacy `application/modules` folder.

## Module Map

| Legacy CI3 module | Test module |
| --- | --- |
| `clients` | `Clients` |
| `guest` | `Clients` |
| `user_clients` | `Clients` |
| `custom_fields` | `Core` |
| `custom_values` | `Core` |
| `dashboard` | `Core` |
| `email_templates` | `Core` |
| `reports` | `Core` |
| `settings` | `Core` |
| `users` | `Core` |
| `import` | `Core` |
| `sessions` | `Core` |
| `layout` | `Core` |
| `mailer` | `Core` |
| `upload` | `Core` |
| `invoices` | `Invoices` |
| `invoice_groups` | `Invoices` |
| `quotes` | `Quotes` |
| `payments` | `Payments` |
| `payment_methods` | `Payments` |
| `products` | `Products` |
| `families` | `Products` |
| `units` | `Products` |
| `projects` | `Projects` |
| `tasks` | `Projects` |

Upcoming modules that live on integration/contributor branches should be first-class test modules when present:

| Module | Test module |
| --- | --- |
| `integrations` | `Integrations` |
| `services` | `Services` |

## Directory Rules

Feature tests that boot CI3 routes belong under:

```text
tests/Feature/<Module>/
```

Unit tests that exercise a class or pure behavior without the HTTP harness belong under:

```text
tests/Unit/<Module>/
```

Security regression tests belong under:

```text
tests/Feature/Security/
tests/Unit/Security/
```

Use `tests/Support` and `tests/Concerns` for shared fixtures and helpers. Keep the baseline database seed minimal; individual tests should seed the records they prove.

## Group Attributes

Add module groups to new or touched tests:

```php
#[Group('invoices')]
#[Group('security')]
#[Group('smoke')]
```

This makes shallow loops possible:

```bash
vendor/bin/phpunit --group invoices
vendor/bin/phpunit --group security
```

## Test Honesty

A test should usually assert more than a successful response. Prefer at least one domain assertion:

- a seeded value appears in the response
- a database row is created, changed, deleted, or preserved
- invalid input is rejected
- unsafe input is not persisted
- a fake client receives the expected payload
