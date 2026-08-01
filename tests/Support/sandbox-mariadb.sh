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
REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." >/dev/null 2>&1 && pwd)"

# 1. Install the server if it isn't present (Ubuntu sandbox image).
if ! command -v mariadbd >/dev/null 2>&1 && ! command -v mysqld >/dev/null 2>&1; then
    echo "==> installing mariadb-server"
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -qq
    apt-get install -y -qq mariadb-server mariadb-client
fi

# 2. Initialise the data dir and start the daemon if it isn't already answering.
if ! mysqladmin ping >/dev/null 2>&1; then
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
        mysqladmin ping >/dev/null 2>&1 && break
        sleep 1
    done
fi
mysqladmin ping >/dev/null 2>&1 || { echo "mariadb failed to start; see /tmp/mariadb.log" >&2; exit 1; }

# 3. Root credentials + database. Tolerate a fresh (passwordless) or existing root.
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

# 4. (Re)build the schema + seed, mirroring the CI "Build MariaDB schema" step.
export MYSQL_PWD="$DB_PASSWORD"
mysql -h "$DB_HOSTNAME" -P "$DB_PORT" -u "$DB_USERNAME" \
    -e "SET GLOBAL sql_mode=''; DROP DATABASE IF EXISTS \`$DB_DATABASE\`; CREATE DATABASE \`$DB_DATABASE\` CHARACTER SET utf8;"
for f in $(ls "$REPO"/application/modules/setup/sql/*.sql | sort); do
    mysql -h "$DB_HOSTNAME" -P "$DB_PORT" -u "$DB_USERNAME" --force "$DB_DATABASE" < "$f"
done
mysql -h "$DB_HOSTNAME" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" < "$REPO/tests/Support/schema_fixups.sql"
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

echo "==> MariaDB ready: $DB_DATABASE ($(mysql -h "$DB_HOSTNAME" -u "$DB_USERNAME" -N \
    -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_DATABASE';") tables)"
