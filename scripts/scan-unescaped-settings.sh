#!/bin/bash
# Security: Scan for unescaped get_setting() calls that could lead to XSS vulnerabilities
# Usage: bash scripts/scan-unescaped-settings.sh [output-format]
# Formats: summary (default), detailed, json

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUTPUT_FORMAT="${1:-summary}"

# Colors for terminal output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo "\xf0\x9f\x94\x8d Scanning for unescaped get_setting() calls in views..."
echo "Repository: $REPO_ROOT"
echo ""

# Find all get_setting() calls in views that are NOT wrapped in htmlsc/html_escape
# This searches for echo/sprintf statements with get_setting but no escaping wrapper
UNESCAPED=$(grep -r "get_setting" "$REPO_ROOT/application/views/" \
    --include="*.php" \
    | grep -v "htmlsc\|html_escape\|_htmlsc" \
    | grep -v "get_setting([^)]*,[[:space:]]*true)" \
    | grep -E "echo|sprintf" \
    | grep -v "^[[:space:]]*//") || true

if [ -z "$UNESCAPED" ]; then
    echo "\xe2\x9c\x85 No unescaped get_setting() calls found in views!"
    echo ""
    exit 0
fi

case "$OUTPUT_FORMAT" in
    summary)
        echo "\xe2\x9a\xa0\xef\xb8\x8f  UNESCAPED get_setting() CALLS FOUND:"
        echo ""
        echo "$UNESCAPED" | awk -F: '{
            file=$1
            if (prev_file != file) {
                if (prev_file != "") print ""
                print "\xf0\x9f\x93\x84 " file
                prev_file=file
            }
            gsub(/^[[:space:]]+/, "", $0)
            match($0, /get_setting\([\x27"][^\x27"]+/)
            setting = substr($0, RSTART + 13, RLENGTH - 13)
            printf "   \xe2\x80\xa2 Setting: %s\n", setting
        }' | sort -u

        echo ""
        echo "\xf0\x9f\x93\x8a Summary:"
        TOTAL=$(echo "$UNESCAPED" | wc -l)
        UNIQUE_SETTINGS=$(echo "$UNESCAPED" | grep -oE "get_setting\([\x27\"][^\x27\"]+" | sort -u | wc -l)
        echo "   Total unescaped calls: $TOTAL"
        echo "   Unique settings: $UNIQUE_SETTINGS"
        ;;

    detailed)
        echo "\xf0\x9f\x93\x8b DETAILED LISTING:"
        echo ""
        echo "$UNESCAPED" | while IFS=: read -r file line content; do
            echo "\xf0\x9f\x93\x84 $file:$line"
            echo "   $content" | sed 's/^[[:space:]]*//'
            echo ""
        done
        ;;

    json)
        echo "{"
        echo '  "timestamp": "'$(date -Iseconds)'",'
        echo '  "repository": "'$REPO_ROOT'",'
        echo '  "findings": ['

        first=true
        echo "$UNESCAPED" | while IFS=: read -r file line content; do
            if [ "$first" = false ]; then echo ","; fi
            first=false

            setting=$(echo "$content" | grep -oE "get_setting\([\x27\"][^\x27\"]+" | sed "s/get_setting([\x27\"]//" | head -1)

            cat <<EOF
    {
      "file": "$file",
      "line": $line,
      "setting": "$setting",
      "code": "$(echo "$content" | sed 's/"/\\"/g')"
    }
EOF
        done

        echo "  ]"
        echo "}"
        ;;

    *)
        echo "\xe2\x9d\x8c Unknown output format: $OUTPUT_FORMAT"
        echo "   Valid formats: summary, detailed, json"
        exit 1
        ;;
esac

echo ""
echo "\xf0\x9f\x94\x90 Remediation: Wrap all get_setting() calls with htmlsc() or html_escape()"
echo "   Example: htmlsc(get_setting('custom_title'))"
echo ""
