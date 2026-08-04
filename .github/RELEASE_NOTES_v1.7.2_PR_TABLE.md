# InvoicePlane v1.7.2 — Pull Request & Release Summary

This document lists all pull requests resolved in the **v1.7.2** release cycle, together with
any linked GitHub issues and associated security advisories. It is intended as the source of
truth for the README / CHANGELOG entry once v1.7.2 goes final.

> **Note:** Security advisories will be formally published after the final v1.7.2 release.
> CVE identifiers marked *Pending* will be updated once assigned.

> **Sort order:** All sections are ordered by PR number (ascending). The Security Fixes section
> is ordered by severity (most critical first), then CVSS score (descending), then PR number
> (ascending) within each tier. The Security Advisories Reference table follows the same rule.

---

## Table of Contents

1. [Security Fixes](#security-fixes)
2. [Bug Fixes](#bug-fixes)
3. [Features & Improvements](#features--improvements)
4. [Performance & Code Quality](#performance--code-quality)
5. [Infrastructure & CI](#infrastructure--ci)
6. [Security Advisories Reference](#security-advisories-reference)

---

## Security Fixes

> Sorted by: Severity DESC → CVSS DESC → PR ID ASC within each tier.

| PR | Title | Linked Issue | Security Advisory / CVE | CVSS | Severity |
|----|-------|-------------|--------------------------|------|----------|
| [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) | Fix RCE vulnerability in template system — replace `directory_map()` whitelist with static constants; fix 5 open-redirect instances; add `security_helper.php` | — | [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) — CVE Pending | 9.9 | 🔴 CRITICAL |
| [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) | CodeRabbit auto-fixes for #1505: correct `security_helper.php` (stacked follow-up) | — | See #1505 | — | 🔴 CRITICAL (follow-up) |
| [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) | Comprehensive XSS fixes across InvoicePlane — 32 vulnerabilities across 17 view files in 4 modules | — | — CVE Pending | 8.0 | 🔴 HIGH |
| [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482) | Fix IDOR, CSRF, and SQL injection vulnerabilities in guest controllers | — | — CVE Pending | 7.5 | 🔴 HIGH |
| [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) | Replace weak PRNG in password reset tokens with `random_bytes(32)` (256-bit entropy) | — | Related to [CVE-2021-29023](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-29023) | 7.5 | 🔴 HIGH |
| [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481) | Fix SQL injection in tax rate decimal places setting | [#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479) | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) | Harden tax rate decimal setting against DDL injection — add `TaxRateDecimalPlacesProcessor`, transaction wrap, unit tests | [#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479) | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) | Fix configuration injection vulnerability in database setup (newline injection) | — | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512) | Fix path traversal vulnerability in logo file deletion (arbitrary file deletion) | — | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) — CVE Pending | 7.1 | 🔴 HIGH |
| [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471) | Fix authorization bypass and CSRF vulnerability in guest quote approve/reject endpoints | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) | Fix CSRF and authorization vulnerabilities in guest quote and payment gateways (Stripe + PayPal) | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491) | Harden setup wizard: lock DB rewrites post-install while allowing upgrades | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) | Sanitize PDF footer content to block SSRF in mPDF rendering | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500) | Fix Stored XSS vulnerabilities with comprehensive application-wide protection | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) | Fix file path traversal vulnerabilities across codebase — add `validate_db_filename()`, symlink protection, XSS prevention in attachment names | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) | Prevent API credential exposure in payment gateway settings (Stripe/PayPal keys no longer in HTML source) | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517) | Fix authorization bypass in guest invoice detail endpoint — add `guest_visible()` filter | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) | Fix authorization bypass in guest payment endpoints allowing access to draft invoices | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) | Automatically disable setup wizard post-install and add admin security warning system | — | — CVE Pending | 6.1 | 🟠 MEDIUM |
| [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486) | Render email template previews with sanitized HTML; harden Grunt build tooling | — | — | 5.4 | 🟡 LOW–MEDIUM |
| [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) | Fix PHPMailer debug output breaking AJAX responses; route SMTP debug to CI log with `sanitize_for_logging()` | — | — | 5.3 | 🟡 LOW–MEDIUM |
| [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) | Prevent duplicate payment processing in Stripe and PayPal callbacks — add `payment_external_id` unique index | — | — | 5.3 | 🟡 LOW–MEDIUM |
| [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) | Add optional EXIF metadata stripping from uploaded images (disabled by default via `SEC_STRIP_EXIF_FROM_IMAGES`) | — | — | 4.3 | 🟡 LOW |
| [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511) | Block setup wizard access when `SETUP_COMPLETED=true` — early 403 guard in `Setup.php` constructor | — | — | 4.3 | 🟡 LOW |
| [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) | Add expiration to password reset tokens (default: 15 minutes, configurable via `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES`) | — | — | 4.3 | 🟡 LOW |
| [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536) | Address PR review: `entrypoint.sh` config-injection guard, XSS trait fix, logo URL escaping, custom templates allowlisting, `Mdl_reports` cast fix, duplicate `remove_logo()` removal | — | — | — | 🟡 MULTIPLE |

