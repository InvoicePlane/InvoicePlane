# Git Branch Hygiene

Lessons from the 116-commit rebase disaster and the `prep/v180` ↔ `develop`
conflict spiral. Apply these rules before starting any git operation.

---

## The Rules

### 1. One canonical trunk: `develop`

Everything lands on `develop` first — features, security fixes, style passes,
config changes. No other long-lived branch receives direct commits.

**Bad:** committing a security fix directly to `prep/v180`.
**Good:** commit to `develop`, then let `prep/v180` follow via merge.

---

### 2. Release branches are cut late, followed often

Cut `prep/vX.Y.Z` from `develop` only when the release is imminent (days, not
months). After cutting, keep it in sync with a regular merge from `develop`:

```bash
git fetch origin
git checkout prep/v180
git merge origin/develop          # absorb develop into release branch
```

Do this at least weekly. Each merge is a handful of commits, not hundreds.

---

### 3. PR branches are rebased before merge

Before opening or updating a PR, rebase onto the target branch:

```bash
git fetch origin
git rebase origin/develop         # or origin/prep/v180 if targeting that
```

A PR that sits for more than a week without a rebase is a merge-conflict
accumulator. Keep PR branches short-lived (days) or rebase them regularly.

---

### 4. Never cherry-pick between long-lived branches

Cherry-pick is a sign that the branching model broke down. It produces
duplicate commits with different SHAs that cause conflicts on the next merge.

If you find yourself reaching for cherry-pick across `develop` ↔ `prep/vX`:
- Stop.
- Merge `develop` into the release branch instead.
- If only specific commits are needed, the branching model needs fixing, not
  the individual commits.

---

### 5. Style/lint commits are absorbed, never standalone

A standalone `pint` or `php-cs-fixer` commit on one branch causes a guaranteed
conflict when that branch merges with another that touched the same files.

**Rule:** run pint as a pre-commit hook or in CI. Style changes are absorbed
into the commit that made the code change. No style-only commits ever land on
any branch.

Pre-commit hook setup (once per repo):

```bash
# .git/hooks/pre-commit  (or via Husky / lint-staged)
./vendor/bin/pint --dirty
git add -u
```

---

### 6. The conflict-free rebase checklist

Before any rebase or merge that touches more than ~10 commits:

```bash
# 1. How far have the branches diverged?
git log --oneline origin/develop..HEAD        # commits on this branch not in develop
git log --oneline HEAD..origin/develop        # commits in develop not on this branch

# 2. Which files are at risk?
git diff --name-only HEAD...origin/develop

# 3. Are any of those files in the merge-corruption protected list?
#    (see .claude/skills/merge-corruption-prevention/SKILL.md)

# 4. Is the divergence > 20 commits? Consider a squash instead:
git rebase -i origin/develop                  # squash PR commits first, then rebase
```

If step 3 hits protected files → use `git checkout ours -- <file>` after the
rebase to restore them.

If step 4 shows > 50 commits on either side → reconsider. Merge instead of
rebase, or cherry-pick the specific logical unit (one bounded feature, not a
grab-bag of 8 security fixes that should have been on develop all along).

---

## Quick Reference

| Situation | Right move |
|---|---|
| Security fix needed on both develop and release branch | Land on `develop` first; merge `develop` → release branch |
| Release branch is 50+ commits behind develop | Merge `develop` → release branch (not rebase) |
| PR branch is 10+ commits behind target | `git rebase origin/<target>` now, before it gets worse |
| Need to sync two long-lived branches | Always merge the older-base into the newer-base; never the reverse |
| About to cherry-pick across long-lived branches | Stop; fix the branching model instead |
| Style pass needed | Run pint in CI/pre-commit; do not commit it separately |
