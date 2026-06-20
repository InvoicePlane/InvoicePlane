---
name: autonomous-coding-workflow
description: Governs safe, incremental, repository-wide development workflow with continuous validation gates
---

# Autonomous Coding Workflow

## Goal

Perform repository-wide modifications safely, incrementally, and with continuous validation.

---

## 1. Instruction Precedence

Before doing anything:

- Check for repository-level instruction files:
  - `.github/copilot-instructions.md`
  - `AGENTS.md`
  - `.junie/*.md`
  - `CLAUDE.md`

If they exist:
- Treat them as higher precedence for architecture and conventions.
- Avoid duplicating rules already defined there.

---

## 2. Preparation

Before modifying code:

1. Read existing implementation.
2. Understand current behavior.
3. Identify existing abstractions and reuse them:
   - Traits
   - Base test cases
   - Base resources
   - Base seeders
   - Services
   - DTOs
   - Transformers
4. Search explicitly for duplication before introducing new abstractions.
5. Preserve existing architectural patterns.

Do not modify code that has not been understood.

---

## 3. Refactoring Heuristics

Apply only when relevant:

- If repeated patterns exist across many test classes, models, or resources, evaluate abstraction opportunities.
- Prefer centralizing duplicated logic into:
  - Traits
  - Base classes
  - Services
- Do not introduce abstraction unless duplication is confirmed.

---

## 4. Incremental Development

Work in small, verifiable steps.

After each change:

1. Verify syntax:
   ```bash
   php -l
   ```
2. Run targeted tests.
3. Fix failures immediately.
4. Run code style checks:
   ```bash
   vendor/bin/pint --dirty --format agent
   ```
5. Continue only if repository is clean.

---

## 5. Validation Gates

Never proceed if any of the following fail:

- PHPUnit tests
- Static analysis
- PHP syntax check (php -l)
- Code style (Pint)

Before completion, additionally ensure:

- migrate:fresh --seed passes
- smoke tests pass
- targeted tests pass
- full suite passes (unless explicitly excluded)

---

## 6. Module Completion

After completing a module:

1. Run targeted test suite.
2. Run `php -l`.
3. Run Pint.
4. Confirm no unintended changes.
5. Commit with clear module description.

Do not start the next module until the current one is fully stable.

---

## 7. Uncertainty Handling

If behavior is unclear:

- Stop immediately.
- Describe ambiguity.
- Request clarification.
- Do not infer or guess missing business rules.

---

## 8. Success Criteria

The task is complete only when:

- Behavior is preserved.
- No duplicate logic introduced.
- Changes are idempotent.
- All tests pass.
- Full suite passes.
- Formatting is clean.
- No unintended architectural drift occurred.
