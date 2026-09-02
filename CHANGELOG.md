# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Security Vulnerability Summary

| Vulnerability | Severity | CVSSv3 | CWE | Security Advisory | Reported By | Fixed In |
|---|---|---|---|---|---|---|
| XSS session hijack via `cookie_httponly=false` | High | 7.4 | CWE-1004 | — | Internal audit | #1567 |
| Clickjacking — `X-Frame-Options` not always sent | Medium | 4.3 | CWE-1021 | — | Internal audit | #1567 |
| Session fixation — `SESS_REGENERATE_DESTROY` defaulted `false` | Medium | 6.8 | CWE-384 | — | Internal audit | #1567 |
| Log injection via password-reset token/email | Low | 3.7 | CWE-117 | — | Internal audit | #1567 |
| Open redirect via raw `$_SERVER['HTTP_REFERER']` | Medium | 6.1 | CWE-601 | — | Internal audit | #1567 |
| Missing `Referrer-Policy` header | Low | — | CWE-116 | — | Internal audit | #1567 |

---

### Security

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
- **Empty `SESS_SAVE_PATH` broke login and the installer.** A bare `SESS_SAVE_PATH=` line in
  `ipconfig.php` (as shipped in `ipconfig.php.example`) is parsed by phpdotenv as a *defined*
  empty string, so `env('SESS_SAVE_PATH', …)` never applied its default. CodeIgniter's
  `Session_files_driver` then ran `ini_set('session.save_path', '')`, overwriting the value
  from `php.ini` / `php-fpm.d` / the vhost; session start did `mkdir('')` and failed, so
  login silently failed and the manual installer stayed stuck on `setup/language`. Documenting
  the key in `ipconfig.php.example` (see *Added*) only clarified intent; it did not stop an
  empty value from reaching the session driver. A new `resolve_session_save_path()` helper
  (`bootstrap/session_path.php`, wired into `application/config/config.php`) now collapses an
  empty, whitespace-only or unset value to `sys_get_temp_dir()` — exactly matching an unset
  one; an explicit path is returned unchanged.
