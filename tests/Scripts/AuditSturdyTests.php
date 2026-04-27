<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$rii  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

$violations = [];

/** @var SplFileInfo $file */
foreach ($rii as $file) {
    if ( ! $file->isFile() || substr($file->getFilename(), -4) !== '.php') {
        continue;
    }

    $path = $file->getPathname();
    if (strpos($path, DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR) !== false) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    if (preg_match_all('/#\[(?:\\\\PHPUnit\\\\Framework\\\\Attributes\\\\)?Test\]\s*public function ([a-zA-Z0-9_]+)\s*\([^)]*\)\s*:\s*void\s*\{(.*?)^\}/ms', $content, $matches, PREG_SET_ORDER) === false) {
        continue;
    }

    foreach ($matches as $match) {
        $method = $match[1];
        $body   = $match[2];

        $hasAssertion = preg_match('/->assert[A-Za-z0-9_]+\s*\(|self::assert[A-Za-z0-9_]+\s*\(|expectException\s*\(/', $body) === 1;
        if ( ! $hasAssertion) {
            $violations[] = $path . "::" . $method . " has no explicit assertion";
        }

        $aaaMarkers = preg_match('/\/\*\s*Arrange\s*\*\/.*\/\*\s*Act\s*\*\/.*\/\*\s*Assert\s*\*\//s', $body) === 1;
        if ( ! $aaaMarkers) {
            $violations[] = $path . "::" . $method . " is missing Arrange/Act/Assert markers";
        }

        $noPhpErrorsCount = preg_match_all('/assertResponseHasNoPhpErrors\s*\(/', $body, $tmp);
        $otherAssertions  = preg_match_all('/->assert[A-Za-z0-9_]+\s*\(|self::assert[A-Za-z0-9_]+\s*\(/', $body, $tmp2);
        if ($noPhpErrorsCount > 0 && $otherAssertions <= $noPhpErrorsCount) {
            $violations[] = $path . "::" . $method . " appears to rely primarily on assertResponseHasNoPhpErrors()";
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "Sturdy test audit findings:\n");
    foreach ($violations as $violation) {
        fwrite(STDERR, " - " . $violation . "\n");
    }

    if (in_array('--strict', $argv, true)) {
        exit(1);
    }

    fwrite(STDERR, "\nNon-strict mode: findings reported for phased refactoring.\n");
    exit(0);
}

fwrite(STDOUT, "OK: sturdy-test heuristic audit passed.\n");
exit(0);
