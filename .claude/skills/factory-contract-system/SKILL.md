---
name: factory-contract-system
description: Ensures factories generate valid model instances aligned with database schema constraints
---

# Factory Contract System

## Purpose

Ensures factories produce valid database-ready model instances.

---

## Scope

Factories MUST:

- satisfy all NOT NULL columns
- reflect migration constraints
- produce valid default state for persistence

---

## Ownership Boundary

Factories do NOT:

- enforce business rules
- define validation rules
- replace service-layer creation logic
- define seeder logic

---

## Schema Alignment Rule

If a migration introduces a NOT NULL column:

- factory MUST be updated immediately
- omission is considered invalid state

---

## Minimum Valid State

Each factory represents the smallest valid persisted entity.

Not random data.
Not business scenarios.
Only valid schema state.

---

## Service Alignment

Factories SHOULD align with service-layer expectations but do NOT depend on it.

Service layer = behavior
Factory = valid structure

---

## Seeder Rule

Seeders depend on factories.

Factories MUST NOT depend on seeders.
