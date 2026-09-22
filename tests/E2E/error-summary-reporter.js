import fs from 'fs';
import path from 'path';

/**
 * Collects every "browser-error" annotation recorded by test.js's page
 * fixture (console errors + uncaught page exceptions) across the whole run
 * and prints/writes a single consolidated report at the end — the point
 * being to surface errors that happened silently in tests that never
 * explicitly checked for them, not just the ones that already fail loudly
 * on their own.
 *
 * (Ported unchanged from the InvoicePlane v2 E2E suite except for the
 * report path, which is kept out of the repo root.)
 */
const REPORT_PATH = path.resolve('tests/E2E/error-report.md');

export default class ErrorSummaryReporter {
  constructor() {
    this.entries = [];
  }

  onTestEnd(test, result) {
    const errors = result.annotations
      .filter((a) => a.type === 'browser-error')
      .map((a) => a.description);

    if (errors.length === 0) return;

    this.entries.push({
      title: test.titlePath().slice(3).join(' › '),
      status: result.status,
      errors,
    });
  }

  onEnd() {
    const lines = [];

    if (this.entries.length === 0) {
      lines.push('No console/page errors captured across the run.');
    } else {
      lines.push(`${this.entries.length} test(s) had console/page errors:`);
      lines.push('');
      for (const entry of this.entries) {
        lines.push(`${entry.title} (${entry.status})`);
        for (const error of entry.errors) {
          lines.push(`  - ${error}`);
        }
        lines.push('');
      }
    }

    const report = lines.join('\n');
    console.log('\n=== Browser error report ===\n' + report + '\n');
    fs.writeFileSync(REPORT_PATH, report + '\n');
  }
}
