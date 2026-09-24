#!/usr/bin/env sh
# Run the Playwright E2E suite against the app served from inside the ivpldock
# workspace container (the same stack `make docker-test` uses for PHPUnit).
#
#   tests/E2E/docker-e2e.sh                 # whole suite
#   tests/E2E/docker-e2e.sh tests/E2E/Products
#   tests/E2E/docker-e2e.sh --ui
#
# The app runs in the container (so ipconfig.php's DB_HOSTNAME=mariadb resolves);
# Playwright and the per-test DB reset run on the host and reach the same MariaDB
# on the published 127.0.0.1:3306.
set -eu

CONTAINER="${IVPLDOCK_WORKSPACE:-ivpldock-workspace-1}"
APP_DIR="${DOCKER_APP_DIR:-/var/www/projects/invoiceplane/exprmt}"
PORT="${E2E_PORT:-8000}"
CSRF_PORT="${E2E_CSRF_PORT:-8001}"

if ! docker inspect "$CONTAINER" >/dev/null 2>&1; then
  echo "container '$CONTAINER' is not running — start the ivpldock stack first" >&2
  exit 1
fi

IP="$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{"\n"}}{{end}}' "$CONTAINER" | head -n1)"
BASE="http://${IP}:${PORT}"
CSRF_BASE="http://${IP}:${CSRF_PORT}"

start_server() {
  # $1 = port, $2 = base url, $3 = extra docker exec -e args (word-split
  # deliberately — either "" or "-e CSRF_PROTECTION=true")
  port="$1" base="$2" extra_env="$3"
  if curl -sf -o /dev/null "${base}/sessions/login" 2>/dev/null; then
    return
  fi
  echo "starting app server in ${CONTAINER} at ${base}"
  docker exec "$CONTAINER" pkill -f "php -d variables_order=EGPCS -S 0.0.0.0:${port}" 2>/dev/null || true
  # IP_URL      -> CI3 emits absolute URLs on the origin Playwright browses
  # COOKIE_SECURE=false -> a bare container IP is not a "secure context", so a
  #                Secure session cookie would be dropped over plain http
  # variables_order=EGPCS -> this php.ini omits E; without it env() (which reads
  #                $_ENV only) can't see the vars above (or CSRF_PROTECTION below)
  # shellcheck disable=SC2086
  docker exec -d \
    -e IP_URL="$base" \
    -e COOKIE_SECURE=false \
    $extra_env \
    "$CONTAINER" sh -lc \
    "cd ${APP_DIR} && php -d variables_order=EGPCS -S 0.0.0.0:${port} -t . bootstrap/test-server.php > /tmp/e2e-srv-${port}.log 2>&1"
  for _ in $(seq 1 30); do
    curl -sf -o /dev/null "${base}/sessions/login" 2>/dev/null && break
    sleep 1
  done
  curl -sf -o /dev/null "${base}/sessions/login"
}

start_server "$PORT" "$BASE" ""
# A second instance with CSRF_PROTECTION=true for the #1694 regression-parity
# pairs (tests/E2E/support/csrf.js) — see that file for why a second process
# is required rather than a per-request toggle.
start_server "$CSRF_PORT" "$CSRF_BASE" "-e CSRF_PROTECTION=true"

export E2E_BASE_URL="$BASE"
export E2E_CSRF_BASE_URL="$CSRF_BASE"
export DB_HOSTNAME="${DB_HOSTNAME:-127.0.0.1}"

echo "E2E_BASE_URL=${E2E_BASE_URL}  E2E_CSRF_BASE_URL=${E2E_CSRF_BASE_URL}  DB_HOSTNAME=${DB_HOSTNAME}"
exec npx playwright test "$@"
