# Merge Corruption Prevention

## What happened

The merge from `claude/vigilant-brahmagupta-jwp454` into `claude/relaxed-heisenberg-cr2w74`
corrupted several critical files:

1. **MX extension files** (`application/third_party/MX/Ci.php`, `Controller.php`,
   `Loader.php`, `Modules.php`) — the merge took the wrong side, replacing our
   working HMVC implementation with an older version that lacked SQLite UDFs, the
   `CI extends CI_Controller` fix, and other runtime patches.

2. **`tests/Integration/Upgrade/UpgradeRegressionTest.php`** — our deterministic
   assertion-based version (from commit `11ddcf00`) was replaced by the old
   snapshot-based version from the incoming branch.

## Files to protect

These files MUST be treated as "ours" in any future merge:

| File | Reason |
|------|--------|
| `application/third_party/MX/Ci.php` | `class CI extends CI_Controller` fix |
| `application/third_party/MX/Controller.php` | HMVC controller base |
| `application/third_party/MX/Loader.php` | SQLite UDF registration + module loader |
| `application/third_party/MX/Modules.php` | Module discovery |
| `tests/Integration/Upgrade/UpgradeRegressionTest.php` | Deterministic assertions |

## Before any merge

1. **Check out the files you want to keep**:
   ```bash
   git stash  # or commit current work
   git log --oneline application/third_party/MX/Loader.php  # confirm last-known-good commit
   ```

2. **After the merge, immediately verify**:
   ```bash
   git diff HEAD~1 application/third_party/MX/
   git diff HEAD~1 tests/Integration/Upgrade/UpgradeRegressionTest.php
   ```

3. **Restore if corrupted** (replace `<good-commit>` with the known-good SHA):
   ```bash
   git checkout <good-commit> -- application/third_party/MX/Ci.php
   git checkout <good-commit> -- application/third_party/MX/Controller.php
   git checkout <good-commit> -- application/third_party/MX/Loader.php
   git checkout <good-commit> -- application/third_party/MX/Modules.php
   git checkout <good-commit> -- tests/Integration/Upgrade/UpgradeRegressionTest.php
   ```

## .gitattributes merge strategy

Add a `.gitattributes` file to force "ours" strategy on critical files:

```
application/third_party/MX/Ci.php         merge=ours
application/third_party/MX/Controller.php merge=ours
application/third_party/MX/Loader.php     merge=ours
application/third_party/MX/Modules.php    merge=ours
tests/Integration/Upgrade/UpgradeRegressionTest.php merge=ours
```

Enable the `ours` merge driver in git config:
```bash
git config merge.ours.driver true
```

This prevents the files from being overwritten during `git merge` or `git rebase`.

## Detecting corruption

After any merge, run:

```bash
# Check MX files have the CI_Controller base
grep -l "class CI extends CI_Controller" application/third_party/MX/Ci.php

# Check SQLite UDFs are registered
grep -c "createFunction" application/third_party/MX/Loader.php

# Check UpgradeRegressionTest uses deterministic assertions (not snapshots)
grep "assertDatabaseHas\|assertEquals\|assertSame" tests/Integration/Upgrade/UpgradeRegressionTest.php
grep "assertMatchesSnapshot\|snapshot" tests/Integration/Upgrade/UpgradeRegressionTest.php
```

If `grep "class CI extends CI_Controller"` returns nothing, or SQLite UDF count
is 0, or the regression test has snapshot assertions — restore from the last
known-good commit.

## Known-good commits

| Commit | What it contains |
|--------|-----------------|
| `11ddcf00` | UpgradeRegressionTest with deterministic assertions |

Always add new known-good commit SHAs here when making significant MX changes.
