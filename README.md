# InvoicePlane

InvoicePlane is a self-hosted open-source application designed to help you manage your invoices, clients, and payments with ease.

## Key Features

- **Invoice & Quote Management:** Create, send, and manage professional invoices and quotes efficiently.
- **Client Management:** Maintain detailed client records, including contact information and transaction history.
- **Payment Tracking:** Monitor payments, set up payment reminders, and integrate with multiple payment gateways.
- **Customization:** Tailor templates, themes, and settings to match your brand and preferences.
- **Reporting:** Generate insightful reports to keep track of your financial performance.

## Getting Started

To get started with InvoicePlane:

1. **Download the Latest Version:**
   - Visit the [InvoicePlane website](https://www.invoiceplane.com/) to download the latest release.

2. **Extract and Upload:**
   - Extract the downloaded package and upload the files to your web server or hosting environment.

3. **Configuration:**
   - Duplicate the `ipconfig.php.example` file and rename the copy to `ipconfig.php`.
   - Open `ipconfig.php` in a text editor and set your base URL as specified.

4. **Run the Installer:**
   - Navigate to `http://your-domain.com/index.php/setup` in your web browser and follow the on-screen instructions to complete the installation.

For a comprehensive installation guide, including prerequisites and troubleshooting tips, please refer to the [INSTALLATION.md](INSTALLATION.md) file.

## Removing `index.php` from URLs (Optional)

To remove `index.php` from your URLs:

1. **Enable mod_rewrite:**
   - Ensure the `mod_rewrite` module is enabled on your web server.

2. **Update Configuration:**
   - Set the `REMOVE_INDEXPHP` setting in your `ipconfig.php` file to `true`.

3. **Rename `.htaccess`:**
   - Rename the `htaccess` file in the root directory to `.htaccess`.

*Note:* If you encounter issues after making these changes, you can revert to the default settings by undoing the steps above.

## Community and Support

Join our vibrant community for support, discussions, and contributions:

## Community and Support  

Join our vibrant community for support, discussions, and contributions:  

- **Community Forums:** [InvoicePlane Forums](https://community.invoiceplane.com/) - Ask questions, share knowledge, and get help from the community.  
- **Discord:** [Join our Discord](https://discord.gg/PPzD2hTrXt) - Chat with other users, developers, and contributors in real time.  
- **Issue Tracker:** [GitHub Issues](https://github.com/InvoicePlane/InvoicePlane/issues) - Report bugs and request features.  
- **Wiki and Documentation:** [InvoicePlane Wiki](https://wiki.invoiceplane.com/) - Find guides, FAQs, and detailed setup instructions.  

*Please note that InvoicePlane is developed and maintained by a dedicated team of volunteers. Support is provided by the community on a best-effort basis.*

## Contributing

We welcome contributions from the community! To get involved:

- **Report Issues:** Use the [Issue Tracker](https://github.com/InvoicePlane/InvoicePlane/issues) to report bugs or request features.
- **Submit Pull Requests:** Fork the repository, make your changes, and submit a pull request for review.
- **Translate InvoicePlane:** Help translate the application into your language.

For detailed contribution guidelines, please see the [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to [mail@invoiceplane.com](mailto:mail@invoiceplane.com) before disclosing it publicly. We will address all security concerns promptly.

*The name 'InvoicePlane' and the InvoicePlane logo are both copyrighted by [Kovah.de](https://kovah.de/) and [InvoicePlane.com](https://www.invoiceplane.com/) and their usage is restricted. For more information, visit [invoiceplane.com/license-copyright](https://www.invoiceplane.com/license-copyright).*

---

*This project is licensed under the terms of the [MIT License](LICENSE.txt).*
