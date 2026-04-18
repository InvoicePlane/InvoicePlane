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

## What's New in Version 1.7.0

**InvoicePlane 1.7.0** brings PHP 8.2+ compatibility and critical security enhancements to keep your financial data safe.

### Major Improvements

- **PHP 8.2+ Compatibility:** Full support for modern PHP versions (8.1, 8.2, 8.3+)
- **Enhanced Security:** Multiple security vulnerabilities have been addressed:
  - Fixed Cross-Site Scripting (XSS) vulnerabilities across templates and user inputs
  - Resolved Local File Inclusion (LFI) vulnerabilities in PDF generation
  - Patched log poisoning vulnerability in file upload handling
- **SVG Logo Protection:** SVG uploads are now blocked to prevent embedded script execution (see details below)
- **Updated Dependencies:** All PHP packages updated for compatibility and security

### Issues Fixed in Version 1.7.0

**Security Fixes:**
- #1433 - Local File Inclusion (LFI) vulnerabilities in PDF template handling (Post-v1.7.0 tag)
- #1388, #1387 - Unsafe jQuery plugin vulnerabilities (Code scanning alerts)
- #1383 - File access vulnerabilities across all controllers
- Security fixes for XSS vulnerabilities (multiple fields sanitized - see CHANGELOG.md)
- Security fix for log poisoning in file upload handling

**Bug Fixes and Improvements:**
- #1389 - Workflow permissions in GitHub Actions
- #1381 - E-invoicing field migration and version checking
- #1380 - Dependency update (qs package bump)
- #1377 - QR code image width reduced to 100px
- #1375 - Email address verification now supports comma and semicolon separators
- #1373 - Removed deprecated library dependencies
- #1367, #1368 - Various bug fixes

### Fields Sanitized for Security

The following fields have been sanitized to prevent XSS attacks:
- Quote and invoice number fields (all templates)
- Tax rate names
- Payment method names
- Custom field labels
- Client addresses
- Sumex observations
- Quote notes and passwords
- Email template content
- File names in upload logging (prevents log poisoning)

### Upgrading from Version 1.6.x

If you're upgrading from InvoicePlane 1.6.x:

1. **Backup your data** - Create a full backup of your database and files
2. **Check PHP version** - Ensure your server runs PHP 8.1 or higher
3. **Update files** - Replace all application files with the new version
4. **Run migrations** - Visit `/index.php/setup` to apply database updates
5. **Review logo settings** - If using an SVG logo, convert it to PNG/JPG (see SVG notice below)

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

### Important Security Notice: SVG Logo Files

**As of this version, SVG (Scalable Vector Graphics) files are no longer supported for logo uploads due to security concerns.**

#### Why were SVG files disabled?

SVG files can contain embedded JavaScript code that could be exploited to perform Cross-Site Scripting (XSS) attacks. Since InvoicePlane handles sensitive financial data, we have taken a proactive security measure by blocking SVG uploads entirely.

#### What file formats are supported?

You can upload logos in the following safe image formats:
- **PNG** (recommended for logos with transparency)
- **JPG/JPEG** (recommended for photographs)
- **GIF** (recommended for simple graphics)

#### What happens to my existing SVG logo?

If you previously uploaded an SVG logo:
- It will not display in the application (blocked for security)
- A warning message will appear in the settings page
- You can easily remove it and upload a replacement in a supported format

#### How do I convert my SVG logo?

You can convert your SVG logo to PNG using free tools:

1. **Online converters:**
   - [CloudConvert](https://cloudconvert.com/svg-to-png)
   - [Convertio](https://convertio.co/svg-png/)

2. **Desktop software:**
   - [Inkscape](https://inkscape.org/) (free, open-source)
   - Adobe Illustrator
   - GIMP

3. **Conversion steps in Inkscape:**
   - Open your SVG file in Inkscape
   - Go to File → Export PNG Image
   - Set your desired resolution (300 DPI recommended)
   - Click Export

#### Need help?

If you have questions about logo formats or need assistance, please visit our [Community Forums](https://community.invoiceplane.com/).

---

## License & Copyright

InvoicePlane is licensed under the [MIT License](LICENSE.txt).

The **InvoicePlane name** and **logo** are copyrighted by [Kovah.de](https://kovah.de/) and [InvoicePlane.com](https://www.invoiceplane.com/). Usage is restricted. For more information, visit [license & copyright](https://www.invoiceplane.com/license-copyright).
