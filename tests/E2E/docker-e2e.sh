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

if ! docker inspect "$CONTAINER" >/dev/null 2>&1; then
  echo "container '$CONTAINER' is not running — start the ivpldock stack first" >&2
  exit 1
fi

IP="$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{"\n"}}{{end}}' "$CONTAINER" | head -n1)"
BASE="http://${IP}:${PORT}"

if ! curl -sf -o /dev/null "${BASE}/sessions/login" 2>/dev/null; then
  echo "starting app server in ${CONTAINER} at ${BASE}"
  docker exec "$CONTAINER" pkill -f "php -d variables_order=EGPCS -S 0.0.0.0:${PORT}" 2>/dev/null || true
  # IP_URL      -> CI3 emits absolute URLs on the origin Playwright browses
  # COOKIE_SECURE=false -> a bare container IP is not a "secure context", so a
  #                Secure session cookie would be dropped over plain http
  # variables_order=EGPCS -> this php.ini omits E; without it env() (which reads
  #                $_ENV only) can't see the two vars above
  # Prepare ipconfig.php for the E2E server (like docker-db-prepare does)
  docker exec "$CONTAINER" bash -lc "
    cd ${APP_DIR} &&
    cp ipconfig.php.example ipconfig.php &&
    sed -i \
      -e \"s|^DB_HOSTNAME=.*|DB_HOSTNAME=mariadb|\" \
      -e \"s|^DB_PORT=.*|DB_PORT=3306|\" \
      -e \"s|^DB_DATABASE=.*|DB_DATABASE=invoiceplane_test|\" \
      -e \"s|^DB_USERNAME=.*|DB_USERNAME=root|\" \
      -e \"s|^DB_PASSWORD=.*|DB_PASSWORD=root|\" \
      -e \"s|^SETUP_COMPLETED=.*|SETUP_COMPLETED=true|\" \
      -e \"s|^ENCRYPTION_KEY=.*|ENCRYPTION_KEY=0123456789abcdef0123456789abcdef|\" \
      -e \"s|^IP_URL=.*|IP_URL=${BASE}|\" \
      -e \"s|^CI_ENV=.*|CI_ENV=testing|\" \
      -e \"s|^# CSRF_PROTECTION=.*|CSRF_PROTECTION=false|\" \
      -e \"s|^CSRF_PROTECTION=.*|CSRF_PROTECTION=false|\" \
      ipconfig.php &&
    printf \"\nDB_DRIVER=mysqli\nCSRF_PROTECTION=false\nCOOKIE_SECURE=false\n\" >> ipconfig.php
  "
  
  docker exec -d \
    -e IP_URL="$BASE" \
    -e COOKIE_SECURE=false \
    -e CI_ENV=testing \
    "$CONTAINER" sh -lc \
    "cd ${APP_DIR} && php -d variables_order=EGPCS -S 0.0.0.0:${PORT} -t . bootstrap/test-server.php > /tmp/e2e-srv.log 2>&1"
  for _ in $(seq 1 30); do
    curl -sf -o /dev/null "${BASE}/sessions/login" 2>/dev/null && break
    sleep 1
  done
  curl -sf -o /dev/null "${BASE}/sessions/login"
fi

export E2E_BASE_URL="$BASE"
export DB_HOSTNAME="${DB_HOSTNAME:-127.0.0.1}"

echo "E2E_BASE_URL=${E2E_BASE_URL}  DB_HOSTNAME=${DB_HOSTNAME}"
exec npx playwright test "$@"
