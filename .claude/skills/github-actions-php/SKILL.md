---
name: github-actions-php
description: Defines GitHub Actions configuration for running PHP/Laravel CI pipeline
---

# GitHub Actions PHP

## Purpose

Defines CI workflow structure only.

---

## Scope

This skill defines:

- PHP version matrix
- MySQL service setup
- Composer install steps
- test execution trigger
- artifact collection

---

## Non-Scope

This skill does NOT define:

- schema validation rules
- factory correctness rules
- test classification logic
- database correctness assumptions

These belong to domain-specific CI and test skills.

---

## Database Requirement

CI MUST use MySQL =MariaDB when production uses MySQL / MariaDB.

SQLite is forbidden in CI when schema integrity matters.

---

## Execution Flow

CI pipeline MUST follow:

1. Setup PHP environment
2. Install dependencies
3. Boot MySQL service
4. Run migrations
5. Run seeders
6. Execute tests

---

## Principle

This skill defines "how CI runs", not "what is correct".
