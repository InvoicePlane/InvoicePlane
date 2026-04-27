#!/usr/bin/env node

/**
 * Tests for .github/scripts/generate-package-update-report.cjs
 *
 * The script is a CommonJS entry-point that is not a module (no exports).  To
 * exercise its logic we intercept Node's module loader before requiring the
 * script so that `require('fs')` and `require('child_process')` return
 * controllable test doubles.  After each load the module cache entry is
 * deleted so the script can be re-evaluated with different mocks.
 *
 * Each scenario validates the content that the script eventually hands to
 * `fs.writeFileSync('updated-packages.txt', ...)`.
 *
 * Run with:  node --test tests/scripts/generate-package-update-report.test.cjs
 */

'use strict';

const { test, describe, beforeEach, afterEach } = require('node:test');
const assert = require('node:assert/strict');
const path = require('path');
const Module = require('module');

const SCRIPT_PATH = path.resolve(__dirname, '../../.github/scripts/generate-package-update-report.cjs');

/**
 * Load the script under test with the supplied fs / child_process mocks.
 *
 * Returns the string that the script passed to `fs.writeFileSync` for the
 * file named `updated-packages.txt`, or null if the call was never made.
 *
 * @param {object} opts
 * @param {string}  opts.packageJsonContent  - Stringified package.json
 * @param {string}  opts.yarnLockDiff        - Output of `git diff yarn.lock`
 * @returns {string|null}
 */
function runScript({ packageJsonContent = '{}', yarnLockDiff = '' } = {}) {
    let capturedReport = null;

    // Build lightweight fs and child_process stubs.
    const fsMock = {
        readFileSync(filePath, _encoding) {
            if (filePath === 'package.json') return packageJsonContent;
            return '';
        },
        writeFileSync(_filePath, content) {
            capturedReport = content;
        },
        existsSync(_filePath) {
            return true;
        },
    };

    const cpMock = {
        execSync(command, _options) {
            if (command === 'git diff yarn.lock') return yarnLockDiff;
            return '';
        },
    };

    // Save the original Module._load and override it to inject the stubs.
    const originalLoad = Module._load;

    Module._load = function (request, parent, isMain) {
        if (request === 'fs') return fsMock;
        if (request === 'child_process') return cpMock;
        return originalLoad.apply(this, arguments);
    };

    // Evict any previously cached version of the script.
    delete require.cache[SCRIPT_PATH];

    try {
        require(SCRIPT_PATH);
    } finally {
        // Always restore the original loader and remove the cache entry.
        Module._load = originalLoad;
        delete require.cache[SCRIPT_PATH];
    }

    return capturedReport;
}

// =============================================================================
// parsePackageJson – direct / dev dependency detection
// =============================================================================

