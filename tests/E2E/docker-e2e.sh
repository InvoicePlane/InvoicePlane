#!/usr/bin/env sh
# Run the Playwright E2E suite against the app served by the ivpldock nginx stack.
#
#   tests/E2E/docker-e2e.sh                 # whole suite
#   tests/E2E/docker-e2e.sh tests/E2E/Products
#   tests/E2E/docker-e2e.sh --ui
#
# The app runs in the ivpldock nginx/php-fpm stack (so ipconfig.php's DB_HOSTNAME=mariadb resolves);
# Playwright and the per-test DB reset run on the host and reach the same MariaDB
# on the published 127.0.0.1:3306.
set -eu

NGINX_CONTAINER="${IVPLDOCK_NGINX:-ivpldock-nginx-1}"
WORKSPACE_CONTAINER="${IVPLDOCK_WORKSPACE:-ivpldock-workspace-1}"
APP_DIR="${DOCKER_APP_DIR:-/var/www/projects/invoiceplane/ivpl-devcheck}"

if ! docker inspect "$NGINX_CONTAINER" >/dev/null 2>&1; then
  echo "container '$NGINX_CONTAINER' is not running — start the ivpldock stack first" >&2
  exit 1
fi

if ! docker inspect "$WORKSPACE_CONTAINER" >/dev/null 2>&1; then
  echo "container '$WORKSPACE_CONTAINER' is not running — start the ivpldock stack first" >&2
  exit 1
fi

# Get nginx container IP for direct access
IP="$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{"\n"}}{{end}}' "$NGINX_CONTAINER" | head -n1)"
BASE="http://${IP}"

# Ensure the app is reachable via nginx
if ! curl -sf -o /dev/null "${BASE}/sessions/login" 2>/dev/null; then
  echo "nginx server not responding at ${BASE} — check ivpldock stack" >&2
  exit 1
fi

# Ensure test database is seeded (run from workspace container with correct DB host)
echo "Seeding test database..."
docker exec "$WORKSPACE_CONTAINER" bash -lc "
  cd ${APP_DIR} &&
  DB_HOSTNAME=mariadb php tests/Support/reset-test-db.php
"

export E2E_BASE_URL="$BASE"
export DB_HOSTNAME="${DB_HOSTNAME:-127.0.0.1}"

echo "E2E_BASE_URL=${E2E_BASE_URL}  DB_HOSTNAME=${DB_HOSTNAME}"
exec npx playwright test "$@"