<?php

/**
 * release-readiness — "can I ship this and go on vacation for six months" audit.
 *
 * See SKILL.md. Exit 0 = GO, 1 = GO WITH EYES OPEN, 2 = NO-GO.
 * `--json` for a machine-readable report. `--fast` skips the full PHPUnit run
 * (use only when you have just run the suite yourself).
 *
 * Usage:  php .claude/skills/release-readiness/audit.php [--json] [--fast]
 */

declare(strict_types=1);

$root = dirname(__DIR__, 3);
$json = in_array('--json', $argv, true);
$fast = in_array('--fast', $argv, true);

/** Grows the baseline => new suppressed debt. Bump only when you deliberately accept it. */
const BASELINE_MAX = 8;

/** Controllers with no HTTP surface worth a Feature ControllerTest. */
const NON_HTTP_CONTROLLERS = [
    'setup/Cli', 'integrations/Cli', 'integrations/Events', 'integrations/Incoming',
    'integrations/Operations', 'integrations/Sync', 'layout/Layout', 'welcome/Welcome',
];

$blockers = [];  // NO-GO
$watch    = [];  // GO, EYES OPEN
$ok       = [];  // passed

$block = static function (string $m) use (&$blockers): void {
    $blockers[] = $m;
};
$eye = static function (string $m) use (&$watch): void {
    $watch[] = $m;
};
$pass = static function (string $m) use (&$ok): void {
    $ok[] = $m;
};

$readAll = static function (array $paths): string {
    $out = '';
    foreach ($paths as $p) {
        if (is_file($p)) {
            $out .= "\n/* FILE:{$p} */\n" . (string) file_get_contents($p);
        }
    }

    return $out;
};
$glob = static function (string $pattern) use ($root): array {
    return glob($root . '/' . $pattern, GLOB_BRACE) ?: [];
};
// PHP's glob() does NOT recurse on "**" — it behaves like a single "*" and
// never crosses a "/", so "application/**/*.php" silently misses everything
// nested past one directory (e.g. every application/modules/*/controllers
// file). Walk the tree for real recursion instead.
$rglob = static function (string $dir, string $suffix = '.php') use ($root): array {
    $base = $root . '/' . $dir;
    if ( ! is_dir($base)) {
        return [];
    }
    $found = [];
    $it    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), $suffix)) {
            $found[] = $file->getPathname();
        }
    }

    return $found;
};

// ---------------------------------------------------------------------------
// 1. suite is actually green, not masked-green
// ---------------------------------------------------------------------------
if ($fast) {
    $eye('PHPUnit run skipped (--fast) — confirm you just ran the full clean-env suite green.');
} else {
    $cmd = 'cd ' . escapeshellarg($root) . ' && '
        . 'env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD '
        . 'XDEBUG_MODE=off vendor/bin/phpunit --no-coverage 2>&1';
    exec($cmd, $lines, $exit);
    $tail = implode("\n", array_slice($lines, -25));

    if (preg_match('/Tests:\s*(\d+),\s*Assertions:\s*(\d+)(?:,\s*Errors:\s*(\d+))?(?:,\s*Failures:\s*(\d+))?(?:,\s*Skipped:\s*(\d+))?(?:,\s*Risky:\s*(\d+))?/', $tail, $m)) {
        [$tests, $asserts, $errors, $failures, $skipped, $risky] = [
            (int) $m[1], (int) $m[2], (int) ($m[3] ?? 0), (int) ($m[4] ?? 0), (int) ($m[5] ?? 0), (int) ($m[6] ?? 0),
        ];
        if ($errors > 0 || $failures > 0) {
            $block("PHPUnit: {$failures} failure(s), {$errors} error(s). Suite is not green.");
        } elseif ($exit !== 0) {
            $block("PHPUnit exited {$exit} with no failure summary — a fatal before the run finished.");
        } else {
            $pass("PHPUnit green: {$tests} tests, {$asserts} assertions.");
        }
        if ($risky > 0) {
            $block("PHPUnit: {$risky} risky test(s). phpunit.xml has failOnRisky=true — fix them.");
        }
        if ($skipped > 25) {
            $block("PHPUnit: {$skipped} skipped — that is the masked-DB profile (~200). The integration tests are not running. See CLAUDE.md 'MariaDB test database'.");
        } elseif ($skipped > 0) {
            $pass("PHPUnit: {$skipped} skip(s) — within the known-genuine guard count.");
        }
    } else {
        $block('PHPUnit produced no parseable summary line. Last output:' . "\n" . $tail);
    }
}

