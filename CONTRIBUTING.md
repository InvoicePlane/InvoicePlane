# Contributing to InvoicePlane

Thank you for considering contributing to InvoicePlane! Your support is invaluable in improving and maintaining this project. Whether you're reporting bugs, suggesting features, writing code, or helping others, your contributions are welcome.

## Table of Contents

1. [How Can I Contribute?](#how-can-i-contribute)
   - [Reporting Bugs](#reporting-bugs)
   - [Suggesting Features](#suggesting-features)
   - [Code Contributions](#code-contributions)
   - [Documentation](#documentation)
   - [Translations](#translations)
   - [Community Support](#community-support)
2. [Development Guidelines](#development-guidelines)
   - [Coding Standards](#coding-standards)
   - [Commit Messages](#commit-messages)
3. [Getting Started with Development](#getting-started-with-development)
4. [Community and Support](#community-and-support)
5. [Code of Conduct](#code-of-conduct)

---

## How Can I Contribute?

### Reporting Bugs

If you encounter a bug, please report it by [opening an issue](https://github.com/InvoicePlane/InvoicePlane/issues) and include:

- **Description:** A clear and concise description of the bug.
- **Steps to Reproduce:** Detailed steps to reproduce the issue.
- **Expected Behavior:** What you expected to happen.
- **Actual Behavior:** What actually happened.
- **Screenshots:** If applicable, add screenshots to help explain the problem.
- **Environment:** Information about your environment (e.g., operating system, browser, InvoicePlane version).

---

### Suggesting Features

To suggest a new feature, please [open an issue](https://github.com/InvoicePlane/InvoicePlane/issues) and include:

- **Feature Description:** A clear and concise description of the feature.
- **Use Case:** Explain why this feature would be useful.
- **Additional Context:** Any other context or screenshots that might help.

---

### Code Contributions

If you'd like to contribute code:

1. **Fork the Repository:** Click the "Fork" button at the top right of the [repository page](https://github.com/InvoicePlane/InvoicePlane).
2. **Clone Your Fork:**
   ```sh
   git clone https://github.com/your-username/InvoicePlane.git
   ```
3. **Create a Branch:**
   ```sh
   git checkout -b feature/your-feature-name
   ```
4. **Make Your Changes:** Implement your feature or fix.
5. **Commit Your Changes:**
   ```sh
   git commit -m "Brief description of your changes"
   ```
6. **Push to Your Fork:**
   ```sh
   git push origin feature/your-feature-name
   ```
7. **Open a Pull Request:** Go to the original repository and [open a pull request](https://github.com/InvoicePlane/InvoicePlane/pulls) from your fork.

Please ensure your code adheres to the [Development Guidelines](#development-guidelines) and includes appropriate tests.

---

### Documentation

Improving documentation is a valuable way to contribute. You can:

- **Enhance Existing Documentation:** Clarify or expand upon current documentation.
- **Create New Guides:** Write guides for new features or common tasks.
- **Fix Typos or Errors:** Correct any mistakes you find.

To contribute to the documentation:

1. **Fork the Repository:** As described above.
2. **Make Your Changes:** Edit the relevant `.md` files.
3. **Open a Pull Request:** As described above.

---

### Translations

Help make InvoicePlane accessible to a global audience by contributing translations:

- **Check Existing Translations:** See if your language is already supported.
- **Improve Translations:** Enhance existing translations for clarity and accuracy.
- **Add New Translations:** If your language isn't supported, consider adding it.

Please refer to the [TRANSLATIONS.md](TRANSLATIONS.md) for detailed instructions.

---

### Community Support

Engage with the InvoicePlane community by:

- **Answering Questions:** Assist others, Help answer questions and contribute to discussions in the [Community Forums](https://community.invoiceplane.com/).

- **[Discord Collaboration](https://discord.gg/PPzD2hTrXt)** - Join the live chat for quick discussions and collaboration.

- **[Participate in GitHub Issues](https://github.com/InvoicePlane/InvoicePlane/issues)** - Participate in bug reports, feature requests, and discussions and feature planning.

- **Helping with Troubleshooting:** Provide solutions and suggestions in support threads on the [Community Forums](https://community.invoiceplane.com/) and on [Discord](https://discord.gg/PPzD2hTrXt)

---

## Development Guidelines

### Setting Up Your Development Environment

1. **Clone the Repository & Set Up Docker**
   ```sh
   git clone https://github.com/InvoicePlane/InvoicePlane.git
   cd InvoicePlane
   cp .env.example .env
   docker-compose up --build -d
   ```

2. **Install Dependencies (if applicable)**
   ```sh
   composer install
   yarn install
   yarn grunt
   ```

### Coding Standards

- **PHP:** PSR-12 Standard
- **JavaScript:** Standard JS Code Formatting

### Testing

- Testing is very much appreciated, especially during Beta phases. Click through all functionalities of the application and check if you see weird behavior (for example: uploads not working anymore after a new Beta version of InvoicePlane), mention it in the community (Discord, community.invoiceplane.com forums) and see if an issue should be made.

### Branching Strategy

- `development` **Active** development
- `bugfix/*` For bug fixes. Please refer to an issue, if you have one.
- `feature/*` New features. Please refer to an issue, if you have one.

- `master` **Stable releases**. Every time a new version of InvoicePlane goes live, that same version is available in the `master` branch.

---

## Community and Support

- **[Community Forums](https://community.invoiceplane.com/)** - Ask questions and share knowledge.
- **[GitHub Issues](https://github.com/InvoicePlane/InvoicePlane/issues)** - Report bugs and request features.

---

## Code of Conduct

We follow a **Code of Conduct** to ensure a friendly and inclusive environment.
By contributing, you agree to abide by these principles.

For full details, see [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

Happy Coding!
