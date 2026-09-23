#!/usr/bin/env bash
#
# One-command PHPUnit + PHPStan bootstrap for the Claude Code web sandbox.
#
# Regular local dev and CI do NOT need this — CI's composer.json already lists
# phpunit/phpstan as dev deps and installs them normally (see .github/workflows/*.yml).
# This script exists only because the web sandbox's outbound proxy hard-blocks
# api.github.com and codeload.github.com (a 403 policy denial, not a timeout — see
# "Hard-blocked hosts" in CLAUDE.md), so `composer install` with dev deps included
# always hangs/fails on phpstan's dist download. Composer *source* installs (git
# through the proxy) and plain `https://github.com/.../releases/download/...` URLs
# both work fine — this script only ever uses those two paths.
#
# It is idempotent: safe to re-run, each step skips if its output already exists.
# All state goes under $REPO/.sandbox-tools (gitignored) — never /tmp, which is a
# tmpfs mount that can be wiped mid-session and isn't a shared convention with the
# rest of this repo's tooling (tests/Support/sandbox-mariadb.sh follows the same
# repo-local-not-/tmp rule for its own state).
#
# Usage (from the repo root):
#   bash tests/Support/sandbox-bootstrap.sh
#   env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD \
#     php .sandbox-tools/punit/vendor/bin/phpunit --bootstrap tests/bootstrap.php
#   php .sandbox-tools/phpstan.phar analyse --memory-limit=1G
#
# (DB_* must stay unset for the phpunit *parent* process — see the "MariaDB test
# database in the sandbox" gotcha in CLAUDE.md. That's unrelated to this script;
# provision the DB separately with tests/Support/sandbox-mariadb.sh.)
set -euo pipefail

: "${PHPSTAN_VERSION:=1.12.34}"

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." >/dev/null 2>&1 && pwd)"
TOOLS="$REPO/.sandbox-tools"
COMPOSER_HOME="$TOOLS/composer-home"
PUNIT="$TOOLS/punit"

mkdir -p "$TOOLS" "$COMPOSER_HOME"
printf '{}\n' > "$COMPOSER_HOME/auth.json"
printf '{"config":{}}\n' > "$COMPOSER_HOME/config.json"
export COMPOSER_HOME

# 1. Runtime dependencies only. --no-dev skips the phpstan dep chain (the thing
#    that hangs on the blocked hosts) so this step completes even on a locked-down
#    sandbox. --prefer-source forces git clones instead of blocked dist zipballs.
if [ ! -f "$REPO/vendor/autoload.php" ]; then
    echo "==> composer install (runtime deps, --no-dev, --prefer-source)"
    (cd "$REPO" && composer install --prefer-source --no-dev --ignore-platform-req=ext-bcmath)
else
    echo "==> vendor/ already present, skipping composer install"
fi

# 2. PHPUnit in a throwaway sibling project — never inside $REPO/vendor, so it
#    can't collide with the runtime-only install from step 1, and installs faster
#    in isolation than trying to fold it into the main composer.json.
if [ ! -x "$PUNIT/vendor/bin/phpunit" ]; then
    echo "==> installing PHPUnit into $PUNIT (throwaway, --prefer-source)"
    mkdir -p "$PUNIT"
    (cd "$PUNIT" && timeout 300 composer require --prefer-source --dev phpunit/phpunit:^10.5 \
        --ignore-platform-req=ext-bcmath) || {
        echo "!! PHPUnit install timed out or failed. Re-run this script — it's idempotent" >&2
        echo "!! and will resume from here. See CLAUDE.md 'Hard-blocked hosts' if it keeps failing." >&2
        exit 1
    }
else
    echo "==> PHPUnit already installed at $PUNIT, skipping"
fi

# 3. Step 1's --no-dev dropped the Tests\ PSR-4 mapping — restore it.
echo "==> composer dump-autoload --dev"
(cd "$REPO" && composer dump-autoload --dev)

# 4. PHPStan phar — dist-only package, but the plain release download isn't behind
#    the blocked hosts (checked: only the composer api/codeload path is blocked).
if [ ! -s "$TOOLS/phpstan.phar" ]; then
    echo "==> downloading phpstan.phar $PHPSTAN_VERSION"
    curl -sSL -o "$TOOLS/phpstan.phar" \
        "https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar"
    chmod +x "$TOOLS/phpstan.phar"
else
    echo "==> phpstan.phar already present, skipping"
fi

cat <<EOF

==> Ready. Run tests with:
    env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD \\
      php "$PUNIT/vendor/bin/phpunit" --bootstrap tests/bootstrap.php

==> Run static analysis with:
    php "$TOOLS/phpstan.phar" analyse --memory-limit=1G

(Provision the sandbox MariaDB first if you haven't: bash tests/Support/sandbox-mariadb.sh)
EOF
