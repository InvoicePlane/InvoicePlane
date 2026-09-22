<?php
/**
 * Analyze 0% hollow tests to identify patterns and refactoring opportunities
 */

// Find all test files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('tests'),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$patterns = [
    'no_assertions' => 0,
    'single_assertion' => 0,
    'only_response_status' => 0,
    'only_response_body' => 0,
    'only_database_check' => 0,
    'redirect_only' => 0,
];

$examples = [
    'no_assertions' => [],
    'only_response_status' => [],
    'only_response_body' => [],
    'redirect_only' => [],
];

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php' || strpos($file->getFilename(), 'Test.php') === false) {
        continue;
    }

    $content = file_get_contents($file->getRealPath());
    $pattern = '/public\s+function\s+(it_[a-z0-9_]+)\s*\([^)]*\)(?:\s*:\s*\w+)?\s*\{/';

    if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $testName = $matches[1][$i][0];
            $startPos = $matches[0][$i][1];

            // Extract function body
            $braceCount = 0;
            $inString = false;
            $stringChar = '';
            $testBody = '';

            for ($pos = strpos($content, '{', $startPos); $pos < strlen($content); $pos++) {
                $char = $content[$pos];

                if ($char === '"' || $char === "'") {
                    if (!$inString) {
                        $inString = true;
                        $stringChar = $char;
                    } elseif ($char === $stringChar && ($pos === 0 || $content[$pos - 1] !== '\\')) {
                        $inString = false;
                    }
                }

                if (!$inString) {
                    if ($char === '{') {
                        $braceCount++;
                    } elseif ($char === '}') {
                        $braceCount--;
                        if ($braceCount === 0) {
                            break;
                        }
                    }
                }

                $testBody .= $char;
            }

            // Count assertions
            $assertCount = preg_match_all('/\$this->assert/', $testBody);
            
            if ($assertCount === 0) {
                $patterns['no_assertions']++;
                if (count($examples['no_assertions']) < 5) {
                    $examples['no_assertions'][] = [
                        'file' => str_replace(getcwd() . '/', '', $file->getRealPath()),
                        'test' => $testName,
                    ];
                }
            } elseif ($assertCount === 1) {
                $patterns['single_assertion']++;
                
                if (preg_match('/assertResponseStatusCode|assertStatus/', $testBody)) {
                    $patterns['only_response_status']++;
                    if (count($examples['only_response_status']) < 5) {
                        $examples['only_response_status'][] = [
                            'file' => str_replace(getcwd() . '/', '', $file->getRealPath()),
                            'test' => $testName,
                        ];
                    }
                } elseif (preg_match('/assertResponseBodyContains|assertStringContains/', $testBody)) {
                    $patterns['only_response_body']++;
                    if (count($examples['only_response_body']) < 5) {
                        $examples['only_response_body'][] = [
                            'file' => str_replace(getcwd() . '/', '', $file->getRealPath()),
                            'test' => $testName,
                        ];
                    }
                }
            }

            if (preg_match('/redirect|->redirect|response.*30\d/', $testBody) && $assertCount <= 1) {
                $patterns['redirect_only']++;
                if (count($examples['redirect_only']) < 3) {
                    $examples['redirect_only'][] = [
                        'file' => str_replace(getcwd() . '/', '', $file->getRealPath()),
                        'test' => $testName,
                    ];
                }
            }
        }
    }
}

echo "## 0% Hollow Test Patterns Analysis\n\n";
echo "### Pattern Distribution\n";
echo "- **No assertions at all**: " . $patterns['no_assertions'] . "\n";
echo "- **Single assertion (status only)**: " . $patterns['only_response_status'] . "\n";
echo "- **Single assertion (body only)**: " . $patterns['only_response_body'] . "\n";
echo "- **Redirect-only tests**: " . $patterns['redirect_only'] . "\n";
echo "\n";

foreach ($examples as $pattern => $tests) {
    if (!empty($tests)) {
        echo "### Examples: " . ucfirst(str_replace('_', ' ', $pattern)) . "\n";
        foreach ($tests as $test) {
            echo "- `" . $test['file'] . "::" . $test['test'] . "`\n";
        }
        echo "\n";
    }
}
