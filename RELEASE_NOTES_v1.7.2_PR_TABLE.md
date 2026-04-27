# InvoicePlane v1.7.2 — Pull Request & Release Summary

This document lists all pull requests resolved in the **v1.7.2** release cycle, together with
any linked GitHub issues and associated security advisories. It is intended as the source of
truth for the README / CHANGELOG entry once v1.7.2 goes final.

> **Note:** Security advisories will be formally published after the final v1.7.2 release.
> CVE identifiers marked *Pending* will be updated once assigned.

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

| PR | Title | Linked Issue | Security Advisory / CVE | CVSS | Severity |
|----|-------|-------------|--------------------------|------|----------|
| [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) | Fix RCE vulnerability in template system (patch bypass of v1.7.1 LFI fix) | — | [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) — CVE Pending | 9.9 | 🔴 CRITICAL |
| [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) | Comprehensive XSS fixes across InvoicePlane (30+ vulnerabilities) | — | — CVE Pending | 8.0 | 🔴 HIGH |
| [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512) | Fix path traversal vulnerability in logo file deletion (arbitrary file deletion) | — | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) — CVE Pending | 7.1 | 🔴 HIGH |
| [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481) | Fix SQL injection in tax rate decimal places setting | [#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479) | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482) | Fix IDOR, CSRF, and SQL injection vulnerabilities in guest controllers | — | — CVE Pending | 7.5 | 🔴 HIGH |
| [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) | Harden tax rate decimal setting against DDL injection | [#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479) | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) | Replace weak PRNG in password reset tokens with `random_bytes(32)` | — | Related to [CVE-2021-29023](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-29023) | 7.5 | 🔴 HIGH |
| [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) | Fix configuration injection vulnerability in database setup (newline injection) | — | — CVE Pending | 7.2 | 🔴 HIGH |
| [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500) | Fix Stored XSS vulnerabilities with comprehensive application-wide protection | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471) | Fix authorization bypass and CSRF vulnerability in guest quote approve/reject endpoints | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) | Fix CSRF and authorization vulnerabilities in guest quote and payment gateways | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491) | Harden setup wizard: lock DB rewrites post-install while allowing upgrades | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) | Sanitize PDF footer content to block SSRF in mPDF rendering | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) | Fix file path traversal vulnerabilities across codebase | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) | Prevent API credential exposure in payment gateway settings | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517) | Fix authorization bypass in guest invoice detail endpoint | — | — CVE Pending | 6.5 | 🟠 MEDIUM |
| [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) | Automatically disable setup wizard and add security warning system | — | — CVE Pending | 6.1 | 🟠 MEDIUM |
| [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486) | Render email template previews with sanitized HTML; harden build tooling | — | — | 5.4 | 🟡 MEDIUM |
| [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) | Fix PHPMailer debug output breaking AJAX responses; sanitize SMTP debug log | — | — | 5.3 | 🟡 MEDIUM |
| [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) | Prevent duplicate payment processing in Stripe and PayPal callbacks | — | — | 5.3 | 🟡 MEDIUM |
| [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) | Add optional EXIF metadata stripping from uploaded images (disabled by default) | — | — | 4.3 | 🟡 LOW–MEDIUM |
| [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511) | Block setup wizard access when `SETUP_COMPLETED=true` | — | — | 4.3 | 🟡 LOW–MEDIUM |
| [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) | Add expiration to password reset tokens (default: 15 minutes) | — | — | 4.3 | 🟡 LOW–MEDIUM |
| [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536) | Address PR review: config injection hardening, helper load order fix, RCE prevention, log-injection removal, HTML escaping | — | — | — | 🟡 MULTIPLE |
| [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) | Fix authorization bypass in guest payment endpoints (draft invoice access) | — | — CVE Pending | 6.5 | 🟠 MEDIUM |

---

## Bug Fixes

