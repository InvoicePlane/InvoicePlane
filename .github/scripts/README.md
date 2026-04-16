# CI/CD Helper Scripts

This directory contains helper scripts used by the GitHub Actions workflows.

---

## generate-package-update-report.cjs

Generates a readable package update report from `yarn.lock` changes.

### Purpose

When Yarn dependencies are updated via the automated workflow, this script analyzes `git diff yarn.lock` and generates a human-readable report distinguishing:

1. **Direct dependencies** — packages listed in `package.json`
2. **Transitive dependencies** — dependencies of dependencies

### Output format

```
Package Update Report
=================================================================

Direct dependencies (from package.json)
-----------------------------------------------------------------

  vite
    7.3.0 -> 7.4.0

  tailwindcss
    4.1.10 -> 4.1.12


Transitive dependencies (dependencies of dependencies)
-----------------------------------------------------------------

  esbuild
    0.27.1 -> 0.27.2


=================================================================
Summary: 2 direct, 1 transitive (3 total)
=================================================================
```

### Usage

The script is called automatically by `yarn-update.yml`. To run manually from the repository root:

```bash
node .github/scripts/generate-package-update-report.cjs
```

Requirements: Node.js, Git, run from the repository root.

---

## parse-phpstan-results.php

Parses PHPStan JSON output and generates a formatted, actionable report.

### Purpose

Translates PHPStan's JSON output into a clean Markdown report grouped by file and error category. Suitable for display in GitHub Actions logs or PR comments.

### Usage

```bash
# Generate JSON output
vendor/bin/phpstan analyse --memory-limit=1G --error-format=json > phpstan.json

# Format the results
php .github/scripts/parse-phpstan-results.php phpstan.json > phpstan-report.md
```

The `phpstan.yml` workflow runs this automatically.

### Output format

```markdown
## PHPStan Analysis Report

**Total errors:** 15

### Error Summary by Category

- **Return Type Errors**: 5 error(s)
- **Method Errors**: 7 error(s)
- **Type Errors**: 3 error(s)

### Detailed Errors by File

#### 1. `application/modules/invoices/models/Mdl_invoices.php` (3 error(s))

- **Line 45** [Return Type Errors]: Method should return array but returns null
...

### Actionable Checklist

- [ ] Fix error in `application/modules/invoices/models/Mdl_invoices.php:45` - ...
```

### Dependencies

- PHP 8.2+
- PHPStan installed via Composer (`vendor/bin/phpstan`)
- `json` and `mbstring` extensions (standard)
