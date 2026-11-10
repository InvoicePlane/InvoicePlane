#!/usr/bin/env bash
# Line coverage for PHPUnit AND the Feature-test request subprocesses, using PCOV.
#
#   bash tests/Support/coverage/run.sh [phpunit args...]     # e.g. tests/Feature/Upload
#
# Needs the pcov extension. Not installable via apt/pecl in the sandbox, but it builds from git:
#   git clone --depth 1 https://github.com/krakjoe/pcov && cd pcov && phpize && ./configure && make
# then export PCOV_SO=/path/to/pcov/modules/pcov.so  (skip if `php -m` already lists pcov).
#
# How it works: a `php` shim first on PATH adds pcov + prepend.php, so request.php children
# (spawned as plain `php`) are instrumented too. Run it ALONE: like the normal suite it
# truncates the shared test DB, so never run two at once (see CLAUDE.md).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
REAL_PHP="$(command -v php)"
WORK="$ROOT/.sandbox-tools/coverage"
rm -rf "$WORK" && mkdir -p "$WORK/dumps" "$WORK/bin"

EXT_ARGS=()
if ! "$REAL_PHP" -m | grep -qi '^pcov$'; then
    : "${PCOV_SO:?pcov is not loaded; set PCOV_SO=/path/to/pcov.so (see header)}"
    EXT_ARGS=(-d "extension=$PCOV_SO")
fi

cat > "$WORK/bin/php" <<SHIM
#!/usr/bin/env bash
exec "$REAL_PHP" ${EXT_ARGS[@]+"${EXT_ARGS[@]}"} -d pcov.enabled=1 -d pcov.directory="$ROOT" -d auto_prepend_file="$ROOT/tests/Support/coverage/prepend.php" "\$@"
SHIM
chmod +x "$WORK/bin/php"

PHPUNIT="$ROOT/.sandbox-tools/punit/vendor/bin/phpunit"
[ -x "$PHPUNIT" ] || PHPUNIT="$ROOT/vendor/bin/phpunit"

cd "$ROOT"
set +e
PATH="$WORK/bin:$PATH" IP_COVERAGE_DIR="$WORK/dumps" IP_COVERAGE_ROOT="$ROOT" \
    env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD \
    php "$PHPUNIT" --no-coverage "$@"
STATUS=$?
set -e

echo
"$REAL_PHP" "$ROOT/tests/Support/coverage/report.php" "$WORK/dumps" "${COVERAGE_MIN_UNCOVERED:-30}"
exit $STATUS
