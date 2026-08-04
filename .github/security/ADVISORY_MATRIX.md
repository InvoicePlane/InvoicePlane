# Security Advisory Matrix

Source: GitHub Security Advisories for `InvoicePlane/InvoicePlane`, scanned on 2026-08-04.

This is the release-control matrix for the v1.7.2 security candidate. It is intentionally operational: each active advisory must be either fixed, tested, and documented; closed as duplicate/not applicable; or accepted as non-blocking with a written maintainer decision.

Do not edit researcher-provided CVSS scores, CWE identifiers, or severity labels as part of this matrix work.

## Current Count

| State | Count |
| --- | ---: |
| Triage | 1 |
| Draft | 50 |
| Published | 12 |
| Closed | 10 |

| Severity | Count |
| --- | ---: |
| Critical | 8 |
| High | 24 |
| Medium | 38 |
| Low | 2 |
| None | 1 |

## Common Ground

The active advisories are not 51 unrelated problems. They collapse into a small set of recurring root causes:

| Root cause class | Primary active advisories | Release risk |
| --- | ---: | --- |
| Setup/install lockout and configuration write paths | 5 | Final blocker. The new RC1 setup advisory shows prior fixes may have been partial. |
| Object authorization and public/guest access scoping | 8 | Final blocker. Includes guest invoices, guest quote actions, attachments, payment views, and user password changes. |
| Account authentication, sessions, password reset, and token handling | 10 | Final blocker when account takeover, login bypass, or reset abuse is possible. |
| File/path/template handling | 5 | Final blocker where read/write/delete/include or template execution is reachable. |
| SQL or SQL identifier injection | 5 | Final blocker. Includes settings identifiers, custom-field table names, and Ajax `HAVING` clauses. |
| CSRF and state-changing GET/DELETE actions | 5 | Final blocker when it mutates invoices, quotes, users, settings, payments, or files. |
| Payment integrity and replay | 2 | Final blocker. Money movement must be exact and idempotent. |
| Output encoding and stored XSS | 4 | Release blocker for admin/session-impacting sinks; otherwise must be fixed and regression-covered before final. |
| Sensitive data exposure | 4 | Final blocker when unauthenticated or web-accessible. |
| Logging, SSRF, and manual classification | 3 | Fix or document explicitly; some are blocker-class depending on reachability. |

## Release Buckets

| Bucket | Meaning |
| --- | --- |
| Fixed and documented | Code fix is merged, GHSA is in changelog/release notes, and a regression test or root-cause test exists. |
| Duplicate/closed | Advisory is closed, duplicate, obsolete, or superseded by another GHSA, with a written reason. |
| Needs code | The reported exploit or its root cause is not fully fixed. |
| Needs test only | Code appears fixed, but no meaningful regression test proves the root cause. |
| Needs advisory metadata | Code/test/docs are ready, but affected versions, patched versions, CVE, credits, or publish timing need maintainer work. |
| Needs maintainer decision | Ambiguous report, duplicate candidate, accepted residual risk, or scope/version question. |

## Active Advisory Matrix

The `Changelog?` column only checks whether the GHSA ID appears in `.github/CHANGELOG.md`. It does not prove the fix is complete.

