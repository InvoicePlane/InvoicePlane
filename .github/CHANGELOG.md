# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Full write-ups for individual vulnerabilities live in the published
[GitHub Security Advisories](https://github.com/InvoicePlane/InvoicePlane/security/advisories)
and in [`.github/security/`](security/). This changelog records *what* changed; the advisories
record *why* and *how*.

---

## [Unreleased]

### Security

> The GHSA links below point to **draft** (unpublished) security advisories — only maintainers
> and collaborators can view them until they're published. They will 404 for external readers
> until then.

- **Authentication Bypass via Deactivated Accounts**: `Mdl_Sessions::auth()` now rejects a login attempt for a deactivated user (`user_active != 1`) even when the supplied password is correct. Previously, disabling an account (e.g. after an employee leaves) did not actually prevent that account's credentials from being used to log in. [[GHSA-jw57-6692-jh2q]: Disabled Users Can Still Authenticate](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jw57-6692-jh2q) (High) — reported by [@mattmumford-git](https://github.com/mattmumford-git).
- **SUMEX Invoice XML Information Disclosure**: SUMEX invoice XML files (Swiss medical invoicing) are now written to a non-web-accessible `storage/temp/` directory instead of the public `uploads/temp/` folder, preventing unauthenticated access to sensitive invoice data via direct HTTP requests. [[GHSA-583r-4pw9-6rc9]: Sensitive SUMEX Invoice XML Disclosure via Web-Accessible Temporary Directory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-583r-4pw9-6rc9) (High) — reported by [@mattmumford-git](https://github.com/mattmumford-git).
- **Access Control on `uploads/import`**: Added a `Deny from all` `.htaccess` to `uploads/import/`, the directory that holds in-progress CSV imports (`invoices.csv`, `payments.csv`, `invoice_items.csv`, ...), preventing direct web access to plaintext financial data while an import is in progress. [[GHSA-wv2c-c285-9hrq]: Sensitive Import CSV Disclosure via Web-Accessible Import Directory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-wv2c-c285-9hrq) (High) — reported by [@mattmumford-git](https://github.com/mattmumford-git).
- **Object-Level Authorization (IDOR Prevention)**: Added authorization checks to prevent Insecure Direct Object Reference (IDOR) vulnerabilities. Access checks now live as model methods — `Mdl_Clients::can_user_access()`, `Mdl_Projects::can_user_access()`, `Mdl_User_Clients::can_user_manage()`, `Mdl_Invoices::can_user_access()`, `Mdl_Quotes::can_user_access()` — rather than the `security_helper.php` functions used in earlier drafts of this change (`user_has_client_access()`, `user_has_project_access()`, `user_can_manage_user_client()`, `user_has_invoice_access()`, `user_has_quote_access()`, all since removed). `/clients/view/{id}` and `/projects/view/{id}` call these checks; the invoice/quote equivalents were added for consistency but are not yet wired into a controller. [[GHSA-h4xh-4jwc-485r]: Authorization Bypass Through User-Controlled Key in InvoicePlane/InvoicePlane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-h4xh-4jwc-485r) (High) — reported by [@0raN9ewww](https://github.com/0raN9ewww).
- **CSRF Protection Hardening**: Added explicit POST and CSRF token validation to all delete endpoints across 12 modules (Projects, Tasks, Users, Invoice Groups, Payment Methods, Custom Fields, Units, Tax Rates, Custom Values, Clients, Products, and Settings logo removal). Implements defense-in-depth protection against cross-site request forgery attacks by validating both HTTP method and CSRF tokens at the controller level, preventing direct GET access to state-changing operations. [[GHSA-qx8h-35wf-cgqf]: CSRF via GET-Based Logo Removal in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-qx8h-35wf-cgqf) (Moderate) — reported by [@mattmumford-git](https://github.com/mattmumford-git); [[GHSA-mhvh-4j3w-7pvj]: Missing CSRF Protection on State-Changing delete Actions](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-mhvh-4j3w-7pvj) (High) — reported by [@venkatesh2003631](https://github.com/venkatesh2003631).

---

## [1.7.2] - 2026-07-17

InvoicePlane 1.7.2 is a **security-focused release**. It resolves every vulnerability
responsibly disclosed against v1.7.0 / v1.7.1 and hardens the application, session handling,
and container tooling throughout. **If you run v1.7.0 or v1.7.1, upgrade immediately** — this
release fixes a critical (CVSSv3 9.9) Remote Code Execution vulnerability.

### Thank you

Huge thanks to the security researchers who responsibly disclosed these issues through private
[GitHub Security Advisories](https://github.com/InvoicePlane/InvoicePlane/security/advisories).
Without your reports this release would not have been possible:

[@akgul7990](https://github.com/akgul7990),
[@ali-iltizar](https://github.com/ali-iltizar),
[@Chittu13](https://github.com/Chittu13),
[@cyabell](https://github.com/cyabell),
[@FelipeSilvany](https://github.com/FelipeSilvany),
[@HuajiHD](https://github.com/HuajiHD),
[@iiihaiii](https://github.com/iiihaiii),
[@kitu232](https://github.com/kitu232),
[@lighthousekeeper1212](https://github.com/lighthousekeeper1212),
[@radoi-teodor](https://github.com/radoi-teodor),
[@tikket1](https://github.com/tikket1),
[@udaypali](https://github.com/udaypali),
[@Vijay-raghav7](https://github.com/Vijay-raghav7).

And thank you to the contributors whose code shipped in this release:
[@drewangell](https://github.com/drewangell) (Stripe/PayPal, Advanced Credit Cards & Venmo),
[@mpldr](https://github.com/mpldr) (Docker application container, configurable QR-code size),
and [@PatrickGTR](https://github.com/PatrickGTR) (quote public-template fix).

### Security Vulnerability Summary

> Each **GHSA** link is the canonical record of the report and its reporter — use them to trace
> and credit disclosures. CVE identifiers are shown where assigned. Please do not disclose
> details of any issue publicly before its advisory is published.

| Vulnerability | Severity | CVSSv3 | CWE | Security Advisory (GHSA) | Reported By | Fixed In |
|---|---|---|---|---|---|---|
| RCE via template filesystem scan | Critical | 9.9 | CWE-693 | [SECURITY_ADVISORY_RCE_FIX.md](security/SECURITY_ADVISORY_RCE_FIX.md) · [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) |
| Broken auth: password-reset tokens never expired | Critical | 9.8 | CWE-640 | [[#1514]: Improper Password Reset Token Expiration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-5r28-6rw3-25c2) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) |
| Arbitrary file deletion via path traversal | High | 7.1 | CWE-22 | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](security/SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) · [[#1512]: [CVE-2026-40298]: Authenticated Arbitrary File Deletion via Path Traversal in "Invoice Logo" Setting](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-65v2-4g37-rxjw), [[#1510]: [CVE-2026-39978]: Authenticated path traversal in logo removal allows arbitrary file deletion outside uploads](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-45vj-9p52-f8mq) | [@ali-iltizar](https://github.com/ali-iltizar), [@iiihaiii](https://github.com/iiihaiii) | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) |
| Weak PRNG in password-reset tokens | High | 7.5 | CWE-338 | [[#1494]: Predictable Password Reset Token via md5(time()) Enables Account Takeover](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jfgr-778p-m943) | [@tikket1](https://github.com/tikket1) | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) |
| SQL/DDL injection in tax rate decimal places | High | 8.8 | CWE-89 | [[#1481]: SQL Injection via Unsanitized Tax Rate Decimal Places Field in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x6rh-cr7q-5w7j), [[#1488]: Improper Neutralization of Special Elements used in an SQL Command ('SQL Injection') in invoiceplane/invoiceplane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-34g5-4hfc-g983) | [@tikket1](https://github.com/tikket1), [@udaypali](https://github.com/udaypali) | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) |
| Configuration injection in DB setup wizard | High | 8.8 | CWE-77 | [[#1513]: Configuration Injection in Setup Module Leading to Environment Manipulation (db_hostname Injection)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-ffq5-mw9f-mv6j) | [@akgul7990](https://github.com/akgul7990) | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) |
| IDOR + CSRF on guest quote approve/reject | High | 8.1 | CWE-639, CWE-352 | [[#1482]: Guest Quote Approval/Reject Horizontal Privilege Escalation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-pjf5-c2m5-7m4x), [[#1471]: Guest user IDOR: Quote approve/reject missing client_id scoping](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6xj3-274m-4mvg) | [@HuajiHD](https://github.com/HuajiHD), [@lighthousekeeper1212](https://github.com/lighthousekeeper1212) | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) |
| Auth bypass in guest invoice/payment endpoints | Medium | 6.5 | CWE-284 | [[#1517]: Improper Access Control in Guest Payment Flow Allows Access to Non-Public Invoices](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-f95x-25mh-wcxv) | [@FelipeSilvany](https://github.com/FelipeSilvany) | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) |
| Setup wizard accessible post-installation | Medium | 5.3 | CWE-285 | [[#1491]: Unauthenticated Setup Reconfiguration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-37pr-q48j-46gg), [[#1511]: Unauthenticated Setup Wizard Re-entry Allows Configuration Overwrite in InvoicePlane 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-2j6j-6f6q-57vq), [[#1518]: Unauthenticated Re-execution of Installation Wizard After Setup Allows Overwrite of Database Configuration, Denial of Service, and Potential Data Compromise](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jx5h-6r8f-m2h3) | [@HuajiHD](https://github.com/HuajiHD), [@iiihaiii](https://github.com/iiihaiii), [@kitu232](https://github.com/kitu232) | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) |
| SSRF via PDF footer content | Medium | 6.5 | CWE-918 | [[#1492]: SSRF via admin-stored PDF footer HTML](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vgq9-469p-q7j3) | [@radoi-teodor](https://github.com/radoi-teodor) | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) |
| Open redirect via `HTTP_REFERER` | Medium | 6.1 | CWE-601 | [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) |
| Payment gateway API credentials in plaintext | Medium | 6.5 | CWE-312 | [[#1515]: Sensitive Data Exposure via HTML Source Code (Stripe & PayPal API Keys)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-8543-x4j8-jj4q) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) |
| Duplicate payment processing (Stripe callback replay) | Low–Medium | 5.3 | CWE-362 | [[#1496]: Stripe Callback Replay](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6cpc-hr8h-xgr2) | [@HuajiHD](https://github.com/HuajiHD) | [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) |
| Email template preview XSS | Low–Medium | 5.4 | CWE-79 | [[#1486]: Stored XSS via Email Templates in InvoicePlane <= 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-4wqv-84px-8jc6) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) |
| Stored XSS via client email in invoice/quote mailer | — | — | CWE-79 | — | — | — |
| EXIF metadata in uploaded images | Low | 3.5 | CWE-212 | [[#1507]: Sensitive Information Disclosure via Unstripped EXIF Metadata in Attachments](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-7f67-2v6p-275v) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) |
| PHPMailer debug output in AJAX responses | Low–Medium | 4.3 | CWE-209 | — | Internal audit | [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) |
| XSS session hijack via `cookie_httponly=false` | High | 7.4 | CWE-1004 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Clickjacking — `X-Frame-Options` not always sent | Medium | 4.3 | CWE-1021 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Session fixation — `SESS_REGENERATE_DESTROY` defaulted `false` | Medium | 6.8 | CWE-384 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Log injection via password-reset token/email | Low | 3.7 | CWE-117 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Missing `Referrer-Policy` header | Low | — | CWE-116 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |

### Security fixes

*Ordered by severity, then CVSS, then PR number.*

- [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) — **RCE:** replace `directory_map()` template whitelist with static constants + `ipconfig.php` allowlist; fix five open-redirect instances; add `security_helper.php`. See [CUSTOM_TEMPLATES.md](docs/CUSTOM_TEMPLATES.md) for the resulting custom-template usage guide.
- [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) — **Stored XSS:** comprehensive, application-wide output escaping (32 findings across 17 view files in 4 modules)
- **Stored XSS (mailer):** escape `client_email` with `htmlsc()` in the invoice/quote mailer views (`to_email` value attribute) — a follow-up sink missed by the earlier sweep — plus `valid_email` validation on `client_email` in `Mdl_Clients` as defense-in-depth
- [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) — **IDOR + CSRF:** guest quote approve/reject and payment gateways now enforce client scoping and require POST
- [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) — **Weak PRNG:** replace password-reset token generation with `random_bytes(32)` (256-bit entropy)
- [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) — **SQL/DDL injection:** harden tax-rate decimal-places setting (`TaxRateDecimalPlacesProcessor`, transaction wrap, unit tests) ([#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479))
- [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) — **Config injection:** block newline injection in the database setup wizard
- [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) — **Arbitrary file deletion:** path-traversal guards for logo deletion; `validate_db_filename()`, symlink protection, attachment-name XSS prevention
- [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) — **Credential exposure:** Stripe/PayPal API keys no longer rendered into HTML source
- [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) — **Auth bypass:** guest invoice/payment endpoints gain a `guest_visible()` filter (no draft-invoice access)
- [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) — **Setup wizard:** locked after install (`SETUP_COMPLETED`), with an admin security-warning system
- [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) — **SSRF:** sanitize PDF footer content before mPDF rendering
- [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) — **Email preview XSS:** render previews as sanitized/plain text instead of raw HTML
- [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) — **Payment replay:** unique `payment_external_id` index prevents duplicate Stripe/PayPal processing
- [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) — **Info leak:** route PHPMailer SMTP debug to the CI log via `sanitize_for_logging()` (no longer breaks AJAX)
- [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) — **EXIF:** optional metadata stripping from uploads (`SEC_STRIP_EXIF_FROM_IMAGES`, off by default)
- [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) — **Session & infrastructure hardening:** `cookie_httponly=true`, default `X-Frame-Options: SAMEORIGIN`, session-fixation fix, log-injection sanitization, `Referrer-Policy` header (details below)
- [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536) — Review follow-ups: `entrypoint.sh` config-injection guard, XSS trait fix, logo URL escaping, custom-template allowlisting, `Mdl_reports` cast fix

### Bug fixes

- [#1426](https://github.com/InvoicePlane/InvoicePlane/pull/1426) — Fix remittance slip / QR-code remittance text ([#1140](https://github.com/InvoicePlane/InvoicePlane/issues/1140))
- [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449) — Fix `quote_templates/public` showing the client on both header and footer
- [#1451](https://github.com/InvoicePlane/InvoicePlane/pull/1451) — Fix QR-code generation for batch invoice processing
- [#1465](https://github.com/InvoicePlane/InvoicePlane/pull/1465) — Simplify redundant conditionals in email template tags
- [#1473](https://github.com/InvoicePlane/InvoicePlane/pull/1473) — Fix undefined-array-key error for mPDF footer names on PHP 8.3+ ([#1180](https://github.com/InvoicePlane/InvoicePlane/issues/1180))
- [#1478](https://github.com/InvoicePlane/InvoicePlane/pull/1478) — Add missing `attachment()` method to the guest `Get` controller

### Features & improvements

- [#1289](https://github.com/InvoicePlane/InvoicePlane/pull/1289) — Re-introduce Stripe & PayPal; add PayPal Advanced Credit Cards and Venmo support ([#1288](https://github.com/InvoicePlane/InvoicePlane/issues/1288))
- [#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489) — Allow configuring the QR-code size on invoices ([#1376](https://github.com/InvoicePlane/InvoicePlane/issues/1376))

### Performance & code quality

- [#1483](https://github.com/InvoicePlane/InvoicePlane/pull/1483) — Batch settings save: reduce DB queries from ~70 to 3 (transaction-wrapped)
- [#1503](https://github.com/InvoicePlane/InvoicePlane/pull/1503) — Add database indexes on frequently queried columns

### Infrastructure & CI

- [#1466](https://github.com/InvoicePlane/InvoicePlane/pull/1466) — Move the setup-php-composer composite action into `.github/actions/`
- [#1490](https://github.com/InvoicePlane/InvoicePlane/pull/1490) — Add a Laravel Pint code-style check
- [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509) — Add the Docker application container (combined web server + PHP)
- [#1529](https://github.com/InvoicePlane/InvoicePlane/pull/1529) — Document the arbitrary file-deletion vulnerability for CVE allocation

---

### Security — session & infrastructure hardening ([#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567))

**HIGH: Fixed XSS session-hijack vector — `cookie_httponly` was `false` (CWE-1004)**

The session cookie was accessible to JavaScript because `cookie_httponly` was hardcoded to
`false` in `application/config/config.php`. Any XSS vulnerability in the application —
present or future — could be used to steal the authenticated session cookie and fully
impersonate the victim.

**Vulnerability Details:**
- **CWE-1004:** Sensitive Cookie Without 'HttpOnly' Flag
- **Attack Vector:** Any reflected or stored XSS in the application
- **Impact:** Complete session takeover; attacker gains full admin access as the victim

**Root Cause:**
1. `$config['cookie_httponly']` was hardcoded to `false` in the committed config file
2. No environment-variable override existed — the value could not be corrected without
   editing source code

**Fix Implementation:**
- **Hardcoded `cookie_httponly` to `true`** in `application/config/config.php`
  - Value is intentionally not configurable — there is no legitimate use case for making
    session cookies readable by JavaScript
  - Closes the primary vector for XSS-based session hijacking application-wide

**Defense-in-Depth Layers:**
1. HttpOnly flag — prevents JS from reading the cookie even when XSS is present
2. Existing `XSS_Protection_Trait` input filtering — reduces likelihood of XSS reaching the browser
3. CSRF tokens on every state-changing form — prevents forged requests even without session theft
4. `cookie_secure` option — forces HTTPS-only transmission when `COOKIE_SECURE=true`

**Files Changed:**
- `application/config/config.php` — `cookie_httponly` set unconditionally to `true`

**Impact:**
- **Before:** Any XSS vulnerability, however minor, led to immediate session compromise
- **After:** Session cookies are invisible to JavaScript; XSS can no longer steal sessions

**Affected Versions:** All InvoicePlane versions prior to this fix  
**Recommended Action:** **UPGRADE IMMEDIATELY**

---

**MEDIUM: Fixed Clickjacking — `X-Frame-Options` was not sent by default (CWE-1021)**

`X-Frame-Options` was only emitted when the `X_FRAME_OPTIONS` environment variable was
explicitly set. In the default configuration (no variable set), the header was absent,
leaving every admin page embeddable inside a cross-origin `<iframe>` and vulnerable to
clickjacking attacks.

**Vulnerability Details:**
- **CWE-1021:** Improper Restriction of Rendered UI Layers or Frames
- **Attack Vector:** An attacker hosts a page that frames an InvoicePlane admin URL;
  the victim clicks an invisible button that performs an admin action
- **Impact:** Unauthorized actions performed silently in the victim's authenticated session

**Root Cause:**
1. `setCacheHeaders()` in `Admin_Controller` only sent the header when the env var was present
2. New deployments that did not set `X_FRAME_OPTIONS` were unprotected out of the box

**Fix Implementation:**
- **`X-Frame-Options` is now always sent**, defaulting to `SAMEORIGIN`
  - `env('X_FRAME_OPTIONS', 'SAMEORIGIN')` — safe default applied if env var is absent
  - `SAMEORIGIN` blocks cross-origin framing while permitting same-domain iframes

**Files Changed:**
- `application/core/Admin_Controller.php` — `setCacheHeaders()` always sends `X-Frame-Options`

**Impact:**
- **Before:** Default deployments had no clickjacking protection
- **After:** All admin pages send `X-Frame-Options: SAMEORIGIN` by default

**Affected Versions:** All InvoicePlane versions prior to this fix  
**Recommended Action:** Upgrade; if you need a different policy, set `X_FRAME_OPTIONS` in `ipconfig.php`

---

**MEDIUM: Fixed Session Fixation — `SESS_REGENERATE_DESTROY` defaulted to `false` (CWE-384)**

When CodeIgniter regenerates the session ID (every 300 seconds by default), the old session
file was kept on disk rather than destroyed. An attacker who obtained the old session ID —
before regeneration — retained a valid session indefinitely.

**Vulnerability Details:**
- **CWE-384:** Session Fixation
- **Attack Vector:** Attacker captures a session ID (e.g., via network sniffing on HTTP,
  or through an application vulnerability) before it is regenerated
- **Impact:** Attacker retains authenticated access even after session regeneration

**Root Cause:**
1. `SESS_REGENERATE_DESTROY` env var defaulted to `false`
2. Old session files accumulated in the session store and remained valid

**Fix Implementation:**
- **`SESS_REGENERATE_DESTROY` now defaults to `true`**
  - `env_bool('SESS_REGENERATE_DESTROY', true)` — destroy-on-regen is opt-out, not opt-in
  - Old session token is invalidated the moment the new one is issued

**Files Changed:**
- `application/config/config.php` — `sess_regenerate_destroy` default changed to `true`
- `ipconfig.php.example` — documents the new default

**Impact:**
- **Before:** Old session files remained valid after regeneration; fixation window was unlimited
- **After:** Old session is destroyed on regeneration; fixation window is eliminated

**Affected Versions:** All InvoicePlane versions prior to this fix  
**Recommended Action:** Upgrade; existing sessions are unaffected (they regenerate on next request)

---

**LOW: Fixed Log Injection in password-reset flow — token and email logged verbatim (CWE-117)**

The password-reset controller logged the raw token string and the raw email address in
`log_message()` calls. Because these values are controlled by the requester, an attacker
could inject newline characters (`\r\n`) and fake additional log lines, corrupting log
integrity and potentially evading intrusion-detection rules.

**Vulnerability Details:**
- **CWE-117:** Improper Output Neutralization for Logs
- **Attack Vector:** Attacker sends a password-reset request with a crafted email or
  manipulates the token value
- **Impact:** False log entries injected; log analysis tools produce misleading results

**Root Cause:**
1. `log_message('error', '... token: ' . $token)` — raw token in log
2. `log_message('warning', '... for: ' . $email)` — raw email in log

**Fix Implementation:**
- **All sensitive values replaced with SHA-256 hashes before logging**
  - `hash('sha256', $token)` — token identity traceable without exposing the secret
  - `hash('sha256', $email)` — email identity correlatable without exposing PII
  - Consistent with `hash_for_logging()` pattern from `file_security_helper.php`

**Files Changed:**
- `application/modules/sessions/controllers/Sessions.php` — token and email hashed before `log_message()`

**Impact:**
- **Before:** Log files contained plaintext tokens (guessable from logs) and raw email addresses (PII leak)
- **After:** Logs contain only SHA-256 hashes; correlation is preserved without exposing secrets

**Affected Versions:** All InvoicePlane versions prior to this fix  
**Recommended Action:** Rotate any password-reset tokens issued before this fix

---

**MEDIUM: Fixed Open Redirect via raw `$_SERVER['HTTP_REFERER']` — CWE-601**

Two locations used `$_SERVER['HTTP_REFERER']` directly as a redirect target without
validating that the URL belonged to the same application domain. An attacker could craft a
link that, after form submission, redirected the victim to an attacker-controlled phishing
site.

**Vulnerable Locations:**
1. `application/modules/custom_fields/views/form.php` — raw referer used to pre-select the
   custom-field table dropdown
2. `application/helpers/mailer_helper.php` — `check_mail_errors()` redirected to the raw referer
   on mail failure

**Fix Implementation:**
- **Replaced raw `HTTP_REFERER` with `get_safe_referer()`** from `security_helper.php`
  - Validates the referer URL belongs to the application base URL
  - External URLs are silently replaced with `base_url()` as a safe fallback
  - No user-visible change for legitimate navigations

- **Removed referer from the view entirely** (`custom_fields/views/form.php`)
  - Default table is now derived server-side in the controller from the validated safe referer
  - View receives a `$custom_field_default_table` variable — no JS or PHP reads `HTTP_REFERER`

**Files Changed:**
- `application/helpers/mailer_helper.php` — `check_mail_errors()` uses `get_safe_referer()`
- `application/modules/custom_fields/controllers/Custom_fields.php` — derives default table server-side
- `application/modules/custom_fields/views/form.php` — reads `$custom_field_default_table` from controller

**Impact:**
- **Before:** Mail-failure redirects and custom-field links could redirect victims off-site
- **After:** All redirect URLs validated against the application base URL; off-domain URLs rejected

**Affected Versions:** All InvoicePlane versions prior to this fix  
**Recommended Action:** Upgrade

---

### Fixed

- **Session config bug:** `sess_table_name` and `sess_cookie_name` both incorrectly read from
  the `SESS_DRIVER` environment variable instead of their own dedicated `SESS_TABLE_NAME` and
  `SESS_COOKIE_NAME` variables. This made it impossible to rename the session cookie or table
  without also changing the driver name.
- **`not_configured.php`:** `<form method="post">` was present without a CSRF field or a
  submit button (the form cannot be submitted, but the missing CSRF field violated the
  project's security rules). `<?php _csrf_field(); ?>` has been added.
- [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) — **Stored XSS:** comprehensive, application-wide output escaping (32 findings across 17 view files in 4 modules).
- [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) — **IDOR + CSRF:** guest quote approve/reject and payment gateways now enforce client scoping and require POST.
- [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) — **Weak PRNG:** replace password-reset token generation with `random_bytes(32)` (256-bit entropy).
- [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) — **SQL/DDL injection:** harden the tax-rate decimal-places setting with strict integer validation and a transaction wrap ([#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479)).
- [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) — **Config injection:** block newline/control-character injection in the database setup wizard.
- [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) — **Arbitrary file deletion:** path-traversal guards and directory confinement for logo deletion.
- [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) — **Credential exposure:** Stripe/PayPal API keys are no longer rendered into HTML source.
- [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) — **Auth bypass:** guest invoice/payment endpoints gain a `guest_visible()` filter (no draft-invoice access).
- [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) — **Setup wizard:** locked after install (`SETUP_COMPLETED`), with an admin security-warning system.
- [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) — **SSRF:** sanitize PDF footer content before mPDF rendering.
- [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) — **Email preview XSS:** render previews as sanitized/plain text instead of raw HTML.
- [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) — **Payment replay:** a unique `payment_external_id` index prevents duplicate Stripe/PayPal processing.
- [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) — **Info leak:** route PHPMailer SMTP debug to the CI log via `sanitize_for_logging()` (no longer breaks AJAX).
- [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) — **EXIF:** optional metadata stripping from uploads (`SEC_STRIP_EXIF_FROM_IMAGES`, off by default).
- [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) — **Session & infrastructure hardening:** `cookie_httponly=true`, default `X-Frame-Options: SAMEORIGIN`, session-fixation fix (`SESS_REGENERATE_DESTROY=true`), password-reset log-injection sanitization, and a `Referrer-Policy` header.
- [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536) — Review follow-ups: `entrypoint.sh` config-injection guard, XSS trait fix, logo URL escaping, custom-template allowlisting, `Mdl_reports` cast fix.

### Added

- **`CLAUDE.md`** — quick-start guide for AI coding agents and new contributors: CI3 mental model, non-negotiable security rules, key helper functions, code style, testing commands, and common pitfalls.
- Security helpers, each a single source of truth for one concern: `sanitize_for_logging()` (log-injection prevention, CWE-117), `validate_safe_filename()` / `validate_file_in_directory()` (path-traversal prevention, CWE-22), `get_safe_referer()` (open-redirect-safe referer resolution, CWE-601), `verify_csrf_token()` (timing-safe CSRF verification), and `generate_secure_token()` / `generate_password_reset_token()` (CSPRNG-based tokens).
- `validate_template_name()` — 7-layer template validation (empty check, path traversal, type, scope, static whitelist, character set, logging).
- Rate limiting for password-reset requests — per IP (`PASSWORD_RESET_IP_MAX_ATTEMPTS`, `PASSWORD_RESET_IP_WINDOW_MINUTES`) and per email (`PASSWORD_RESET_EMAIL_MAX_ATTEMPTS`, `PASSWORD_RESET_EMAIL_WINDOW_HOURS`).
- `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` env var (default `15`) and `SEC_STRIP_EXIF_FROM_IMAGES` env var (default `false`).
- Session env vars `SESS_SAVE_PATH`, `SESS_TABLE_NAME`, `SESS_COOKIE_NAME`, and `SESS_REGENERATE_DESTROY`, all documented in `ipconfig.php.example`. `SESS_SAVE_PATH` may point outside the document root for additional security; it still defaults to PHP's system temp directory.
- Allowlist-based custom template discovery via `CUSTOM_TEMPLATES_FOLDER` plus the explicit `CUSTOM_INVOICE_TEMPLATES_PDF`, `CUSTOM_INVOICE_TEMPLATES_PUBLIC`, `CUSTOM_QUOTE_TEMPLATES_PDF`, and `CUSTOM_QUOTE_TEMPLATES_PUBLIC` env vars. Names containing spaces or hyphens must be quoted, e.g. `CUSTOM_INVOICE_TEMPLATES_PDF="Corporate - Modern,My Template"`.
- `Referrer-Policy: strict-origin-when-cross-origin` header sent on every admin response.

### Changed

- Re-introduced Stripe & PayPal, adding PayPal Advanced Credit Cards and Venmo support ([#1289](https://github.com/InvoicePlane/InvoicePlane/pull/1289), [#1288](https://github.com/InvoicePlane/InvoicePlane/issues/1288)).
- The QR-code size on invoices is now configurable ([#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489), [#1376](https://github.com/InvoicePlane/InvoicePlane/issues/1376)).
- The email-template live preview now displays the raw template source as plain text instead of rendering it as HTML, eliminating a DOM-based XSS vector.
- XSS input sanitisation now covers deeply nested POST arrays, with full field-path logging.
- **Breaking:** `phpmail_send()` now returns the actual `bool` result of the send instead of always returning `true`. Any integration that treated the return value as always-truthy must handle `false` as a delivery failure.

### Fixed

- [#1426](https://github.com/InvoicePlane/InvoicePlane/pull/1426) — Remittance slip / QR-code remittance text ([#1140](https://github.com/InvoicePlane/InvoicePlane/issues/1140)).
- [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449) — `quote_templates/public` showing the client on both header and footer.
- [#1451](https://github.com/InvoicePlane/InvoicePlane/pull/1451) — QR-code generation for batch invoice processing.
- [#1465](https://github.com/InvoicePlane/InvoicePlane/pull/1465) — Simplify redundant conditionals in email template tags.
- [#1473](https://github.com/InvoicePlane/InvoicePlane/pull/1473) — Undefined-array-key error for mPDF footer names on PHP 8.3+ ([#1180](https://github.com/InvoicePlane/InvoicePlane/issues/1180)).
- [#1478](https://github.com/InvoicePlane/InvoicePlane/pull/1478) — Add the missing `attachment()` method to the guest `Get` controller.
- Session config bug: `sess_table_name` and `sess_cookie_name` incorrectly read from the `SESS_DRIVER` env var instead of their own `SESS_TABLE_NAME` / `SESS_COOKIE_NAME` variables, making it impossible to rename the session cookie or table without also changing the driver name.
- `not_configured.php`: added the missing `<?php _csrf_field(); ?>` to a `<form method="post">` that violated the project's CSRF rule.
- `Cryptor::decryptString()`: use byte-safe `strlen()` / `substr()` instead of `mb_strlen()` / `mb_substr()` when splitting binary ciphertext, fixing intermittent decryption failures under multibyte internal encodings.

### Removed

- SVG logo upload support — SVGs can carry embedded JavaScript (an XSS vector). Convert existing logos to PNG, JPG, or GIF (see [SVG_FILES.md](security/SVG_FILES.md)).
- Dynamic filesystem scanning for template discovery — replaced with static allowlist constants (RCE prevention).

### Performance

- [#1483](https://github.com/InvoicePlane/InvoicePlane/pull/1483) — Batch settings save: reduce DB queries from ~70 to 3 (transaction-wrapped).
- [#1503](https://github.com/InvoicePlane/InvoicePlane/pull/1503) — Add database indexes on frequently queried columns.

### Infrastructure & CI

- [#1466](https://github.com/InvoicePlane/InvoicePlane/pull/1466) — Move the setup-php-composer composite action into `.github/actions/`.
- [#1490](https://github.com/InvoicePlane/InvoicePlane/pull/1490) — Add a Laravel Pint code-style check.
- [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509) — Add the Docker application container (combined web server + PHP).
- [#1529](https://github.com/InvoicePlane/InvoicePlane/pull/1529) — Document the arbitrary file-deletion vulnerability for CVE allocation.
- Declared explicit `permissions: contents: read` in the `phpunit.yml`, `quickstart.yml`, and `setup.yml` workflows (CWE-272).

---

## [1.7.0] — 2024

### Added

- PHP 8.2+ compatibility (minimum PHP 8.1 required; PHP 7.x no longer supported).
- Updated all Composer and Yarn dependencies.

### Security

- **Critical — Local File Inclusion (LFI) in PDF template handling ([#1433](https://github.com/InvoicePlane/InvoicePlane/issues/1433)):** invoice and quote template parameters are now validated before use, blocking directory traversal through template selection, with security logging added for template operations.
- **Critical — Cross-Site Scripting (XSS):** quote/invoice numbers, tax-rate names, payment-method names, custom-field labels, client addresses, SUMEX observations, and quote notes are now escaped in all templates and views; email templates use proper HTML escaping throughout.
- **High — Log poisoning in the file-upload controller:** file names are sanitized before logging to prevent control-character injection.
- **High — SVG logo files blocked:** SVGs can contain embedded JavaScript; convert to PNG, JPG, or GIF instead.

### Fixed

- [#1388](https://github.com/InvoicePlane/InvoicePlane/issues/1388), [#1387](https://github.com/InvoicePlane/InvoicePlane/issues/1387) — Unsafe jQuery plugin vulnerabilities.
- [#1389](https://github.com/InvoicePlane/InvoicePlane/issues/1389) — Missing workflow permissions in GitHub Actions.
- [#1383](https://github.com/InvoicePlane/InvoicePlane/issues/1383) — File access vulnerabilities across controllers.
- [#1381](https://github.com/InvoicePlane/InvoicePlane/issues/1381) — Version checking and logging for `client_einvoicing` fields.
- [#1380](https://github.com/InvoicePlane/InvoicePlane/issues/1380) — Dependency: `qs` package bump.
- [#1377](https://github.com/InvoicePlane/InvoicePlane/issues/1377) — QR code image width reduced to 100 px.
- [#1375](https://github.com/InvoicePlane/InvoicePlane/issues/1375) — Email address verification now accepts both comma and semicolon separators.
- [#1373](https://github.com/InvoicePlane/InvoicePlane/issues/1373) — Removed deprecated library dependencies.
- [#1367](https://github.com/InvoicePlane/InvoicePlane/issues/1367), [#1368](https://github.com/InvoicePlane/InvoicePlane/issues/1368) — Various bug fixes.

### Removed

- PHP 7.x compatibility.
- Deprecated library dependencies.

---

## [1.6.4] and earlier

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please report it privately by opening a
[GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/new)
before disclosing it publicly. See [SECURITY.md](../SECURITY.md) for the full policy.