- **`not_configured.php`:** `<form method="post">` was present without a CSRF field or a
  submit button (the form cannot be submitted, but the missing CSRF field violated the
  project's security rules). `<?php _csrf_field(); ?>` has been added.

### Added

- **`CLAUDE.md`** — quick-start guide for AI coding agents and new contributors: CI3 mental
  model, non-negotiable security rules, key helper functions table, code style, testing
  commands, and common pitfalls.
- **`SESS_SAVE_PATH`, `SESS_TABLE_NAME`, `SESS_COOKIE_NAME`, `SESS_REGENERATE_DESTROY`**
  documented in `ipconfig.php.example` with comments. `SESS_SAVE_PATH` can be set to an
  absolute path outside the document root for additional security; leaving it empty now
  reliably falls back to PHP's system temp directory (enforced in code — see *Fixed*).
- **Template allowlist format clarified:** Quote the whole value in `ipconfig.php` when
  template names contain spaces or hyphens:
  ```
  CUSTOM_INVOICE_TEMPLATES_PDF="Corporate - Modern,My Template"
  ```
- **`Referrer-Policy: strict-origin-when-cross-origin`** header sent on every admin response.

---

## [1.7.2] - 2026-04-06

### Security Vulnerability Summary

| Vulnerability | Severity | CVSSv3 | CWE | Security Advisory | Reported By | Fixed In |
|---|---|---|---|---|---|---|
| RCE via template filesystem scan | Critical | 9.9 | CWE-693 | [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) · [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) |
| Broken auth: password-reset tokens never expired | Critical | 9.8 | CWE-640 | [[#1514]: Improper Password Reset Token Expiration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-5r28-6rw3-25c2) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) |
| Arbitrary file deletion via path traversal | High | 7.1 | CWE-22 | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) · [[#1512]: [CVE-2026-40298]: Authenticated Arbitrary File Deletion via Path Traversal in "Invoice Logo" Setting](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-65v2-4g37-rxjw), [[#1510]: [CVE-2026-39978]: Authenticated path traversal in logo removal allows arbitrary file deletion outside uploads](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-45vj-9p52-f8mq) | [@ali-iltizar](https://github.com/ali-iltizar), [@iiihaiii](https://github.com/iiihaiii) | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) |
| Weak PRNG in password-reset tokens | High | 7.5 | CWE-338 | [[#1494]: Predictable Password Reset Token via md5(time()) Enables Account Takeover](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jfgr-778p-m943) | [@tikket1](https://github.com/tikket1) | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) |
| SQL/DDL injection in tax rate decimal places | High | 8.8 | CWE-89 | [[#1481]: SQL Injection via Unsanitized Tax Rate Decimal Places Field in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x6rh-cr7q-5w7j), [[#1488]: Improper Neutralization of Special Elements used in an SQL Command ('SQL Injection') in invoiceplane/invoiceplane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-34g5-4hfc-g983) | [@tikket1](https://github.com/tikket1), [@udaypali](https://github.com/udaypali) | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) |
| Configuration injection in DB setup wizard | High | 8.8 | CWE-77 | [[#1513]: Configuration Injection in Setup Module Leading to Environment Manipulation (db_hostname Injection)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-ffq5-mw9f-mv6j) | [@akgul7990](https://github.com/akgul7990) | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) |
| IDOR + CSRF on guest quote approve/reject | High | 8.1 | CWE-639, CWE-352 | [[#1482]: Guest Quote Approval/Reject Horizontal Privilege Escalation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-pjf5-c2m5-7m4x), [[#1471]: Guest user IDOR: Quote approve/reject missing client_id scoping](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6xj3-274m-4mvg) | [@HuajiHD](https://github.com/HuajiHD), [@lighthousekeeper1212](https://github.com/lighthousekeeper1212) | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) |
| Auth bypass in guest invoice/payment endpoints | Medium | 6.5 | CWE-284 | [[#1517]: Improper Access Control in Guest Payment Flow Allows Access to Non-Public Invoices](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-f95x-25mh-wcxv) | [@FelipeSilvany](https://github.com/FelipeSilvany) | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) |
| Setup wizard accessible post-installation | Medium | 5.3 | CWE-285 | [[#1491]: Unauthenticated Setup Reconfiguration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-37pr-q48j-46gg), [[#1511]: Unauthenticated Setup Wizard Re-entry Allows Configuration Overwrite in InvoicePlane 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-2j6j-6f6q-57vq), [[#1518]: Unauthenticated Re-execution of Installation Wizard After Setup Allows Overwrite of Database Configuration, Denial of Service, and Potential Data Compromise](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jx5h-6r8f-m2h3) | [@HuajiHD](https://github.com/HuajiHD), [@iiihaiii](https://github.com/iiihaiii), [@kitu232](https://github.com/kitu232) | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) |
| SSRF via PDF footer content | Medium | 6.5 | CWE-918 | [[#1492]: SSRF via admin-stored PDF footer HTML](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vgq9-469p-q7j3) | [@radoi-teodor](https://github.com/radoi-teodor) | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) |
| Open redirect via `HTTP_REFERER` | Medium | 6.1 | CWE-601 | [[#1505]: Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) |
| Payment gateway API credentials in plaintext | Medium | 6.5 | CWE-312 | [[#1515]: Sensitive Data Exposure via HTML Source Code (Stripe & PayPal API Keys)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-8543-x4j8-jj4q) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) |
| PHPMailer debug output in AJAX responses | Low–Medium | 4.3 | CWE-209 | — | Internal audit | [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) |
| Duplicate payment processing | Low–Medium | 5.3 | CWE-362 | [[#1496]: Stripe Callback Replay](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6cpc-hr8h-xgr2) | [@HuajiHD](https://github.com/HuajiHD) | [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) |
| Email template preview XSS | Low–Medium | 5.4 | CWE-79 | [[#1486]: Stored XSS via Email Templates in InvoicePlane <= 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-4wqv-84px-8jc6) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486) |
| EXIF metadata in uploaded images | Low | 3.5 | CWE-212 | [[#1507]: Sensitive Information Disclosure via Unstripped EXIF Metadata in Attachments](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-7f67-2v6p-275v) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) |

> † GHSA advisories are currently private drafts; links become public once CVEs are assigned and the release is final. Researchers listed in [RELEASE_NOTES_v1.7.2_PR_TABLE.md](RELEASE_NOTES_v1.7.2_PR_TABLE.md#contributor-acknowledgements).

---

### Security

**CRITICAL: Fixed Remote Code Execution (RCE) vulnerability in template system - CVSSv3 9.9**

This is a patch bypass of the v1.7.1 LFI fix. The vulnerability allowed authenticated
administrators to achieve Remote Code Execution as any unauthenticated visitor.

**Root Cause:**
The v1.7.1 template validation system used `directory_map()` to dynamically construct the
whitelist at runtime. Because template directories were writable by the web server, attackers
could:
1. Write a PHP webshell to the templates directory
2. Have it automatically added to the "trusted" whitelist on next scan
3. Set it as the active template via admin settings
4. Trigger execution by any unauthenticated visitor accessing a public invoice URL

**Fix Implementation:**
- **Replaced dynamic whitelist with static hardcoded constants** (CWE-693 fix)
  - Template names are now defined in code constants, NEVER scanned from filesystem
  - Even if attacker writes `evil.php` to the templates directory, it will NOT be in the whitelist
  - Primary defense against RCE attacks

- **Enhanced validation with 7 security layers** (defense-in-depth)
  1. Empty/non-string value rejection
  2. Path traversal detection using `validate_safe_filename()`
  3. Type parameter validation (invoice/quote only)
  4. Scope parameter validation (pdf/public only)
  5. Static whitelist validation (CRITICAL — blocks any file not in hardcoded list)
  6. Character validation (alphanumeric, spaces, hyphens, underscores only)
  7. Comprehensive security logging

- **Added file existence verification before template inclusion** (CWE-98 mitigation)
  - Verifies template file exists before including it
  - Falls back to default template if configured template is invalid
  - Shows error if even default template is missing
  - Prevents arbitrary file inclusion even if whitelist is somehow bypassed

- **Added template directory permission checking** (CWE-732 monitoring)
  - New `check_template_directory_permissions()` method in `Mdl_templates`
  - Detects if template directories are writable by web server
  - Allows administrators to audit and fix permission issues

**Defense-in-Depth Layers:**
1. Static whitelist — only explicitly listed templates can be loaded
2. Character allowlist — template names restricted to `[A-Za-z0-9 _-]`
3. Path traversal detection — `../`, `..\\`, null bytes, absolute paths all rejected
4. Type/scope validation — parameters restricted to known values
5. File existence check — template file must exist on disk
6. Directory permission audit — admins alerted when template dirs are world-writable
7. Secure logging — all validation failures logged with hashed values

**Files Changed:**
- `application/modules/invoices/models/Mdl_templates.php` — static whitelist implementation
- `application/helpers/template_helper.php` — enhanced multi-layer validation
- `application/modules/guest/controllers/View.php` — file existence verification

**Impact:**
- **Before:** Attacker with admin access could execute arbitrary PHP code on the server
- **After:** Only templates explicitly defined in code constants can be loaded

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** **UPGRADE IMMEDIATELY** and audit template directories for malicious files

See `SECURITY_ADVISORY_RCE_FIX.md` for full details and verification procedures.

---

**CRITICAL: Fixed Broken Authentication — Password reset tokens never expired**

Password reset tokens were generated and stored indefinitely. An old reset link remained
valid forever, allowing an attacker who obtained a token (e.g., from an email log, forwarded
email, or shoulder-surfing) to reset the victim's password at any future time.

**Vulnerability Details:**
- **CWE-640:** Weak Password Recovery Mechanism for Forgotten Password
- **Attack Vector:** Attacker acquires a valid-but-unused reset token from any source
- **Impact:** Full account takeover; attacker sets a new password of their choosing

**Root Cause:**
1. `user_passwordreset_token` stored in the database with no creation timestamp
2. No expiry check in the reset validation flow
3. Tokens generated with `md5(time() + email + mt_rand())` — weak PRNG (see below)

**Fix Implementation:**
- **Added `user_passwordreset_token_expiry` column** to the `ip_users` table
  - Stores the UTC timestamp when the token was issued
  - Database migration added for existing installations

- **Token expiry enforced on validation**
  - Default window: 15 minutes (`PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` env var)
  - Expired tokens are rejected and cleared from the database

- **Tokens cleared on use or expiry**
  - Successful password change: token deleted immediately
  - Expired check: token deleted to prevent reuse

**Files Changed:**
- `application/modules/sessions/models/Mdl_user_passwordreset.php` — expiry logic
- `application/modules/sessions/controllers/Sessions.php` — expiry validation
- `ipconfig.php.example` — `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` documented

**Impact:**
- **Before:** Reset tokens valid indefinitely; stolen link = permanent account takeover
- **After:** Tokens expire after 15 minutes (configurable); links are useless after expiry

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** **UPGRADE IMMEDIATELY** and run the database migration at `/index.php/setup`

---

**HIGH: Fixed Arbitrary File Deletion via Path Traversal - CVSSv3 7.1 (CWE-22)**

Authenticated administrators could delete arbitrary files on the server through path
traversal sequences in logo filename settings. An attacker could set a malicious logo
filename (e.g., `../../config/database.php`) via the settings page, then trigger deletion
through the `remove_logo` endpoint.

**Vulnerability Details:**
- **CWE-22:** Improper Limitation of a Pathname to a Restricted Directory
- **Attack Vector:** Authenticated administrator could exploit path traversal
- **Impact:** Application failure, data loss, denial of service through deletion of critical files

**Root Cause:**
1. Settings save functionality accepted arbitrary logo filenames without validation
2. The `remove_logo()` function used database values directly for file deletion
3. No path traversal detection or directory confinement checks

**Fix Implementation:**
- **Added input validation on settings save** (`Settings.php` lines 78–87)
  - Logo filenames validated using `validate_safe_filename()` before saving to database
  - Path traversal sequences rejected with error logging
  - Invalid filenames blocked with a user-friendly error message

- **Added type parameter validation** (`Settings.php` lines 272–282)
  - Logo type restricted to allow-list: `['invoice', 'login']`
  - Invalid types logged and rejected
  - Prevents arbitrary type parameter injection

- **Added comprehensive file access validation** (`Settings.php` lines 293–323)
  - Multi-layer validation using `validate_file_access()` helper
  - Files must be within `./uploads/` directory (directory confinement)
  - Path traversal sequences detected and blocked
  - Null byte injection prevented
  - Absolute path attempts rejected
  - All validation failures logged with secure hashing

- **Enhanced file security helper** (`application/helpers/file_security_helper.php`)
  - `validate_safe_filename()` — detects path traversal, null bytes, absolute paths
  - `validate_file_in_directory()` — ensures files stay within the allowed directory
  - `validate_file_access()` — complete file validation with 5 security checks

**Defense-in-Depth Layers:**
1. Input validation — filenames validated on settings save
2. Type validation — logo type restricted to allow-list
3. Path traversal detection — multiple checks for `../`, `..\\`, `/../`, etc.
4. Null byte detection — prevents path truncation attacks
5. Absolute path rejection — blocks `/etc/passwd`-style attacks
6. Directory confinement — files must be inside `uploads/`
7. Secure logging — attack attempts logged with hash (prevents log injection)

**Files Changed:**
- `application/modules/settings/controllers/Settings.php` — input and file access validation
- `application/helpers/file_security_helper.php` — file security validation functions

**Impact:**
- **Before:** Authenticated admin could delete ANY file on the server
- **After:** Only files inside `uploads/` can be deleted, with strict path validation

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** **UPGRADE IMMEDIATELY** and audit systems for suspicious file deletions

See `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md` for full details, proof of concept, and verification procedures.

---

**HIGH: Fixed Weak PRNG in password reset tokens (CWE-338)**

Password reset tokens were generated using `md5(time() + email + mt_rand())`. This is
predictable: `time()` is known to within a few seconds, `mt_rand()` is a non-cryptographic
PRNG seeded from a guessable value, and MD5 is a fast hash. An attacker can brute-force
the token space within a practical time window.

**Fix Implementation:**
- **Replaced with `random_bytes(32)`** — 256 bits of OS-provided cryptographic entropy
- Token stored as hex string (`bin2hex()`) for safe database and URL handling
- Combined with the token-expiry fix (above), the attack window is now 15 minutes
  against a 256-bit random token — computationally infeasible

**Files Changed:**
- `application/helpers/security_helper.php` — `generate_secure_token()` / `generate_password_reset_token()`
- `application/modules/sessions/controllers/Sessions.php` — uses new token generation

**Impact:**
- **Before:** Tokens predictable to an attacker with knowledge of the request timestamp
- **After:** 256-bit random token; brute-force in any realistic time window is impossible

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** Invalidate all existing password-reset tokens after upgrading

---

**HIGH: Fixed SQL/DDL Injection in tax rate decimal places (CWE-89)**

The tax rate decimal places value was interpolated directly into an `ALTER TABLE` SQL statement
without sanitization. An attacker with admin access could supply a crafted value to execute
arbitrary DDL statements on the database.

**Fix Implementation:**
- **Strict integer validation** added before the value is used in the query
- Value cast to `(int)` and range-checked (must be 0–10)
- Invalid values rejected with an error; the `ALTER TABLE` is never executed

**Files Changed:**
- `application/modules/tax_rates/controllers/Tax_rates.php` — strict integer validation

**Impact:**
- **Before:** Arbitrary DDL execution possible by an authenticated admin
- **After:** Only valid integer values in the expected range reach the query

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1

---

**HIGH: Fixed Configuration Injection in database setup wizard (CWE-77)**

User-supplied database credentials entered during the setup wizard were written verbatim to
`ipconfig.php` without escaping special characters. A specially crafted hostname, username,
or password could inject arbitrary configuration directives or PHP code into the config file.

**Fix Implementation:**
- All credential values sanitised before writing to `ipconfig.php`
- Values that cannot be safely represented (contain line-break or control characters)
  are rejected with an error rather than written
- Setup wizard marks `SETUP_COMPLETED=true` on completion to block future access

**Files Changed:**
- `application/modules/setup/controllers/Setup.php` — sanitisation before config write

**Impact:**
- **Before:** Attacker controlling the setup wizard could inject PHP into `ipconfig.php`
- **After:** Only safe, properly escaped values are written to the config file

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1

---

**HIGH: Fixed IDOR + CSRF on guest quote approve/reject endpoints**

The guest quote approve and reject endpoints (`/guest/quotes/approve`, `/guest/quotes/reject`)
accepted any quote ID without verifying that the requesting guest owned the quote, and did
not require a CSRF token. An attacker could approve or reject any quote in the system by
sending a crafted request.

**Fix Implementation:**
- **Ownership check added** — `user_has_quote_access()` called before processing; requests
  for quotes not belonging to the guest's client are rejected with HTTP 403
- **CSRF token required** — `verify_csrf_token()` called on every state-changing request
  to these endpoints

**Files Changed:**
- `application/modules/guest/controllers/Quotes.php` — ownership check and CSRF verification

**Impact:**
- **Before:** Any unauthenticated user could approve or reject any quote by guessing its ID
- **After:** Only the owning guest can approve/reject their own quotes, with CSRF protection

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1

---

**MEDIUM: Fixed Authorization Bypass in guest invoice/payment endpoints (CWE-284)**

Guest users could access invoices and initiate payments for invoices not assigned to their
client accounts by manipulating the invoice ID in the URL.

**Fix Implementation:**
- `user_has_invoice_access()` guard added to all guest invoice and payment endpoints
- Requests for invoices not belonging to the guest's client are rejected with HTTP 403

**Files Changed:**
- `application/modules/guest/controllers/View.php`
- `application/modules/guest/controllers/Payments.php`
- `application/helpers/security_helper.php` — `user_has_invoice_access()` / `user_has_quote_access()`

---

**MEDIUM: Fixed Setup Wizard accessible post-installation (CWE-285)**

The setup wizard remained reachable after installation, allowing an attacker to overwrite
database credentials or re-run the installer on an existing installation.

**Fix Implementation:**
- `SETUP_COMPLETED=true` (set automatically by the wizard on success) blocks all setup routes
- `DISABLE_SETUP=true` provides an additional explicit lockout for environments where the flag
  was not set automatically (existing installations)
- Admin warning displayed in the dashboard if neither flag is set

---

**MEDIUM: Fixed SSRF via PDF footer content (CWE-918)**

User-controlled content in invoice footers could contain HTML that triggered server-side
HTTP requests via mPDF's resource-loading engine (e.g., `<img src="http://attacker.com/...">`)
exposing internal network topology.

**Fix Implementation:**
- Footer content sanitised before being passed to the PDF renderer
- External resource URLs stripped from footer HTML

---

**MEDIUM: Fixed Open Redirect via unvalidated `HTTP_REFERER` (CWE-601)**

Multiple endpoints redirected directly to `$_SERVER['HTTP_REFERER']` without validating that
the URL belonged to the same application origin.

**Fix Implementation:**
- All usages replaced with `get_safe_referer()` from `security_helper.php`
- Referer validated against `base_url()`; external URLs replaced with `base_url()` as default

---

**MEDIUM: Fixed Payment Gateway API Credential Exposure (CWE-312)**

Payment gateway API keys (Stripe, PayPal) were stored in plaintext in the database. Anyone
with read access to the database could extract live API credentials.

**Fix Implementation:**
- Credentials encrypted at rest using `Cryptor` with the application's `ENCRYPTION_KEY`
- Decrypted transparently at runtime; no change to the UI or gateway behaviour

---

**LOW–MEDIUM: Fixed PHPMailer Debug Output in AJAX Responses (CWE-209)**

PHPMailer's SMTP debug output (server banners, authentication strings, error messages) was
appended directly to JSON responses in AJAX contexts, leaking server information to the
browser and potentially to browser extensions or network observers.

**Fix Implementation:**
- Debug output suppressed in AJAX context
- SMTP debug strings passed through `sanitize_for_logging()` before any logging

---

**LOW–MEDIUM: Fixed Duplicate Payment Processing (CWE-362)**

Stripe and PayPal payment callbacks (webhooks) could process the same payment event twice
under network-retry conditions, resulting in duplicate records or duplicate payouts.

**Fix Implementation:**
- Idempotency check added: incoming payment ID verified against existing records before processing
- Duplicate events are acknowledged and discarded without creating new payment records

---

**LOW–MEDIUM: Fixed Email Template Preview XSS (CWE-79)**

The email-template preview endpoint returned the raw template HTML without sanitisation.
Template content containing `<script>` tags or event handlers would execute in the admin's
browser when the preview was opened.

**Fix Implementation:**
- HTML Purifier applied to template body before rendering the preview
- Sanitisation uses `sanitize_email_template_html()` from `html_sanitizer_helper.php`

---

**LOW: EXIF Metadata in Uploaded Images (CWE-212)**

Uploaded images could contain EXIF metadata including GPS coordinates, timestamps, and
camera information, inadvertently exposing client location data or photographer identity.

**Opt-in Fix:**
- Set `SEC_STRIP_EXIF_FROM_IMAGES=true` in `ipconfig.php` to enable automatic EXIF stripping
- Supported formats: JPEG, PNG, GIF, WEBP
- Disabled by default to avoid unexpected image changes in existing workflows

---

**LOW: Fixed Binary Data Corruption in `Cryptor::decryptString()` (CWE-704)**

`mb_strlen()` and `mb_substr()` were used to split raw binary ciphertext (IV + encrypted
data). Under multibyte-string internal encodings, these functions operate on characters
rather than bytes, corrupting the extracted IV and causing intermittent decryption failures.

**Fix Implementation:**
- Replaced with byte-safe `strlen()` and `substr()` throughout `Cryptor`

**Files Changed:**
- `application/libraries/Cryptor.php`

---

**LOW: Restricted GitHub Actions `GITHUB_TOKEN` permissions (CWE-272)**

The `phpunit.yml`, `quickstart.yml`, and `setup.yml` workflows did not declare explicit
`permissions` blocks, granting the default broad token scope to all workflow jobs.

**Fix Implementation:**
- Added `permissions: contents: read` at the workflow level in all three files

---

**MEDIUM: Sanitized PHPMailer SMTP debug output — log injection prevention (CWE-117)**

SMTP debug strings (server banners, addresses, status responses) were logged verbatim.
Because these strings can contain data from the remote SMTP server, they could be used
to inject false log entries or control characters.

**Fix Implementation:**
- The `Debugoutput` callback passes all strings through `sanitize_for_logging()` before
  writing to the CI log

---

**MEDIUM: Fixed `phpmail_send()` always returning `true` on failure (CWE-252)**

`phpmail_send()` always returned `true` regardless of whether the underlying `$mail->send()`
call succeeded. This masked delivery failures from callers, causing "Email sent successfully"
log lines to appear inside the failure branch.

**Fix Implementation:**
- Function now returns the actual `bool` result of `$mail->send()`
- Success log message moved to the success branch
- Failure branch sets flash error data only

> **Breaking change for custom integrations:** Any code that called `phpmail_send()` and
> treated the return value as always-truthy must be updated. Handle `false` as a send failure.

---

### Added

- `sanitize_for_logging()` helper — single source of truth for log injection prevention (CWE-117)
- `validate_safe_filename()` and `validate_file_in_directory()` helpers — path traversal prevention (CWE-22)
- `get_safe_referer()` helper — open-redirect-safe referer resolution (CWE-601)
- `verify_csrf_token()` helper — timing-safe CSRF verification
- `user_has_invoice_access()` / `user_has_quote_access()` helpers — IDOR guards
- `validate_template_name()` — 7-layer template validation (empty check, path traversal, type, scope, static whitelist, character set, logging)
- `generate_secure_token()` / `generate_password_reset_token()` — CSPRNG-based token generation
- XSS sanitisation now covers deeply nested POST arrays, with full field-path logging
- Rate limiting for password reset requests — per IP (`PASSWORD_RESET_IP_MAX_ATTEMPTS`, `PASSWORD_RESET_IP_WINDOW_MINUTES`) and per email (`PASSWORD_RESET_EMAIL_MAX_ATTEMPTS`, `PASSWORD_RESET_EMAIL_WINDOW_HOURS`)
- `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` env var (default `15`)
- `SEC_STRIP_EXIF_FROM_IMAGES` env var (default `false`)
- Custom template discovery via `CUSTOM_TEMPLATES_FOLDER` — templates placed in that directory appear alongside built-in templates in admin selectors. Template names are validated against the allowlist before use (RCE fix preserved).
- `CUSTOM_INVOICE_TEMPLATES_PDF`, `CUSTOM_INVOICE_TEMPLATES_PUBLIC`, `CUSTOM_QUOTE_TEMPLATES_PDF`, `CUSTOM_QUOTE_TEMPLATES_PUBLIC` env vars for explicit template allowlists

### Changed

- **Email template preview** — the live preview panel now displays the raw template source as plain text instead of rendering it as HTML. Eliminates DOM-based XSS risk from user-controlled template content set as `innerHTML` without sanitization.
- **`phpmail_send()` return value** — now returns `false` on delivery failure (previously always `true`). Any code relying on the return value must be updated.

### Removed

- SVG logo upload support — SVGs can contain embedded JavaScript (XSS vector)
- Dynamic filesystem scanning for template discovery — replaced with static allowlist constants (RCE prevention)

### Fixed Issues

- #1433 — LFI vulnerabilities in PDF template handling
- #1388, #1387 — Unsafe jQuery plugin vulnerabilities (Code scanning alerts #8, #10)
- #1389 — Workflow does not contain permissions (Code scanning alert #5)
- #1383 — File access vulnerabilities across multiple controllers
- #1381 — E-invoicing field migration and version checking
- #1380 — Dependency update: `qs` package bump
- #1377 — QR code image width reduced to 100 px
- #1375 — Email address verification now accepts both comma and semicolon separators
- #1373 — Removed deprecated library dependencies
- #1367, #1368 — Various bug fixes
- Code scanning alerts #6, #7, #9, #11, #12, #13, #14, #15 — workflow permissions and XSS patterns

### Fields Sanitized for XSS Protection

The following fields were sanitized and properly escaped to prevent XSS attacks:

- `invoice_number` — escaped in all templates and views
- `quote_number` — escaped in all templates and views
- `tax_rate_name` — sanitized on input, escaped on output
- `payment_method_name` — sanitized on input, escaped on output
- `custom_field_label` — protected in all custom field displays
- Client address fields — sanitized for safe display
- `sumex_observations` — sanitized on input
- `quote_password` — sanitized on input
- `quote_notes` — sanitized on input
- Email template content — HTML Purifier applied before rendering
- File names in upload operations — sanitized before logging (prevents log injection)

---

## [1.7.0] — 2024

### Added

- PHP 8.2+ compatibility (minimum PHP 8.1 required; PHP 7.x no longer supported)
- Updated all Composer and Yarn dependencies

### Security

**CRITICAL: Fixed Local File Inclusion (LFI) vulnerabilities (#1433)**
- Template validation added to PDF generation endpoints
- Invoice and quote template parameters now validated before use
- Prevented directory traversal attacks through template selection
- Added security logging for template operations

**CRITICAL: Fixed Cross-Site Scripting (XSS) vulnerabilities**
- Quote and invoice number fields now properly escaped in all templates
- Tax rate names, payment method names, and custom field labels sanitized
- Client addresses, Sumex observations, quote notes sanitized for display
- Email templates use proper HTML escaping throughout

**HIGH: Fixed log poisoning in file upload controller**
- File names sanitized before logging
- Prevents control character injection in log files

**HIGH: SVG logo files blocked**
- SVGs can contain embedded JavaScript that executes in user browsers
- Blocked entirely; users should convert to PNG, JPG, or GIF

### Fixed Issues

- #1388, #1387 — Unsafe jQuery plugin vulnerabilities
- #1389 — Missing workflow permissions in GitHub Actions
- #1383 — File access vulnerabilities across all controllers
- #1381 — Version checking and logging for `client_einvoicing` fields
- #1380 — Dependency: `qs` package bump
- #1377 — QR code image width reduced to 100 px
- #1375 — Email address verification: comma and semicolon separators
- #1373 — Removed deprecated library dependencies
- #1367, #1368 — Various bug fixes

### Removed

- PHP 7.x compatibility
- Deprecated library dependencies

---

## [1.6.4] and earlier

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly. We will address all security concerns promptly.
