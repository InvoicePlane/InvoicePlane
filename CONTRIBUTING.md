## Developing with Vagrant

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

## Running Tests

Tested with:
```bash
$> phpunit -V
PHPUnit 3.7.21 by Sebastian Bergmann.
```

In project root directory run:
```bash
$> phpunit
```
Please ask questions related to this process if you are unclear.

[![Community Forums](https://invoiceplane.com/content/badges/badge_community.png)](https://community.invoiceplane.com/)  

---
  
*Please notice: The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright*