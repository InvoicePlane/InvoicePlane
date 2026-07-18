<img align="right" alt="InvoicePlane logo" src="/assets/core/img/favicon.png">

# _InvoicePlane_

_A libre self-hosted web application designed to help you manage invoices, clients, and payments efficiently._

[![Curent version](https://img.shields.io/badge/dynamic/json.svg?label=Current%20Version&url=https%3A%2F%2Fapi.github.com%2Frepos%2FInvoicePlane%2FInvoicePlane%2Freleases%2Flatest&query=%24.name&colorB=%23429ae1)](https://www.invoiceplane.com/)
[![Downloads](https://img.shields.io/github/downloads/invoiceplane/invoiceplane/total?colorB=%23429ae1)](https://www.invoiceplane.com/)
[![Translation](https://img.shields.io/badge/Translations-%40%20Crowdin-429ae1)](https://translations.invoiceplane.com/project/fusioninvoice)


[![Discord](https://img.shields.io/badge/Chat%3A-Discord-5865F2.svg?logo=discord&logoColor=white)](https://discord.gg/PPzD2hTrXt)
[![Wiki](https://img.shields.io/badge/Help%3A-Official%20Wiki-429ae1.svg)](https://wiki.invoiceplane.com/)
[![Community Forums](https://img.shields.io/badge/Help%3A-Community%20Forums-429ae1.svg)](https://community.invoiceplane.com/)
[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://github.com/invoiceplane/invoiceplane/issues/)
[![Contribution Guide](https://img.shields.io/badge/Development%3A-Contribution%20Guide-429ae1.svg)](.github/CONTRIBUTING.md)

---

## Release Notes

Every release is documented in [CHANGELOG.md](.github/CHANGELOG.md). Downloadable packages and the
per-version release notes are published on the
[GitHub Releases](https://github.com/InvoicePlane/InvoicePlane/releases) page. Formal security
advisories live in [`.github/security/`](.github/security/); step-by-step upgrade instructions
are in [UPGRADE.md](.github/docs/UPGRADE.md).

> **Security releases matter here.** Check the CHANGELOG before upgrading, and subscribe to
> [GitHub Releases](https://github.com/InvoicePlane/InvoicePlane/releases) notifications so you
> don't miss one.

---

## Key Features

- **Invoice & Quote Management:** Effortlessly create, send, and manage professional invoices and quotes.
- **Client Management:** Maintain detailed client records, including contact information and transaction history.
- **Product Management:** Maintain products to add to your Invoices.
- **Project & Tasks Management:** Maintain tasks to add to your Invoices.
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

# Build and start the app + database — dependencies, assets, and
# configuration are all handled inside the container.
docker compose up -d --build

# Access the application
# InvoicePlane: http://localhost:4895
```

See [Installation instructions](.github/docs/INSTALLATION.md)

> `compose.yml` is for local development/testing only — it ships a fixed `ENCRYPTION_KEY` and
> database password (uploads, storage, and the database do persist across restarts via named
> volumes). For a real deployment, see
> [Container (Docker) Deployment Instructions](.github/docs/CONTAINER_DEPLOYMENT.md) for the environment variables
> to set instead.

### Production Installation

1. Download the latest release from the [InvoicePlane website](https://www.invoiceplane.com/).
2. Extract and upload the files to your web server.
3. Copy `ipconfig.php.example` to `ipconfig.php` and set your base URL and database credentials.
4. Navigate to `http://your-domain.com/index.php/setup` to run the installer.

For a detailed installation guide, see [Installation instructions](.github/docs/INSTALLATION.md).

---

## Removing `index.php` from URLs

To remove `index.php` from your URLs:

1. Enable `mod_rewrite` on your web server.
2. Set `REMOVE_INDEXPHP=true` in `ipconfig.php`.
3. Rename the `htaccess` file in the root directory to `.htaccess`.

> **Note:** If you experience issues after making these changes, revert to the default settings by undoing the steps above.

---

## Custom Invoice & Quote Templates

Since version 1.7.2, **custom template names** are added through an **allowlist** in `ipconfig.php` —
the filesystem is never scanned, which is what keeps the template system safe from remote code
execution. See [CUSTOM_TEMPLATES.md](.github/docs/CUSTOM_TEMPLATES.md) for the how-to.

---

## Session Storage

Session files are stored in PHP's default session save path (`sys_get_temp_dir()`) unless
overridden. Set `SESS_SAVE_PATH` in `ipconfig.php` to an absolute path to store sessions
elsewhere, e.g. outside the document root for additional security:

```
SESS_SAVE_PATH=/var/lib/invoiceplane/storage/framework/sessions
```

If you mount a volume in Docker, include the configured path in your persistent volumes
(see [Container (Docker) Deployment Instructions](.github/docs/CONTAINER_DEPLOYMENT.md)).

---

## Container Deployment

A pre-built container image is available, configured entirely through environment variables —
no `ipconfig.php` file needed. The entrypoint generates the configuration and runs any pending
database migrations automatically on startup.

See [Container (Docker) Deployment Instructions](.github/docs/CONTAINER_DEPLOYMENT.md) for the full list of
required/optional environment variables, default admin user setup, and persistent volumes.

---

## Community and Support

**[Join our Discord](https://discord.gg/PPzD2hTrXt)** — it's the fastest way to reach users,
developers, and contributors in real time, whether you need help, want to report something, or
are interested in contributing.

Other resources:

- **Discord:** [discord.gg/PPzD2hTrXt](https://discord.gg/PPzD2hTrXt) — real-time chat with the community.
- **Community Forums:** [community.invoiceplane.com](https://community.invoiceplane.com/) — ask questions, share knowledge, and get help from the community.
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

## Security

If you discover a security vulnerability, please report it privately by opening a
[GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/new)
before disclosing it publicly. See our [Security Policy](SECURITY.md) for the full process.

Published advisories and per-version security notes are collected in
[`.github/security/`](.github/security/) and on the
[GitHub Security Advisories](https://github.com/InvoicePlane/InvoicePlane/security/advisories) page.

---

## License & Copyright

InvoicePlane is licensed under the [MIT License](LICENSE.txt).

The **InvoicePlane name** and **logo** are copyrighted by [Kovah.de](https://kovah.de/) and [InvoicePlane.com](https://www.invoiceplane.com/). Usage is restricted. For more information, visit [invoiceplane.com/license-copyright](https://www.invoiceplane.com/license-copyright).
