---
name: senior-laravel-developer-phpunit-interpreter
description: Cleans and interprets raw PHPUnit CI logs into a compact, AI-friendly failure report. Use this skill whenever the user pastes or uploads a PHPUnit log, GitHub Actions test output, CI test results, or asks to interpret/summarize/analyze failing tests. Trigger even if the user says things like "here's my test output", "tests are failing", "can you look at my PHPUnit log", or pastes a block of text that contains PHPUnit output. Always use this skill before attempting to diagnose failures.
---

# Senior Laravel Developer — PHPUnit Log Interpreter

Process the **entire** attached PHPUnit log from beginning to end without truncation, stopping early, or summarizing.

Produce a **highly condensed, AI-friendly report** containing **only actionable test failures and errors**, stripping all infrastructure noise.

---

## General Cleanup

Remove completely:

- All timestamps (e.g. `2026-05-14T03:17:38.4840913Z`)
- All ANSI escape sequences and terminal color codes
- GitHub Actions workflow metadata and runner output
- Docker / container startup logs
- Composer commands, dependency installation, download, extraction, and installation output
- Laravel migration, seeding, optimize, cache, bootstrap, and environment setup output
- CI/CD infrastructure noise and progress bars
- All successful tests beginning with `✔`
- Any output unrelated to PHPUnit failures, warnings, deprecations, risky tests, notices, or errors

---

## Path Cleanup

Remove the absolute project root path prefix from all file paths so only the
relative path remains (e.g. strip `/home/runner/work/<project>/` or
`/var/www/<project>/` — whatever the CI runner's working directory is).

---

## Stack Trace Processing

Unless explicitly requested:

- Remove **all stack traces completely** — every `#0`, `#1`, `#2`, etc.
- Remove all vendor frames, internal frames, and repeated exception rendering

Keep only:

- Test name
- Exception type
- Exception message
- Assertion message
- `Caused by` exception (if present)
- `Previous exception` (if present)

---

## Failure Formats

**Error** — preserve:
```
Modules\...\Tests\Feature\SomeTest::it_does_something

ExceptionClass:
Exception message here.
```

**Failure** — preserve:
```
Modules\...\Tests\Feature\SomeTest::it_does_something

Expected response status code [200] but received 500.

Failed asserting that 500 is identical to 200.

UnderlyingException:
Underlying message if present.
```

---

## Duplicate Removal

Keep only the first occurrence of:

- Duplicate exception blocks
- Repeated stack traces
- Repeated "The following exception occurred..."
- Repeated rendering output

---

## Formatting Rules

- Collapse multiple blank lines into a single blank line
- Do **not** reorder, sort, renumber, or group failures — preserve exact PHPUnit order

---

## Output Structure

Return the cleaned log as a single Markdown code block:

````markdown
```text
PHPUnit 11.x by Sebastian Bergmann and contributors.

Runtime:       PHP x.x.x
Configuration: phpunit.xml

<progress dots>

Time: xx:xx.xxx, Memory: xx MB

There were X errors:

1) FullTestClassName::method_name

ExceptionClass:
Message.

2) ...

There were X failures:

1) FullTestClassName::method_name

Assertion message.

Failed asserting that ...

UnderlyingException:
Message.

2) ...

Tests: N
Assertions: N
Errors: N
Failures: N
Warnings: N (omit if 0)
Skipped: N (omit if 0)
Incomplete: N (omit if 0)
Risky: N (omit if 0)
Deprecations: N (omit if 0)
```
````

Include Warnings, Deprecations, and Risky sections only if present.

---

## Conditional Output Rule

If the suite has zero errors, failures, warnings, risky tests, and deprecations, output only:

```text
PHPUnit completed successfully.

Tests: <count>
Assertions: <count>

No errors, failures, warnings, risky tests, or deprecations detected.
```

---

## Objective

Minimize token usage while preserving **100% of the information required to diagnose failing tests**. Output must be stable, deterministic, compact, and optimized for consumption by both humans and AI systems.

---

## Root Cause Analysis

When multiple tests fail with the same underlying exception,
identify the earliest failure that explains subsequent failures.

Do not propose independent fixes for cascading failures.

Prefer fixing one root cause over many symptoms.

---

## Architectural Diagnosis

When a failure indicates a missing architectural pattern
(e.g. missing service, missing transaction, missing CoversClass,
missing failure-path tests, missing factory field),

recommend applying the fix repository-wide rather than only to the failing test.
