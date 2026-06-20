# No Conflict Markers

## Rule — Never commit or push unresolved merge conflicts

A file containing Git conflict markers (`<<<<<<<`, `=======`, `>>>>>>>`) is
broken. It must never be committed. The CI job `.github/workflows/no-conflict-markers.yml`
will fail the build if any are found.

### When you see a conflict

Resolve it — pick one side, merge both, or rewrite the block — then stage and
commit the resolved file. Never leave conflict markers as "to be sorted later."

### In `.gitignore` specifically

Both sides of a conflict usually add valid entries. Keep all of them:

```gitignore
# Wrong — conflict left unresolved
<<<<<<< HEAD
/phpunit.txt
=======
/todo.txt
/yarnpack.txt
.phpunit.result.cache
>>>>>>> 86d98da0

# Correct — all entries kept, markers removed
/phpunit.txt
/todo.txt
/yarnpack.txt
.phpunit.result.cache
```

### CI check

The workflow runs `git grep` for the three marker patterns on every push and PR.
The YAML, vendor, `.claude/`, and `LICENSE.txt` directories are excluded to avoid
false positives in third-party files and skill examples.