---

## Bug Fixes

> Sorted by PR ID ascending.

| PR | Title | Linked Issue | Notes |
|----|-------|-------------|-------|
| [#1426](https://github.com/InvoicePlane/InvoicePlane/pull/1426) | Remittance — fix remittance slip generation | [#1140](https://github.com/InvoicePlane/InvoicePlane/issues/1140) | Remittance text on QR code |
| [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449) | Fix `/quote_templates/public/` template displaying client on both header and footer | — | Template rendering fix |
| [#1451](https://github.com/InvoicePlane/InvoicePlane/pull/1451) | Fix QR code generation for batch invoice processing | — | — |
| [#1465](https://github.com/InvoicePlane/InvoicePlane/pull/1465) | Simplify redundant conditional checks in email template tags | — | Code cleanup |
| [#1473](https://github.com/InvoicePlane/InvoicePlane/pull/1473) | Fix undefined array key error for mPDF footer names in PHP 8.3+ | [#1180](https://github.com/InvoicePlane/InvoicePlane/issues/1180) | PHP 8.3+ compatibility |
| [#1478](https://github.com/InvoicePlane/InvoicePlane/pull/1478) | Add missing `attachment()` method to guest `Get` controller; refactor duplicate code | — | [Bug](https://github.com/InvoicePlane/InvoicePlane/labels/Bug) label |
| [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) | Fix email template preview rendering raw HTML instead of rendered content | — | DOM-XSS risk also eliminated |

---

## Features & Improvements

> Sorted by PR ID ascending.

| PR | Title | Linked Issue | Notes |
|----|-------|-------------|-------|
| [#1289](https://github.com/InvoicePlane/InvoicePlane/pull/1289) | Re-introduce Stripe & PayPal; add PayPal Advanced Credit Cards and Venmo support | [#1288](https://github.com/InvoicePlane/InvoicePlane/issues/1288) | By [@drewangell](https://github.com/drewangell); enables Advanced CC fields, Venmo, improved error handling |
| [#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489) | Allow specifying the QR-code size in invoices | [#1376](https://github.com/InvoicePlane/InvoicePlane/issues/1376) | New `ipconfig.php` option |

---

## Performance & Code Quality

> Sorted by PR ID ascending.

| PR | Title | Notes |
|----|-------|-------|
| [#1483](https://github.com/InvoicePlane/InvoicePlane/pull/1483) | Optimize settings save: batch operations reduce DB queries from ~70 to 3 | Transaction-wrapped insert+update |
| [#1503](https://github.com/InvoicePlane/InvoicePlane/pull/1503) | Add database indexes for performance optimization | Indexes on frequently queried columns |

---

## Infrastructure & CI

> Sorted by PR ID ascending.

| PR | Title | Notes |
|----|-------|-------|
| [#1466](https://github.com/InvoicePlane/InvoicePlane/pull/1466) | Rename `.github/action.yml.txt` to `.github/actions/setup-php-composer/action.yml` | CI housekeeping |
| [#1490](https://github.com/InvoicePlane/InvoicePlane/pull/1490) | Set up code style check with Laravel Pint | CI: auto-formatting |
| [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509) | Add application container (Docker) | New Docker build/entrypoint |
| [#1529](https://github.com/InvoicePlane/InvoicePlane/pull/1529) | Document arbitrary file deletion vulnerability for CVE allocation | Adds `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md`, `CVE_REQUEST_SUMMARY.md`, `verify_file_deletion_fix.php`, `SECURITY_DOCS_README.md` |

---

## Security Advisories Reference

The following formal security advisories have been prepared for v1.7.2. CVE IDs will be
disclosed once the final release is published.

> Sorted by: Severity DESC → CVSS DESC → first fixing PR ID ASC within each tier.

| Advisory / GHSA | Vulnerability | CWE | CVSS v3.1 | Severity | Reported By | Fixed In PR(s) | Affected Versions |
|-----------------|--------------|-----|-----------|----------|-------------|----------------|-------------------|
| [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) · [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | Remote Code Execution via template system (dynamic whitelist bypass of v1.7.1 LFI fix) | CWE-693, CWE-98 | 9.9 | 🔴 CRITICAL | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) | v1.7.0, v1.7.1 |
| [[#1500]: Stored XSS via Email Templates in InvoicePlane <= 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-4wqv-84px-8jc6) | Stored XSS — 32 vulnerabilities across 17 view files (settings, invoices, quotes, projects) | CWE-79 | 8.0 | 🔴 HIGH | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) | v1.7.0, v1.7.1 |
| [[#1482]: Guest Quote Approval/Reject Horizontal Privilege Escalation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-pjf5-c2m5-7m4x), [[#1471]: Guest user IDOR: Quote approve/reject missing client_id scoping](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6xj3-274m-4mvg) | IDOR + CSRF in guest quote approve/reject endpoints | CWE-639, CWE-352 | 7.5 | 🔴 HIGH | [@HuajiHD](https://github.com/HuajiHD), [@lighthousekeeper1212](https://github.com/lighthousekeeper1212) | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) | v1.7.0, v1.7.1 |
| [[#1494]: Predictable Password Reset Token via md5(time()) Enables Account Takeover](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jfgr-778p-m943) | Weak PRNG in password reset tokens (related to CVE-2021-29023) | CWE-338 | 7.5 | 🔴 HIGH | [@tikket1](https://github.com/tikket1) | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) | v1.7.0, v1.7.1 |
| [[#1481]: SQL Injection via Unsanitized Tax Rate Decimal Places Field in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x6rh-cr7q-5w7j), [[#1488]: Improper Neutralization of Special Elements used in an SQL Command ('SQL Injection') in invoiceplane/invoiceplane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-34g5-4hfc-g983) | SQL / DDL injection in tax rate decimal places setting | CWE-89 | 7.2 | 🔴 HIGH | [@tikket1](https://github.com/tikket1), [@udaypali](https://github.com/udaypali) | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) | v1.7.0, v1.7.1 |
| [[#1513]: Configuration Injection in Setup Module Leading to Environment Manipulation (db_hostname Injection)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-ffq5-mw9f-mv6j) | Configuration (newline) injection in database setup | CWE-93 | 7.2 | 🔴 HIGH | [@akgul7990](https://github.com/akgul7990) | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) | v1.7.0, v1.7.1 |
| [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) · [[#1512]: [CVE-2026-40298]: Authenticated Arbitrary File Deletion via Path Traversal in "Invoice Logo" Setting](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-65v2-4g37-rxjw), [[#1510]: [CVE-2026-39978]: Authenticated path traversal in logo removal allows arbitrary file deletion outside uploads](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-45vj-9p52-f8mq) | Arbitrary file deletion via path traversal in logo settings | CWE-22 | 7.1 | 🔴 HIGH | [@ali-iltizar](https://github.com/ali-iltizar), [@iiihaiii](https://github.com/iiihaiii) | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) | v1.7.0, v1.7.1 |
| [[#1491]: Unauthenticated Setup Reconfiguration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-37pr-q48j-46gg), [[#1511]: Unauthenticated Setup Wizard Re-entry Allows Configuration Overwrite in InvoicePlane 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-2j6j-6f6q-57vq), [[#1518]: Unauthenticated Re-execution of Installation Wizard After Setup Allows Overwrite of Database Configuration, Denial of Service, and Potential Data Compromise](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jx5h-6r8f-m2h3) | Setup wizard not locked post-installation (unauthenticated config overwrite) | CWE-287 | 6.5 | 🟠 MEDIUM | [@HuajiHD](https://github.com/HuajiHD), [@iiihaiii](https://github.com/iiihaiii), [@kitu232](https://github.com/kitu232) | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) | v1.7.0, v1.7.1 |
| [[#1492]: SSRF via admin-stored PDF footer HTML](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vgq9-469p-q7j3) | SSRF via PDF footer content in mPDF rendering | CWE-918 | 6.5 | 🟠 MEDIUM | [@radoi-teodor](https://github.com/radoi-teodor) | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) | v1.7.0, v1.7.1 |
| [[#1510]: [CVE-2026-40298]: Authenticated Arbitrary File Deletion via Path Traversal in "Invoice Logo" Setting](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-65v2-4g37-rxjw) | File path traversal across codebase (settings, uploads, guest controller) | CWE-22 | 6.5 | 🟠 MEDIUM | [@ali-iltizar](https://github.com/ali-iltizar) | [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) | v1.7.0, v1.7.1 |
| [[#1515]: Sensitive Data Exposure via HTML Source Code (Stripe & PayPal API Keys)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-8543-x4j8-jj4q) | API credential exposure in payment gateway settings (Stripe/PayPal keys in HTML source) | CWE-312 | 6.5 | 🟠 MEDIUM | [@ali-iltizar](https://github.com/ali-iltizar) | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) | v1.7.0, v1.7.1 |
| [[#1517]: Improper Access Control in Guest Payment Flow Allows Access to Non-Public Invoices](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-f95x-25mh-wcxv) | Authorization bypass in guest invoice/payment endpoints (draft invoice access) | CWE-863 | 6.5 | 🟠 MEDIUM | [@FelipeSilvany](https://github.com/FelipeSilvany) | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) | v1.7.0, v1.7.1 |
| [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | Open redirect via unvalidated `HTTP_REFERER` (5 instances) | CWE-601 | 6.1 | 🟠 MEDIUM | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) | v1.7.0, v1.7.1 |
| [[#1514]: Improper Password Reset Token Expiration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-5r28-6rw3-25c2) | Improper password reset token expiration (tokens valid indefinitely) | CWE-640 | 6.1 | 🟠 MEDIUM | [@ali-iltizar](https://github.com/ali-iltizar) | [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) | v1.7.0, v1.7.1 |
| [[#1496]: Stripe Callback Replay](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6cpc-hr8h-xgr2) | Stripe callback replay — duplicate payment processing | CWE-362 | 5.3 | 🟡 LOW–MEDIUM | [@HuajiHD](https://github.com/HuajiHD) | [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) | v1.7.0, v1.7.1 |
| [[#1486]: Stored XSS via Email Templates in InvoicePlane <= 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-4wqv-84px-8jc6) | Stored XSS via email template previews | CWE-79 | 5.4 | 🟡 LOW–MEDIUM | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486) | v1.7.0, v1.7.1 |
| [[#1507]: Sensitive Information Disclosure via Unstripped EXIF Metadata in Attachments](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-7f67-2v6p-275v) | Sensitive information disclosure via unstripped EXIF metadata in attachments | CWE-212 | 3.5 | 🟡 LOW | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) | v1.7.0, v1.7.1 |

---

## Contributor Acknowledgements

### Security Researchers

Many thanks to the security researchers who responsibly disclosed vulnerabilities through GitHub
Security Advisories and private reports. Without their responsible disclosure, these issues
would have remained undetected.

[akgul7990](https://github.com/akgul7990),
[ali-iltizar](https://github.com/ali-iltizar),
[Chittu13](https://github.com/Chittu13),
[cyabell](https://github.com/cyabell),
[FelipeSilvany](https://github.com/FelipeSilvany),
[HuajiHD](https://github.com/HuajiHD),
[iiihaiii](https://github.com/iiihaiii),
[kitu232](https://github.com/kitu232),
[lighthousekeeper1212](https://github.com/lighthousekeeper1212),
[radoi-teodor](https://github.com/radoi-teodor),
[tikket1](https://github.com/tikket1),
[udaypali](https://github.com/udaypali),
[Vijay-raghav7](https://github.com/Vijay-raghav7)

### Contributors

Many thanks to the community members who contributed code that was merged in this release.

[mpldr](https://github.com/mpldr) — Docker application container with combined web server + PHP in a
single image (`entrypoint.sh`, PR [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509));
configurable QR-code size in invoices (PR [#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489))

[PatrickGTR](https://github.com/PatrickGTR) — Fix quote public template displaying client name on both
header and footer (PR [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449))

---

*Document generated: 2026-04-27 — to be incorporated into CHANGELOG.md and README for v1.7.2 final.*