| GHSA | State | Severity | Root cause class | Reporter | CVE | Affected versions | Changelog? | Current bucket | Notes / next audit |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| GHSA-pp5w-98m2-gvc8 | triage | high | Setup/install lockout | FelipeSilvany | - | 1.7.2 RC1 | yes | Fixed and documented | Valid RC1 regression fixed by #1659. Covered by `SetupControllerTest::it_locks_every_http_setup_route_after_setup_is_completed`; unlocked `/setup` flow remains covered by `SetupControllerTest::it_allows_the_setup_flow_when_setup_is_explicitly_unlocked`. |
| GHSA-p7w2-hmm5-qw7m | draft | medium | Password reset rate limiting | Char0n1507 | - | < current | yes | Needs test only | Verify DB-backed counters survive missing/rotated session cookies. |
| GHSA-p875-mj5j-x2fc | draft | medium | User password authorization | Char0n1507 | - | < current | yes | Fixed and documented | Covered by `UsersControllerTest::it_prevents_a_non_primary_admin_from_changing_another_users_password`. |
| GHSA-wcqc-qqv5-65ph | draft | medium | Timing-safe token comparison | alham-rizvi | - | 1.7.2 | yes | Needs test only | Assert password-reset token comparison uses `hash_equals()` and invalid tokens do not mutate state. |
| GHSA-chqc-v432-8pj8 | draft | medium | CSPRNG token generation | alham-rizvi | - | 1.7.2 | yes | Needs test only | Assert reset/url/cron keys are generated through CSPRNG helpers, not `random_string()`/`str_shuffle()`. |
| GHSA-346c-gqqq-mrm2 | draft | medium | Strict auth/session comparison | Char0n1507 | - | <= 1.7.2 | yes | Needs test only | Assert auth guards reject type-juggling values and accept real string session identities. |
| GHSA-g53q-v2pv-xr83 | draft | medium | Log injection | Char0n1507 | - | <= 1.7.2 | yes | Needs test only | Audit every log sink touched by advisory reports for `sanitize_for_logging()`. |
| GHSA-x38q-xhjj-jr8w | draft | medium | User password authorization | Char0n1507 | - | <= 1.7.2 | yes | Fixed and documented | Same root cause as GHSA-p875-mj5j-x2fc; keep both GHSA links in release docs. |
| GHSA-9372-vj68-hmc3 | draft | medium | CSRF/state-changing delete | Char0n1507 | - | <= 1.7.2 | yes | Needs test only | Confirm all reported delete endpoints reject GET and tokenless POST. |
| GHSA-qf9q-2hxm-4wh9 | draft | medium | CSRF/state-changing GET | Char0n1507 | - | <= 1.7.2 | yes | Needs test only | Confirm `Recurring::stop()` rejects GET and tokenless POST. |
| GHSA-477r-xmgc-vcvj | draft | medium | Stored XSS | capt-bl4ck0ut | - | v1.7.2-beta-1 | yes | Needs test only | Add sink-level mailer form escaping test for client email. |
| GHSA-98vm-2r9v-mpgj | draft | medium | SQL identifier injection | capt-bl4ck0ut | - | v1.7.2-beta-1 | yes | Needs test only | Same root cause as custom-field table allowlist; verify persistence and second-order query use. |
| GHSA-g7f2-mj3r-xx88 | draft | high | SQL injection | PLpaPLpa | - | <=1.7.2 | yes | Needs test only | Assert Ajax `name_query` is bound/escaped and malicious input does not alter query semantics. |
| GHSA-h4xh-4jwc-485r | draft | high | Object authorization | 0raN9ewww | - | v1.7.1 | yes | Needs maintainer decision | Summary is generic; map exact user-controlled key to controller/model and test that object boundary. |
| GHSA-jw57-6692-jh2q | draft | high | Account authentication | mattmumford-git | - | 1.7.2 | yes | Needs test only | Assert inactive users cannot login and existing sessions are invalidated or blocked as intended. |
| GHSA-qx8h-35wf-cgqf | draft | medium | CSRF/state-changing settings | mattmumford-git | - | 1.7.2 | yes | Needs test only | Assert logo removal requires POST plus CSRF and unsafe paths are rejected. |
| GHSA-wv2c-c285-9hrq | draft | high | Web-accessible sensitive files | mattmumford-git | - | 1.7.2 | yes | Needs test only | Assert import staging files are outside webroot or denied by web-server rules/package layout. |
| GHSA-583r-4pw9-6rc9 | draft | high | Web-accessible sensitive files | mattmumford-git | - | 1.7.2 | yes | Needs test only | Assert SUMEX XML temp files are outside webroot or denied by web-server rules/package layout. |
| GHSA-j89p-m59x-86r9 | draft | high | mPDF local file read | tonghuaroot | CVE-2026-65977 | <= 1.7.1 | yes | Needs test only | Assert PDF-rendered report fields cannot trigger `file://` reads through unescaped VAT IDs. |
| GHSA-35qh-94hc-hm2w | draft | high | Payment integrity | tonghuaroot | CVE-2026-65961 | >= 1.6.2, <= 1.7.2-beta-1 | yes | Needs test only | Assert PayPal capture validates invoice, amount, currency, and already-paid state. |
| GHSA-7r3g-fppx-2p79 | draft | medium | Guest quote CSRF/authorization | de3erve-hunter | CVE-2026-61562 | <= 1.7.1 | yes | Needs test only | Assert approve/reject require authenticated guest POST and client ownership. |
| GHSA-jg68-6mqc-hxcr | draft | low | Timing-safe cron key comparison | alanturing881 | CVE-2026-55589 | <= 1.7.1 | yes | Needs test only | Assert cron key comparison uses `hash_equals()` or shared timing-safe helper. |
| GHSA-vm87-2qmm-rhg8 | draft | medium | Stored XSS | EvidentObscurity | CVE-2026-55104 | <= 1.7.2 | yes | Needs test only | Add CSV import to admin display regression for payment method name escaping. |
| GHSA-vv4r-cgmw-w6x2 | draft | medium | SQL identifier injection | 5ud0er | CVE-2026-54790 | <= 1.7.1 | yes | Needs test only | Same custom-field root cause; assert unsafe stored table identifiers are rejected before query use. |
| GHSA-9248-qccw-7x7j | draft | high | File/path/template handling | venkatesh2003631 | CVE-2026-50547 | all | yes | Needs maintainer decision | Broad title. Map exact path sink before marking fixed. |
| GHSA-mhvh-4j3w-7pvj | draft | high | CSRF/delete actions | venkatesh2003631 | CVE-2026-49850 | all | yes | Needs maintainer decision | Advisory metadata lists path CWEs despite CSRF title; verify exact report before final. |
| GHSA-vphv-wmr3-68fp | draft | medium | Sensitive credential storage | chakrapani150 | CVE-2026-49437 | all | yes | Needs test only | Assert invoice PDF passwords are no longer retrievable plaintext through DB/export/UI. |
| GHSA-9hx6-6h6f-2wq3 | draft | high | Public invoice data exposure | chakrapani150 | CVE-2026-49438 | all | yes | Needs test only | Assert unauthenticated public invoice paths only expose guest-visible invoices by valid URL key. |
| GHSA-x66r-7wf6-wgv7 | draft | high | Password hash/auth type juggling | geo-chen | CVE-2026-49354 | All versions | yes | Needs test only | Assert magic-hash-looking credentials cannot bypass login checks. |
| GHSA-wj6q-j965-2w2v | draft | medium | Needs manual classification | FORIMOC, nnin-nnin, invoke1442 | CVE-2026-43932 | 2bf9fc787668cd9830344f2c63930dd8f794cf27 | yes | Needs maintainer decision | Title is not actionable. Read advisory body and map to exact root cause. |
| GHSA-phh5-3jc3-pm6w | draft | high | Guest attachment authorization | QiaoNPC | CVE-2026-43933 | 1.7.2 | yes | Needs test only | Assert attachment download requires valid guest-visible invoice/quote URL key and safe filename. |
| GHSA-f95x-25mh-wcxv | draft | high | Guest payment authorization | FelipeSilvany | CVE-2026-43931 | InvoicePlane v1.7.1 | yes | Needs test only | Assert payment forms/callbacks reject draft/non-public/non-owned invoices. |
| GHSA-jx5h-6r8f-m2h3 | draft | critical | Setup/install lockout | kitu232 | CVE-2026-41420 | v1.7.1 | yes | Needs code | Re-audit with GHSA-pp5w-98m2-gvc8; setup must not expose any upgrade/config write path after install. |
| GHSA-xvrc-hv8j-8v8w | draft | medium | Stored XSS | ali-iltizar | CVE-2026-41209 | <= 1.7.1 | yes | Needs test only | Assert online payment settings are escaped in admin views and generated pages. |
| GHSA-8543-x4j8-jj4q | draft | medium | Sensitive credential exposure | ali-iltizar | - | <= 1.7.1 | yes | Needs test only | Assert Stripe/PayPal secret values are not rendered into HTML source. |
| GHSA-5r28-6rw3-25c2 | draft | medium | Password reset expiry | ali-iltizar | - | <= 1.7.1 | yes | Needs test only | Assert expired reset tokens are rejected and cleared. |
| GHSA-65v2-4g37-rxjw | draft | medium | Path traversal file deletion | ali-iltizar | CVE-2026-40298 | <= 1.7.1 | yes | Needs test only | Assert logo deletion validates safe filename and directory containment. |
| GHSA-ffq5-mw9f-mv6j | draft | critical | Setup/config injection | akgul7990 | CVE-2026-40297 | 1.7.1 | yes | Needs code | Re-audit setup config writers for newline/env/config injection after setup lockout. |
| GHSA-2j6j-6f6q-57vq | draft | critical | Setup/install lockout | iiihaiii | CVE-2026-39982 | 1.7.1 | yes | Needs code | Same setup family; verify no route reaches setup mutation when installed. |
| GHSA-45vj-9p52-f8mq | draft | medium | Path traversal file deletion | iiihaiii | CVE-2026-39978 | 1.7.1 | yes | Needs test only | Same logo deletion family as GHSA-65v2-4g37-rxjw. |
| GHSA-7j67-2v6p-275v | draft | medium | Metadata disclosure | Vijay-raghav7, Chittu13 | CVE-2026-39372 | <= 1.7.1 | yes | Needs test only | Assert uploaded images have EXIF stripped or decision documented if unsupported. |
| GHSA-v735-2x3r-gwpp | draft | critical | Template include/RCE | Vijay-raghav7 | CVE-2026-39353 | <= 1.7.1 | yes | Needs test only | Assert template names come only from explicit allowlists and no filesystem scan exists. |
| GHSA-4wqv-84px-8jc6 | draft | high | Stored XSS | Vijay-raghav7 | CVE-2026-35510 | <= 1.7.1 | yes | Needs test only | Assert email template preview renders sanitized/escaped content. |
| GHSA-jfgr-778p-m943 | draft | high | Predictable reset token | tikket1 | CVE-2026-34199 | < 1.6.0 | yes | Needs test only | Assert reset tokens are high-entropy CSPRNG values and stored with expiry. |
| GHSA-x6rh-cr7q-5w7j | draft | high | SQL injection | tikket1 | CVE-2026-34407 | < 1.6.0 | yes | Needs test only | Same tax decimal-place root cause as other SQL reports. |
| GHSA-vgq9-469p-q7j3 | draft | medium | SSRF / outbound fetch | radoi-teodor | CVE-2026-34201 | <=1.7.1 | yes | Needs test only | Assert PDF footer HTML cannot fetch internal/network resources. |
| GHSA-6cpc-hr8h-xgr2 | draft | high | Payment replay | HuajiHD | CVE-2026-34440 | 1.7.1 | yes | Needs test only | Assert duplicate Stripe callback/payment intent is idempotent and cannot double-pay. |
| GHSA-37pr-q48j-46gg | draft | critical | Setup/install lockout | HuajiHD | CVE-2026-33878 | 1.7.1 | yes | Needs code | Same setup family; keep grouped with the RC1 sequel until re-audited. |
| GHSA-34g5-4hfc-g983 | draft | high | SQL injection | udaypali | CVE-2026-33639 | <=1.7.1 | yes | Needs test only | Same tax decimal-place / identifier validation class. |
| GHSA-pjf5-c2m5-7m4x | draft | high | Guest quote IDOR | HuajiHD | CVE-2026-33629 | 1.7.1 | yes | Needs test only | Same quote approve/reject root cause as GHSA-6xj3-274m-4mvg and GHSA-7r3g-fppx-2p79. |
| GHSA-6xj3-274m-4mvg | draft | medium | Guest quote IDOR | lighthousekeeper1212 | CVE-2026-55561 | 1.7.1 | yes | Needs test only | Same quote approve/reject root cause; verify client scoping on every action variant. |

