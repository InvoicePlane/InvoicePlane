# Installation Guide

Follow the instructions below to install InvoicePlane on your preferred platform.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Methods](#installation-methods)
   - [Using the .zip File (Production)](#1-using-the-zip-file-production)
   - [Cloning the Repository (Development)](#2-cloning-the-repository-development)
3. [Development Workflow](#development-workflow)
   - [Prepare: Initial Setup](#prepare-initial-setup)
   - [StartMeUp: Start Development Environment](#startmeup-start-development-environment)
   - [Workflow: Daily Development](#workflow-daily-development)
4. [Platform-Specific Instructions](#platform-specific-instructions)
   - [Windows](#windows)
   - [macOS](#macos)
   - [Linux](#linux)
5. [Docker Installation](#docker-installation)
6. [Post-Installation](#post-installation)
7. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- **Web Server:** Apache or Nginx
- **PHP:** **8.4** is the default and recommended version. 8.2, 8.3, 8.4 and 8.5 are all supported.
- **Database:** MariaDB
- **For Development:** Docker (recommended), Composer, Yarn/npm

---

## Installation Methods

### 1. Using the .zip File (Production)

This method is recommended for production deployments:

1. **Download:**
   - Get the latest version from the [InvoicePlane website](https://www.invoiceplane.com/).

2. **Extract:**
   - Unzip the package and upload the contents to your web server.

3. **Configuration:**
   - Rename `ipconfig.php.example` to `ipconfig.php`.
   - Edit `ipconfig.php` and set your base URL and database credentials.

4. **Setup:**
   - Navigate to `http://your-domain.com/index.php/setup` in your browser and follow the on-screen instructions.

---

### 2. Cloning the Repository (Development)

This method is recommended for development and contributing to InvoicePlane:

See [Development Workflow](#development-workflow) section below for detailed steps.

---

## Development Workflow

This section outlines the three-phase workflow for developing InvoicePlane:

### Prepare: Initial Setup

The **Prepare** phase sets up your development environment for the first time.

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/InvoicePlane/InvoicePlane.git
   cd InvoicePlane
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```
   This installs all PHP packages defined in `composer.json`.

3. **Install JavaScript Dependencies:**
   ```bash
   yarn install
   ```
   This installs all frontend dependencies (Bootstrap, jQuery, etc.).

4. **Build Frontend Assets:**
   ```bash
   yarn build
   ```
   This compiles SASS to CSS and minifies JavaScript files using Grunt.

5. **Configure Application** (optional — only if you're not using `docker-compose.yml`'s
   automatic config generation, see below):
   ```bash
   cp ipconfig.php.example ipconfig.php
   ```
   Edit `ipconfig.php` to set:
   - Database credentials (use the values from `docker-compose.yml` for local dev:
     `DB_HOSTNAME=db`, `DB_USERNAME=ipdevdb`, `DB_PASSWORD=ipdevdb`, `DB_DATABASE=invoiceplane_db`)
   - `IP_URL=http://ivpl.local` for local dev (see the hostname note below)
   - Environment settings

### StartMeUp: Start Development Environment

The **StartMeUp** phase launches your development environment. Two Docker setups are available
— see [resources/docker/README.md](../../resources/docker/README.md) for the full comparison.

#### Using Docker (Recommended)

`docker-compose.yml` provides separated `php`/`nginx`/`db`/`phpmyadmin`/`mailpit` services that bind-mount
your working tree, so PHP/frontend edits are reflected immediately without a rebuild — this is
the one to use for active development. (`compose.yml` is the other option: a single
self-contained image, good for quickly spinning up InvoicePlane to test something, but not for
iterating on code — see [resources/docker/README.md](../../resources/docker/README.md).)

Add `127.0.0.1 ivpl.local` to your `/etc/hosts` file first — the bundled nginx config expects
that hostname, not `localhost`.

**Start Docker:**
```bash
docker compose -f docker-compose.yml up -d --build
```
`ipconfig.php` is generated automatically on first run if you skipped step 5 above.

**View Logs:**
```bash
docker compose -f docker-compose.yml logs -f
```

**Stop Services:**
```bash
docker compose -f docker-compose.yml down
```

**Access Points:**
- **InvoicePlane**: <http://ivpl.local>
- **phpMyAdmin**: <http://localhost:8081>
  - Username: `ipdevdb`
  - Password: `ipdevdb`
- **Mailpit** (outgoing mail is caught here, never delivered): <http://localhost:8025>

#### Without Docker (Alternative)

If you're not using Docker, ensure you have:
- PHP 8.4 installed and configured (8.2 – 8.5 are supported)
- MariaDB running locally
- Nginx or Apache configured to serve the project directory

### Workflow: Daily Development

The **Workflow** phase covers your day-to-day development activities.

1. **Start Docker Environment** (if using Docker):
   ```bash
   docker compose -f docker-compose.yml up -d
   ```

2. **Make Code Changes:**
   - Edit PHP files in `application/` directory
   - Edit SASS files in `assets/` directory
   - Edit JavaScript files in `assets/` directory

3. **Build Assets (if you changed frontend files):**
   ```bash
   # One-time build
   yarn build
   
   # Or use watch mode for automatic rebuilds
   grunt watch
   ```

4. **Run Linters** (before committing):
   ```bash
   # Run all code quality checks
   composer check
   
   # Or run individually
   composer rector    # Automated refactoring
   composer phpcs     # PHP CodeSniffer
   composer pint      # Laravel Pint (PSR-12)
   ```

5. **Test Your Changes:**
   - Access <http://localhost> in your browser
   - Manually test the features you changed
   - Check for any console errors or warnings

6. **Commit Your Changes:**
   ```bash
   git add .
   git commit -m "Brief description of your changes"
   git push
   ```

---

## Platform-Specific Instructions

### Windows
- Using XAMPP or similar is **not recommended**. 
- **Recommended approach**: Use Docker Desktop for Windows (see [Docker Installation](#docker-installation))
- **Alternative**: Use WSL2 (Windows Subsystem for Linux) with Docker
- Follow the standard [Development Workflow](#development-workflow) steps

### macOS
- **Recommended**: Use Docker Desktop for Mac (see [Docker Installation](#docker-installation))
- **Alternative with Laravel Herd**:
  - Install [Laravel Herd](https://herd.laravel.com/).
  - Place InvoicePlane files in the Herd sites directory.
  - Follow the standard installation steps.
- **Manual Setup**: Install PHP 8.4 via Homebrew (`brew install php@8.4`) and follow Linux instructions

### Linux
- **Nginx + MariaDB + PHP Setup** (LEMP Stack):
  - Install required packages:
    ```bash
    # Ubuntu (24.04 ships PHP 8.3; PHP 8.4 comes from the ondrej/php PPA)
    sudo add-apt-repository -y ppa:ondrej/php
    sudo apt-get update
    sudo apt-get install nginx mariadb-server php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-gd php8.4-bcmath
    ```
  - Configure Nginx to serve InvoicePlane, starting from
    [`resources/docker/nginx/invoiceplane.conf`](../../resources/docker/nginx/invoiceplane.conf).
    **Keep its deny rules.** nginx ignores the `.htaccess` files that protect private paths on
    Apache, so without them archived invoice PDFs (`uploads/archive`), customer attachments,
    import files, `vendor/` and `application/` are downloadable without logging in. Only
    `index.php` should be executed as PHP.
  - Follow the [Development Workflow](#development-workflow) steps
- **Docker**: Recommended for consistent environment (see [Docker Installation](#docker-installation))

---

## Docker Installation

Docker provides the easiest and most consistent development environment for InvoicePlane. This
repository ships two different compose files for two different purposes — see
[resources/docker/README.md](../../resources/docker/README.md) for the full comparison. This
section covers `docker-compose.yml`, the one for active development.

### Prerequisites

- **Docker**: [Install Docker](https://docs.docker.com/get-docker/)
- **Docker Compose**: Usually included with Docker Desktop

### Quick Start

1. **Clone Repository:**
   ```bash
   git clone https://github.com/InvoicePlane/InvoicePlane.git
   cd InvoicePlane
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   yarn install
   yarn build
   ```

3. **Add the required hostname** — the bundled nginx config expects `ivpl.local`, not
   `localhost`:
   ```bash
   echo "127.0.0.1 ivpl.local" | sudo tee -a /etc/hosts
   ```

4. **Configure Application** (optional): `ipconfig.php` is generated automatically on first
   start. To configure it yourself instead:
   ```bash
   cp ipconfig.php.example ipconfig.php
   ```
   Edit `ipconfig.php` with these settings for Docker:
   ```ini
   # Database settings — DB_HOSTNAME is the compose service name, not the container name
   DB_HOSTNAME=db
   DB_USERNAME=ipdevdb
   DB_PASSWORD=ipdevdb
   DB_DATABASE=invoiceplane_db
   DB_PORT=3306

   # URL settings — the config key is IP_URL, not URL_BASE
   IP_URL=http://ivpl.local
   ```

5. **Start Services:**
   ```bash
   docker compose -f docker-compose.yml up -d --build
   ```

6. **Complete Setup:**
   - Navigate to <http://ivpl.local/index.php/setup>
   - Follow the setup wizard
   - Use the database credentials from step 4

### Docker Services Included

- **PHP-FPM**: PHP 8.4 by default (set `PHP_VERSION` in `docker-compose.yml` to use 8.2 – 8.5), with Composer
- **Nginx**: Web server on port 80 (`http://ivpl.local`)
- **MariaDB**: `db:3306` inside the stack; not published on the host (use phpMyAdmin)
- **phpMyAdmin**: Database management on port 8081
- **Mailpit**: Mail catcher; web UI on port 8025, SMTP for the app at `mailpit:1025`

### Useful Docker Commands

```bash
# View logs
docker compose -f docker-compose.yml logs -f

# View logs for specific service
docker compose -f docker-compose.yml logs -f php

# Restart services
docker compose -f docker-compose.yml restart

# Stop services
docker compose -f docker-compose.yml down

# Rebuild containers (after Dockerfile changes)
docker compose -f docker-compose.yml up -d --build

# Access PHP container shell
docker exec -it invoiceplane-php bash

# Access database
docker exec -it invoiceplane-db mysql -u ipdevdb -pipdevdb invoiceplane_db
```

---

## Post-Installation
- Access `http://your-domain.com/index.php/setup` to complete the installation.
- It will guide you through the install wizard.
- Log in with the credentials you provided in the wizard
- In the `settings` you can set up InvoicePlane to your liking
- Add `Invoice Groups`, `Product Families`, `Product Units`, etcetera
- Start using InvoicePlane

---

## Troubleshooting

If you encounter issues during installation or setup, follow these steps:

1. **Visit the [Community Forums](https://community.invoiceplane.com/)** - Engage with other users and developers for help.
2. **Join our [Discord Server](https://discord.gg/PPzD2hTrXt)** - Get real-time assistance from the community.
3. **Check the [InvoicePlane Wiki](https://wiki.invoiceplane.com/)** - Look for documented solutions to common problems.
