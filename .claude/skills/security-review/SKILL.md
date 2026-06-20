---
name: security-review
description: Static review rules for authorization, validation, and privilege escalation risks
---

# Security Review

## Purpose

Detect security risks in code during review phase.
This skill does NOT enforce security. It identifies issues.

---

## Scope

This skill evaluates:

- authorization checks (missing or bypassed)
- policy usage correctness
- privilege escalation risks
- unsafe controller or action exposure
- validation gaps on external input

---

## Ownership Boundary

Security Review does NOT:

- implement policies
- define roles/permissions
- execute middleware logic
- enforce runtime access control

Those belong to application security layers (Policies, Middleware, Gates).

---

## Rules

- Every sensitive action MUST have explicit authorization check
- No unguarded resource actions (create/update/delete/view)
- No direct access to privileged operations without policy validation
- Input from external sources MUST be validated before use

---

## Escalation Principle

If a potential security issue is detected:

- assume it is a defect until proven otherwise
- prioritize security over architectural convenience
