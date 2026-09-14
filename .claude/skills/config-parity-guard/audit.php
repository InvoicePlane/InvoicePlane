<?php

/**
 * config-parity-guard — static release gate.
 *
 * Proves the test suite exercises the PRODUCTION configuration, not just the
 * permissive test one, and that no test skips its way out of coverage.
 * See SKILL.md. Exit 0 = safe, non-zero = gap list printed.
 *
 * Usage:  php .claude/skills/config-parity-guard/audit.php [--json]
 */

declare(strict_types=1);

$root = dirname(__DIR__, 3);
$json = in_array('--json', $argv, true);

$gaps = [];
$add  = static function (string $kind, string $where, string $detail) use (&$gaps): void {
    $gaps[] = compact('kind', 'where', 'detail');
};

// ---------------------------------------------------------------------------
// helpers
// ---------------------------------------------------------------------------
$glob = static function (string $pattern) use ($root): array {
    return glob($root . '/' . $pattern, GLOB_BRACE) ?: [];
};
// PHP's glob() does NOT recurse on "**" — it is treated the same as a single
// "*" and never crosses a "/", so a pattern like "tests/**/*.php" silently
// misses everything nested more than one directory deep. Walk the tree for
// real recursion instead.
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
$controllerFiles = $glob('application/modules/*/controllers/*.php');
$testFiles       = $rglob('tests/Feature');

$allTestSrc = '';
foreach ($testFiles as $t) {
    $allTestSrc .= "\n/* FILE:{$t} */\n" . file_get_contents($t);
}

// module -> which of its test files enable CSRF protection somewhere
$csrfOnInFile = [];
foreach ($testFiles as $t) {
    $src = file_get_contents($t);
    if (preg_match('/enableCsrfProtection\s*\(|CSRF_PROTECTION\'\s*=>\s*\'true\'/', $src)) {
        $csrfOnInFile[$t] = $src;
    }
}
$csrfOnSrc = implode("\n", $csrfOnInFile);

// ---------------------------------------------------------------------------
// 1. guarded / mutating endpoints must have a production-config test
// ---------------------------------------------------------------------------
$mutatingName = '/^(delete|save|remove|stop|approve|reject)(_|$)|^(create_[a-z]+|update_[a-z]+)$/i';

foreach ($controllerFiles as $file) {
    $src = file_get_contents($file);
    $mod = basename(dirname($file, 2));                 // e.g. "invoices"
    $ctl = strtolower(basename($file, '.php'));         // e.g. "invoices", "ajax", "recurring"
    // controller class -> route base
    $routeBase = $ctl === 'ajax'
        ? $mod . '/ajax'
        : ($ctl === basename(dirname($file, 2)) || $ctl === strtolower($mod) ? $mod : $mod . '/' . $ctl);

    // Two mechanisms make a mutating action safe WITHOUT a per-route
    // ensure_valid_post_request()/verify_csrf_token() call: (1) the whole
    // controller requires the X-Requested-With XHR header ($ajax_controller
    // = true — CI3's Base_Controller 404s a plain cross-site GET/form/img
    // request before any action runs, since a simple cross-site request
    // cannot set that header), or (2) the action itself rejects anything but
    // POST, which puts it behind CI3's bootstrap Security::csrf_verify()
    // (that check is unconditional for every POST when CSRF_PROTECTION is
    // on, regardless of whether the controller re-checks). Neither needs the
    // controller-level check that #1694 broke.
    $ajaxGated = (bool) preg_match('/\$ajax_controller\s*=\s*true/', $src);

    if ( ! preg_match_all('/public function ([a-z_0-9]+)\s*\(([^)]*)\)/i', $src, $m, PREG_OFFSET_CAPTURE)) {
        continue;
    }

    foreach ($m[1] as $i => [$method, $off]) {
        // body of this method (rough: from here to next "public function" or EOF)
        $start = $off;
        $end   = $m[0][$i + 1][1] ?? strlen($src);
        $body  = substr($src, $start, $end - $start);

        $guarded       = (bool) preg_match('/ensure_valid_post_request\s*\(|verify_csrf_token\s*\(/', $body);
        $mutates       = (bool) preg_match('/->save\s*\(|->delete\s*\(|->update\s*\(|db->insert\s*\(|db->update\s*\(|db->delete\s*\(|set_all_clients|save_custom/', $body);
        $byName        = (bool) preg_match($mutatingName, $method);
        $postOnlyCheck = (bool) preg_match(
            '/input->method\s*\(\s*\)\s*!==?\s*[\'"]post[\'"]|REQUEST_METHOD.{0,20}!==?\s*[\'"]POST[\'"]/i',
            $body
        );
        // MY_Model::run_validation() builds its field list from array_keys($_POST)
        // and hands it to CI3's Form_validation, which validates against $_POST
        // alone — an empty-$_POST GET request cannot satisfy a `required` rule,
        // so a mutation gated behind it is unreachable without a real POST body.
        $validationGated = (bool) preg_match('/run_validation\s*\(/', $body);
        // Base_Controller::__construct() 404s any GET whose URL contains the
        // literal substring "delete", for every controller that extends it —
        // a blanket, route-name-based gate independent of the per-method code.
        $deleteUrlGated = str_contains(strtolower($method), 'delete');

        // only care about endpoints that both change state and are POST-guardable
        if ( ! ($guarded || ($byName && $mutates))) {
            continue;
        }
        // form() handlers only count when they actually persist
        if ($method === 'form' && ! $mutates) {
            continue;
        }

        $route = $routeBase . '/' . $method;
        $label = "{$mod}::{$ctl}::{$method}  (POST {$route})";

        // Is there ANY test that hits this route with CSRF protection on?
        $routeRe       = '#[\'"]/?' . preg_quote($route, '#') . '(/|\b)#';
        $hitWithCsrfOn = (bool) preg_match($routeRe, $csrfOnSrc);
        $hitAtAll      = (bool) preg_match($routeRe, $allTestSrc);

        if ( ! $guarded && ! $mutates) {
            continue;
        }

        // Worse than "guarded but undertested": no controller-level check AT
        // ALL, and neither of the two safe-without-it mechanisms applies. If
        // this route is reachable via a plain GET (CI3 does not restrict
        // verbs by routing), it bypasses the bootstrap CSRF check entirely —
        // trivially triggerable via an <img>/<a> from any origin.
        if ( ! $guarded && $byName && $mutates && ! $ajaxGated && ! $postOnlyCheck && ! $validationGated && ! $deleteUrlGated) {
            $add(
                'unguarded-mutation',
                $label,
                'mutates state with no ensure_valid_post_request()/verify_csrf_token() call, no '
                . '$ajax_controller = true XHR gate, no explicit POST-only check, no '
                . 'run_validation()-gated mutation, and a method name that does not contain "delete" (so '
                . "Base_Controller's blanket GET-to-delete 404 does not apply) — if reachable via GET this "
                . 'is CSRF-able even with CSRF_PROTECTION on.'
            );
        }

        if ($guarded && ! $hitWithCsrfOn) {
            $add(
                'csrf-parity',
                $label,
                $hitAtAll
                    ? 'route is tested, but never with CSRF_PROTECTION=true — #1694 could not be caught'
                    : 'no test posts to this guarded route at all'
            );
        }
    }
}

