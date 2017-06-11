![InvoicePlane](http://invoiceplane.com/content/logo/PNG/logo_300x150.png)
#### _Version 1.5.3_

InvoicePlane is a self-hosted open source application for managing your invoices, clients and payments.    
For more information visit __[InvoicePlane.com](https://invoiceplane.com)__ or try the __[demo](https://demo.invoiceplane.com)__.

---

### Quick Installation

1. Download the latest version [from the InvoicePlane website](https://invoiceplane.com/downloads).
2. Extract the package and copy all files to your webserver / webspace.
3. Make a copy of the `ipconfig.php.example` file and rename this copy to `ipconfig.php`.
4. Open the `ipconfig.php` file and add your URL like specified in the file.
5. Open `http://your-invoiceplane-domain.com/index.php/setup` and follow the instructions.


_Notice: Please download InvoicePlane from our [website](https://invoiceplane.com/downloads) only as the packages contain additional needed components. If you are a developer, read the [development guide](DEVELOPMENT.md)._

#### Remove `index.php` from the URL

If you want to remove `index.php` from the URL, follow these instructions. However, this is an optional step and not a requirement. If it's not working correctly, take a step back and use the application with out removing that part from the URL.

1. Make sure that [mod_rewrite](https://go.invoiceplane.com/apachemodrewrite) is enabled on your web server.
2. Set the `REMOVE_INDEXPHP` setting in your `ipconfig.php` to `true`.
3. Rename the `htaccess` file to `.htaccess`

If you want to install InvoicePlane in a subfolder (e.g. `http://your-invoiceplane-domain.com/invoices/`) you have to change the `ipconfig.php` and `.htaccess` file. The instructions can be found within the files.

---

### Support / Development / Chat

Need some help or want to talk with other about InvoicePlane? Follow these links to get in touch.

[![Wiki](https://img.shields.io/badge/Help%3A-Official%20Wiki-429ae1.svg)](https://wiki.invoiceplane.com/)  
[![Community Forums](https://img.shields.io/badge/Help%3A-Community%20Forums-429ae1.svg)](https://community.invoiceplane.com/)  
[![Slack Chat](https://img.shields.io/badge/Development%3A-Slack%20Chat-429ae1.svg)](https://invoiceplane-slack.herokuapp.com/)  
[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://development.invoiceplane.com/)  
[![Roadmap](https://img.shields.io/badge/Development%3A-Roadmap-429ae1.svg)](https://go.invoiceplane.com/roadmapv1)  
[![Contribution Guide](https://img.shields.io/badge/Development%3A-Contribution%20Guide-429ae1.svg)](CONTRIBUTING.md)  

---

### Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to mail@invoiceplane.com before disclosing the vulnerability to the public.
All security vulnerabilities will be promptly addressed.

---

> _The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright_
