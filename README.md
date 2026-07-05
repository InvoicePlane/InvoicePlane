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
[![Contribution Guide](https://img.shields.io/badge/Development%3A-Contribution%20Guide-429ae1.svg)](.github/CONTRIBUTING.md)

</div>

---

## What's New in Version 1.7.2

**InvoicePlane 1.7.2 is a security-focused release.** It resolves every vulnerability
responsibly disclosed against v1.7.0 / v1.7.1 — including a critical (CVSSv3 9.9) Remote Code
Execution issue — and hardens the application, session handling, and container tooling throughout.

**If you run v1.7.0 or v1.7.1, upgrade immediately.**

Highlights:

- **Remote Code Execution (Critical):** the invoice/quote template system never scans the
  filesystem. Built-in templates come from a static allowlist; custom templates are opt-in via
  `ipconfig.php` (see [Custom Invoice & Quote Templates](#custom-invoice--quote-templates)).
- **Arbitrary file deletion, path traversal, SSRF, SQL/DDL injection, IDOR/CSRF, auth bypass** —
  fixed across guest, settings, setup, and payment flows.
- **Password reset hardening:** cryptographically secure tokens (`random_bytes(32)`) with a
  configurable expiry (default 15 minutes).
- **Session & transport hardening:** `cookie_httponly` is always `true`, `X-Frame-Options`
  defaults to `SAMEORIGIN`, session fixation is closed, and a `Referrer-Policy` header is sent.

> **Full details:** the [CHANGELOG](.github/CHANGELOG.md#172---2026-04-06) contains the complete,
> GHSA-linked vulnerability table (CWE, CVSS, reporter, and fixing PR for every issue) and the
> categorized list of all changes. Formal advisories live in
> [`.github/security/`](.github/security/). For step-by-step upgrade instructions see
> [UPGRADE.md](.github/docs/UPGRADE.md).

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

For a detailed installation guide, see [INSTALLATION.md](.github/docs/INSTALLATION.md).

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

Session files are stored in PHP's default session save path (`sys_get_temp_dir()`) unless
overridden. Set `SESS_SAVE_PATH` in `ipconfig.php` to an absolute path to store sessions
elsewhere, e.g. outside the document root for additional security:

```
SESS_SAVE_PATH=/var/lib/invoiceplane/sessions
```

If you mount a volume in Docker, include the configured path in your persistent volumes
(see [Container Deployment](#container-deployment) below).

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
| `SESS_SAVE_PATH` | PHP's `sys_get_temp_dir()` | Directory for session files. Set to an absolute path outside the document root for extra security. |
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
- **Translate:** Help translate InvoicePlane — see [TRANSLATIONS.md](.github/TRANSLATIONS.md).

For contribution guidelines, see [CONTRIBUTING.md](.github/CONTRIBUTING.md).

### Developer Resources

- **[Development Guidelines](.junie/guidelines.md)** — Security patterns and code review checklist
- **[Agent / AI Instructions](AGENTS.md)** — Guide for AI coding assistants working on this codebase
- **[Copilot Instructions](.github/copilot-instructions.md)** — GitHub Copilot context
- **[Docker Setup](resources/docker/README.md)** — Docker configuration guide

---

## Security Vulnerabilities

If you discover a security vulnerability, please report it privately by opening a
[GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/new)
before disclosing it publicly. See our [Security Policy](SECURITY.md) for the full process. Past
advisories are published in [`.github/security/`](.github/security/).

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
