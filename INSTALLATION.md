# Installation Guide

Follow the instructions below to install InvoicePlane on your preferred platform.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Methods](#installation-methods)
   - [Using the .zip File](#1-using-the-zip-file)
   - [Cloning the Repository](#2-cloning-the-repository)
3. [Platform-Specific Instructions](#platform-specific-instructions)
   - [Windows](#windows)
   - [macOS](#macos)
   - [Linux](#linux)
4. [Docker Installation](#docker-installation)
5. [Post-Installation](#post-installation)
6. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- **Web Server:** Apache or Nginx
- **PHP:** Version **8.1 or higher**
- **Database:** MariaDB

---

## Installation Methods

### 1. Using the .zip File

1. **Download:**
   - Get the latest version from the [InvoicePlane website](https://www.invoiceplane.com/).

2. **Extract:**
   - Unzip the package and upload the contents to your Nginx web server.

3. **Configuration:**
   - Rename `ipconfig.php.example` to `ipconfig.php`.
   - Edit `ipconfig.php` and set your base URL.

4. **Setup:**
   - Navigate to `http://your-domain.com/index.php/setup` in your browser and follow the on-screen instructions.

---

### 2. Cloning the Repository

1. **Clone:**
   ```sh
   git clone https://github.com/InvoicePlane/InvoicePlane.git
   cd InvoicePlane
   ```

2. **Dependencies:**
   - Install PHP dependencies:
     ```sh
     composer install
     ```
   - Install Node.js dependencies:
     ```sh
     yarn install
     ```

3. **Build Assets:**
   ```sh
   yarn build
   ```

4. **Configuration:**
   - Rename `ipconfig.php.example` to `ipconfig.php`.
   - Edit `ipconfig.php` and set your base URL.

5. **Setup:**
   - Navigate to `http://your-domain.com/index.php/setup` in your browser and follow the on-screen instructions.

---

## Platform-Specific Instructions

### Windows
- Using XAMPP or similar is **not recommended**. Follow the standard installation steps. Clone Repository (if you want to contribute) or use the .zip file (if you want to quickly use InvoicePlane)

### macOS
- Using **Laravel Herd**:
  - Install [Laravel Herd](https://herd.laravel.com/).
  - Place InvoicePlane files in the Herd sites directory.
  - Follow the standard installation steps.

### Linux
- **Nginx + MariaDB + PHP Setup**:
If you have a local LAMPP (or LEMPP) stack, basically follow the steps from the Dockerfile files in the Docker setup
- Configure Nginx to serve InvoicePlane.
- `cp ipconfig.php.example ipconfig.php`
- edit ipconfig.php to reflect location of the MariaDB database and the url to InvoicePlane
- `composer install`
- Install yarn
- `yarn build`

---

## Docker Installation

1. **Clone Docker Repository:**
   ```sh
   git clone https://github.com/InvoicePlane/InvoicePlane-Docker.git
   cd InvoicePlane-Docker
   ```

2. **Build and Run:**
   ```sh
   docker-compose up --build -d
   ```

3. **Setup:**
- Access `http://your-domain.com/index.php/setup` to complete the installation.

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
