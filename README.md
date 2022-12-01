<p align="center">
  <img src="/assets/core/img/logo.svg">
</p>
<p>&nbsp;</p>

<p align="center">
<a href="https://github.com/InvoicePlane/InvoicePlane/releases"><img src="https://img.shields.io/badge/dynamic/json.svg?label=Current%20Version&url=https%3A%2F%2Fapi.github.com%2Frepos%2FInvoicePlane%2FInvoicePlane%2Freleases%2Flatest&query=%24.name&colorB=%23429ae1"></a>
<a href="https://github.com/InvoicePlane/InvoicePlane/releases"><img src="https://img.shields.io/github/downloads/invoiceplane/invoiceplane/total?colorB=%23429ae1"></a>
<a href="https://translations.invoiceplane.com/project/fusioninvoice"><img src="https://img.shields.io/badge/Translations-%40%20Crowdin-429ae1"></a>
</p>

<p align="center" bgcolor="#429ae1"><b>InvoicePlane is a self-hosted open source application for managing your invoices, clients and payments.<br>
  For more information visit <a href="https://www.invoiceplane.com">InvoicePlane.com</a> or try the <a href="https://demo.invoiceplane.com">Demo</a>.</b></p>

---

Since the start of the project in 2014, InvoicePlane evolved into a software that is used world wide. However, it is
still developed in our free time, as a hobby. We do your best to fulfill any legal requirements but please note that we
cannot make sure that the app is working 100% correct. Also, due to the fact that InvoicePlane is a free and open
source software without an income, there are no professional audits of the app yet.

---

### Quick Installation

1. Download the latest version [from the InvoicePlane website](https://www.invoiceplane.com/downloads).
2. Extract the package and copy all files to your webserver / webspace.
3. Make a copy of the `ipconfig.php.example` file and rename this copy to `ipconfig.php`.
4. Open the `ipconfig.php` file in an editor and set your URL like specified in the file.
5. Open `http://your-invoiceplane-domain.com/index.php/setup` and follow the instructions.


_Notice: Please download InvoicePlane from our [website](https://www.invoiceplane.com/downloads) only as the packages contain additional needed components. If you are a developer, read the [development guide](CONTRIBUTING.md)._

---

#### Issues

As soon as you run into issues and you want to report it, make sure we can replicate that issue.

Something like "_It doesn't work_" will not help in finding your issue.

We've improved the github issue template to help you answering the most common questions that are needed for reporting an issue.

Try to report your issue on the forums first: https://community.invoiceplane.com

Once the issue is _reproducable / replicatable_, you will be asked to create an issue in the issues list.

---

#### Remove `index.php` from the URL

If you want to remove `index.php` from the URL, follow these instructions. However, this is an _optional_ step and not a requirement. If it's not working correctly, take a step back and use the application with out removing that part from the URL.

1. Make sure that mod_rewrite is enabled on your web server.
2. Set the `REMOVE_INDEXPHP` setting in your `ipconfig.php` to `true`.
3. Rename the `htaccess` file to `.htaccess`

If you want to install InvoicePlane in a subfolder (e.g. `http://your-invoiceplane-domain.com/invoices/`) you have to change the `ipconfig.php` and `.htaccess` file. The instructions can be found within the files.

---

### Support / Development / Chat

Need some help or want to talk with other about InvoicePlane? Follow these links to get in touch.
Please notice that InvoicePlane is **not** a commercial software but a small open source project and we neither offer
24/7 support nor any form of SLA or paid help.

[![Wiki](https://img.shields.io/badge/Help%3A-Official%20Wiki-429ae1.svg)](https://wiki.invoiceplane.com/)
[![Community Forums](https://img.shields.io/badge/Help%3A-Community%20Forums-429ae1.svg)](https://community.invoiceplane.com/)
[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://github.com/invoiceplane/invoiceplane/issues/)
[![Contribution Guide](https://img.shields.io/badge/Development%3A-Contribution%20Guide-429ae1.svg)](CONTRIBUTING.md)

---

### Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to `mail@invoiceplane.com` before disclosing the vulnerability to the public.
All security vulnerabilities will be promptly addressed.

---

> _The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright_