## Duplicate / Closed Queue

These advisories are not active blockers unless reopened, but they still need a written reason in the matrix if they overlap an active root cause.

| GHSA | State | Severity | Summary | Reporter | Changelog? | Notes |
| --- | --- | --- | --- | --- | --- | --- |
| GHSA-f8g5-6r57-xpw7 | closed | high | Unauthenticated Invocation of Database Upgrade Operations Through an Incompletely Locked Setup Wizard | FelipeSilvany | no | Closed duplicate candidate of GHSA-pp5w-98m2-gvc8. |
| GHSA-53hr-6777-2gvv | closed | low | Arbitrary file write/upload in `Upload.php` | Tonoss-412 | no | Decide whether it is duplicate of upload/path traversal coverage. |
| GHSA-pf36-9ww2-g2gr | closed | medium | Stored XSS via invoice/quote numbers | de3erve-hunter | no | Closed, but overlaps XSS sink coverage. |
| GHSA-59hc-4457-ww33 | closed | medium | Stored XSS via VAT/Tax-Code fields | de3erve-hunter | no | Closed, but overlaps public template escaping. |
| GHSA-2c9x-9qvc-r45r | closed | medium | Guest-portal IDOR quote approve/reject | Santoshkumarpuppala | no | Closed duplicate candidate of guest quote IDOR family. |
| GHSA-6qfv-cg7h-956g | closed | high | SQL injection via `tax_rate_decimal_places` | FelipeSilvany | no | Closed duplicate candidate of tax decimal SQLi family. |
| GHSA-v7xm-q6rp-w6c5 | closed | high | Guest quote approval IDOR | cyabell | no | Closed duplicate candidate of guest quote IDOR family. |
| GHSA-mhvf-2mxp-57g4 | closed | critical | SQL/DDL injection in settings module | akgul7990 | no | Closed duplicate candidate of settings SQLi family. |
| GHSA-8r5q-hjfx-rggf | closed | high | Stored XSS placeholder/duplicate | Vijay-raghav7 | no | Needs written closure reason. |
| GHSA-m5vf-pcvh-qhmf | closed | none | Placeholder advisory | nielsdrost7 | no | No release action expected. |

