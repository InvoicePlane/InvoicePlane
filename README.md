<img align="right" alt="InvoicePlane logo" src="/assets/core/img/favicon.png">

# _InvoicePlane_

<div align="center">

_A libre self-hosted web application designed to help you manage invoices, clients, and payments efficiently._

<br>

[![Curent version](https://img.shields.io/badge/dynamic/json.svg?label=Current%20Version&url=https%3A%2F%2Fapi.github.com%2Frepos%2FInvoicePlane%2FInvoicePlane%2Freleases%2Flatest&query=%24.name&colorB=%23429ae1)](https://www.invoiceplane.com/)
[![Downloads](https://img.shields.io/github/downloads/invoiceplane/invoiceplane/total?colorB=%23429ae1)](https://www.invoiceplane.com/)
[![Translation](https://img.shields.io/badge/Translations-%40%20Crowdin-429ae1)](https://translations.invoiceplane.com/project/fusioninvoice)

<br>

[![Wiki](https://img.shields.io/badge/Help%3A-Official%20Wiki-429ae1.svg)](https://wiki.invoiceplane.com/)
[![Community Forums](https://img.shields.io/badge/Help%3A-Community%20Forums-429ae1.svg)](https://community.invoiceplane.com/)
[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://github.com/invoiceplane/invoiceplane/issues/)
[![Contribution Guide](https://img.shields.io/badge/Development%3A-Contribution%20Guide-429ae1.svg)](CONTRIBUTING.md)

</div>

---

## What's New in Version 1.7.2

**InvoicePlane 1.7.2** is a security-focused release that closes multiple high-severity vulnerabilities
reported against v1.7.0 and v1.7.1 and hardens the application infrastructure throughout.

### Major Security Improvements

- **Remote Code Execution (RCE) — Critical (CVSSv3 9.9):** The invoice/quote template system
  no longer scans the filesystem at runtime. Template names are controlled by a static allowlist
  in `ipconfig.php`. Any PHP file placed in the templates directory is ignored unless explicitly listed.
- **Arbitrary File Deletion — High (CVSSv3 7.1):** Path traversal sequences in logo filenames are
  now rejected; the delete endpoint validates that files are within the `uploads/` directory before removing them.
- **Password Reset — High:** Tokens now expire after a configurable window (default 15 minutes) and
  are generated with a cryptographically secure PRNG (`random_bytes(32)`).
- **SQL/DDL Injection — High:** The tax rate decimal places setting is now strictly validated before
  being used in database operations.
- **IDOR/CSRF on Quote Endpoints — High:** Guest quote approve/reject endpoints now enforce
  ownership checks and require a valid CSRF token.
- **Session Hardening:** `cookie_httponly` is now `true` (session cookies inaccessible to JavaScript),
  `X-Frame-Options` defaults to `SAMEORIGIN`, and a `Referrer-Policy` header is sent on every response.
- **Log Injection:** All user-controlled values are sanitised before `log_message()` calls.

### Security Vulnerability Summary

| Vulnerability | Severity | CVSSv3 | CWE | Security Advisory | Reported By | Fixed In |
|---|---|---|---|---|---|---|
| RCE via template filesystem scan | Critical | 9.9 | CWE-693 | [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) | via GHSA† | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) |
| Password reset tokens never expired | Critical | 9.8 | CWE-640 | via GHSA† | via GHSA† | [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) |
| Arbitrary file deletion via path traversal | High | 7.1 | CWE-22 | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) | via GHSA† | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) |
| Weak PRNG in password reset tokens | High | 7.5 | CWE-338 | via GHSA† | via GHSA† | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) |
| SQL/DDL injection in tax rate decimal places | High | 8.8 | CWE-89 | via GHSA† | via GHSA† | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) |
| Configuration injection in DB setup wizard | High | 8.8 | CWE-77 | via GHSA† | via GHSA† | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) |
| IDOR + CSRF on guest quote approve/reject | High | 8.1 | CWE-639, CWE-352 | via GHSA† | via GHSA† | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) |
| Auth bypass in guest invoice/payment | Medium | 6.5 | CWE-284 | via GHSA† | via GHSA† | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) |
| Setup wizard accessible post-install | Medium | 5.3 | CWE-285 | via GHSA† | via GHSA† | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) |
| SSRF via PDF footer content | Medium | 6.5 | CWE-918 | via GHSA† | via GHSA† | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) |
| Open redirect via `HTTP_REFERER` | Medium | 6.1 | CWE-601 | via GHSA† | via GHSA† | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) |
| Payment gateway API credentials plaintext | Medium | 6.5 | CWE-312 | via GHSA† | via GHSA† | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) |
| XSS via session cookie theft (`cookie_httponly=false`) | High | 7.4 | CWE-1004 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Clickjacking — `X-Frame-Options` missing | Medium | 4.3 | CWE-1021 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Session fixation | Medium | 6.8 | CWE-384 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Log injection in password reset flow | Low | 3.7 | CWE-117 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Open redirect via `$_SERVER['HTTP_REFERER']` | Medium | 6.1 | CWE-601 | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |

> † Reported via private [GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories).
> Per-vulnerability attribution will be published once CVEs are assigned.
> Researchers acknowledged in [RELEASE_NOTES_v1.7.2_PR_TABLE.md](RELEASE_NOTES_v1.7.2_PR_TABLE.md):
> [@akgul7990](https://github.com/akgul7990), [@ali-iltizar](https://github.com/ali-iltizar), [@Chittu13](https://github.com/Chittu13), [@cyabell](https://github.com/cyabell), [@HuajiHD](https://github.com/HuajiHD), [@iiihaiii](https://github.com/iiihaiii), [@kitu232](https://github.com/kitu232), [@radoi-teodor](https://github.com/radoi-teodor), [@tikket1](https://github.com/tikket1), [@udaypali](https://github.com/udaypali), [@Vijay-raghav7](https://github.com/Vijay-raghav7)

### Issues Fixed in Version 1.7.2

**Security Fixes:**
- [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) — Remote Code Execution (RCE) via template filesystem scan (CVSSv3 9.9)
- [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) — Session hardening: `cookie_httponly`, `X-Frame-Options`, session fixation, log injection,
  open redirect via `HTTP_REFERER`, `Referrer-Policy` header
- [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) — Password reset token expiry (`PASSWORD_RESET_TOKEN_EXPIRY_MINUTES`, default 15 min)
- [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) — CSPRNG password reset token generation (`random_bytes(32)`, 256-bit entropy)
- [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) — IDOR + CSRF on guest quote approve/reject endpoints
- [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) — Authorization bypass in guest invoice/payment endpoints
- [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) — SQL/DDL injection in tax rate decimal places ([#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479))
- [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) — Configuration injection in database setup wizard
- [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) — Payment gateway API credential exposure (Stripe/PayPal keys)
- [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) — Email template preview XSS
- [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) — Arbitrary file deletion via path traversal in logo settings
- [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) — Setup wizard accessible post-installation
- [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) — SSRF via PDF footer content
- [#1433](https://github.com/InvoicePlane/InvoicePlane/pull/1433) — Local File Inclusion (LFI) vulnerabilities in PDF template handling
- [#1388](https://github.com/InvoicePlane/InvoicePlane/pull/1388), [#1387](https://github.com/InvoicePlane/InvoicePlane/pull/1387) — Unsafe jQuery plugin vulnerabilities
- [#1389](https://github.com/InvoicePlane/InvoicePlane/pull/1389) — Missing GitHub Actions workflow permissions
- [#1383](https://github.com/InvoicePlane/InvoicePlane/pull/1383) — File access vulnerabilities across multiple controllers

**Bug Fixes and Improvements:**
- #1381 — E-invoicing field migration and version checking
- #1380 — Dependency update (`qs` package bump)
- #1377 — QR code image width reduced to 100 px
- #1375 — Email address verification now supports comma and semicolon separators
- #1373 — Removed deprecated library dependencies
- #1367, #1368 — Various bug fixes
- Fixed `phpmail_send()` always returning `true` on delivery failure
- Fixed binary data corruption in `Cryptor::decryptString()` (byte-safe `strlen`/`substr`)

### Fields Sanitized for XSS Protection

The following fields were hardened to prevent XSS attacks:
- `invoice_number` and `quote_number` — escaped in all templates and views
- `tax_rate_name` — sanitized on input, escaped on output
- `payment_method_name` — sanitized on input, escaped on output
- `custom_field_label` — protected in all custom field displays
- Client address fields — sanitized for safe display
- `sumex_observations`, `quote_password`, `quote_notes` — sanitized on input
- Email template content — HTML Purifier applied before rendering
- File names in upload operations — sanitized before logging (prevents log injection)

### Upgrading from Version 1.7.0 or 1.7.1

1. **Back up** your database and files
2. Replace all application files with the 1.7.2 release
3. Run the database migration wizard at `/index.php/setup` (adds the `user_passwordreset_token_expiry` column)
4. **Re-register your custom templates** in `ipconfig.php` — the old filesystem scan is gone
   (see [Custom Invoice & Quote Templates](#custom-invoice--quote-templates) below)
5. If you use an SVG logo, convert it to PNG/JPG — SVG uploads remain blocked

### Upgrading from Version 1.6.x

Follow the 1.7.0/1.7.1 upgrade guide first, then apply the 1.7.2 steps above.

For detailed upgrade instructions, visit the [InvoicePlane Wiki](https://wiki.invoiceplane.com/).

> **Full Release Notes:** See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes, security fixes, and improvements.

---

## Key Features

- **Invoice & Quote Management:** Create, send, and manage professional invoices and quotes effortlessly.
- **Client Management:** Maintain detailed client records, including contact information and transaction history.
- **Payment Tracking:** Monitor payments, set up reminders, and integrate with multiple payment gateways.
- **Customization:** Tailor templates, themes, and settings to match your brand preferences.
- **Reporting:** Generate insightful reports to track your financial performance.

---

## Getting Started

### Quick Start with Docker (Recommended for Development)

```bash
# Clone the repository
git clone https://github.com/InvoicePlane/InvoicePlane.git
cd InvoicePlane

# Install dependencies
composer install
yarn install
yarn build

# Configure the application
cp ipconfig.php.example ipconfig.php
# Edit ipconfig.php: set IP_URL and DB_* values

# Start Docker containers (PHP 8.2, MariaDB, nginx, phpMyAdmin)
docker-compose up -d

# Access the application
# InvoicePlane: http://localhost
# phpMyAdmin:   http://localhost:8081
```

### Production Installation

1. Download the latest release from the [InvoicePlane website](https://www.invoiceplane.com/).
2. Extract and upload the files to your web server.
3. Copy `ipconfig.php.example` to `ipconfig.php` and set your base URL and database credentials.
4. Navigate to `http://your-domain.com/index.php/setup` to run the installer.

For a detailed installation guide, see [INSTALLATION.md](INSTALLATION.md).

---

## Removing `index.php` from URLs

To remove `index.php` from your URLs:

1. Enable `mod_rewrite` on your web server.
2. Set `REMOVE_INDEXPHP=true` in `ipconfig.php`.
3. Rename the `htaccess` file in the root directory to `.htaccess`.

> **Note:** If you experience issues after making these changes, revert to the default settings by undoing the steps above.

---

## Custom Invoice & Quote Templates

> **Security note:** The filesystem is never scanned for templates. This prevents Remote Code
> Execution (RCE) attacks where an attacker writes a PHP file to the templates directory and
> then triggers it via the admin panel.

To add a custom template:

1. **Create the template `.php` file** inside `CUSTOM_TEMPLATES_FOLDER` under the appropriate sub-path:
   ```
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/public/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/public/MyTemplate.php
   ```

2. **Add the template name** (without `.php`) to the matching allowlist key in `ipconfig.php`.
   Quote the value when names contain spaces or hyphens:
   ```
   CUSTOM_INVOICE_TEMPLATES_PDF="MyTemplate,Corporate - Modern"
   CUSTOM_INVOICE_TEMPLATES_PUBLIC="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PDF="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PUBLIC="MyTemplate"
   ```
   Template names may only contain letters, digits, spaces, hyphens (`-`), and underscores (`_`).

3. The template will appear in **Settings → Invoice / Quote** once it is listed.

> The built-in template directories are never scanned — only the `CUSTOM_TEMPLATES_FOLDER` is
> searched, and only for names you have explicitly listed. This is the RCE prevention mechanism.

---

## Session Storage

Session files are stored in `storage/framework/sessions/` by default. This directory can be
moved **outside the document root** for additional security — set `SESS_SAVE_PATH` in
`ipconfig.php` to an absolute path:

```
SESS_SAVE_PATH=/var/lib/invoiceplane/sessions
```

The `storage/` directory follows the same convention as Laravel. If you mount a volume in
Docker, include it in your persistent volumes (see [Container Deployment](#container-deployment) below).

---

## Container Deployment

> [!WARNING]
> The container always uses the new (per-item) tax calculation mode.

A pre-built container image is available. Configuration is provided entirely through environment
variables — no `ipconfig.php` file is needed. The entrypoint generates the configuration and
runs any pending database migrations automatically on startup.

### Required environment variables

| Variable | Description |
|---|---|
| `IP_URL` | Public base URL without trailing slash, e.g. `https://invoices.example.com` |
| `DB_HOSTNAME` | Database host |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `DB_DATABASE` | Database name |
| `ENCRYPTION_KEY` | Secret key for encrypted data — generate with `openssl rand -base64 32` |

### Optional environment variables

| Variable | Default | Description |
|---|---|---|
| `DB_PORT` | `3306` | Database port |
| `CI_ENV` | `production` | Set to `development` to show all PHP errors |
| `ENABLE_DEBUG` | `false` | Enable advanced debug logging |
| `SESS_SAVE_PATH` | `storage/framework/sessions` | Directory for session files. Set to an absolute path outside the document root for extra security. |
| `SESS_COOKIE_NAME` | `ip_session` | Session cookie name |
| `SESS_TABLE_NAME` | `ip_sessions` | Session database table name (only used when `SESS_DRIVER=database`) |
| `SESS_EXPIRATION` | `864000` | Session lifetime in seconds (0 = expire on browser close) |
| `SESS_MATCH_IP` | `true` | Tie sessions to the client IP address |
| `SESS_REGENERATE_DESTROY` | `true` | Destroy the old session file on ID regeneration (prevents session fixation) |
| `COOKIE_SECURE` | `false` | Send cookies only over HTTPS — set to `true` on HTTPS-only deployments |
| `X_FRAME_OPTIONS` | `SAMEORIGIN` | Value for the `X-Frame-Options` response header |
| `ENABLE_X_CONTENT_TYPE_OPTIONS` | `true` | Send the `X-Content-Type-Options: nosniff` header |
| `LEGACY_CALCULATION` | `false` | Use the classic (pre-1.6.3) tax/discount calculation mode. Required `false` for valid e-invoice XML. |
| `ENABLE_INVOICE_DELETION` | `false` | Allow invoices to be permanently deleted |
| `DISABLE_READ_ONLY` | `false` | Disable the read-only mode for sent invoices |
| `PASSWORD_RESET_IP_MAX_ATTEMPTS` | `5` | Max password reset requests per IP within the time window |
| `PASSWORD_RESET_IP_WINDOW_MINUTES` | `60` | Time window in minutes for IP-based reset rate limiting |
| `PASSWORD_RESET_EMAIL_MAX_ATTEMPTS` | `3` | Max password reset requests per email within the time window |
| `PASSWORD_RESET_EMAIL_WINDOW_HOURS` | `1` | Time window in hours for email-based reset rate limiting |
| `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` | `15` | Minutes before a password reset link expires |
| `CUSTOM_TEMPLATES_FOLDER` | — | Absolute path to a directory of custom invoice/quote templates. The directory must mirror the built-in structure (`invoice_templates/pdf/`, etc.). |
| `CUSTOM_INVOICE_TEMPLATES_PDF` | — | Comma-separated allowlist of custom PDF invoice template names (without `.php`). Quote the value if names contain spaces: `"My Template,Another"` |
| `CUSTOM_INVOICE_TEMPLATES_PUBLIC` | — | Same, for public/web invoice templates |
| `CUSTOM_QUOTE_TEMPLATES_PDF` | — | Same, for PDF quote templates |
| `CUSTOM_QUOTE_TEMPLATES_PUBLIC` | — | Same, for public/web quote templates |
| `SEC_STRIP_EXIF_FROM_IMAGES` | `false` | Strip EXIF metadata (GPS, timestamps, camera info) from uploaded images |
| `SUMEX_SETTINGS` | `false` | Enable Swiss medical invoice (Sumex) customizations |
| `SUMEX_URL` | — | URL to post Sumex XML to in order to receive a generated PDF |
| `ENCRYPTION_CIPHER` | `AES-256` | Cipher used for encrypted settings |

### Default admin user

On first startup the entrypoint creates an admin account if the database is empty.

| Variable | Default | Description |
|---|---|---|
| `DEFAULT_LANGUAGE` | `english` | Application language (`english`, `german`, `french`, …). Only applied on fresh installs. |
| `DEFAULT_ADMIN_EMAIL` | `admin@localhost` | Email for the default admin account |
| `DEFAULT_ADMIN_PASSWORD` | *(random)* | Password for the default admin account. If unset, a random 24-character password is printed to the container log on first startup. |
| `DEFAULT_ADMIN_NAME` | `admin` | Display name for the default admin account |

> User creation is skipped on every subsequent startup once at least one user exists.

### Persistent volumes

| Path | Contents |
|---|---|
| `/var/www/html/uploads` | Client files, logos, and imported documents |
| `/var/www/html/storage` | Session files, framework cache, and application logs |

---

## Community and Support

Join our community for support, discussions, and contributions:

- **Community Forums:** [community.invoiceplane.com](https://community.invoiceplane.com/) — ask questions, share knowledge, and get help from the community.
- **Discord:** [Join our Discord](https://discord.gg/PPzD2hTrXt) — chat with users, developers, and contributors in real time.
- **Issue Tracker:** [GitHub Issues](https://github.com/InvoicePlane/InvoicePlane/issues) — report bugs and request features.
- **Wiki & Documentation:** [wiki.invoiceplane.com](https://wiki.invoiceplane.com/) — find guides, FAQs, and detailed setup instructions.

> *InvoicePlane is developed and maintained by a dedicated team of volunteers. Support is provided by the community on a best-effort basis.*

---

## Contributing

We welcome contributions from the community! To get involved:

- **Report Issues:** Use the [Issue Tracker](https://github.com/InvoicePlane/InvoicePlane/issues) to report bugs or request features.
- **Submit Pull Requests:** Fork the repository, make your changes, and open a pull request.
- **Translate:** Help translate InvoicePlane — see [TRANSLATIONS.md](TRANSLATIONS.md).

For contribution guidelines, see [CONTRIBUTING.md](CONTRIBUTING.md).

### Developer Resources

- **[Development Guidelines](.junie/guidelines.md)** — Security patterns and code review checklist
- **[Agent / AI Instructions](AGENTS.md)** — Guide for AI coding assistants working on this codebase
- **[Copilot Instructions](.github/copilot-instructions.md)** — GitHub Copilot context
- **[Docker Setup](resources/docker/README.md)** — Docker configuration guide

---

## Security Vulnerabilities

If you discover a security vulnerability, email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly.

### Important Security Notice: SVG Logo Files

**SVG (Scalable Vector Graphics) files are not accepted for logo uploads.**

#### Why are SVG files disabled?

SVG files can contain embedded JavaScript code that could be exploited to perform Cross-Site
Scripting (XSS) attacks. Since InvoicePlane handles sensitive financial data, SVG uploads are
blocked entirely as a proactive security measure.

#### What file formats are supported?

- **PNG** (recommended for logos with transparency)
- **JPG/JPEG** (recommended for photographs)
- **GIF** (recommended for simple graphics)

#### What happens to my existing SVG logo?

If you previously uploaded an SVG logo:
- It will not display in the application (blocked for security)
- A warning message will appear on the settings page
- You can remove it and upload a replacement in a supported format

#### How do I convert my SVG logo?

**Online tools:**
- [CloudConvert](https://cloudconvert.com/svg-to-png)
- [Convertio](https://convertio.co/svg-png/)

**Desktop software:**
- [Inkscape](https://inkscape.org/) (free, open-source) — File → Export PNG Image → set resolution → Export
- Adobe Illustrator
- GIMP

#### Need help?

Visit our [Community Forums](https://community.invoiceplane.com/) for assistance with logo conversion.

---

## License & Copyright

InvoicePlane is licensed under the [MIT License](LICENSE.txt).

The **InvoicePlane name** and **logo** are copyrighted by [Kovah.de](https://kovah.de/) and [InvoicePlane.com](https://www.invoiceplane.com/). Usage is restricted. For more information, visit [invoiceplane.com/license-copyright](https://www.invoiceplane.com/license-copyright).
