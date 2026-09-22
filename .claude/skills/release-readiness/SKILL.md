---
name: release-readiness
description: >-
  The "can I ship this and go on vacation for six months" audit. Run before
  tagging a release. Produces a GO / GO-WITH-EYES-OPEN / NO-GO verdict plus the
  short list of things that will fill the maintainer's mailbox while they are
  away. Use it whenever someone asks "is it good enough to walk away from",
  "is this release safe", or is about to cut a tag on prep/v180.
---

# release-readiness

## Why this exists

A green test suite is not the same question as "is this safe to leave alone for
six months". #1694 shipped with 700 passing tests. The suite answers *"do the
things we tested still work"*; a release-readiness audit answers *"what have we
not tested, what is fragile, and what breaks silently"* — the things that
generate support mail.

This skill is the checklist for that second question, plus an `audit.php` that
mechanically checks the parts a script can check.

## How to run it

```
php .claude/skills/release-readiness/audit.php
```

Exit `0` = GO, exit `1` = GO-WITH-EYES-OPEN (ship, but the report lists what to
watch), exit `2` = NO-GO (a release-blocking problem). Add `--json` for a
machine-readable report.

The script is necessary but not sufficient. After it passes, still walk the
**Manual review** section below — those items need a human.

## What the script checks

1. **Suite is actually green, not masked-green.** Runs the full PHPUnit suite
   through the same clean-env invocation CI uses. Fails the audit on any
   failure/error, on `> 25` skips (the masked-DB profile is ~200 skips), and on
   any risky test.
2. **config-parity-guard passes.** Delegates to
   `.claude/skills/config-parity-guard/audit.php`. A gap there is a NO-GO —
   that is the #1694 class.
3. **One ControllerTest per controller.** Every
   `application/modules/*/controllers/*.php` (minus the known non-HTTP ones)
   has a matching `tests/Feature/<Module>/<Name>ControllerTest.php` or
   `<Name>AjaxControllerTest.php`. A controller with no test file is a
   NO-GO — it is an un-audited surface.
4. **Every guarded mutating action is named in a test.** For each controller
   method behind `ensure_valid_post_request()` / `verify_csrf_token()`, its
   route string appears in the matching test file. Missing => eyes-open.
5. **No environmental `markTestSkipped()`.** Same rule as config-parity-guard:
   a skip whose reason mentions the DB / an extension / `env()` is coverage
   rot unless tagged `PARITY-OK:`.
6. **Security helpers carry no `TODO` / `FIXME` / `XXX` / `@phpstan-ignore`.**
   Unfinished business in `application/helpers/*security*`,
   `application/helpers/file_security_helper.php` or `core/MY_Security*` is a
   NO-GO.
7. **`ipconfig.php.example` production defaults are safe.** `CSRF_PROTECTION`,
   `COOKIE_HTTPONLY` must not ship commented-out or `false`; `SETUP_COMPLETED`
   default must be `false`; `REMOVE_INDEXPHP` guidance present.
8. **No debug hatch left open.** No `var_dump(` / `print_r(` / `dd(` /
   `error_reporting(E_ALL` / `display_errors', 1` / `xdebug_break(` outside
   `tests/` and `vendor/`.
9. **PHPStan baseline did not grow.** `phpstan-baseline.neon` entry count is
   compared to the value recorded in `audit.php` (`BASELINE_MAX`). A larger
   baseline means new debt was suppressed rather than fixed.

## Manual review (a human, every release)

These cannot be scripted and matter most for a long unattended window:

- **Migrations.** Does `application/modules/setup/sql/` + `Mdl_setup::upgrade_*`
  apply cleanly on top of the *previous* released schema? A broken upgrade path
  is the single biggest mailbox filler. Test an actual upgrade from the last
  tag, not a fresh install.
- **Money math.** Spot-check invoice/quote totals, tax, discounts, rounding and
  currency formatting against a hand calculation for at least one non-trivial
  case. `beStrictAboutOutputDuringTests` will not catch a wrong number.
- **Email + PDF.** Send a real invoice email through a real SMTP server and open
  the PDF. The Feature suite asserts a `%PDF-` header, not that the document is
  correct or that the mail leaves the building.
- **The AJAX invoice/quote editor** end to end in a browser with
  `CSRF_PROTECTION=true`. Known latent issue:
  `Base_Controller::json_encode_ajax()` re-calls `csrf_verify()` after
  CodeIgniter already consumed the token, so any admin who also sets
  `CSRF_REGENERATE=true` gets 403s on every AJAX write. Ship-blocking only if
  regenerate is going to be recommended.
- **Third-party gateways.** PayPal / Stripe callbacks against the providers'
  sandboxes. Nothing in CI touches a real gateway.
- **Backups.** Confirm the deployment has an automated DB backup the maintainer
  can restore from a phone. Six months is long enough for a disk to die.
- **Dependency CVEs.** `composer audit` (or the GitHub advisory tab) for
  anything in `composer.lock` with a known advisory and no patch window.

## The verdict wording

Report one of:

- **GO** — script clean, manual review clean. Safe to leave.
- **GO, EYES OPEN** — script clean or only eyes-open items, but named residual
  risks the maintainer accepts. List them explicitly; do not bury them.
- **NO-GO** — a release blocker. Name it, fix it, re-run.

Never soften a NO-GO into an eyes-open. The whole point of the skill is that the
person on vacation was told the truth before they left.