| PR | Title | Linked Issue | Notes |
|----|-------|-------------|-------|
| [#1473](https://github.com/InvoicePlane/InvoicePlane/pull/1473) | Fix undefined array key error for mPDF footer names in PHP 8.3+ | [#1180](https://github.com/InvoicePlane/InvoicePlane/issues/1180) | PHP 8.3+ compatibility |
| [#1426](https://github.com/InvoicePlane/InvoicePlane/pull/1426) | Remittance — fix remittance slip generation | — | — |
| [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449) | Fix `/quote_templates/public/` template displaying client on both header and footer | — | Template rendering fix |
| [#1451](https://github.com/InvoicePlane/InvoicePlane/pull/1451) | Fix QR code generation for batch invoice processing | — | — |
| [#1465](https://github.com/InvoicePlane/InvoicePlane/pull/1465) | Simplify redundant conditional checks in email template tags | — | Code cleanup |
| [#1478](https://github.com/InvoicePlane/InvoicePlane/pull/1478) | Add missing `attachment()` method to guest `Get` controller; refactor duplicate code | — | [Bug](https://github.com/InvoicePlane/InvoicePlane/labels/Bug) label |
| [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) | Fix email template preview rendering raw HTML instead of rendered content | — | DOM-XSS risk also eliminated |

---

## Features & Improvements

| PR | Title | Linked Issue | Notes |
|----|-------|-------------|-------|
| [#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489) | Allow specifying the QR-code size in invoices | [#1376](https://github.com/InvoicePlane/InvoicePlane/issues/1376) | New `ipconfig.php` option |

---

## Performance & Code Quality

| PR | Title | Notes |
|----|-------|-------|
| [#1483](https://github.com/InvoicePlane/InvoicePlane/pull/1483) | Optimize settings save: batch operations reduce DB queries from ~70 to 3 | Transaction-wrapped insert+update |
| [#1503](https://github.com/InvoicePlane/InvoicePlane/pull/1503) | Add database indexes for performance optimization | Indexes on frequently queried columns |

---

## Infrastructure & CI

| PR | Title | Notes |
|----|-------|-------|
| [#1466](https://github.com/InvoicePlane/InvoicePlane/pull/1466) | Rename `.github/action.yml.txt` to `.github/actions/setup-php-composer/action.yml` | CI housekeeping |
| [#1490](https://github.com/InvoicePlane/InvoicePlane/pull/1490) | Set up code style check with Laravel Pint | CI: auto-formatting |
| [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509) | Add application container (Docker) | New Docker build/entrypoint |

---

## Security Advisories Reference

The following formal security advisories have been prepared for v1.7.2. CVE IDs will be
disclosed once the final release is published.

| Advisory File | Vulnerability | CWE | CVSS v3.1 | Severity | Fixed In PR | Affected Versions |
|---------------|--------------|-----|-----------|----------|-------------|-------------------|
| [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) | Remote Code Execution via template system (patch bypass of v1.7.1 LFI fix) | CWE-693 | 9.9 | 🔴 CRITICAL | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) | v1.7.0, v1.7.1 |
| [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) | Arbitrary file deletion via path traversal in logo settings | CWE-22 | 7.1 | 🔴 HIGH | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512) | v1.7.0, v1.7.1 |
| *(pending)* | SQL / DDL injection in tax rate decimal places setting | CWE-89 | 7.2 | 🔴 HIGH | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) | v1.7.0, v1.7.1 |
| *(pending)* | IDOR + CSRF in guest quote approve/reject endpoints | CWE-639, CWE-352 | 7.5 | 🔴 HIGH | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) | v1.7.0, v1.7.1 |
| *(pending)* | Weak PRNG in password reset tokens (related to CVE-2021-29023) | CWE-338 | 7.5 | 🔴 HIGH | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) | v1.7.0, v1.7.1 |
| *(pending)* | Configuration (newline) injection in database setup | CWE-93 | 7.2 | 🔴 HIGH | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) | v1.7.0, v1.7.1 |
| *(pending)* | Stored XSS — 30+ vulnerabilities across 17 view files | CWE-79 | 8.0 | 🔴 HIGH | [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516), [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500) | v1.7.0, v1.7.1 |
| *(pending)* | Authorization bypass in guest invoice/payment endpoints (draft invoice access) | CWE-863 | 6.5 | 🟠 MEDIUM | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) | v1.7.0, v1.7.1 |
| *(pending)* | SSRF via PDF footer content in mPDF rendering | CWE-918 | 6.5 | 🟠 MEDIUM | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) | v1.7.0, v1.7.1 |
| *(pending)* | File path traversal across codebase (settings, uploads, templates) | CWE-22 | 6.5 | 🟠 MEDIUM | [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) | v1.7.0, v1.7.1 |
| *(pending)* | API credential exposure in payment gateway settings (Stripe/PayPal keys in HTML) | CWE-312 | 6.5 | 🟠 MEDIUM | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) | v1.7.0, v1.7.1 |
| *(pending)* | Setup wizard not locked post-installation (unauthenticated config overwrite) | CWE-287 | 6.5 | 🟠 MEDIUM | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) | v1.7.0, v1.7.1 |
| *(pending)* | Open redirect via unvalidated `HTTP_REFERER` | CWE-601 | 6.1 | 🟠 MEDIUM | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) | v1.7.0, v1.7.1 |

---

## Contributor Acknowledgements

Many thanks to the security researchers who responsibly disclosed vulnerabilities:

[Vijay-raghav7](https://github.com/Vijay-raghav7),
[cyabell](https://github.com/cyabell),
[HuajiHD](https://github.com/HuajiHD),
[udaypali](https://github.com/udaypali),
[radoi-teodor](https://github.com/radoi-teodor),
[tikket1](https://github.com/tikket1),
[Chittu13](https://github.com/Chittu13),
[iiihaiii](https://github.com/iiihaiii),
[akgul7990](https://github.com/akgul7990),
[ali-iltizar](https://github.com/ali-iltizar),
[kitu232](https://github.com/kitu232)

And thanks to [@drewangell](https://github.com/drewangell) for re-introducing Stripe and PayPal support.

---

*Document generated: 2026-04-27 — to be incorporated into CHANGELOG.md and README for v1.7.2 final.*
