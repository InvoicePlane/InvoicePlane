---
name: sync-stale-branches
description: Brings diverged remote branches up to date with develop — classifies, rescues unique work, then resets or deletes stale branches
---

# Skill: sync-stale-branches

Bring old/diverged remote branches up to date with `develop`.
Run this periodically to keep the branch list clean and PR-able.

---

## Inputs

- `EXCLUDE` — branches to leave untouched (space-separated, no `origin/` prefix)  
  Default: `develop master`

---

## Step 1 — List candidate branches

```bash
git fetch --prune

# All remote branches minus the exclude list
git branch -r | grep -v 'origin/HEAD' \
  | sed 's|remotes/||' \
  | grep -v -E '^origin/(develop|master)$'
```

Add any other branches to exclude to the grep pattern.

---

## Step 2 — Classify each branch

For every candidate `origin/<branch>`:

**A — unique file count (three-dot diff from merge-base):**
```bash
git diff --name-only origin/develop...origin/<branch> | wc -l
```

**B — files ONLY in the branch (not in develop):**
```bash
git diff --name-only --diff-filter=A origin/develop origin/<branch>
```

Classify as:
- **EMPTY** — A = 0 AND B = 0 → branch adds nothing, safe to delete
- **COVERED** — B > 0 but every file in B is already present in a known feature branch → safe to reset
- **HAS_UNIQUE** — B > 0 with at least one file not in any feature branch → must rescue first

---

## Step 3 — Handle EMPTY branches

These branches were never extended beyond the old fork point.

```bash
git push origin --delete <branch>
```

---

## Step 4 — Handle COVERED branches

All unique files are already captured in a feature branch we are keeping.
Reset the branch to develop HEAD so it is current but carries no stale code.

```bash
git push origin origin/develop:refs/heads/<branch> --force
```

---

## Step 5 — Handle HAS_UNIQUE branches

Rescue uncovered files before resetting.

### 5a — Identify which feature branch the files belong to

Group uncovered files by module/domain:
- `Modules/Foo/…` → belongs to whatever feature owns Foo
- If unclear, create a new feature branch named after the owning issue/feature

### 5b — Extract files onto the correct feature branch

On the target feature branch (must already exist and be ahead of develop):

```bash
git checkout origin/<stale-branch> -- <uncovered-file1> <uncovered-file2> ...
git add <files>
git commit -m "chore: rescue <description> from stale <stale-branch>"
git push origin HEAD --force-with-lease
```

If the target feature branch does not yet exist, use the feature-branch-extraction
procedure to create it properly on top of develop HEAD first.

### 5c — Reset the stale branch to develop

```bash
git push origin origin/develop:refs/heads/<branch> --force
```

---

## Step 6 — Verify

```bash
# Confirm each branch is now equal to develop
for branch in <cleaned-branches>; do
  ahead=$(git rev-list origin/develop..origin/$branch --count)
  behind=$(git rev-list origin/$branch..origin/develop --count)
  echo "$branch → ahead=$ahead behind=$behind"
done
```

Expected: all cleaned branches show `ahead=0 behind=0`.

---

## Notes

- Only force-push to branches that are NOT open PRs unless the PR is yours and you  
  intend to update it.
- GitHub Copilot branches (`copilot/*`) are AI-generated; resetting them is safe —  
  Copilot will recreate them if needed.
- The `--diff-filter=A` flag catches files the branch **adds** that develop lacks.  
  Files the branch **modifies** relative to develop but which also exist in develop  
  are not "unique" — develop's version is preferred.
- Run `git fetch --prune` first so local remote-tracking refs are current.
