# Package Update Report Generator

This script generates a readable package update report from yarn.lock changes.

## Purpose

When Yarn dependencies are updated via the automated workflow, this script analyzes the git diff of `yarn.lock` and generates a human-readable report showing:

1. **Direct Dependencies** - Packages explicitly listed in `package.json`
2. **Transitive Dependencies** - Dependencies of dependencies

## Output Format

The script generates a tree-like report with clear version transitions:

```
╔═══════════════════════════════════════════════════════════════╗
║                    Package Update Report                     ║
╚═══════════════════════════════════════════════════════════════╝

📦 DIRECT DEPENDENCIES (from package.json)
─────────────────────────────────────────────────────────────────

  ✓ vite
    7.3.0 → 7.4.0

  ✓ tailwindcss
    4.1.10 → 4.1.12


🔗 TRANSITIVE DEPENDENCIES (dependencies of dependencies)
─────────────────────────────────────────────────────────────────

  └─ esbuild
     0.27.1 → 0.27.2

  └─ rollup
     4.28.0 → 4.29.1


═════════════════════════════════════════════════════════════════
SUMMARY: 2 direct, 2 transitive (4 total)
═════════════════════════════════════════════════════════════════
```

## Usage

The script is automatically run by the `yarn-update.yml` GitHub Actions workflow. It can also be run manually:

```bash
# Run from the repository root
node .github/scripts/generate-package-update-report.cjs
```

### Requirements

- Node.js (the version used by the project)
- Git (for detecting changes in yarn.lock)
- Must be run from the repository root directory

## How It Works

1. Reads `package.json` to identify direct dependencies
2. Parses `git diff yarn.lock` to detect version changes
3. Categorizes each updated package as direct or transitive
4. Generates a formatted report with clear version transitions
5. Writes the report to `updated-packages.txt`

## Integration with Workflow

The script is called in the `yarn-update.yml` workflow after dependency updates:

```yaml
- name: Get updated packages
  if: steps.check-changes.outputs.changes_detected == 'true'
  run: |
    node .github/scripts/generate-package-update-report.cjs
```

The generated report is then included in the pull request description for easy review.

## Benefits

- **Readability**: Clean, scannable format vs. raw yarn.lock diff
- **Clarity**: Direct dependencies highlighted separately from transitive ones
- **Version Tracking**: Clear "from → to" notation for all updates
- **Consistency**: Similar to `yarn upgrade` output that developers are familiar with

---

# PHPStan Results Parser

Parses PHPStan JSON output and generates formatted, actionable reports.

## parse-phpstan-results.php

### Purpose

PHPStan's default output can be verbose and difficult to parse, especially when integrating with Copilot or creating PR comments. This script:

1. **Groups errors by file and category** for easier comprehension
2. **Strips noise** and formats messages for readability
3. **Generates actionable checklists** suitable for GitHub PRs
4. **Categorizes errors** (type errors, method errors, property errors, etc.)

### Usage

#### Local Development

```bash
# Generate JSON output from PHPStan
vendor/bin/phpstan analyse --error-format=json > phpstan.json

# Parse and format the results
php .github/scripts/parse-phpstan-results.php phpstan.json > phpstan-report.md

# View the formatted report
cat phpstan-report.md
```

#### In GitHub Actions

The script is automatically called by the PHPStan workflow (`.github/workflows/phpstan.yml`):

```yaml
- name: Run PHPStan (JSON output)
  run: |
    vendor/bin/phpstan analyse --memory-limit=1G --error-format=json > phpstan.json || true

- name: Parse and format PHPStan results
  run: |
    php .github/scripts/parse-phpstan-results.php phpstan.json > phpstan-report.md
```

### Output Format

The script generates a markdown report with:

1. **Error Summary** - Total errors and breakdown by category
2. **Detailed Errors** - Grouped by file with line numbers
3. **Actionable Checklist** - Ready-to-use task list for fixing errors

Example output:

```markdown
## 🔍 PHPStan Analysis Report

**Total Errors:** 15

### 📊 Error Summary by Category

- ↩️ **Return Type Errors**: 5 error(s)
- 🔧 **Method Errors**: 7 error(s)
- 🔢 **Type Errors**: 3 error(s)

### 📝 Detailed Errors by File

#### 1. `Modules/Core/Models/User.php` (3 error(s))

- **Line 45** [Return Type Errors]: Method should return Company but returns Collection
- **Line 78** [Method Errors]: Cannot call method label() on string
...

### ✅ Actionable Checklist

- [ ] Fix error in `Modules/Core/Models/User.php:45` - Method should return Company...
- [ ] Fix error in `Modules/Core/Models/User.php:78` - Cannot call method label()...
```

### Integration with Copilot

The formatted output is optimized for Copilot:

- **JSON** as source format (precise, machine-readable)
- **Trimmed context** focusing on actionable items
- **Explicit categorization** for better understanding
- **Checklist format** for task tracking

### Best Practices

1. **Run PHPStan locally** before committing:
   ```bash
   vendor/bin/phpstan analyse --error-format=json > phpstan.json
   php .github/scripts/parse-phpstan-results.php phpstan.json
   ```

2. **Use the checklist** to track fixes systematically

3. **Feed to Copilot** for automated suggestions:
   - Copy the formatted report
   - Paste into Copilot chat
   - Ask for fixes grouped by category

4. **Generate baseline** when needed:
   ```bash
   vendor/bin/phpstan analyse --generate-baseline
   ```

### Customization

Edit the script to adjust:

- **Error categorization** in `categorizeError()`
- **Message formatting** in `trimMessage()`
- **Output format** in the main generation loop

### Dependencies

- PHP 8.2+ (project-wide minimum; uses `??` null coalescing operator and `str_contains()`)
- PHPStan installed via Composer
- JSON extension enabled (standard with PHP)
- mbstring extension enabled (for multi-byte string handling)
