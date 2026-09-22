/**
 * Shared E2E test configuration.
 *
 * Everything is read from the environment so credentials and host specifics
 * never live in source. Override via shell exports, an .env loader, or CI
 * secrets:
 *   E2E_BASE_URL   where the running InvoicePlane instance answers
 *   E2E_EMAIL      admin login
 *   E2E_PASSWORD   admin password
 *
 * Defaults match tests/Support/seed_baseline.php — the "Admin" account it
 * inserts into ip_users on every fresh seed — and the php -S webServer that
 * playwright.config.js starts on localhost:8000 when E2E_BASE_URL is unset.
 */

export const E2E_EMAIL = process.env.E2E_EMAIL || 'admin@test.local';
export const E2E_PASSWORD = process.env.E2E_PASSWORD || 'password';

export const E2E_BASE_URL =
  process.env.E2E_BASE_URL || process.env.IP_URL || 'http://localhost:8000';

// InvoicePlane's login/logout routes. Not tenant-scoped (InvoicePlane is a
// single-company app, unlike the multi-tenant source these were adapted from).
export const LOGIN_PATH = '/sessions/login';
export const LOGOUT_PATH = '/sessions/logout';
