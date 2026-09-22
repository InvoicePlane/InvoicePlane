<?php
/**
 * Automated Test Refactoring Tool
 *
 * Analyzes hollow tests and generates refactoring suggestions with code templates.
 * Can automatically apply common fixes to improve test honesty scores.
 */

class TestRefactoringTool
{
    private $testFile;
    private $content;
    private $suggestions = [];

    public function __construct($filePath)
    {
        $this->testFile = $filePath;
        $this->content = file_get_contents($filePath);
    }

    public function analyze()
    {
        $pattern = '/public\s+function\s+(it_[a-z0-9_]+)\s*\([^)]*\)(?:\s*:\s*\w+)?\s*\{/';

        if (preg_match_all($pattern, $this->content, $matches, PREG_OFFSET_CAPTURE)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $testName = $matches[1][$i][0];
                $startPos = $matches[0][$i][1];
                $testBody = $this->extractTestBody($startPos);

                $suggestions = $this->generateSuggestions($testName, $testBody);
                if ($suggestions) {
                    $this->suggestions[$testName] = $suggestions;
                }
            }
        }

        return $this->suggestions;
    }

    private function extractTestBody($startPos)
    {
        $braceCount = 0;
        $inString = false;
        $stringChar = '';
        $testBody = '';

        for ($pos = strpos($this->content, '{', $startPos); $pos < strlen($this->content); $pos++) {
            $char = $this->content[$pos];

            if ($char === '"' || $char === "'") {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar && ($pos === 0 || $this->content[$pos - 1] !== '\\')) {
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

        return $testBody;
    }

    private function generateSuggestions($testName, $testBody)
    {
        $suggestions = [];

        // Count existing assertions
        $assertCount = preg_match_all('/\$this->assert\w+/', $testBody);

        // Detect test type
        if ($assertCount === 0) {
            $suggestions[] = $this->suggestNoAssertions($testName, $testBody);
        } elseif (preg_match('/assertResponseStatusCode|assertStatus/', $testBody) && $assertCount === 1) {
            $suggestions[] = $this->suggestStatusOnly($testName, $testBody);
        } elseif (preg_match('/assertResponseBodyContains/', $testBody) && $assertCount === 1) {
            $suggestions[] = $this->suggestBodyOnly($testName, $testBody);
        } elseif (preg_match('/redirect\(|assertResponseStatus.*30\d/', $testBody)) {
            $suggestions[] = $this->suggestRedirectOnly($testName, $testBody);
        }

        return $suggestions;
    }

    private function suggestNoAssertions($testName, $testBody)
    {
        $isUnit = strpos($this->testFile, '/Unit/') !== false;

        if ($isUnit) {
            return [
                'issue' => 'No assertions - unit test with no verification',
                'type' => 'unit-no-assertions',
                'suggestions' => [
                    'Add assertion for return value: assertSame($expected, $result);',
                    'Add assertion for side effects: assertDatabaseHas(...)',
                    'Add assertion for output: assertStringContains($output, ...)',
                    'Add assertion for object state: assertTrue($obj->property);',
                ],
                'template' => '/* Add assertions to verify the result */
$result = /* ...call the code... */;
$this->assertSame($expectedValue, $result);
$this->assertDatabaseHas(\'table_name\', [\'column\' => $value]);',
            ];
        } else {
            return [
                'issue' => 'No assertions - feature test missing state verification',
                'type' => 'feature-no-assertions',
                'suggestions' => [
                    'Add assertion for response status: assertResponseStatusCode($response, 200);',
                    'Add assertion for database state: assertDatabaseHas(...);',
                    'Add assertion for no side effects: assertDatabaseMissing(...);',
                    'Add assertion for data integrity: assertDatabaseRow(...);',
                ],
                'template' => '$response = $this->post(\'/endpoint\');
$this->assertResponseStatusCode($response, 200);
$this->assertDatabaseHas(\'ip_table\', [\'id\' => $id, \'status\' => 1]);
$this->assertDatabaseMissing(\'ip_logs\', [\'error\' => 1]);',
            ];
        }
    }

    private function suggestStatusOnly($testName, $testBody)
    {
        return [
            'issue' => 'Only checking HTTP status code - missing state/side-effect verification',
            'type' => 'status-only',
            'suggestions' => [
                'Add state isolation check: assertDatabaseMissing() for rejection tests',
                'Add state change check: assertDatabaseHas() for success tests',
                'Add idempotency test: repeat request and verify same result',
                'Add boundary value test: test with invalid/nonexistent IDs',
            ],
            'templates' => [
                'For rejection tests (404/403):' => '
$response = $this->post(\'/endpoint\', [\'id\' => 99999]);
$this->assertResponseStatusCode($response, 404);
// NEW: Verify no side effects
$this->assertDatabaseMissing(\'ip_table\', [\'id\' => 99999]);
// NEW: Test idempotency
$response2 = $this->post(\'/endpoint\', [\'id\' => 99999]);
$this->assertResponseStatusCode($response2, 404);',
                'For success tests (201/200):' => '
$countBefore = $this->databaseCount(\'ip_payments\');
$response = $this->post(\'/payments\', $data);
$this->assertResponseStatusCode($response, 201);
// NEW: Verify state changed
$this->assertDatabaseHas(\'ip_payments\', [\'amount\' => 100]);
$countAfter = $this->databaseCount(\'ip_payments\');
$this->assertSame($countBefore + 1, $countAfter);',
            ],
        ];
    }

    private function suggestBodyOnly($testName, $testBody)
    {
        return [
            'issue' => 'Only checking response body - missing status code and state verification',
            'type' => 'body-only',
            'suggestions' => [
                'Add HTTP status assertion: assertResponseStatusCode($response, 200);',
                'Add database state check: assertDatabaseHas() or assertDatabaseMissing()',
                'Add data integrity check: assertDatabaseRow() for relationships',
                'Add boundary test: nonexistent ID, invalid input, null values',
            ],
            'template' => '
$response = $this->post(\'/endpoint\', $data);
// NEW: Add status code assertion
$this->assertResponseStatusCode($response, 200);
// Keep existing body assertion
$this->assertResponseBodyContains($response, \'expected message\');
// NEW: Add database verification
$this->assertDatabaseHas(\'ip_table\', [\'id\' => $id]);',
        ];
    }

    private function suggestRedirectOnly($testName, $testBody)
    {
        return [
            'issue' => 'Only checking redirect - missing guard verification and boundary cases',
            'type' => 'redirect-only',
            'suggestions' => [
                'Add assertion for correct redirect target',
                'Add boundary tests: nonexistent ID, invalid input, null values',
                'Verify no side effects occurred (assertDatabaseMissing)',
                'Test that authorization guard actually works',
            ],
            'template' => '
$response = $this->post(\'/admin-endpoint\');
// NEW: Test boundary - invalid user
$this->assertResponseStatus($response, 302);
// NEW: Verify we redirected to login, not elsewhere
$this->assertStringContains($response->headers(\'Location\'), \'/login\');
// NEW: Verify no operation occurred
$this->assertDatabaseMissing(\'ip_payments\', [...]);',
        ];
    }

    public function printReport()
    {
        echo "## Test Refactoring Suggestions for: " . basename($this->testFile) . "\n\n";

        if (empty($this->suggestions)) {
            echo "✓ No obvious refactoring needed (tests appear to have adequate assertions)\n";
            return;
        }

        foreach ($this->suggestions as $testName => $sugg) {
            echo "### `" . $testName . "`\n";
            echo "**Issue**: " . $sugg['issue'] . "\n\n";

            echo "**Suggestions**:\n";
            foreach ($sugg['suggestions'] as $s) {
                echo "- " . $s . "\n";
            }

            if (isset($sugg['template'])) {
                echo "\n**Template**:\n";
                echo "```php\n" . $sugg['template'] . "\n```\n";
            } elseif (isset($sugg['templates'])) {
                echo "\n**Templates**:\n";
                foreach ($sugg['templates'] as $label => $template) {
                    echo "\n**" . $label . "**\n";
                    echo "```php\n" . $template . "\n```\n";
                }
            }

            echo "\n---\n\n";
        }
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    if (isset($argv[1])) {
        $tool = new TestRefactoringTool($argv[1]);
        $suggestions = $tool->analyze();

        if (!empty($suggestions)) {
            $tool->printReport();
        }
    } else {
        echo "Usage: php test-refactoring-tool.php <test-file>\n";
        echo "Example: php test-refactoring-tool.php tests/Unit/Security/FileSecurityHelperTest.php\n";
    }
}
