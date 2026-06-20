---
name: safe-refactoring-rules
description: Ensures all refactoring is deterministic, behavior-preserving, and non-breaking
---

# Safe Refactoring Rules

## Purpose

Ensure all refactoring is deterministic, non-breaking, and behavior-preserving.

This skill enforces *how changes are made*, not *how the system is structured*.

---

# 1. Behavior Preservation

- Never change runtime behavior unless explicitly instructed.
- Any refactoring must preserve observable outputs.
- Moving code between layers must not alter execution results.

---

# 2. Existing Code Respect

- Never overwrite an existing method if it already satisfies part of the requirement.
- Extend existing implementations instead of replacing them.
- Do not delete or rewrite working logic unless required for a fix.

---

# 3. Dependency Integrity

- Always preserve constructor injection.
- Never replace dependency injection with service locators (`app()`, `resolve()`).
- Do not introduce new dependencies when existing ones suffice.
- Do not change dependency graphs without explicit intent.

---

# 4. Public API Stability

- Never change public method signatures unless all call sites are updated in the same change.
- Avoid breaking changes at all costs.
- Prefer internal adaptation over external contract modification.

---

# 5. Idempotency Requirement

- Refactoring must be idempotent.
- Running the same change twice must produce no further diff.
- No duplicate logic, imports, traits, or methods may be introduced.

---

# 6. Uncertainty Handling

If any of the following is unclear:

- intended behavior
- service contract
- domain rule
- expected output

Then:

- Stop immediately
- Do not guess
- Report ambiguity explicitly
- Request clarification

---

# 7. Abstraction Reuse Rule (Local Scope Only)

This skill only enforces reuse during refactoring operations.

Global abstraction policy is defined in application-architecture-standard.

Before introducing:

- Trait
- Service
- DTO
- Transformer
- Base class

Search for an existing implementation.

Reuse existing abstractions whenever practical.

Duplicate abstractions are architectural defects.

---

# 8. Scope Discipline

This skill does NOT define:

- architecture layering (handled by application-architecture-standard)
- testing strategy (handled by test-honesty / filament-resource-testing)
- security rules (handled separately if present)

It ONLY defines safe transformation rules.

---

# 9. Enforcement Priority

If this skill conflicts with others:

1. application-architecture-standard
2. domain-specific skills
3. execution workflows
4. this skill (always subordinate to architecture)
