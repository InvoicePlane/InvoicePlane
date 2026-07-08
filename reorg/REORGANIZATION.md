# e-invoicing branch reorganization — reality map & execution plan

Investigation of the branches involved in the requested split, what actually
holds vs. what the plan assumed, and exact commands to execute each achievable
piece.

## Executed status

- **Step 1 (feature → -improved): DONE.** Built `-improved` with `einvoice/`
  replaced by prep/v180's `integrations/` module (+ wiring, composer classmap;
  test/entry-point scaffolding excluded). Pushed to
  `InvoicePlane/InvoicePlane:claude/einvoicing-branch-reorganization-ci28h7` and
  opened **PR #1614 → einvoicing-provider-integration-improved**.
- **Step 3 (clean prep/v180): BUILT, NOT PUSHED.** Local commit reduces
  underdogg `prep/v180` to phpunit harness + `public/index.php` prep only
  (integrations feature removed, −7360 lines). Force-updating shared `prep/v180`
  awaits explicit go-ahead.
- **Step 2 (4 einvoice UI improvements → einvoicing-provider-integration):
  DEFERRED** until PR #1614 merges, per the requested sequencing. Patch series
  in `reorg/improvement-patches/`.

## Branch inventory (SHAs at time of writing)

| Branch | Repo | SHA | What it actually is |
|---|---|---|---|
| `einvoicing-provider-integration` | invoiceplane | `79d84b3` | `einvoice/` module, 4 improvements applied **then reverted** |
| `einvoicing-provider-integration-improved` | invoiceplane | `955b9e4` | `einvoice/` module **with** the 4 improvements kept |
| `claude/integrations-for-1582` | underdogg | `98c60c7` | security-hardening branch, **already merged into `develop`** |
| `claude/integrations-for-1609` | underdogg | `98c60c7` | **identical commit to 1582** |
| `prep/v180` | underdogg | `a96b5b8` | `integrations/` module + phpunit infra + develop syncs |
| `prep/v180` | invoiceplane | `ccf3090` | clean ancestor of underdogg's prep/v180 |

## Where the plan diverges from reality

1. **1582 and 1609 are the same commit** (`98c60c7`) — "1609 merges into 1582"
   is a no-op, and that commit is a security-hardening branch **already in
   `develop`**, not the e-invoicing improvements.
2. **The real "improvements"** are 5 commits (`703bb58..955b9e4`): align UI,
   improve navigation, improve code, clean routes, pint — on the
   `application/modules/einvoice/` module. `einvoicing-provider-integration`
   contains those same commits **and then reverts all four**, so the delta
   between the two einvoicing branches *is exactly those improvements*. ✅ This
   half of the dream is clean and correct.
3. **underdogg `prep/v180` ships a different module**:
   `application/modules/integrations/` (LetsPeppol + Qonto + SuperPdp, issues
   #31–34), **not** `application/modules/einvoice/`. The two lines share only
   the `develop` ancestor `530cb6c`; prep/v180's feature work is **not** derived
   from `-improved`.
4. **Two `prep/v180` branches** exist; the invoiceplane one is a clean ancestor
   of underdogg's (`ip/prep/v180` + ~16 commits).
5. **prep/v180's split is not mechanical**: of 312 delta files, ~56 are the
   `integrations` feature (+ its tests), ~12 are clear release scaffolding, and
   ~244 are develop-sync noise (unrelated tests, SCSS, other modules). The
   feature and scaffolding are interleaved across "Sync with develop" merges.

## The clean, confident deliverable — the einvoice improvements

`reorg/improvement-patches/` holds the 5-commit series that is the entire
difference between `-improved` and `einvoicing-provider-integration`. To offer
it as a PR (the direction the dream intends — improvements onto the einvoice
module):

```
# on invoiceplane/invoiceplane
git checkout -b einvoice-improvements einvoicing-provider-integration
git am reorg/improvement-patches/*.patch
git push -u origin einvoice-improvements
# open PR: einvoice-improvements -> einvoicing-provider-integration-improved
```
Note: `-improved` *already contains* these commits, so a PR **into** `-improved`
would be empty. The meaningful PR is `einvoicing-provider-integration` +
improvements **→** `-improved` (which makes them equal), or improvements **→**
`einvoicing-provider-integration` (removing the reverts).

## The release-prep split of prep/v180 — needs one decision

Clear scaffolding files (safe to treat as "prep, keep in prep/v180"):
```
.github/workflows/phpunit.yml   .github/workflows/phpstan.yml
application/controllers/Phpunit.php   phpunit.xml.dist
bootstrap/constants.php  bootstrap/env.php  bootstrap/kernel.php
composer.json  composer.lock
resources/rector/AddCoversClassRector.php
resources/rector/MarkWeakTestIncompleteRector.php   testsrector.php
```
Clear feature files (the `integrations` module, candidate to lift into a PR):
```
application/modules/integrations/**   (31 files)
tests/**Integration** / **Peppol** / **Qonto** / **Merchant**  (~25 files)
```
The ~244 remaining files are develop-sync artifacts and need a rule from you:
keep in prep/v180, or drop as noise.

## Open decisions (blocking outward-facing pushes)

- **Feature-PR content**: the 4 einvoice improvements (clean) — or lift
  prep/v180's `integrations/` module into `-improved` (larger, parallel module,
  likely conflicts)?
- **prep/v180 target**: build scaffolding-only result on my review branch, or
  force-update the shared `prep/v180`? (The latter rewrites shared history —
  explicit go-ahead only.)
