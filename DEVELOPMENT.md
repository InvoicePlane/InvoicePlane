# InvoicePlane v1 Development Guide

If you want to help developing InvoicePlane you need to prepare InvoicePlane for working with it as huge parts of the application are missing in the source code and need to be downloaded and compiled.  
If you have any questions, feel free to join the `#development` channel in our [Slack Chat](https://invoiceplane-slack.herokuapp.com/).

Please read the [Contribution Guide](CONTRIBUTING.md) before starting to work on the app.

[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://development.invoiceplane.com/) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/InvoicePlane/InvoicePlane/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/InvoicePlane/InvoicePlane/?branch=master) 

---

## Contents

* Prepare your machine for InvoicePlane
* Setup InvoicePlane
* Developing with Vagrant


## Prepare your machine for InvoicePlane

The following tasks need to be accomplished before starting to work on and with InvoicePlane:

* Composer is installed
* Node.js and NPM are installed (Stable version is recommended)
* The `grunt-cli` package is installed globally via NPM


## Setup InvoicePlane

The following steps are required to make InvoicePlane runnable:

* Make a copy of the `ipconfig.php.example` file and rename the copy to `ipconfig.php`.
* Run `composer install`.
* Run `npm install`.
* Run `grunt build`.

Then point your webserver to the InvoicePlane directory. That's it.


## Developing with Vagrant

### Prerequisites

Download the following to setup the InvoicePlane VM.  Please note: the VM is for development purposes only.

* http://www.ansible.com/home
* https://www.vagrantup.com/
* https://github.com/cogitatio/vagrant-hostsupdater
* https://www.virtualbox.org/wiki/Downloads
* On the virtualbox.org page you will also need to download the appropriate VirtualBox Extension Pack
* Clone the repo to your local machine with `git clone`
* Run `vagrant up` from the cloned directory
* If provisioning fails just run `vagrant provision` from the terminal

You should be able to navigate to your local though by doing www.invoiceplane.local  

On the setup screen specify localhost, root, *blank password*, invoiceplane as the database configuration.

*Note:* When the VM is booted you can change the `php.ini` file to setup xdebug or other preferences.

__Ask questions!__ Please ask questions related to this process if you are unclear.

---

> _The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright_
