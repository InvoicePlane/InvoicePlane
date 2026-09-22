/**
 * Per-test database reset.
 *
 * Runs tests/Support/reset-test-db.php (truncate + reseed baseline) so every
 * browser test starts from the same clean state the PHPUnit Feature suite gets.
 * Without this the shared app database accumulates rows across the run and
 * list/pagination assertions become order-dependent and flaky.
 *
 * The DB_* environment is passed straight through; a local host run needs
 * `DB_HOSTNAME=127.0.0.1` (the value in ipconfig.php, `mariadb`, only resolves
 * inside the Docker network). CI already exports DB_* for the job.
 */

import { execFileSync } from 'node:child_process';
import path from 'node:path';

const RESET_SCRIPT = path.resolve('tests/Support/reset-test-db.php');
const SQL_SCRIPT = path.resolve('tests/Support/e2e-sql.php');
const dbEnv = () => ({ ...process.env, DB_HOSTNAME: process.env.DB_HOSTNAME || '127.0.0.1' });

export function resetDatabase() {
  execFileSync('php', [RESET_SCRIPT], { stdio: 'pipe', env: dbEnv() });
}

/**
 * Insert a row and return its id — the E2E equivalent of the PHPUnit suite's
 * databaseInsert(), for setup rows with no clean UI path.
 */
export function dbInsert(table, row) {
  const out = execFileSync('php', [SQL_SCRIPT, 'insert', table, JSON.stringify(row)], {
    stdio: ['ignore', 'pipe', 'pipe'],
    env: dbEnv(),
  });

  return Number(out.toString().trim());
}

/** Run a SELECT and return the rows as an array of plain objects. */
export function dbQuery(sql) {
  const out = execFileSync('php', [SQL_SCRIPT, 'query', sql], {
    stdio: ['ignore', 'pipe', 'pipe'],
    env: dbEnv(),
  });

  return JSON.parse(out.toString() || '[]');
}

/** Run an UPDATE/DELETE/INSERT statement; returns the affected-row count. */
export function dbExec(sql) {
  const out = execFileSync('php', [SQL_SCRIPT, 'exec', sql], {
    stdio: ['ignore', 'pipe', 'pipe'],
    env: dbEnv(),
  });

  return Number(out.toString().trim() || 0);
}