// ---------------------------------------------------------------------------
// 2. config-override parity
// ---------------------------------------------------------------------------
$configPhp   = file_get_contents($root . '/application/config/config.php');
$prodDefault = [];   // KEY => 'true'|'false'|string
if (preg_match_all("/env_bool\('([A-Z0-9_]+)',\s*'?(true|false)'?\)/", $configPhp, $mm)) {
    foreach ($mm[1] as $k => $key) {
        $prodDefault[$key] = $mm[2][$k];
    }
}
// keys a lowered value of which weakens a guarantee
$sensitive = ['CSRF_PROTECTION', 'COOKIE_SECURE', 'ENABLE_X_CONTENT_TYPE_OPTIONS', 'CSRF_REGENERATE'];

foreach ($sensitive as $key) {
    $lowered = (bool) preg_match("/'{$key}'\s*=>\s*'false'/", $allTestSrc);
    $atProd  = (bool) preg_match("/'{$key}'\s*=>\s*'true'/", $allTestSrc) || ($key === 'CSRF_PROTECTION' && preg_match('/enableCsrfProtection/', $allTestSrc));
    $default = $prodDefault[$key] ?? 'true';
    if ($default === 'true' && $lowered && ! $atProd) {
        $add('config-parity', $key, "tests set it to 'false' but no test runs it at the production default 'true'");
    }
}

// ---------------------------------------------------------------------------
// 3. silent-skip audit
// ---------------------------------------------------------------------------
$skipRe = '/markTestSkipped\s*\(\s*[\'"]([^\'"]*)[\'"]/';
$envish = '/\b(database|db|connect|unavailable|env|extension|ext-|not installed|driver|mysqli|mariadb)\b/i';

$scanForSkips = array_unique(array_merge($testFiles, $rglob('tests')));
$seen         = [];
foreach ($scanForSkips as $t) {
    if (isset($seen[$t]) || ! is_file($t)) {
        continue;
    }
    $seen[$t] = true;
    $src      = file_get_contents($t);
    if ( ! preg_match_all($skipRe, $src, $sm)) {
        continue;
    }
    foreach ($sm[1] as $reason) {
        if (str_contains($reason, 'PARITY-OK:')) {
            continue;
        }
        if (preg_match($envish, $reason)) {
            $add('silent-skip', str_replace($root . '/', '', $t), "environmental markTestSkipped(): \"{$reason}\" — make it fail loud or tag 'PARITY-OK:'");
        }
    }
}
// the harness itself
foreach (['tests/Concerns/InteractsWithDatabase.php', 'tests/AbstractTestCase.php'] as $h) {
    $p = $root . '/' . $h;
    if (is_file($p) && preg_match('/markTestSkipped\s*\([^)]*(database|connect|unavailable)/i', file_get_contents($p))) {
        $add('silent-skip', $h, 'the harness skips on DB-connection failure — a broken DB then reads as a green build');
    }
}

// ---------------------------------------------------------------------------
// report
// ---------------------------------------------------------------------------
if ($json) {
    echo json_encode(['ok' => $gaps === [], 'gaps' => $gaps], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
    exit($gaps === [] ? 0 : 1);
}

if ($gaps === []) {
    echo "[OK] config-parity-guard: every guarded mutating endpoint is tested at the production config; no silent skips.\n";
    exit(0);
}

$byKind = [];
foreach ($gaps as $g) {
    $byKind[$g['kind']][] = $g;
}
echo '[FAIL] config-parity-guard — ' . count($gaps) . " gap(s):\n";
foreach ($byKind as $kind => $list) {
    echo "\n## {$kind} (" . count($list) . ")\n";
    foreach ($list as $g) {
        echo "  - {$g['where']}\n      {$g['detail']}\n";
    }
}
echo "\nSee .claude/skills/config-parity-guard/SKILL.md for how to close each.\n";
exit(1);
