#!/usr/bin/env php
<?php

/**
 * PHPStan Results Parser.
 *
 * This script parses PHPStan JSON output and generates a formatted, actionable report.
 * It groups errors by class, strips noise, and generates a markdown checklist suitable
 * for GitHub PR comments or Copilot context.
 *
 * Usage: php parse-phpstan-results.php phpstan.json
 */
if ($argc < 2) {
    echo "Usage: php parse-phpstan-results.php <phpstan.json>\n";
    exit(1);
}

$jsonFile = $argv[1];

if ( ! file_exists($jsonFile)) {
    echo "Error: File '{$jsonFile}' not found.\n";
    exit(1);
}

$content = file_get_contents($jsonFile);
$data    = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error: Invalid JSON in '{$jsonFile}': " . json_last_error_msg() . "\n";
    exit(1);
}

// Extract errors from PHPStan JSON format
$files       = $data['files'] ?? [];
$totalErrors = $data['totals']['file_errors'] ?? 0;

if ($totalErrors === 0) {
    echo "## ✅ PHPStan Analysis - No Errors Found\n\n";
    echo "All files passed static analysis!\n";
    exit(0);
}

// Group errors by class/file
$errorsByFile     = [];
$errorsByCategory = [
    'type_errors'        => [],
    'method_errors'      => [],
    'property_errors'    => [],
    'return_type_errors' => [],
    'other_errors'       => [],
];

foreach ($files as $filePath => $fileData) {
    $messages = $fileData['messages'] ?? [];

    foreach ($messages as $message) {
        $errorText = $message['message'] ?? '';
        $line      = $message['line'] ?? 0;

        // Categorize errors
        $category = categorizeError($errorText);

        $errorsByFile[$filePath][] = [
            'line'     => $line,
            'message'  => $errorText,
            'category' => $category,
        ];

        $errorsByCategory[$category][] = [
            'file'    => $filePath,
            'line'    => $line,
            'message' => $errorText,
        ];
    }
}

// Generate markdown report
echo "## 🔍 PHPStan Analysis Report\n\n";
echo "**Total Errors:** {$totalErrors}\n\n";

// Summary by category
echo "### 📊 Error Summary by Category\n\n";
foreach ($errorsByCategory as $category => $errors) {
    $count = count($errors);
    if ($count > 0) {
        $emoji = getCategoryEmoji($category);
        $label = getCategoryLabel($category);
        echo "- {$emoji} **{$label}**: {$count} error(s)\n";
    }
}
echo "\n---\n\n";

// Detailed errors grouped by file
echo "### 📝 Detailed Errors by File\n\n";

$fileCount = 0;
foreach ($errorsByFile as $filePath => $errors) {
    $fileCount++;
    $shortPath  = getShortPath($filePath);
    $errorCount = count($errors);

    echo "#### {$fileCount}. `{$shortPath}` ({$errorCount} error(s))\n\n";

    foreach ($errors as $error) {
        $line     = $error['line'];
        $message  = trimMessage($error['message']);
        $category = getCategoryLabel($error['category']);

        echo "- **Line {$line}** [{$category}]: {$message}\n";
    }

    echo "\n";
}

echo "---\n\n";

// Generate actionable checklist
echo "### ✅ Actionable Checklist\n\n";
echo "Use this checklist to track fixes:\n\n";

foreach ($errorsByFile as $filePath => $errors) {
    $shortPath = getShortPath($filePath);

    foreach ($errors as $error) {
        $line    = $error['line'];
        $message = trimMessage($error['message'], 80);

        echo "- [ ] Fix error in `{$shortPath}:{$line}` - {$message}\n";
    }
}

echo "\n---\n";

/**
 * Categorize error based on message content.
 */
function categorizeError(string $message): string
{
    $normalizedMessage = mb_strtolower($message);

    $hasShouldReturn = str_contains($normalizedMessage, 'should return');
    $hasMethod       = str_contains($normalizedMessage, 'method');
    $hasCallTo       = str_contains($normalizedMessage, 'call to');
    $hasProperty     = str_contains($normalizedMessage, 'property');
    $hasType         = str_contains($normalizedMessage, 'type');
    $hasExpects      = str_contains($normalizedMessage, 'expects');

    // Prioritize explicit "should return" wording for return type issues
    if ($hasShouldReturn) {
        return 'return_type_errors';
    }

    // Method-related errors that are not already classified as return type errors
    if ($hasMethod || $hasCallTo) {
        return 'method_errors';
    }

    // Property issues that are not part of method/return-type problems
    if ($hasProperty && ! $hasMethod && ! $hasCallTo) {
        return 'property_errors';
    }

    // Generic type expectations that are not already covered above
    if (($hasType || $hasExpects) && ! $hasMethod && ! $hasCallTo && ! $hasProperty) {
        return 'type_errors';
    }

    return 'other_errors';
}

/**
 * Get emoji for error category.
 */
function getCategoryEmoji(string $category): string
{
    $emojis = [
        'type_errors'        => '🔢',
        'method_errors'      => '🔧',
        'property_errors'    => '📦',
        'return_type_errors' => '↩️',
        'other_errors'       => '⚠️',
    ];

    return $emojis[$category] ?? '❓';
}

/**
 * Get human-readable label for category.
 */
function getCategoryLabel(string $category): string
{
    $labels = [
        'type_errors'        => 'Type Errors',
        'method_errors'      => 'Method Errors',
        'property_errors'    => 'Property Errors',
        'return_type_errors' => 'Return Type Errors',
        'other_errors'       => 'Other Errors',
    ];

    return $labels[$category] ?? 'Unknown';
}

/**
 * Shorten file path for readability.
 */
function getShortPath(string $path): string
{
    // Normalize path separators for consistency across environments
    $normalizedPath = str_replace('\\', '/', $path);

    // Derive project root based on this script's location: .github/scripts => project root is two levels up
    $projectRoot = dirname(__DIR__, 2);
    if (is_string($projectRoot) && $projectRoot !== '') {
        $normalizedRoot = mb_rtrim(str_replace('\\', '/', $projectRoot), '/') . '/';

        if (str_starts_with($normalizedPath, $normalizedRoot)) {
            $normalizedPath = mb_substr($normalizedPath, mb_strlen($normalizedRoot));
        }
    }

    // Fallback: also try stripping the current working directory if it is a prefix
    $cwd = getcwd();
    if (is_string($cwd) && $cwd !== '') {
        $normalizedCwd = mb_rtrim(str_replace('\\', '/', $cwd), '/') . '/';

        if (str_starts_with($normalizedPath, $normalizedCwd)) {
            $normalizedPath = mb_substr($normalizedPath, mb_strlen($normalizedCwd));
        }
    }

    return $normalizedPath;
}

/**
 * Trim message to reasonable length.
 */
function trimMessage(string $message, int $maxLength = 150): string
{
    // Remove excessive whitespace
    $message = preg_replace('/\s+/', ' ', $message);
    $message = mb_trim($message);

    // Truncate if too long (multibyte-safe)
    if (mb_strlen($message, 'UTF-8') > $maxLength) {
        $message = mb_substr($message, 0, $maxLength - 3, 'UTF-8') . '...';
    }

    return $message;
}
