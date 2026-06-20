---
name: test-honesty
description: Ensures factory, seeder, and schema alignment with production database reality
---

# Purpose

Prevents schema drift between migrations, factories, and seeders.

---

# 1. Schema Contract

Every NOT NULL column defined in migrations must be supported by:

- factory definition
- or seeder definition (only for seed data)
- or explicit DB default in migration

This is a **schema-only rule**, not a validation rule.

---

# 2. Factory Rule

Factories MUST produce valid database rows for the schema.

Factories are schema-aligned, not business-logic aware.

---

# 3. Seeder Rule

Seeders MUST only insert schema-valid data.

No reliance on implicit database defaults.

---

# 4. Database Parity Rule

MySQL / MariaDB is the canonical database.

SQLite differences are invalid for schema validation assumptions.

---

# 5. Drift Triggers

The following indicate schema drift:

- migration changes
- factory mismatch
- seeder mismatch
- SQLSTATE constraint violations
- CI vs local DB mismatch

---

# 6. Identity Rule

Primary keys are non-deterministic.

Tests MUST NOT rely on hardcoded IDs.

---

# 7. Execution Rule (CI boundary)

Schema validation requires:

- migrate:fresh
- seed

before running test suites.

This ensures schema correctness before test execution.
