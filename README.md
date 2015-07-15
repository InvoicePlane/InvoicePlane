![InvoicePlane](http://invoiceplane.com/content/logo/PNG/logo_300x150.png)
#### _Version 1.4.3_

InvoicePlane is a self-hosted open source application for managing your invoices, clients and payments.    
For more information visit __[InvoicePlane.com](https://invoiceplane.com)__ or take a look at the __[demo](https://demo.invoiceplane.com)__

### Quick Installation

1. Download the [latest version](https://invoiceplane.com/downloads)
2. Extract the package and copy all files to your webserver / webspace. Don't forget to copy the `.htaccess` file!
3. Make sure that [mod_rewrite](https://go.invoiceplane.com/apachemodrewrite) is enabled on your webserver.
4. Open `http://your-invoiceplane-domain.com/setup` and follow the instructions.

===

### Support / Development

If you need support or want to help developing please visit the [official wiki](https://wiki.invoiceplane.com) or join the community:

[![Community Forums](https://invoiceplane.com/content/badges/badge_community.png)](https://community.invoiceplane.com/)   
[![Gitter chat](https://badges.gitter.im/InvoicePlane/InvoicePlane.png)](https://gitter.im/InvoicePlane/InvoicePlane)   
[![Freenode](https://invoiceplane.com/content/badges/badge_freenode.png)](irc://irc.freenode.net/InvoicePlane)

### Developing with Vagrant

*Prerequisites*

Download the following to setup the InvoicePlane VM.  Please note: the VM is for development purposes only.

1. http://www.ansible.com/home
2. https://www.vagrantup.com/
3. https://github.com/cogitatio/vagrant-hostsupdater
4. https://www.virtualbox.org/wiki/Downloads
5. On the virtualbox.org page you will also need to download the appropriate VirtualBox Extension Pack
6. Clone the repo to your local machine with `git clone`
7. Run `vagrant up` from the cloned directory
8. If provisioning fails just run `vagrant provision` from the terminal

You should be able to navigate to your local though by doing www.invoiceplane.local  

On the setup screen specify localhost, root, *blank password*, invoiceplane as the database configuration.

*Note:* When the VM is booted you can change the `php.ini` file to setup xdebug or other preferences.

__Ask questions!__ Please ask questions related to this process if you are unclear.

---
  
*Please notice: The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright*

