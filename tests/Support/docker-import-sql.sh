#!/usr/bin/env bash
#
# Import one .sql file into the MariaDB test database, retrying transient InnoDB
# deadlocks / lock-wait timeouts.
#
# In the local ivpldock stack the mariadb container is shared with php-fpm,
# php-worker and phpmyadmin. Their connections take metadata locks that
# intermittently deadlock the DDL in application/modules/setup/sql/*.sql
# (ERROR 1213 "try restarting transaction"). The old recipe imported each file
# with `mysql --force`, which SILENTLY skipped the whole failed statement — so a
# multi-column `ALTER TABLE ip_users ADD COLUMN user_bank ...` that deadlocked
# left every one of its columns missing, and hundreds of later tests then died
# with `Unknown column 'ip_users.user_bank'`. Retrying the transient failure
# fixes it; a non-transient failure now aborts loudly instead of being masked.
#
# Usage:
#   tests/Support/docker-import-sql.sh <mariadb-container> <user> <password> <database> <file.sql>
#
# Exit status: 0 on success (or when the file was already applied by a previous
# partial attempt), 1 on a real error.

set -euo pipefail

container=${1:?mariadb container name/id required}
user=${2:?db user required}
password=${3:?db password required}
database=${4:?db name required}
file=${5:?sql file path required}

max_attempts=${DB_IMPORT_MAX_ATTEMPTS:-8}
retry_sleep=${DB_IMPORT_RETRY_SLEEP:-2}

attempt=0
while :; do
    attempt=$((attempt + 1))

    # First attempt: no --force, so a real (non-transient) error aborts loudly.
    # Retry attempts: --force, so DDL statements that already committed on an
    # earlier attempt ("Duplicate column") are skipped while the statement that
    # actually deadlocked still gets re-executed. `make docker-db-prepare` runs a
    # hard schema-completeness check afterwards as the real gate.
    force=''
    [ "$attempt" -gt 1 ] && force='--force'

    if out=$(docker exec -i "$container" mariadb $force -u"$user" -p"$password" "$database" < "$file" 2>&1); then
        [ -n "$out" ] && printf '%s\n' "$out"
        [ "$attempt" -gt 1 ] && echo "  ${file##*/}: succeeded on attempt ${attempt}"
        exit 0
    fi

    if printf '%s' "$out" | grep -qiE 'Deadlock found|Lock wait timeout exceeded|try restarting transaction' \
        && [ "$attempt" -lt "$max_attempts" ]; then
        echo "  ${file##*/}: transient lock error (attempt ${attempt}/${max_attempts}) — retrying in ${retry_sleep}s"
        sleep "$retry_sleep"
        continue
    fi

    echo "ERROR importing ${file} after ${attempt} attempt(s):" >&2
    printf '%s\n' "$out" >&2
    exit 1
done
