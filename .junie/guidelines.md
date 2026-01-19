# InvoicePlane Development Guidelines

This document provides comprehensive guidelines for developers working on InvoicePlane.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Development Workflow](#development-workflow)
4. [Coding Standards](#coding-standards)
5. [Testing Guidelines](#testing-guidelines)
6. [Git Workflow](#git-workflow)
7. [Security Best Practices](#security-best-practices)

---

## Project Overview

InvoicePlane is a self-hosted open source application for managing invoices, clients, and payments. This is a simple application built with CodeIgniter 3.x framework that is fully compatible with PHP 8.2+.

### Key Characteristics

- **Framework**: CodeIgniter 3.x - A simple and lightweight PHP framework
- **PHP Compatibility**: PHP 8.1, 8.2, and 8.3+ supported
- **Database**: MariaDB
- **Frontend**: Bootstrap 3, jQuery, custom JavaScript
- **Build Tools**: Grunt for asset compilation
- **Simplicity**: Straightforward architecture focusing on core invoicing functionality

---

## Technology Stack

### Backend
- **PHP**: 8.1, 8.2, 8.3+ (fully compatible)
- **Framework**: CodeIgniter 3.x (simple, lightweight framework)
- **Composer Dependencies**: See `composer.json` for full list

### Frontend
- **CSS Framework**: Bootstrap 3.4 (SASS)
- **JavaScript**: jQuery 3.7+, jQuery UI 1.14
- **Icons**: Font Awesome 4.7
- **Build Tool**: Grunt

### Development Tools
- **Linting**: Laravel Pint (PSR-12), PHP_CodeSniffer
- **Refactoring**: Rector
- **Package Management**: Composer (PHP), Yarn (JavaScript)

---

## Development Workflow

### Initial Setup (Prepare)

1. **Clone the Repository**
   ```bash
   git clone https://github.com/InvoicePlane/InvoicePlane.git
   cd InvoicePlane
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies**
   ```bash
   yarn install
   ```

4. **Build Assets**
   ```bash
   yarn build
   # or for development with watch
   grunt watch
   ```

5. **Configure Application**
   ```bash
   cp ipconfig.php.example ipconfig.php
   ```
   Edit `ipconfig.php` to set your database credentials and base URL.

### Start Development Environment (StartMeUp)

#### Using Docker (Recommended)

```bash
# Start all services (PHP 8.1, MariaDB, nginx, phpMyAdmin)
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

The application will be available at:
- **InvoicePlane**: http://localhost
- **phpMyAdmin**: http://localhost:8081

#### Using PHP 8.2+

To switch to PHP 8.2+, see [Docker PHP Version Switching](#docker-php-version-switching).

### Daily Workflow

1. **Start Docker Environment**
   ```bash
   docker-compose up -d
   ```

2. **Make Code Changes**
   - Edit PHP files in `application/`
   - Edit frontend files in `assets/` (SASS, JS)

3. **Build Frontend Assets** (if needed)
   ```bash
   yarn build
   # or use watch mode
   grunt watch
   ```

4. **Test Changes**
   - Manual testing via browser
   - Run linters: `composer check`

5. **Commit Changes**
   ```bash
   git add .
   git commit -m "Brief description of changes"
   git push
   ```

### Docker PHP Version Switching

The project includes Docker configurations for both PHP 8.1 and PHP 8.2+:

- **PHP 8.1**: Default configuration in `docker-compose.yml`
- **PHP 8.2+**: Available in `resources/docker/php-fpm-8.2/Dockerfile`

To switch PHP versions, modify the `docker-compose.yml` file to reference the desired Dockerfile.

---

## Coding Standards

### PHP Standards

- **Standard**: PSR-12
- **Array Syntax**: Short array syntax (`[]` not `array()`)
- **String Quotes**: Single quotes for strings
- **Line Endings**: Unix style (`\n`)
- **Encoding**: UTF-8 without BOM
- **Closing Tag**: Omit `?>` from PHP-only files

### Configuration Files

- **Pint**: `pint.json` - Laravel Pint configuration for PSR-12
- **PHPCS**: `phpcs.xml` - PHP_CodeSniffer rules
- **Rector**: `rector.php` - Automated refactoring rules

### Running Linters

```bash
# Run all checks (rector, phpcs, pint)
composer check

# Run individual tools
composer rector
composer phpcs
composer pint
```

### JavaScript Standards

- Use Standard JS code formatting
- Prefer modern ES6+ syntax where supported
- Comment complex logic
- Keep functions small and focused

### File Organization

- **Controllers**: `application/controllers/`
- **Models**: `application/models/`
- **Views**: `application/views/`
- **Helpers**: `application/helpers/`
- **Libraries**: `application/libraries/`
- **Configuration**: `application/config/`

---

## Testing Guidelines

### Linting and Code Quality

The project uses automated linting tools to maintain code quality:

1. **Run All Checks**:
   ```bash
   composer check
   ```
   This runs Rector, PHP_CodeSniffer, and Laravel Pint

2. **Individual Tools**:
   ```bash
   composer rector    # Automated refactoring
   composer phpcs     # PHP CodeSniffer
   composer pint      # Laravel Pint (PSR-12)
   ```

### Building Assets

When modifying frontend files (CSS/JavaScript):

```bash
# One-time build
yarn build

# Watch mode for development
grunt watch
```

### Manual Testing

During active development (especially beta phases):

1. **Functional Testing**: Click through all application features
2. **Regression Testing**: Ensure existing features still work
3. **Browser Testing**: Test in multiple browsers
4. **Responsive Testing**: Test on different screen sizes

### Reporting Issues

If you find issues during testing:
1. Check existing issues on GitHub
2. Discuss in Discord or Community Forums
3. Create a GitHub issue with detailed reproduction steps

### Note on Automated Testing

PHPStan and PHPUnit are not currently required for this CodeIgniter 3 application. The focus is on:
- Code linting (Pint, PHPCS, Rector)
- Manual testing of functionality
- Building and verifying assets

---

## Git Workflow

### Branch Strategy

- **`master`**: Stable releases only
- **`development`**: Active development branch
- **`feature/*`**: New features (reference issue if available)
- **`bugfix/*`**: Bug fixes (reference issue if available)

### Commit Messages

- Use clear, descriptive commit messages
- Start with a verb in present tense ("Add", "Fix", "Update", "Remove")
- Reference issue numbers when applicable
- Keep subject line under 72 characters

**Examples**:
```
Add invoice export feature (#123)
Fix payment gateway integration bug
Update README with Docker instructions
Remove deprecated API endpoints
```

### Pull Requests

1. Fork the repository
2. Create a feature/bugfix branch
3. Make your changes
4. Test thoroughly
5. Run linters: `composer check`
6. Push to your fork
7. Open a pull request with:
   - Clear description of changes
   - Reference to related issues
   - Screenshots (if UI changes)

---

## Security Best Practices

### General Principles

- **Input Validation**: Validate and sanitize all user input
- **Output Encoding**: Escape output to prevent XSS
- **SQL Injection**: Use parameterized queries
- **Authentication**: Secure password storage (bcrypt/argon2)
- **Authorization**: Check permissions before actions
- **CSRF Protection**: Use CodeIgniter's CSRF protection
- **File Uploads**: Validate file types and sizes

### CodeIgniter Security Features

- Use `$this->input->post()` for POST data (auto-sanitized)
- Use `$this->input->get()` for GET data (auto-sanitized)
- Enable CSRF protection in config
- Use `xss_clean()` when necessary
- Validate and escape database queries

### Sensitive Data

- Never commit credentials or API keys
- Use environment variables (via `ipconfig.php`)
- Keep `.gitignore` updated

### Reporting Security Issues

Email **mail@invoiceplane.com** before public disclosure.

---

## Additional Resources

- **Wiki**: https://wiki.invoiceplane.com/
- **Community Forums**: https://community.invoiceplane.com/
- **Discord**: https://discord.gg/PPzD2hTrXt
- **Issue Tracker**: https://github.com/InvoicePlane/InvoicePlane/issues

---

**Last Updated**: January 2026