## Published Advisory Queue

Published advisories predate the current draft queue. They should not block RC2 unless the same root cause is still present in active code, but they are useful regression sources.

| GHSA | Severity | CVE | Summary | Reporter | Release use |
| --- | --- | --- | --- | --- | --- |
| GHSA-ccpx-2v5c-cc24 | medium | CVE-2026-26281 | Stored XSS in Sumex invoice view | IamLeandrooooo | XSS regression source. |
| GHSA-432m-jv69-qp5j | medium | CVE-2026-26270 | Stored XSS in identifier formatting | IamLeandrooooo | XSS regression source. |
| GHSA-3wjq-822q-98f4 | medium | CVE-2026-25596 | Stored XSS via product unit name | lagathos | XSS regression source. |
| GHSA-xxvr-2564-6jg6 | medium | CVE-2026-25595 | Stored XSS via invoice number | lagathos | XSS regression source. |
| GHSA-wrr7-2f27-8h94 | medium | CVE-2026-25594 | Stored XSS via family name | lagathos | XSS regression source. |
| GHSA-w2wc-6mf4-99x8 | medium | - | Multiple stored XSS vulnerabilities in admin panel | lagathos | XSS regression source. |
| GHSA-g6rw-m9mf-33ch | critical | CVE-2026-25548 | RCE via local file inclusion and log poisoning | lagathos | Template/logging regression source. |
| GHSA-485m-4725-2428 | medium | CVE-2026-24743 | Stored XSS in InvoicePlane 1.7.0 | SonNTB21DCAT164 | XSS regression source. |
| GHSA-r9rq-f946-6x54 | medium | CVE-2026-24745 | Stored XSS in InvoicePlane 1.7.0 | SonNTB21DCAT164 | XSS regression source. |
| GHSA-5mxx-553h-m62w | medium | CVE-2026-24744 | Stored XSS in InvoicePlane 1.7.0 | SonNTB21DCAT164 | XSS regression source. |
| GHSA-73x8-gr6v-vjvj | medium | CVE-2026-24746 | Stored XSS in quote editing | SonNTB21DCAT164 | XSS regression source. |
| GHSA-88gq-mv54-v3fc | critical | CVE-2026-23491 | Unauthenticated path traversal in Guest controller | lukasz-rybak | Guest attachment/path traversal regression source. |

