---
name: service-layer
description: Defines application service structure and business orchestration boundaries
license: MIT
metadata:
  author: project
---

# Service Layer

Services are the only place business logic lives. They are framework-agnostic.

---

# 1. Responsibility

Services MUST:

- contain business logic
- coordinate models
- enforce domain rules
- return models or DTOs

Services MUST NOT:

- import or use Filament
- accept or return HTTP request/response objects
- contain UI logic
- use `app()` or `resolve()` internally

---

# 2. Dependency Rule

Constructor injection only:

```php
public function __construct(
    private InvoiceRepository $repository
) {}
```

---

# 3. DTO Rule

DTOs are **not** required for Filament → Service calls. Arrays are fine when the
source is a trusted Filament form.

Use DTOs when:
- crossing system boundaries (API, queues, external integrations)
- multiple services share a contract
- the payload must be stable across refactors

Skip DTOs when:
- input comes from a single Filament form
- the data is short-lived and not reused

---

# 4. Filament Action Exception

Filament closures do not support constructor DI. `app()` is the only acceptable
escape hatch — and it belongs in the closure, not inside the service:

```php
Action::make('create')
    ->action(function (array $data) {
        app(InvoiceService::class)->createInvoice($data);
    });
```

---

# 5. Standard Shape

```
Modules/{Name}/Services/{Model}Service.php
```

Standard method names: `createX`, `updateX`, `deleteX`, `findOrFail`, `listForCompany`.
