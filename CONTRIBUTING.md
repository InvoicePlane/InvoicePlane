# Contribution Guide

## Contribution to InvoicePlane

Every help is welcome, you don't have to be a professional PHP developer or SQL database engineer.
If you are not familiar with PHP or coding in general you could also help us in other ways.
You may also join our [Slack Chat](https://invoiceplane-slack.herokuapp.com/) to discuss certain
topics with other developers or get some help.

## Contents

* Get familiar with our Development Guidelines
* Tools for development
* Contributing Code

---

## Get familiar with our Development Guidelines

We are following some strict development guidelines while developing.

### PHP
Coding Standard: PSR-2

### JavaScript
Use JShint / JSlint to check your JavaScript code and try to resolve all code issues

### CSS
We use SASS only.

---

## Contributing Code

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


Please ask questions related to this process if you are unclear. Asking before doing anything may save you and us time.

---

## Tools for development
We received some licenses for commercial development tools you can use if you want. Please contact use at mail@invoiceplane.com to get the license / access as we can't publish them here.

* JetBrains PhpStorm (PHP IDE)
* Mailtrap.io eMail Testing Solution

---

> _The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright_
