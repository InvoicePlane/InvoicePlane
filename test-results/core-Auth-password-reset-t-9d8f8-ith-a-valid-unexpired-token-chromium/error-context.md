# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: core/Auth/password-reset-token-expiry.spec.js >> Password reset token expiry >> it allows a password change with a valid unexpired token
- Location: tests/E2E/core/Auth/password-reset-token-expiry.spec.js:81:7

# Error details

```
Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php insert ip_users {"user_name":"resettarget-mvrx70up-1","user_email":"reset-mvrx70up-2@example.com","user_password":"$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFcqDx9DHqXQ2X4uJ1DkPZ2mEwqTzH2u","user_psalt":"e2e","user_type":1,"user_active":1,"user_passwordreset_token":"b53eff0cd88941de2d01b36a87c121519973f5ad0ac7f706192086d1f5252933","user_passwordreset_token_expiry":"2026-10-28 09:46:44","user_date_created":"2026-10-28 09:36:44","user_date_modified":"2026-10-28 09:36:44"}
Could not open input file: /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php

```

# Test source

```ts
  1  | /**
  2  |  * Per-test database reset.
  3  |  *
  4  |  * Runs tests/Support/reset-test-db.php (truncate + reseed baseline) so every
  5  |  * browser test starts from the same clean state the PHPUnit Feature suite gets.
  6  |  * Without this the shared app database accumulates rows across the run and
  7  |  * list/pagination assertions become order-dependent and flaky.
  8  |  *
  9  |  * The DB_* environment is passed straight through; a local host run needs
  10 |  * `DB_HOSTNAME=127.0.0.1` (the value in ipconfig.php, `mariadb`, only resolves
  11 |  * inside the Docker network). CI already exports DB_* for the job.
  12 |  */
  13 | 
  14 | import { execFileSync } from 'node:child_process';
  15 | import path from 'node:path';
  16 | 
  17 | const RESET_SCRIPT = path.resolve('tests/Support/reset-test-db.php');
  18 | const SQL_SCRIPT = path.resolve('tests/Support/e2e-sql.php');
  19 | const dbEnv = () => ({ ...process.env, DB_HOSTNAME: process.env.DB_HOSTNAME || '127.0.0.1' });
  20 | 
  21 | export function resetDatabase() {
  22 |   execFileSync('php', [RESET_SCRIPT], { stdio: 'pipe', env: dbEnv() });
  23 | }
  24 | 
  25 | /**
  26 |  * Insert a row and return its id — the E2E equivalent of the PHPUnit suite's
  27 |  * databaseInsert(), for setup rows with no clean UI path.
  28 |  */
  29 | export function dbInsert(table, row) {
> 30 |   const out = execFileSync('php', [SQL_SCRIPT, 'insert', table, JSON.stringify(row)], {
     |                           ^ Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php insert ip_users {"user_name":"resettarget-mvrx70up-1","user_email":"reset-mvrx70up-2@example.com","user_password":"$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFcqDx9DHqXQ2X4uJ1DkPZ2mEwqTzH2u","user_psalt":"e2e","user_type":1,"user_active":1,"user_passwordreset_token":"b53eff0cd88941de2d01b36a87c121519973f5ad0ac7f706192086d1f5252933","user_passwordreset_token_expiry":"2026-10-28 09:46:44","user_date_created":"2026-10-28 09:36:44","user_date_modified":"2026-10-28 09:36:44"}
  31 |     stdio: ['ignore', 'pipe', 'pipe'],
  32 |     env: dbEnv(),
  33 |   });
  34 | 
  35 |   return Number(out.toString().trim());
  36 | }
  37 | 
  38 | /** Run a SELECT and return the rows as an array of plain objects. */
  39 | export function dbQuery(sql) {
  40 |   const out = execFileSync('php', [SQL_SCRIPT, 'query', sql], {
  41 |     stdio: ['ignore', 'pipe', 'pipe'],
  42 |     env: dbEnv(),
  43 |   });
  44 | 
  45 |   return JSON.parse(out.toString() || '[]');
  46 | }
  47 | 
  48 | /** Run an UPDATE/DELETE/INSERT statement; returns the affected-row count. */
  49 | export function dbExec(sql) {
  50 |   const out = execFileSync('php', [SQL_SCRIPT, 'exec', sql], {
  51 |     stdio: ['ignore', 'pipe', 'pipe'],
  52 |     env: dbEnv(),
  53 |   });
  54 | 
  55 |   return Number(out.toString().trim() || 0);
  56 | }
  57 | 
```