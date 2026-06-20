---
name: filament-resource-testing
description: Defines how Filament UI resources are tested using Livewire
license: MIT
metadata:
  author: project
---

# Filament Resource Testing

## Purpose

This skill defines **UI-level testing patterns for Filament resources only**.

It validates:
- Create/Edit/List pages
- form interaction
- Livewire-based UI flows
- user-visible behavior

It does NOT define:
- factories
- tenancy rules
- database integrity rules
- security rules
- primary key rules

These are owned by other skills.

---

# 1. Scope Rule

This skill ONLY covers:

- Filament Pages
- Filament Actions
- Livewire interactions
- UI assertions

---

# 2. Test Structure Rule

Each test MUST validate one UI behavior:

- listing records
- creating records
- editing records
- deleting records
- validation errors

No multi-behavior tests allowed.

---

# 3. Livewire Execution Rule

All Filament tests MUST use Livewire:

```php
Livewire::actingAs($this->user)
    ->test(CreateInvoice::class)
```

No direct HTTP testing of Filament pages.

---

# 4. Form Interaction Rule

Form input MUST use:

```php
->set('data.field', value)
```

Not:
- fillForm
- request payload simulation
- raw HTTP input

---

# 5. Assertion Rule

Tests MUST assert business outcome:

- database state change
- UI state change
- form validation error state

NOT framework internals.

---

# 6. Delete Action Rule

Delete actions are tested as UI actions only:

```php
->callAction(DeleteAction::class)
```

Outcome MUST be verified via database assertion.

---

# 7. Multi-tenancy Note

Tenant behavior is NOT owned by this skill.

If multi-tenancy is present:
- it is assumed to be already configured
- this skill only validates UI behavior within active tenant context

---

# 8. Core Principle

Filament resource tests verify **what the user sees and does**, not how the system enforces rules internally.