describe('parsePackageJson behaviour (via generateReport)', () => {
    test('it treats a package listed in dependencies as a direct dependency', () => {
        /* Arrange */
        const packageJsonContent = JSON.stringify({
            dependencies: { vite: '^5.0.0' },
        });

        const yarnLockDiff = [
            ' vite@^5.0.0:',
            '-  version "5.0.0"',
            '+  version "5.1.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('Direct dependencies'), 'should have Direct section');
        assert.ok(report.includes('vite'), 'direct dep vite should appear');
        assert.ok(!report.includes('Transitive dependencies'), 'should not have Transitive section');
    });

    test('it treats a package listed in devDependencies as a direct dependency', () => {
        /* Arrange */
        const packageJsonContent = JSON.stringify({
            devDependencies: { tailwindcss: '^4.0.0' },
        });

        const yarnLockDiff = [
            ' tailwindcss@^4.0.0:',
            '-  version "4.0.0"',
            '+  version "4.1.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('Direct dependencies'), 'should have Direct section');
        assert.ok(report.includes('tailwindcss'), 'devDep tailwindcss should appear');
        assert.ok(!report.includes('Transitive dependencies'), 'should not have Transitive section');
    });

    test('it treats a package absent from package.json as a transitive dependency', () => {
        /* Arrange – package.json has no deps, but the yarn.lock diff shows an update */
        const packageJsonContent = JSON.stringify({});

        const yarnLockDiff = [
            ' esbuild@^0.27.0:',
            '-  version "0.27.1"',
            '+  version "0.27.2"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('Transitive dependencies'), 'should have Transitive section');
        assert.ok(report.includes('esbuild'), 'transitive dep esbuild should appear');
    });

    test('it returns an empty Set and produces "No package updates detected." when package.json is invalid JSON', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' lodash@^4.0.0:',
            '-  version "4.0.0"',
            '+  version "4.1.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: 'NOT_VALID_JSON', yarnLockDiff });

        /* Assert – invalid package.json means all updates are treated as transitive */
        assert.ok(report.includes('Transitive dependencies') || report.includes('No package updates detected.'));
    });
});

// =============================================================================
// parseYarnLockDiff – diff parsing
// =============================================================================

describe('parseYarnLockDiff behaviour (via generateReport)', () => {
    test('it detects a simple unquoted package version update', () => {
        /* Arrange */
        const packageJsonContent = JSON.stringify({});
        const yarnLockDiff = [
            ' lodash@^4.0.0:',
            '-  version "4.0.0"',
            '+  version "4.1.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('4.0.0 -> 4.1.0'), 'should show version transition');
    });

    test('it detects a quoted package name version update', () => {
        /* Arrange */
        const packageJsonContent = JSON.stringify({});
        const yarnLockDiff = [
            ' "@babel/core@^7.0.0":',
            '-  version "7.22.0"',
            '+  version "7.23.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('7.22.0 -> 7.23.0'), 'should show version transition for scoped pkg');
    });

    test('it returns "No package updates detected." when the diff is empty', () => {
        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff: '' });

        /* Assert */
        assert.strictEqual(report, 'No package updates detected.');
    });

    test('it ignores a version line without a preceding package header', () => {
        /* Arrange – version lines with no package name context */
        const yarnLockDiff = [
            '-  version "1.0.0"',
            '+  version "2.0.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert – no package name means no valid update recorded */
        assert.strictEqual(report, 'No package updates detected.');
    });

    test('it skips updates where old and new version are identical', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' react@^18.0.0:',
            '-  version "18.2.0"',
            '+  version "18.2.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert */
        assert.strictEqual(report, 'No package updates detected.');
    });

    test('it does not record updates with invalid version strings', () => {
        /* Arrange – a version string containing a space (invalid) */
        const yarnLockDiff = [
            ' weird-pkg@^1.0.0:',
            '-  version "1.0 alpha"',
            '+  version "2.0 beta"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert */
        assert.strictEqual(report, 'No package updates detected.');
    });
});

// =============================================================================
// generateReport – output format
// =============================================================================

describe('generateReport output format', () => {
    test('it includes a report header line', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' chalk@^5.0.0:',
            '-  version "5.2.0"',
            '+  version "5.3.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('Package Update Report'), 'report header should be present');
    });

    test('it includes a summary line with correct counts', () => {
        /* Arrange – one direct dep, one transitive */
        const packageJsonContent = JSON.stringify({
            dependencies: { vite: '^5.0.0' },
        });

        const yarnLockDiff = [
            ' vite@^5.0.0:',
            '-  version "5.0.0"',
            '+  version "5.1.0"',
            '',
            ' esbuild@^0.27.0:',
            '-  version "0.27.1"',
            '+  version "0.27.2"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(
            report.includes('Summary: 1 direct, 1 transitive (2 total)'),
            `expected summary line, got: ${report.slice(-200)}`
        );
    });

    test('it sorts direct updates alphabetically', () => {
        /* Arrange – provide deps in reverse alphabetical order */
        const packageJsonContent = JSON.stringify({
            dependencies: { zlib: '^1.0.0', alpha: '^1.0.0' },
        });

        const yarnLockDiff = [
            ' zlib@^1.0.0:',
            '-  version "1.0.0"',
            '+  version "1.1.0"',
            '',
            ' alpha@^1.0.0:',
            '-  version "1.0.0"',
            '+  version "1.1.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert – "alpha" must appear before "zlib" in the output */
        const alphaIdx = report.indexOf('alpha');
        const zlibIdx  = report.indexOf('zlib');

        assert.ok(alphaIdx !== -1, 'alpha should be present');
        assert.ok(zlibIdx !== -1, 'zlib should be present');
        assert.ok(alphaIdx < zlibIdx, 'alpha should come before zlib (alphabetical)');
    });

    test('it shows "No direct dependencies updated." when no direct deps changed', () => {
        /* Arrange – only transitive dep updated */
        const packageJsonContent = JSON.stringify({});
        const yarnLockDiff = [
            ' some-internal-lib@^0.1.0:',
            '-  version "0.1.0"',
            '+  version "0.2.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('No direct dependencies updated.'));
    });

    test('it includes the version transition arrow for each changed package', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' semver@^7.0.0:',
            '-  version "7.5.0"',
            '+  version "7.6.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('7.5.0 -> 7.6.0'), 'should include version transition with arrow');
    });

    test('it writes the report to updated-packages.txt', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' react@^18.0.0:',
            '-  version "18.2.0"',
            '+  version "18.3.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert – any non-null return means writeFileSync was called */
        assert.notEqual(report, null, 'writeFileSync should have been called');
    });

    test('it handles a prerelease version string in the diff', () => {
        /* Arrange */
        const yarnLockDiff = [
            ' my-pkg@^1.0.0-alpha.1:',
            '-  version "1.0.0-alpha.1"',
            '+  version "1.0.0-beta.1"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent: '{}', yarnLockDiff });

        /* Assert */
        assert.ok(report.includes('1.0.0-alpha.1 -> 1.0.0-beta.1'), 'should handle prerelease versions');
    });

    test('it does not emit a Transitive section when all updates are direct deps', () => {
        /* Arrange */
        const packageJsonContent = JSON.stringify({
            dependencies: { chalk: '^5.0.0', dayjs: '^1.0.0' },
        });

        const yarnLockDiff = [
            ' chalk@^5.0.0:',
            '-  version "5.2.0"',
            '+  version "5.3.0"',
            '',
            ' dayjs@^1.0.0:',
            '-  version "1.11.0"',
            '+  version "1.12.0"',
        ].join('\n');

        /* Act */
        const report = runScript({ packageJsonContent, yarnLockDiff });

        /* Assert */
        assert.ok(!report.includes('Transitive dependencies'), 'should not emit Transitive section');
    });
});