// ---------------------------------------------------------------------------
// 2. config-parity-guard (the #1694 class)
// ---------------------------------------------------------------------------
$cpg = $root . '/.claude/skills/config-parity-guard/audit.php';
if (is_file($cpg)) {
    exec('php ' . escapeshellarg($cpg) . ' 2>&1', $cpgOut, $cpgExit);
    if ($cpgExit === 0) {
        $pass('config-parity-guard: [OK] — every guarded mutating endpoint tested at production config.');
    } else {
        $block("config-parity-guard FAILED — this is the #1694 class:\n" . implode("\n", array_slice($cpgOut, 0, 40)));
    }
} else {
    $eye('config-parity-guard skill missing — cannot prove #1694 cannot recur.');
}

// ---------------------------------------------------------------------------
// 3. one ControllerTest per HTTP controller
// ---------------------------------------------------------------------------
$testBlob = $readAll($rglob('tests/Feature'));
$missing  = [];
foreach ($glob('application/modules/*/controllers/*.php') as $file) {
    $mod  = basename(dirname($file, 2));
    $name = basename($file, '.php');
    $key  = $mod . '/' . $name;
    if (in_array($key, NON_HTTP_CONTROLLERS, true)) {
        continue;
    }
    // "Ajax" controller => <Module>AjaxControllerTest ; else <Name>ControllerTest.
    // Strip underscores on both branches (email_templates -> "Emailtemplates"
    // still matches EmailTemplatesAjaxControllerTest case-insensitively) — the
    // Ajax branch used to skip this and false-flagged every underscored module.
    $needleClass = $name === 'Ajax'
        ? '/class\s+\w*' . preg_quote(str_replace('_', '', ucfirst($mod)), '/') . '\w*AjaxControllerTest/i'
        : '/class\s+\w*' . preg_quote(str_replace('_', '', $name), '/') . '\w*ControllerTest/i';
    if ( ! preg_match($needleClass, $testBlob)) {
        $missing[] = $key;
    }
}
if ($missing === []) {
    $pass('Every HTTP controller has a discoverable ControllerTest.');
} else {
    $ratio = count($missing);
    $eye("{$ratio} controller(s) with no discoverable ControllerTest — un-audited surface: " . implode(', ', $missing));
}

// ---------------------------------------------------------------------------
// 4. environmental markTestSkipped (coverage rot)
// ---------------------------------------------------------------------------
$rot = [];
foreach ($rglob('tests') as $t) {
    if ( ! is_file($t)) {
        continue;
    }
    if (preg_match_all('/markTestSkipped\s*\(\s*[\'"]([^\'"]*)[\'"]/', (string) file_get_contents($t), $sm)) {
        foreach ($sm[1] as $reason) {
            if (str_contains($reason, 'PARITY-OK:')) {
                continue;
            }
            if (preg_match('/\b(database|db|connect|unavailable|env|extension|ext-|driver|mysqli|mariadb)\b/i', $reason)) {
                $rot[] = str_replace($root . '/', '', $t) . ': "' . $reason . '"';
            }
        }
    }
}
$rot === []
    ? $pass('No environmental markTestSkipped() — a broken environment is a red build.')
    : $block("Environmental markTestSkipped() (coverage rot):\n  - " . implode("\n  - ", $rot));

// ---------------------------------------------------------------------------
// 5. security helpers carry no unfinished business
// ---------------------------------------------------------------------------
$secFiles = array_merge(
    $glob('application/helpers/*security*'),
    $glob('application/helpers/file_security_helper.php'),
    $glob('core/MY_Security*'),
    $glob('application/core/MY_Security*'),
);
$secHits = [];
foreach ($secFiles as $f) {
    foreach (preg_split('/\R/', (string) file_get_contents($f)) as $n => $line) {
        if (preg_match('/\b(TODO|FIXME|XXX|HACK)\b|@phpstan-ignore/', $line)) {
            $secHits[] = str_replace($root . '/', '', $f) . ':' . ($n + 1) . '  ' . trim($line);
        }
    }
}
$secHits === []
    ? $pass('Security helpers carry no TODO/FIXME/@phpstan-ignore.')
    : $block("Unfinished business in a security helper:\n  - " . implode("\n  - ", $secHits));

