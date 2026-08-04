# Senior Code Review Audit

Target branch: `prep/v180`

The custom `senior-laravel-code-reviewer` skill was not installed in this Codex session, so this report records the equivalent manual review. InvoicePlane is still CodeIgniter 3, but this review uses Laravel-style expectations for clarity: thin controllers, explicit validation, predictable test fixtures, safe redirects, encoded output, and no hidden global-state surprises.

## Checks Run

- PHP syntax lint over `application/**/*.php`, including files with spaces in their names.
- Conflict-marker scan over `application`, `tests`, Composer files, PHPUnit/PHPStan config, and GitHub workflow files.
- Pattern scan for direct superglobals, raw `HTTP_REFERER`, direct `header()` calls, `exit()`, `directory_map()`, filesystem scans, debugging leftovers, and TODO/HACK comments.
- Test-honesty spot check for feature tests that only assert status codes.

## Good Signals

- No PHP syntax errors were found.
- No merge conflict markers were found in source/config/test files.
- The security-sensitive template allowlist work is present in tests and helpers; no custom template whitelist is built by scanning `application/views`.
- Most newer CRUD-style tests seed their own fixtures and assert response content and database state.
- Download responses generally sanitize filenames before `Content-Disposition`.

## Findings

### Medium: Direct `HTTP_REFERER` Parsing Is Still Duplicated

`application/modules/filter/controllers/Ajax.php` parses `$_SERVER['HTTP_REFERER']` directly in several filter methods to infer context. The code validates path basenames before use, so this is not the same risk as an open redirect, but the pattern is still fragile and duplicates security-sensitive referer handling.

Preferred direction:

- Extract a helper such as `safe_referer_path_basename()` or use a shared referer parser.
- Keep redirect URL validation in `get_safe_referer()`.
- Add focused tests for malicious referers, empty referers, query strings, and path traversal-like basenames.

### Medium: Controller Download Responses Still Use Raw `header()` / `exit()`

Several controllers still emit downloads or JSON with direct `header()` and `exit()` calls. This is normal legacy CI3 style, but it is hard to test and easy to bypass shared response hardening.

Preferred direction:

- Move repeated download response construction into a small helper/service.
- Keep filename sanitization mandatory.
- Add tests for content type, content disposition, missing file behavior, and path validation.

### Low: Old TODO/Hack Comments Are Concentrated In Legacy Areas

`Sumex.php` and some controller paths still have old TODO/Hack comments. These are not automatically bugs, but they make it harder to distinguish accepted legacy debt from forgotten release blockers.

Preferred direction:

- Either convert important TODOs into issues/tests or remove comments that no longer guide work.
- Do not churn these files during unrelated feature work.

### Low: Smoke Tests Remain In Core

Several Core tests still mostly prove that a page returns 200 and contains generic HTML. This is acceptable as boot coverage, but it should not be mistaken for realistic business coverage.

Preferred direction:

- When touching a Core controller, add one module-specific assertion: seeded row visible, validation error visible, setting persisted, setting not persisted, or expected redirect.

## Branch Boundary Notes

`prep/v180` should stay focused on `develop` plus PHPUnit/PHPStan readiness. The e-invoicing/services look-ahead work currently belongs in integration branches. Findings related to those contributor branches should be fixed in fork integration branches unless they are test-infrastructure-only changes appropriate for `prep/v180`.

## Suggested Follow-Up PRs

1. Add a small referer-basename helper and tests for filter ajax context parsing.
2. Add a reusable download-response helper and migrate one controller as a pattern.
3. Add targeted Quotes coverage to close the largest non-Core behavior gap.
4. Add Services coverage only on the integration branch where Services exists.
