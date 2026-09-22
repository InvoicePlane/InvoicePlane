# Provenance of pre-existing commits on `claude/db-password-special-chars-6s3w4k` (InvoicePlane/InvoicePlane)

## Why this exists

While working the DB-password-special-characters fix (issue #1700) on the
branch `claude/db-password-special-chars-6s3w4k`, the `InvoicePlane/InvoicePlane`
copy of that branch turned out to already have 6 commits ahead of what this
session's local `develop` ref showed - none related to the password fix:

```
8a655c6 fix(session): treat empty SESS_SAVE_PATH as unset
1d47d88 tmp
97a67dd Update ipconfig.php.example
e726294 Extend authorization: secondary admins can only edit their own account
e96f633 Fix: Canonicalize user ID and restore email field updatability
0c45a97 Fix: Prevent secondary admin email takeover via primary admin account
```

Force-pushing over them, or silently bundling them into the #1700 PR, were
both wrong - this is what they actually are and where they came from.
`git fetch origin develop` resolved the immediate concern: `origin/develop`'s
tip was already `8a655c6`, i.e. all six commits were already merged into
`develop` through their own PRs before this branch was created from it. There
was no unmerged work at risk. What follows is the paper trail for each group,
reconstructed from commit metadata, `Claude-Session` footers, cross-session
lookups via the `claude-code-remote` MCP tool, and the GitHub PRs/issues those
sessions produced.

**Caveat**: cross-session lookup returns session *metadata* (title, origin,
model, branches, status, timing), not the verbatim prompt text of another
session's conversation. Nothing below claims to quote what was typed; it's
reconstructed with high confidence from that metadata plus the PRs, issues,
and CHANGELOG entries those sessions produced as their record of intent.

---

## Group 1 - Admin email takeover fix

**Commits:** `0c45a97`, `e96f633`, `e726294`
**Authored by:** `Claude <noreply@anthropic.com>`, each with
`Co-Authored-By: Claude Haiku 4.5` and
`Claude-Session: https://claude.ai/code/session_017eeagb2fMrBiwTu2Cq3Dp9`

### Session metadata (`session_017eeagb2fMrBiwTu2Cq3Dp9`)

| Field | Value |
|---|---|
| Title | "Primary admin email takeover via form" |
| Origin | `android` (mobile app) |
| Model | `claude-haiku-4-5-20251001` |
| Created | 2026-08-29T20:27:53Z |
| Updated | 2026-08-30T02:44:36Z |
| Sources | `InvoicePlane/InvoicePlane@develop`, `underdogg-forks/ivpl-exprmt@prep/v180` |
| Outcome branches | `claude/admin-email-takeover-fix-01kht4` on **both** repos |
| Final status | `review_ready`, "reversed sync direction; both branches now in sync" |

### What it produced

Merged into `develop` via **PR #1689**, "Fix: Prevent secondary admin email
takeover of primary admin account" (opened and merged 2026-08-29T20:51:10Z by
`nielsdrost7`). The PR description is the clearest record of intent:

> PR #1638 fixed direct password-change authorization (IDOR), but left the
> `user_email` field unprotected. A secondary admin (`user_type=1`,
> `user_id != 1`) could edit the primary admin's email through the user
> form, then use the public password-recovery flow to reset the password
> and gain full access to the primary admin account.

Fix: an authorization check in `Users::form()` blocking secondary admins
from editing `user_id=1`, plus `user_email` added to `Mdl_users::PROTECTED_FIELDS`
as defense-in-depth. `e96f633` is a same-day follow-up addressing review
feedback (canonicalize `$id` to int to close a leading-zero bypass; remove
`user_email` from `PROTECTED_FIELDS` again once the authorization gate made
the blanket field-block unnecessary and it was breaking legitimate email
edits). `e726294` extended the same authorization gate so *any* secondary
admin editing *any* other admin - not just user_id=1 - is blocked, closing
the general IDOR, not just the primary-admin special case.

### Where the original report came from

PR #1689 traces back to **PR #1638**, "Protect primary admin account in
`Users::change_password()` (CWE-639)" (merged 2026-07-18), which itself
came from a private vulnerability report (the PR's CHANGELOG entry shipped
with literal `TODO` placeholders for the GHSA link and reporter, later
credited in #1689's CHANGELOG entry as **@0xMoError-22**, responsible
disclosure). No public GitHub issue exists for this report - `search_issues`
for "0xMoError" returns zero results - consistent with it being a private
security advisory rather than a public bug report.