## Immediate Release Audit Order

1. Setup/install lockout family: GHSA-jx5h-6r8f-m2h3, GHSA-ffq5-mw9f-mv6j, GHSA-2j6j-6f6q-57vq, GHSA-37pr-q48j-46gg. GHSA-pp5w-98m2-gvc8 is fixed and covered, but keep it linked to this family for release notes.
2. Guest/public object authorization family: guest invoice visibility, guest payment visibility, guest attachments, quote approve/reject, and password-change authorization.
3. File/path/template family: upload download/delete, logo deletion, guest file download, mPDF `file://`, template allowlists, and writable template directories.
4. SQL family: tax decimal places, settings identifiers, custom-field table identifiers, import table identifiers, Ajax `HAVING`.
5. Payment integrity family: PayPal amount/currency validation and Stripe replay/idempotency.
6. Account/session/token family: disabled users, magic hashes, reset token entropy/expiry/timing, persistent reset rate limits, strict auth guard comparisons.
7. XSS sink family: invoice/quote numbers, VAT/tax fields, email templates, client emails in mailer forms, payment settings, CSV-imported payment method names.
8. Packaging/data exposure family: import CSV and SUMEX temp files must not be web-accessible; secrets must not render into HTML source.

## Test Gate

Before final release, each root cause class above needs at least one meaningful regression test that proves the vulnerable state cannot recur. A test that only asserts `200 OK`, route reachability, or page text is not sufficient for this matrix.

The advisory is ready for final only when every active GHSA row is moved to one of:

- Fixed and documented
- Duplicate/closed
- Needs advisory metadata
- Needs maintainer decision with written non-blocking rationale
