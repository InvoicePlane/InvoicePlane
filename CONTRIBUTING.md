# Contribution Guide

## Contribution to InvoicePlane

Every help is welcome, you don't have to be a professional PHP developer or SQL database engineer.
If you are not familiar with PHP or coding in general you could also help us in other ways.

### 1. Community links

[![Wiki](https://img.shields.io/badge/Help%3A-Official%20Wiki-429ae1.svg)](https://wiki.invoiceplane.com/)    
[![Community Forums](https://img.shields.io/badge/Help%3A-Community%20Forums-429ae1.svg)](https://community.invoiceplane.com/)    
[![Issue Tracker](https://img.shields.io/badge/Development%3A-Issue%20Tracker-429ae1.svg)](https://development.invoiceplane.com/)    
[![Roadmap](https://img.shields.io/badge/Development%3A-Roadmap-429ae1.svg)](https://go.invoiceplane.com/roadmapv1)    
[![Gitter chat](https://img.shields.io/badge/Chat%3A-Gitter-green.svg)](https://gitter.im/InvoicePlane/InvoicePlane)    
[![Freenode](https://img.shields.io/badge/Chat%3A-Freenode%20(IRC)-green.svg)](irc://irc.freenode.net/InvoicePlane) 

### 2. Get familiar with our Development Guidelines
We are following some strict development guidelines while developing.

#### PHP
Coding Standard: PSR-2

#### JavaScript
Use JShint / JSlint to check your JavaScript code and try to resolve all code issues

#### CSS
We use SASS only.

### Tools for development
We received some licenses for commercial development tools you can use if you want. Please contact use at mail@invoiceplane.com to get the license / access as we can't publish them here.

* JetBrains PhpStorm (PHP IDE)
* Balsamiq Mockups
* Mailtrap.io eMail Testing Solution

### 3. Contributing Code

:warning: **Read this carefully to prevent your pull request to be closed!**

1. Before you submit any code to the repository please take a look at the [development tracker](https://development.invoiceplane.com) and search for your issue!
    * If an issue exists and someone is working on it, please contact this person.
    * If an issue exists and no one is working on it, assign it to yourself or write a comment.
2. Always reference the issue ID (e.g. IP-317) in all commits you make for this issue.
3. Before you create a pull request, rebase from the development branch.
    * Add the development branch as a remote: `git remote add ip git@github.com:InvoicePlane/InvoicePlane.git`
    * Do a rebase with the following command: `git pull --rebase ip development`
    Where `ip` is the name of the remote and `development` the branch.
    * Solve all conflicts and check if your code is still working.
4. Submit the pull request, reference the issue ID in the title and add a meaningful description.

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

## Running Tests

Tested with:
```bash
$ phpunit -V
PHPUnit 3.7.21 by Sebastian Bergmann.
```

In project root directory run:
```bash
$ phpunit
```

Please ask questions related to this process if you are unclear.

---
  
*Please notice: The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright*
