<?php

// auto_prepend_file for PCOV runs (see run.sh). Loaded by the phpunit parent AND by every
// request subprocess (tests/Integration/bin/request.php), so Feature tests that execute the
// application in a child process are measured too. Each process dumps its own JSON file.
if ( ! extension_loaded('pcov') || ! ($dir = getenv('IP_COVERAGE_DIR')) || ! ($root = getenv('IP_COVERAGE_ROOT'))) {
    return;
}

\pcov\start();

register_shutdown_function(static function () use ($dir, $root): void {
    \pcov\stop();
    $out = [];

    foreach (\pcov\collect(\pcov\all) as $file => $lines) {
        if ( ! str_starts_with($file, $root . '/application/') && ! str_starts_with($file, $root . '/bootstrap/')) {
            continue;
        }

        $all = $hit = [];
        foreach ($lines as $line => $state) {
            $all[] = $line;
            if ($state > 0) {
                $hit[] = $line;
            }
        }
        $out[$file] = [$all, $hit];
    }

    if ($out !== []) {
        file_put_contents($dir . '/' . getmypid() . '-' . bin2hex(random_bytes(4)) . '.json', json_encode($out));
    }
});
