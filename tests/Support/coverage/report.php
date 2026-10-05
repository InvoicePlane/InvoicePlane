<?php

// Merge the per-process dumps from run.sh into one line-coverage report.
// usage: php tests/Support/coverage/report.php <dump-dir> [min-uncovered-lines]
$dir = $argv[1] ?? '';
$min = (int) ($argv[2] ?? 30);
$root = dirname(__DIR__, 3);

if ( ! is_dir($dir)) {
    fwrite(STDERR, "usage: php report.php <dump-dir> [min-uncovered-lines]\n");
    exit(2);
}

$all = $hit = [];
foreach (glob($dir . '/*.json') as $dump) {
    foreach (json_decode((string) file_get_contents($dump), true) as $file => [$lines, $hits]) {
        foreach ($lines as $l) {
            $all[$file][$l] = true;
        }
        foreach ($hits as $l) {
            $hit[$file][$l] = true;
        }
    }
}

$skip = static fn (string $p): bool => str_contains($p, '/language/') || str_contains($p, '/views/')
    || str_contains($p, '/third_party/') || str_contains($p, '/logs/') || str_contains($p, '/config/')
    || str_contains($p, '/country-list/') || str_ends_with($p, 'index.html');

$rows = [];
foreach ($all as $file => $lines) {
    if ($skip($file)) {
        continue;
    }
    $rows[substr($file, strlen($root) + 1)] = [count($lines), count($hit[$file] ?? []), 'loaded'];
}

// Files no test ever loaded have no executable-line data; approximate with non-comment lines.
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/application', FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $p = (string) $f;
    if ( ! str_ends_with($p, '.php') || isset($all[$p]) || $skip($p)) {
        continue;
    }
    $n = 0;
    foreach (file($p) as $line) {
        $t = trim($line);
        if ($t !== '' && ! str_starts_with($t, '//') && ! str_starts_with($t, '*') && ! str_starts_with($t, '/*') && $t !== '{' && $t !== '}' && ! str_starts_with($t, '<?php')) {
            $n++;
        }
    }
    $rows[substr($p, strlen($root) + 1)] = [$n, 0, 'never loaded (lines estimated)'];
}

$total = array_sum(array_column($rows, 0));
$covered = array_sum(array_column($rows, 1));
printf("Logic-code line coverage: %d/%d = %.1f%%  (%d files; views, language, config, country data excluded)\n\n", $covered, $total, $total ? 100 * $covered / $total : 0, count($rows));

uasort($rows, static fn ($a, $b) => ($b[0] - $b[1]) <=> ($a[0] - $a[1]));
printf("%8s %7s  %s\n", 'uncov', 'cov%', 'file');
foreach ($rows as $file => [$n, $c, $state]) {
    if ($n - $c < $min) {
        break;
    }
    printf("%8d %6.1f%%  %s%s\n", $n - $c, $n ? 100 * $c / $n : 100, $file, $state === 'loaded' ? '' : '  [' . $state . ']');
}
