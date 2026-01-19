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
- **PHP:** Version **8.1 or higher** (PHP 8.2+ supported)
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

5. **Configure Application:**
   ```bash
   cp ipconfig.php.example ipconfig.php
   ```
   Edit `ipconfig.php` to set:
   - Database credentials (use values from `docker-compose.yml` for local dev)
   - Base URL (use `http://localhost` for local dev)
   - Environment settings

### StartMeUp: Start Development Environment

The **StartMeUp** phase launches your development environment.

#### Using Docker (Recommended)

Docker provides a consistent development environment with PHP, MariaDB, nginx, and phpMyAdmin.

**For PHP 8.1 (default):**
```bash
docker-compose up -d
```

**For PHP 8.2+:**
```bash
docker-compose -f docker-compose.php82.yml up -d
```

**View Logs:**
```bash
docker-compose logs -f
```

**Stop Services:**
```bash
docker-compose down
```

**Access Points:**
- **InvoicePlane**: <http://localhost>
- **phpMyAdmin**: <http://localhost:8081>
  - Username: `ipdevdb`
  - Password: `ipdevdb`

#### Without Docker (Alternative)

If you're not using Docker, ensure you have:
- PHP 8.1+ installed and configured
- MariaDB running locally
- Nginx or Apache configured to serve the project directory

### Workflow: Daily Development

The **Workflow** phase covers your day-to-day development activities.

1. **Start Docker Environment** (if using Docker):
   ```bash
   docker-compose up -d
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

**Switching PHP Versions:**

To switch between PHP 8.1 and PHP 8.2+:

```bash
# Stop current containers
docker-compose down

# Start with PHP 8.1 (default)
docker-compose up -d

# OR start with PHP 8.2+
docker-compose -f docker-compose.php82.yml up -d
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
- **Manual Setup**: Install PHP 8.1+ via Homebrew and follow Linux instructions

### Linux
- **Nginx + MariaDB + PHP Setup** (LEMP Stack):
  - Install required packages:
    ```bash
    # Ubuntu/Debian
    sudo apt-get update
    sudo apt-get install nginx mariadb-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl
    ```
  - Configure Nginx to serve InvoicePlane (see Docker nginx config for reference)
  - Follow the [Development Workflow](#development-workflow) steps
- **Docker**: Recommended for consistent environment (see [Docker Installation](#docker-installation))

---

## Docker Installation

Docker provides the easiest and most consistent development environment for InvoicePlane.

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

3. **Configure Application:**
   ```bash
   cp ipconfig.php.example ipconfig.php
   ```
   Edit `ipconfig.php` with these settings for Docker:
   ```ini
   # Database settings
   DB_HOSTNAME=invoiceplane-db
   DB_USERNAME=ipdevdb
   DB_PASSWORD=ipdevdb
   DB_DATABASE=invoiceplane_db
   DB_PORT=3306
   
   # URL settings
   URL_BASE=http://localhost/
   ```

4. **Start Services:**
   ```bash
   # PHP 8.1 (default)
   docker-compose up -d
   
   # OR PHP 8.2+
   docker-compose -f docker-compose.php82.yml up -d
   ```

5. **Complete Setup:**
   - Navigate to <http://localhost/index.php/setup>
   - Follow the setup wizard
   - Use the database credentials from step 3

### Docker Services Included

- **PHP-FPM**: PHP 8.1 or 8.2+ (based on your choice)
- **Nginx**: Web server on port 80
- **MariaDB**: Database server on port 3306
- **phpMyAdmin**: Database management on port 8081

### Useful Docker Commands

```bash
# View logs
docker-compose logs -f

# View logs for specific service
docker-compose logs -f php

# Restart services
docker-compose restart

# Stop services
docker-compose down

# Rebuild containers (after Dockerfile changes)
docker-compose up -d --build

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
