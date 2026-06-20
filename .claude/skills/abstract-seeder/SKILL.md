---
name: abstract-seeder
description: Provides structured seeding workflow for module data initialization
---

# Abstract Seeder

## Purpose

Provides a structured way to seed database data per module.

---

## Scope

Seeders are responsible for:

- creating initial dataset for a company
- using factories to generate valid records
- orchestrating dependency order between models

---

## Ownership Boundary

Seeders MUST NOT:

- define validation rules
- define factory structure
- enforce schema constraints
- contain business logic

---

## Factory Dependency Rule

Seeders MUST rely on factories for object creation.

Factories are the source of truth for valid model state.

---

## Dependency Resolution

Seeders MAY resolve dependencies using helper methods:

- findOrCreateClient
- findOrCreateProject
- findOrCreateUser

These helpers are convenience utilities, not business logic.

---

## Execution Hooks

- beforeSeed(): setup state
- afterSeed(): cleanup or summary

---

## Principle

Seeders assemble data. They do not define data correctness.
