<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$rii  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

$violations = [];

/** @var SplFileInfo $file */
foreach ($rii as $file) {
    if ( ! $file->isFile()) {
        continue;
    }

    $path = $file->getPathname();

    if (substr($path, -4) !== '.php') {
        continue;
    }

    if (strpos($path, DIRECTORY_SEPARATOR . 'Feature' . DIRECTORY_SEPARATOR) === false) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        $violations[] = $path . ': unable to read file';
        continue;
    }

    if (preg_match('/\broute\s*\(/', $content)) {
        $violations[] = $path . ': contains route(...) helper call';
    }

    if (preg_match('/->(?:get|post|put|patch|delete|json|assertRedirect|isRedirect)\s*\(\s*([\'"])\/[^\'"]*\\\\[^\'"]*\1/', $content)) {
        $violations[] = $path . ': contains a URI string with a backslash';
    }
}

if ($violations !== []) {
    fwrite(STDERR, "Explicit test route policy violations found:\n");
    foreach ($violations as $violation) {
        fwrite(STDERR, ' - ' . $violation . "\n");
    }
    exit(1);
}

fwrite(STDOUT, "OK: no route(...) helpers and no backslashes in URI strings under tests/Feature.\n");
exit(0);
