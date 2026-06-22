# Merge Corruption Prevention

## Protected Files

These files MUST be treated as "ours" in any merge. They contain runtime patches
(SQLite UDFs, CI3 inheritance fix) that are absent on other branches.

| File | What it contains |
|------|-----------------|
| `application/third_party/MX/Ci.php` | `class CI extends CI_Controller` fix |
| `application/third_party/MX/Controller.php` | HMVC controller base |
| `application/third_party/MX/Loader.php` | SQLite UDF registration + module loader |
| `application/third_party/MX/Modules.php` | Module discovery |
| `tests/Integration/Upgrade/UpgradeRegressionTest.php` | Deterministic assertions (not snapshots) |

## Before Any Merge

```bash
git log --oneline application/third_party/MX/Loader.php   # note last-known-good SHA
```

## After Any Merge — Verify

```bash
# MX base class present
grep -l "class CI extends CI_Controller" application/third_party/MX/Ci.php

# SQLite UDFs registered
grep -c "createFunction" application/third_party/MX/Loader.php

# Regression test uses real assertions, not snapshots
grep -c "assertDatabaseHas\|assertEquals\|assertSame" tests/Integration/Upgrade/UpgradeRegressionTest.php
grep -c "assertMatchesSnapshot" tests/Integration/Upgrade/UpgradeRegressionTest.php
```

Expected: first three greps return non-zero; last grep returns 0.

## Restore If Corrupted

```bash
git checkout <last-known-good-sha> -- \
  application/third_party/MX/Ci.php \
  application/third_party/MX/Controller.php \
  application/third_party/MX/Loader.php \
  application/third_party/MX/Modules.php \
  tests/Integration/Upgrade/UpgradeRegressionTest.php
```

## Permanent Guard (.gitattributes)

```
application/third_party/MX/Ci.php         merge=ours
application/third_party/MX/Controller.php merge=ours
application/third_party/MX/Loader.php     merge=ours
application/third_party/MX/Modules.php    merge=ours
tests/Integration/Upgrade/UpgradeRegressionTest.php merge=ours
```

Enable once:
```bash
git config merge.ours.driver true
```
