#!/usr/bin/env python3
"""Per-advisory verification loop for the draft GHSA list.

Phase A (fast, every advisory first): baseline tests green, then the fix PR's patch is reverse-applied
and the tests must go red. Phase B (slow, cheapest advisory first): mutate the lines the fix added.
Results are one JSON file per advisory, so a killed run resumes where it stopped.

Inputs (generated beforehand, not committed): advisory-ledger.json, pr-files.json in DATA.
Run:  python3 -I tests/Support/fully-check-all-vulnerabilities.py [GHSA-id ...]
Do not run any other phpunit while this runs: the mutation tool refuses to start if one is active.
"""
import json, os, re, subprocess, sys, time

REPO = "/data/Projects/invoiceplane/exprmt"
DATA = "/home/wysiwyg/projects/invoiceplane/_scratch"
DONE_OLD = f"{DATA}/mut2"      # first run: advisories finished there count as done in both phases
PHASE_A = f"{DATA}/phaseA"
PHASE_B = f"{DATA}/phaseB"
DK = ('docker exec -e XDEBUG_MODE=off --user=ivpldock ivpldock-workspace-1 bash -lc "cd /var/www/projects/invoiceplane/exprmt && '
      'env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD vendor/bin/phpunit {tests}"')
KILL = ("docker exec ivpldock-workspace-1 bash -lc 'for d in /proc/[0-9]*; do c=$(tr \"\\0\" \" \" < $d/cmdline 2>/dev/null); "
        "case \"$c\" in \"php vendor/bin/phpunit\"*) kill -9 ${d#/proc/};; esac; done'")

L = json.load(open(f"{DATA}/advisory-ledger.json"))
PF = json.load(open(f"{DATA}/pr-files.json"))
for d in (PHASE_A, PHASE_B):
    os.makedirs(d, exist_ok=True)
only = sys.argv[1:]


def norm(x):
    return re.sub(r"\s+", "", x)


def clusters(prs, f):
    try:
        body = open(f"{REPO}/{f}").read().splitlines()
    except Exception:
        return []
    nb = [norm(l) for l in body]
    hit = set()
    for n in prs:
        for pf in PF.get(str(n)) or []:
            if pf["filename"] != f or not pf.get("patch"):
                continue
            for l in pf["patch"].splitlines():
                if l.startswith("+") and not l.startswith("+++"):
                    t = norm(l[1:])
                    if len(t) >= 6 and not l[1:].strip().startswith(("//", "*", "/*", "#")):
                        hit |= {i + 1 for i, x in enumerate(nb) if x == t}
    cl = []
    for ln in sorted(hit):
        if cl and ln - cl[-1][1] <= 5:
            cl[-1][1] = ln
        else:
            cl.append([ln, ln])
    cl = [(a, b) for a, b in cl]
    cl.sort(key=lambda c: -(c[1] - c[0]))
    return cl


def run_tests(tests):
    return subprocess.run(DK.format(tests=" ".join(tests)), shell=True, capture_output=True, text=True)


def phase_a(g):
    v = L[g]
    res = {"ghsa": g, "tests": v["tests"], "baseline": None}
    if v["tests"]:
        r = run_tests(v["tests"])
        res["baseline"] = "PASS" if r.returncode == 0 else "FAIL"
        res["baseline_tail"] = r.stdout.strip().splitlines()[-3:]
    if res["baseline"] != "PASS":
        return res
    applied, skipped = [], []
    try:
        for n in sorted(v["prs"], reverse=True):
            for pf in PF.get(str(n)) or []:
                f = pf["filename"]
                if f not in v["fix_files"] or not pf.get("patch"):
                    continue
                p = f"--- a/{f}\n+++ b/{f}\n" + pf["patch"] + "\n"
                chk = subprocess.run(["git", "-C", REPO, "apply", "-R", "--check", "--recount", "-"], input=p, text=True, capture_output=True)
                if chk.returncode == 0:
                    subprocess.run(["git", "-C", REPO, "apply", "-R", "--recount", "-"], input=p, text=True, capture_output=True)
                    applied.append((n, f))
                else:
                    skipped.append((n, f))
        if applied:
            r = run_tests(v["tests"])
            res["revert"] = {"applied": applied, "skipped": skipped, "tests_after_revert": "RED" if r.returncode != 0 else "GREEN",
                             "tail": r.stdout.strip().splitlines()[-3:]}
        else:
            res["revert"] = {"applied": [], "skipped": skipped, "tests_after_revert": "NOT-APPLICABLE"}
    finally:
        for f in {f for _, f in applied}:
            subprocess.run(["git", "-C", REPO, "checkout", "--", f])
        subprocess.run(KILL, shell=True, capture_output=True)
    return res


def mutation_plan(g):
    v = L[g]
    files = [f for f in v["fix_files"] if "/views/" not in f][:3]
    return [(f, clusters(v["prs"], f)[:2]) for f in files]


def phase_b(g):
    v = L[g]
    res = {"ghsa": g, "files": {}}
    for f, cl in mutation_plan(g):
        res["files"][f] = {"clusters": cl, "runs": []}
        for i, (a, b) in enumerate(cl):
            a, b = max(1, a - 4), b + 4
            jp = f"{PHASE_B}/{g}.{os.path.basename(f)}.{i}.mut.json"
            for _ in range(12):
                r = subprocess.run(["php", ".claude/skills/mutation-check/mutate.php", f"--file={f}", f"--lines={a}-{b}",
                                    "--tests=" + " ".join(v["tests"]), "--cmd=" + DK, "--max=8", "--timeout=120", "--allow-dirty",
                                    f"--json={jp}"], cwd=REPO, capture_output=True, text=True)
                if r.returncode != 4:
                    break
                time.sleep(20)
            res["files"][f]["runs"].append({"lines": f"{a}-{b}", "exit": r.returncode,
                                            "tail": r.stdout.strip().splitlines()[-4:] or r.stderr.strip().splitlines()[-1:]})
            subprocess.run(KILL, shell=True, capture_output=True)
            subprocess.run(["git", "-C", REPO, "checkout", "--", f])
    return res


def cost(g):
    return sum(len(cl) for _, cl in mutation_plan(g)) * max(1, len(L[g]["tests"]))


old_done = {g for g in L if os.path.exists(f"{DONE_OLD}/{g}.json")}
sel = [g for g in L if not only or g in only]

# Phase A: every advisory not finished before, reverse of the old critical-first order
for g in sorted((g for g in sel if g not in old_done), key=lambda k: (-["critical", "high", "medium", "low"].index(L[k]["severity"]), k), reverse=False):
    fn = f"{PHASE_A}/{g}.json"
    if os.path.exists(fn):
        continue
    res = phase_a(g)
    json.dump(res, open(fn, "w"), indent=1)
    print("A", g, res["baseline"], (res.get("revert") or {}).get("tests_after_revert"), flush=True)

# Phase B: mutation, cheapest first; only where the baseline was green
todo = [g for g in sel if g not in old_done and (json.load(open(f"{PHASE_A}/{g}.json")).get("baseline") == "PASS")]
for g in sorted(todo, key=cost):
    fn = f"{PHASE_B}/{g}.json"
    if os.path.exists(fn):
        continue
    res = phase_b(g)
    json.dump(res, open(fn, "w"), indent=1)
    print("B", g, "cost", cost(g), {k: [(r["tail"] or ["NO-OUTPUT"])[-1][:50] for r in x["runs"]] for k, x in res["files"].items()}, flush=True)