// ---------------------------------------------------------------------------
// 6. ipconfig.php.example production defaults
// ---------------------------------------------------------------------------
$ex = $root . '/ipconfig.php.example';
if (is_file($ex)) {
    $src = (string) file_get_contents($ex);
    if (preg_match('/^\s*CSRF_PROTECTION\s*=\s*false/mi', $src)) {
        $block('ipconfig.php.example ships CSRF_PROTECTION=false.');
    }
    if (preg_match('/^\s*COOKIE_HTTPONLY\s*=\s*false/mi', $src)) {
        $block('ipconfig.php.example ships COOKIE_HTTPONLY=false — session cookie readable by JS.');
    }
    if (preg_match('/^\s*SETUP_COMPLETED\s*=\s*true/mi', $src)) {
        $eye('ipconfig.php.example ships SETUP_COMPLETED=true — fresh installs skip the wizard.');
    }
    if ($blockers === [] || ! preg_match('/CSRF_PROTECTION|COOKIE_HTTPONLY/', implode('', $blockers))) {
        $pass('ipconfig.php.example production defaults look safe.');
    }
} else {
    $eye('ipconfig.php.example not found.');
}

// ---------------------------------------------------------------------------
// 7. no debug hatch left open outside tests/ and vendor/
// ---------------------------------------------------------------------------
$hatch    = [];
$appFiles = array_merge($rglob('application'), $rglob('core'), $rglob('libraries'));
foreach ($appFiles as $f) {
    if (str_contains($f, '/tests/') || str_contains($f, '/vendor/')) {
        continue;
    }
    foreach (preg_split('/\R/', (string) file_get_contents($f)) as $n => $line) {
        $trimmed = ltrim($line);
        if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '#')) {
            continue;
        }
        // print_r/var_export with a truthy 2nd arg is string formatting, not output — ignore those.
        $isOutput = preg_match('/\b(var_dump|xdebug_break)\s*\(/', $line)
            || preg_match('/\bdd\s*\(\s*\$/', $line)
            || preg_match('/\b(print_r|var_export)\s*\((?!.*,\s*(true|TRUE)\s*\))/', $line)
            || preg_match('/error_reporting\s*\(\s*E_ALL/', $line)
            || preg_match('/[\'"]display_errors[\'"]\s*,\s*[\'"]?1\b/', $line);
        if ($isOutput) {
            $hatch[] = str_replace($root . '/', '', $f) . ':' . ($n + 1) . '  ' . trim($line);
        }
    }
}
$hatch === []
    ? $pass('No debug hatch (var_dump/print_r/dd/display_errors) left in application code.')
    : $block("Debug hatch left open:\n  - " . implode("\n  - ", array_slice($hatch, 0, 20)));

// ---------------------------------------------------------------------------
// 8. PHPStan baseline did not grow
// ---------------------------------------------------------------------------
$bl = $root . '/phpstan-baseline.neon';
if (is_file($bl)) {
    $count = substr_count((string) file_get_contents($bl), 'message:');
    $count <= BASELINE_MAX
        ? $pass("PHPStan baseline: {$count} entr(y/ies) (<= BASELINE_MAX " . BASELINE_MAX . ').')
        : $eye("PHPStan baseline grew to {$count} (BASELINE_MAX " . BASELINE_MAX . ') — new debt was suppressed, not fixed.');
} else {
    $eye('phpstan-baseline.neon not found.');
}

// ---------------------------------------------------------------------------
// verdict
// ---------------------------------------------------------------------------
$verdict = $blockers !== [] ? 'NO-GO' : ($watch !== [] ? 'GO, EYES OPEN' : 'GO');
$code    = $blockers !== [] ? 2 : ($watch !== [] ? 1 : 0);

if ($json) {
    echo json_encode([
        'verdict'  => $verdict,
        'blockers' => $blockers,
        'watch'    => $watch,
        'passed'   => $ok,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
    exit($code);
}

echo "\n";
echo "================ release-readiness ================\n";
echo "  VERDICT: {$verdict}\n";
echo "==================================================\n\n";

if ($blockers !== []) {
    echo 'NO-GO — release blockers (' . count($blockers) . "):\n";
    foreach ($blockers as $b) {
        echo "  ✗ {$b}\n";
    }
    echo "\n";
}
if ($watch !== []) {
    echo 'EYES OPEN — residual risk to accept explicitly (' . count($watch) . "):\n";
    foreach ($watch as $w) {
        echo "  ! {$w}\n";
    }
    echo "\n";
}
if ($ok !== []) {
    echo 'Passed (' . count($ok) . "):\n";
    foreach ($ok as $o) {
        echo "  ✓ {$o}\n";
    }
    echo "\n";
}

echo "The script is necessary, not sufficient. Now walk the Manual review\n";
echo "section of SKILL.md — migrations, money math, email+PDF, the AJAX editor,\n";
echo "gateways, backups, dependency CVEs. Those need a human.\n\n";

exit($code);
