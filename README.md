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

## Key Features

- **Invoice & Quote Management:** Create, send, and manage professional invoices and quotes effortlessly.
- **Client Management:** Maintain detailed client records, including contact information and transaction history.
- **Payment Tracking:** Monitor payments, set up reminders, and integrate with multiple payment gateways.
- **Customization:** Tailor templates, themes, and settings to match your brand preferences.
- **Reporting:** Generate insightful reports to track your financial performance.

---

## Getting Started

To get started with InvoicePlane:

1. **Download the Latest Version:**
   - Visit the [InvoicePlane website](https://www.invoiceplane.com/) to download the latest release.

2. **Extract and Upload:**
   - Extract the downloaded package and upload the files to your web server or hosting environment.

3. **Configuration:**
   - Duplicate `ipconfig.php.example` and rename it to `ipconfig.php`.
   - Open `ipconfig.php` in a text editor and set your base URL.

4. **Run the Installer:**
   - Navigate to `http://your-domain.com/index.php/setup` in your browser and follow the on-screen instructions to complete the installation.

For a **detailed installation guide**, including prerequisites and troubleshooting tips, refer to [INSTALLATION.md](INSTALLATION.md).

---

## Removing `index.php` from URLs (Optional)

To remove `index.php` from your URLs:

1. **Enable mod_rewrite:**
   - Ensure the `mod_rewrite` module is enabled on your web server.

2. **Update Configuration:**
   - Set `REMOVE_INDEXPHP` to `true` in your `ipconfig.php` file.

3. **Rename `.htaccess`:**
   - Rename the `htaccess` file in the root directory to `.htaccess`.

> **Note:** If you experience issues after making these changes, revert to the default settings by undoing the steps above.

---

## Community and Support

Join our vibrant community for support, discussions, and contributions:

- **Community Forums:** [InvoicePlane Forums](https://community.invoiceplane.com/) - Ask questions, share knowledge, and get help from the community.
- **Discord:** [Join our Discord](https://discord.gg/PPzD2hTrXt) - Chat with users, developers, and contributors in real time.
- **Issue Tracker:** [GitHub Issues](https://github.com/InvoicePlane/InvoicePlane/issues) - Report bugs and request features.
- **Wiki & Documentation:** [InvoicePlane Wiki](https://wiki.invoiceplane.com/) - Find guides, FAQs, and detailed setup instructions.

> *InvoicePlane is developed and maintained by a dedicated team of volunteers. Support is provided by the community on a best-effort basis.*

---

## Contributing

We welcome contributions from the community! To get involved:

- **Report Issues:** Use the [Issue Tracker](https://github.com/InvoicePlane/InvoicePlane/issues) to report bugs or request features.
- **Submit Pull Requests:** Fork the repository, make your changes, and submit a pull request for review.

- **Translate InvoicePlane:** Help translate the application into your language. Also see [Translations.md](TRANSLATIONS.md)

For detailed contribution guidelines, please see [CONTRIBUTING.md](CONTRIBUTING.md).

---

## Security Vulnerabilities

If you discover a security vulnerability, please email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly. We will address all security concerns promptly.

---

## License & Copyright

InvoicePlane is licensed under the [MIT License](LICENSE.txt).

The **InvoicePlane name** and **logo** are copyrighted by [Kovah.de](https://kovah.de/) and [InvoicePlane.com](https://www.invoiceplane.com/). Usage is restricted. For more information, visit [license & copyright](https://www.invoiceplane.com/license-copyright).
