#!/usr/bin/env bash
#
# One-command MariaDB test database for the Claude Code web sandbox.
#
# Regular local dev and CI do NOT need this — CI provisions MariaDB as a service
# container (see .github/workflows/phpunit.yml) and imports the same schema. This
# script exists only for the ephemeral web sandbox, which starts with no database
# server at all. It is idempotent: installs MariaDB if missing, starts it, then
# (re)builds the invoiceplane_test schema from the setup SQL migrations, applies
# tests/Support/schema_fixups.sql, seeds the baseline rows, and writes ipconfig.php.
#
# Usage (from the repo root):
#   bash tests/Support/sandbox-mariadb.sh
#   DB_HOSTNAME=127.0.0.1 DB_PORT=3306 DB_DATABASE=invoiceplane_test \
#     DB_USERNAME=root DB_PASSWORD=root php <phpunit> --bootstrap tests/bootstrap.php
#
# The DB_* values below match .github/workflows/phpunit.yml so the sandbox
# reproduces CI exactly. Export the same DB_* vars before running phpunit so the
# test parent process connects to the same database.
set -euo pipefail

: "${DB_HOSTNAME:=127.0.0.1}" "${DB_PORT:=3306}" "${DB_DATABASE:=invoiceplane_test}"
: "${DB_USERNAME:=root}" "${DB_PASSWORD:=root}"
: "${MYSQL_CONTAINER:=}"
REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." >/dev/null 2>&1 && pwd)"

# Prefer an already-running Docker MariaDB service when the host has no mysql
# client/server. The local InvoicePlane dev stack exposes this container on
# 127.0.0.1:3306, which is exactly what host-side PHPUnit needs.
if [ -z "$MYSQL_CONTAINER" ] && command -v docker >/dev/null 2>&1; then
    MYSQL_CONTAINER="$(docker ps --format '{{.Names}}' | grep -E '(^|-)mariadb(-|$)' | head -n 1 || true)"
fi

mysql_exec() {
    if command -v mysql >/dev/null 2>&1; then
        MYSQL_PWD="$DB_PASSWORD" mysql -h "$DB_HOSTNAME" -P "$DB_PORT" -u "$DB_USERNAME" "$@"
        return
    fi

    if [ -n "$MYSQL_CONTAINER" ]; then
        docker exec -i "$MYSQL_CONTAINER" mariadb -h 127.0.0.1 -P 3306 -u "$DB_USERNAME" "-p$DB_PASSWORD" "$@"
        return
    fi

    echo "mysql client not found and no MariaDB Docker container is available" >&2
    return 127
}

mysql_ping() {
    if command -v mysqladmin >/dev/null 2>&1; then
        MYSQL_PWD="$DB_PASSWORD" mysqladmin -h "$DB_HOSTNAME" -P "$DB_PORT" -u "$DB_USERNAME" ping >/dev/null 2>&1
        return
    fi

    if [ -n "$MYSQL_CONTAINER" ]; then
        docker exec "$MYSQL_CONTAINER" mariadb-admin -h 127.0.0.1 -P 3306 -u "$DB_USERNAME" "-p$DB_PASSWORD" ping >/dev/null 2>&1
        return
    fi

    return 1
}

# 1. Install the server if it isn't present (Ubuntu sandbox image) and no
# running Docker MariaDB service is available.
if [ -z "$MYSQL_CONTAINER" ] && ! command -v mariadbd >/dev/null 2>&1 && ! command -v mysqld >/dev/null 2>&1; then
    echo "==> installing mariadb-server"
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -qq
    apt-get install -y -qq mariadb-server mariadb-client
fi

# 2. Initialise the data dir and start the daemon if it isn't already answering.
if ! mysql_ping; then
    mkdir -p /run/mysqld
    chown -R mysql:mysql /run/mysqld /var/lib/mysql 2>/dev/null || true
    if [ ! -d /var/lib/mysql/mysql ]; then
        echo "==> initialising data directory"
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql \
            --auth-root-authentication-method=normal >/dev/null 2>&1
    fi
    echo "==> starting mariadbd"
    nohup mysqld_safe --user=mysql --datadir=/var/lib/mysql >/tmp/mariadb.log 2>&1 &
    for _ in $(seq 1 30); do
        mysql_ping && break
        sleep 1
    done
fi
mysql_ping || { echo "mariadb failed to start; see /tmp/mariadb.log" >&2; exit 1; }

# 3. Root credentials + database. Tolerate a fresh (passwordless) or existing root.
if command -v mysql >/dev/null 2>&1; then
    mysql -u root <<SQL 2>/dev/null || mysql -u root -p"$DB_PASSWORD" <<SQL2
SET PASSWORD FOR 'root'@'localhost' = PASSWORD('$DB_PASSWORD');
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL
SET PASSWORD FOR 'root'@'localhost' = PASSWORD('$DB_PASSWORD');
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL2
fi

# 4. (Re)build the schema + seed, mirroring the CI "Build MariaDB schema" step.
mysql_exec -e "SET GLOBAL sql_mode=''; DROP DATABASE IF EXISTS \`$DB_DATABASE\`; CREATE DATABASE \`$DB_DATABASE\` CHARACTER SET utf8;"
for f in $(ls "$REPO"/application/modules/setup/sql/*.sql | sort); do
    mysql_exec --force "$DB_DATABASE" < "$f"
done
mysql_exec "$DB_DATABASE" < "$REPO/tests/Support/schema_fixups.sql"
php "$REPO/tests/Support/seed-test-db.php" >/dev/null

# 5. Write ipconfig.php so the app boots against this database (same as CI).
if [ ! -f "$REPO/ipconfig.php" ] || ! grep -q "^SETUP_COMPLETED=true" "$REPO/ipconfig.php"; then
    cp "$REPO/ipconfig.php.example" "$REPO/ipconfig.php"
    sed -i \
        -e "s|^DB_HOSTNAME=.*|DB_HOSTNAME=${DB_HOSTNAME}|" \
        -e "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" \
        -e "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" \
        -e "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" \
        -e "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" \
        -e 's|^SETUP_COMPLETED=.*|SETUP_COMPLETED=true|' \
        -e 's|^ENCRYPTION_KEY=.*|ENCRYPTION_KEY=0123456789abcdef0123456789abcdef|' \
        "$REPO/ipconfig.php"
    echo 'DB_DRIVER=mysqli'      >> "$REPO/ipconfig.php"
    echo 'CSRF_PROTECTION=false' >> "$REPO/ipconfig.php"
fi
sed -i \
    -e "s|^DB_HOSTNAME=.*|DB_HOSTNAME=${DB_HOSTNAME}|" \
    -e "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" \
    -e "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" \
    -e "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" \
    -e "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" \
    "$REPO/ipconfig.php"

echo "==> MariaDB ready: $DB_DATABASE ($(mysql_exec -N \
    -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_DATABASE';") tables)"