---

## Group 2 - `SESS_SAVE_PATH` regression fix

**Commits:** `97a67dd`, `1d47d88` ("tmp"), `8a655c6`
**Authored by:** `Niels Drost <nielsdrost7@gmail.com>` / `<...@users.noreply.github.com>`
directly - no `Claude-Session` footer on any of the three.

### Timeline

| Commit | UTC time (Sep 2) | What it did |
|---|---|---|
| `97a67dd` | 00:56 | Comments out the shipped `SESS_SAVE_PATH=` line in `ipconfig.php.example` |
| `1d47d88` "tmp" | 01:00 | Checkpoint commit: CHANGELOG entry, README + container-deployment doc updates, expanded `ipconfig.php.example` guidance - the exact prose later published in PR #1699's own CHANGELOG entry |
| *(session runs)* | 01:02-02:05 | `session_01NFBKftNhN1QcaKwCKZ1VH9`, see below |
| `8a655c6` | 01:28 | The actual code fix: guards `sess_save_path` in `application/config/config.php` |

`97a67dd` and `1d47d88` both predate or overlap the session's start
(01:02:28Z) and carry no session footer, so they read as the human directly
drafting the fix's documentation/changelog by hand - `1d47d88`'s message is
literally "tmp" - before or alongside invoking Claude for the code change
itself.

### Session metadata (`session_01NFBKftNhN1QcaKwCKZ1VH9`)

| Field | Value |
|---|---|
| Title | "save-path-170-regression" |
| Origin | `claude_code_cli` |
| Tags | `remote-control-auto` |
| Model | `claude-sonnet-5` |
| Created | 2026-09-02T01:02:28Z |
| Updated / archived | 2026-09-02T02:05:14Z |
| Source / outcome | `InvoicePlane/InvoicePlane@fix/extra` (both) |
| Final status | `review_ready`, "release readiness: develop risky (432 commits behind prep/v180); mailbox won't empty" |

The `remote-control-auto` tag indicates this session was fired by an
automated trigger/routine rather than started by a person typing a prompt
in the moment - consistent with `8a655c6` landing on `develop` directly
(authored as `nielsdrost7`, not `Claude`) rather than via a bot-authored PR
commit: per the merged PR's own description, *"The equivalent fix has also
been applied directly to `develop` as `8a655c64` ... since `develop` has
neither `bootstrap/` nor a test suite"* - i.e. the session's real work
happened on `fix/extra` (which has `bootstrap/session_path.php` and a
regression test suite) and a minimal, hand-applied equivalent was pushed
straight to `develop` in parallel.

### What it produced

Merged into `develop` via **PR #1699**, "fix(session): treat empty
SESS_SAVE_PATH as unset (+ harden docker-db-prepare)" (merged
2026-09-02T01:56:24Z by `nielsdrost7`). Root cause per the PR body:

> `ipconfig.php.example` shipped a bare `SESS_SAVE_PATH=` line. phpdotenv
> parses that as a **defined** variable whose value is `""`, not as unset.
> `env($key, $default)` branches on `isset($_ENV[$key])`, and
> `isset("") === true`, so the default is never applied ... CodeIgniter's
> `Session_files_driver` ... runs `ini_set('session.save_path', '')` ...
> Session open then does `mkdir('')` and fails. User-visible result: login
> silently fails, and a manual install gets stuck forever on
> `.../setup/language`.

This traces conceptually back to **PR #984** (2023), "own env option for
'sess_save_path'", which originally introduced `SESS_SAVE_PATH` as a
configurable env var, and references an earlier, broader speculative
session-path change that had been reverted on `develop-cleanup` /
`prep/develop-pre-172-beta-2` for changing more than this narrow fix does.

---

## Bottom line

Neither group is related to #1700 (DB password special characters). Both
are real, already-merged security/reliability fixes that happened to land
on `develop` in the days immediately before this task's branch was created
from it - which is exactly why `claude/db-password-special-chars-6s3w4k`
inherited them as its base history. Nothing was at risk of being lost; the
earlier concern was a stale local `develop` ref in this session, not
orphaned work upstream. The #1700 fix was cherry-picked as its own single
commit on top of that same tip and PR'd separately
(InvoicePlane/InvoicePlane#1701) so its diff stays scoped to the password
issue alone.
