---
name: dto-contract
description: Defines DTO structure, lifecycle, and transformation rules across the application
license: MIT
metadata:
  author: project
---

# DTO Contracts

DTOs define structured, transport-safe data contracts used between layers of the application.

They exist to replace unstructured arrays when data shape matters, is reused, or must remain consistent across boundaries.

---

# 1. Responsibility

DTOs MUST:

- represent structured application data
- act as transport carriers between layers
- be filled by Transformers
- avoid business logic
- avoid persistence logic

DTOs MUST NOT:

- contain ORM logic
- contain validation rules
- contain side effects
- depend on framework components (Filament, Request, etc.)

---

# 2. Structure

DTOs are simple POPOs with fluent getters and setters.

Example:

```php
class InvoiceDto
{
    private int $invoiceId;
    private int $companyId;
    private float $amount;

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
}
```

---

# 3. Creation Rule

DTOs MUST be created via Transformers.

```php
$dto = InvoiceTransformer::fromModel($invoice);
```

or

```php
$dto = InvoiceTransformer::fromArray($data);
```

DTOs MUST NOT be manually assembled inside services unless trivial and explicitly justified.

---

# 4. Transformer Dependency Rule

Transformers are the ONLY layer allowed to construct DTOs.

DTOs MUST NOT depend on Transformers.

Direction is strictly:

```
Model / Array → Transformer → DTO → Service
```

---

# 5. When DTOs are Required

Use DTOs when:

- data is shared across multiple services
- structure must remain stable across changes
- transformation logic exists (model → structured output)
- array shape would otherwise be ambiguous or inconsistent

---

# 6. When DTOs are NOT Required

DTOs MAY be skipped when:

- data is short-lived within a single method
- input comes from trusted UI layer (Filament forms)
- structure is trivial and not reused elsewhere

---

# 7. Core Principle

DTOs are **explicit data contracts**, not business logic containers.

## IDE Hints (Optional)

DTOs MAY include region markers to improve IDE navigation (e.g. PhpStorm folding).

These are purely cosmetic and MUST NOT affect runtime behavior or architecture decisions.

Example:

```php
class InvoiceDto
{
    #region Properties
    private int $invoiceId;
    private int $companyId;
    private float $amount;
    #endregion

    #region Getters
    public function getInvoiceId(): int { ... }
    public function getCompanyId(): int { ... }
    public function getAmount(): float { ... }
    #endregion

    #region Setters
    public function setInvoiceId(int $invoiceId): self { ... }
    public function setCompanyId(int $companyId): self { ... }
    public function setAmount(float $amount): self { ... }
    #endregion
}
```

They exist to stabilize data shape across the system, not to introduce unnecessary abstraction.
