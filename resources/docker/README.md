# Docker Setup for InvoicePlane

This directory contains Docker configurations for InvoicePlane development.

## Available PHP Versions

InvoicePlane supports multiple PHP versions:

- **PHP 8.1** (default) - Located in `php-fpm/`
- **PHP 8.2+** - Located in `php-fpm-8.2/`

## Directory Structure

```
resources/docker/
├── php-fpm/              # PHP 8.1 Docker configuration
│   ├── Dockerfile
│   ├── fpm.conf
│   ├── php-dev.ini
│   └── xdebug.ini
├── php-fpm-8.2/          # PHP 8.2+ Docker configuration
│   ├── Dockerfile
│   ├── fpm.conf
│   ├── php-dev.ini
│   └── xdebug.ini
├── nginx/                # Nginx web server configuration
├── mariadb/              # MariaDB database configuration
├── phpmyadmin/           # phpMyAdmin configuration
└── databases/            # Database import/export directory
```

## Quick Start

### Using PHP 8.1 (Default)

```bash
cd /path/to/InvoicePlane
docker-compose up -d
```

### Using PHP 8.2+

```bash
cd /path/to/InvoicePlane
docker-compose -f docker-compose.php82.yml up -d
```

## Switching Between PHP Versions

To switch from one PHP version to another:

```bash
# Stop current containers
docker-compose down

# Start with desired PHP version
docker-compose up -d                              # PHP 8.1
# OR
docker-compose -f docker-compose.php82.yml up -d  # PHP 8.2+
```

## Container Services

The Docker setup includes the following services:

### PHP-FPM Container (`invoiceplane-php`)
- **PHP 8.1** or **PHP 8.2+** (based on your choice)
- Installed extensions: bcmath, dom, exif, gd, imagick, imap, intl, mysqli, pdo_mysql, redis, soap, xdebug, xml, xsl, zip
- Xdebug configured for debugging (disabled by default)
- Port: 9000 (internal)

### Nginx Container (`invoiceplane-nginx`)
- Web server serving InvoicePlane
- Port: 80 (mapped to host)
- Configuration: `resources/docker/nginx/invoiceplane.conf`

### MariaDB Container (`invoiceplane-db`)
- MariaDB 10.9
- Port: 3306 (mapped to host)
- Default credentials:
  - Root password: `ipdevdb`
  - Database: `invoiceplane_db`
  - User: `ipdevdb`
  - Password: `ipdevdb`

### phpMyAdmin Container (`invoiceplane-dbadmin`)
- Database management interface
- Port: 8081 (mapped to host)
- Access: http://localhost:8081

## Configuration Files

### php-dev.ini
Development PHP configuration with appropriate settings for local development.

### fpm.conf
PHP-FPM pool configuration.

### xdebug.ini
Xdebug configuration for debugging PHP code.

## Useful Commands

### View Container Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f db
```

### Access Container Shell
```bash
# PHP container
docker exec -it invoiceplane-php bash

# Database container
docker exec -it invoiceplane-db bash

# Run MySQL client
docker exec -it invoiceplane-db mysql -u ipdevdb -pipdevdb invoiceplane_db
```

### Restart Services
```bash
# All services
docker-compose restart

# Specific service
docker-compose restart php
docker-compose restart nginx
```

### Rebuild Containers
After modifying Dockerfile or configuration:
```bash
docker-compose up -d --build
```

### Stop Services
```bash
# Stop all services
docker-compose down

# Stop and remove volumes (WARNING: This deletes database data!)
docker-compose down -v
```

## Enabling Xdebug

Xdebug is installed but disabled by default for performance.

To enable Xdebug:

1. Edit the Dockerfile in `php-fpm/` or `php-fpm-8.2/`
2. Uncomment the xdebug extension line:
   ```dockerfile
   # Change this line:
   RUN sed -i 's/^zend_extension=/;zend_extension=/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
   
   # To comment it out (or remove the line entirely)
   ```
3. Rebuild the container:
   ```bash
   docker-compose up -d --build
   ```

## Troubleshooting

### Port Already in Use
If port 80, 3306, or 8081 is already in use:
1. Stop the conflicting service
2. OR modify the port mapping in `docker-compose.yml`

### Permission Issues
If you encounter permission issues with files:
```bash
# Fix ownership (from host)
sudo chown -R $USER:$USER .

# OR adjust PUID/PGID in docker-compose.yml to match your user
```

### Database Connection Issues
Ensure `ipconfig.php` has correct settings:
```php
DB_HOSTNAME=invoiceplane-db
DB_USERNAME=ipdevdb
DB_PASSWORD=ipdevdb
DB_DATABASE=invoiceplane_db
DB_PORT=3306
```

### Container Won't Start
```bash
# Check logs for errors
docker-compose logs

# Rebuild from scratch
docker-compose down
docker-compose up -d --build
```

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [InvoicePlane Documentation](https://wiki.invoiceplane.com/)
