#!/bin/bash
set -e
set -o pipefail
trap 'echo "Error on line $LINENO" >&2; exit 1' ERR

# Computes the next v1 release version and, after confirmation, creates and
# pushes the tag — which is all it takes to trigger the "Release on Tag"
# GitHub Action (.github/workflows/release-tag.yml). Mirrors that workflow's
# own version-resolution logic exactly, so what you get here is what CI
# would also compute via workflow_dispatch.
#
# Usage:
#   ./cut-release.sh              # rc (default): v1.7.2-rc-1, -rc-2, ...
#   ./cut-release.sh beta         # v1.7.2-beta-1, -beta-2, ...
#   ./cut-release.sh stable       # v1.7.2 (errors if already tagged)
#   ./cut-release.sh rc --yes     # skip the confirmation prompt
#
# Run from the repository root.

RELEASE_TYPE="rc"
SKIP_CONFIRM=false

for arg in "$@"; do
  case "$arg" in
    rc|beta|stable) RELEASE_TYPE="$arg" ;;
    --yes|-y) SKIP_CONFIRM=true ;;
    *)
      echo "ERROR: Unknown argument '$arg'. Usage: $0 [rc|beta|stable] [--yes]" >&2
      exit 1
      ;;
  esac
done

SQL_DIR="application/modules/setup/sql"
if [ ! -d "$SQL_DIR" ]; then
  echo "ERROR: $SQL_DIR not found. Run this from the InvoicePlane repository root." >&2
  exit 1
fi

echo "Fetching tags from origin..."
git fetch origin --tags --quiet

# The highest-numbered migration file names the version the codebase
# currently builds towards — the same source of truth release-tag.yml uses.
LATEST_SQL_FILE=$(find "$SQL_DIR" -maxdepth 1 -type f -name '[0-9][0-9][0-9]_*.sql' | sort | tail -n 1)
if [ -z "$LATEST_SQL_FILE" ]; then
  echo "ERROR: No migration files found in $SQL_DIR — cannot determine the base version." >&2
  exit 1
fi
BASE_VERSION=$(basename "$LATEST_SQL_FILE" .sql | sed -E 's/^[0-9]+_//')
echo "Base version from latest schema migration ($(basename "$LATEST_SQL_FILE")): $BASE_VERSION"

# package.json must agree — same cross-check build-scripts/workflow.sh does
# before it will actually build a release.
PACKAGE_VERSION=$(php -r 'echo json_decode(file_get_contents($argv[1]))->version ?? "";' package.json)
if [ -z "$PACKAGE_VERSION" ]; then
  echo "ERROR: Could not read \"version\" from package.json." >&2
  exit 1
fi
PACKAGE_BASE_VERSION="${PACKAGE_VERSION%%-*}"
if [ "$PACKAGE_BASE_VERSION" != "$BASE_VERSION" ]; then
  echo "ERROR: package.json targets $PACKAGE_BASE_VERSION but the latest schema migration" >&2
  echo "       targets $BASE_VERSION. Bump package.json's version before releasing." >&2
  exit 1
fi
echo "package.json agrees: $PACKAGE_VERSION"

case "$RELEASE_TYPE" in
  stable)
    VERSION="v${BASE_VERSION}"
    if git rev-parse -q --verify "refs/tags/${VERSION}" >/dev/null; then
      echo "ERROR: Tag ${VERSION} already exists." >&2
      exit 1
    fi
    ;;
  rc|beta)
    LAST_NUM=$(git tag -l "v${BASE_VERSION}-${RELEASE_TYPE}-*" | sed -E "s/^v${BASE_VERSION}-${RELEASE_TYPE}-//" | sort -n | tail -n 1)
    NEXT_NUM=$(( ${LAST_NUM:-0} + 1 ))
    VERSION="v${BASE_VERSION}-${RELEASE_TYPE}-${NEXT_NUM}"
    ;;
esac

# Regression guard, scoped to v1.x — v2.0.0-alpha.1 etc. belong to the
# unrelated release.yml/v2 line and must not be compared against here.
LATEST_TAG=$(git tag -l | grep -E '^v1\.[0-9]+\.[0-9]+(-.*)?$' | sort -V | tail -n 1 || true)
if [ -n "$LATEST_TAG" ]; then
  LATEST_BASE="${LATEST_TAG%%-*}"
  THIS_BASE="v${BASE_VERSION}"
  HIGHEST=$(printf '%s\n%s\n' "$LATEST_BASE" "$THIS_BASE" | sort -V | tail -n 1)
  if [ "$HIGHEST" = "$LATEST_BASE" ] && [ "$THIS_BASE" != "$LATEST_BASE" ]; then
    echo "ERROR: ${VERSION} (base ${THIS_BASE}) is older than the latest existing tag ${LATEST_TAG}." >&2
    exit 1
  fi
fi

echo ""
echo "About to tag and push: ${VERSION}"
echo "This triggers the 'Release on Tag' GitHub Action (.github/workflows/release-tag.yml)."
echo ""

if [ "$SKIP_CONFIRM" != true ]; then
  read -r -p "Proceed? [y/N] " reply
  case "$reply" in
    [yY]|[yY][eE][sS]) ;;
    *) echo "Aborted."; exit 1 ;;
  esac
fi

git tag "$VERSION"
git push origin "$VERSION"
echo "Pushed ${VERSION} — check the Actions tab for the release build."
