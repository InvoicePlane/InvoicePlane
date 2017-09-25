# Contribution Information

## Contribution to InvoicePlane

Every help is welcome, you don't have to be a professional PHP developer or SQL database engineer.
We appreciate any support with helping other users, translating the software or simply spreading
the word. You can find more information in our [official contribution guide](https://go.invoiceplane.com/contribution).


## Contents

* The Roadmap
* Get familiar with our Development Guidelines
* Contributing Code

---

## The roadmap

We have a forum thread that is updated on a regular base with the most important bugs and feature requests.
You can find the overview [here](https://go.invoiceplane.com/roadmapv1).

---

## Get familiar with our Development Guidelines

We are following some strict development guidelines while developing.

### PHP
Coding Standard: PSR-2

### JavaScript
Use JShint / JSlint to check your JavaScript code and try to resolve all code issues.

### CSS
We use SASS only. The Sass files are compiled to minified and automatically prefixed CSS files.

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

Please ask questions related to this process if you are unclear. Asking before doing anything will save your and our time.

---

> _The name 'InvoicePlane' and the InvoicePlane logo are both copyright by Kovah.de and InvoicePlane.com
and their usage is restricted! For more information visit invoiceplane.com/license-copyright_
