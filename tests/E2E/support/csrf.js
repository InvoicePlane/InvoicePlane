/**
 * Shared session against the CSRF-on server (E2E_CSRF_BASE_URL) — a second
 * InvoicePlane instance booted with CSRF_PROTECTION=true, started alongside
 * the main CSRF-off server by tests/E2E/docker-e2e.sh / the CI workflow /
 * playwright.config.js's local webServer array.
 *
 * A second instance is required, not a per-request toggle, because
 * bootstrap/kernel.php only loads ipconfig.php once per process
 * (CI_KERNEL_BOOTED) and the built-in PHP server serves the whole run from
 * one long-lived process — CSRF_PROTECTION is fixed for a server's entire
 * lifetime once it boots.
 *
 * Used by the #1694 CSRF-regression parity pairs scattered across the CRUD
 * specs ("it still deletes/updates/… when csrf protection is on and the
 * token is valid" / "… when the csrf token is missing"), each of which
 * otherwise stays test.skip()'d against the main server.
 */

import { chromium } from '@playwright/test';
import { E2E_CSRF_BASE_URL, E2E_EMAIL, E2E_PASSWORD, LOGIN_PATH } from '../config.js';

let sharedPage;

async function login(page) {
  await page.goto(LOGIN_PATH);
  await page.fill('input[name="email"]', E2E_EMAIL);
  await page.fill('input[name="password"]', E2E_PASSWORD);
  await Promise.all([
    page.waitForURL((u) => !u.pathname.includes(LOGIN_PATH)),
    page.click('form button[type="submit"]'),
  ]);
}

/**
 * Same class of flakiness auth.js's ensureAdminSession() works around for the
 * main server: under the single-process dev server the shared session
 * occasionally dies between requests (observed here even for a single admin
 * doing nothing but its own CRUD — root cause not pinned down, e.g. a
 * CI3 session-regeneration edge case; not worth chasing further given the
 * probe-and-relogin pattern already exists for exactly this failure mode).
 * A dead session redirects to /sessions/login; a plain GET reveals that
 * without needing a full browser navigation.
 */
async function sessionIsAlive(page) {
  const response = await page.request.get('/dashboard', { maxRedirects: 0 });
  if (response.status() >= 300 && response.status() < 400) return false;

  return !(await response.text()).includes('InvoicePlane - Login');
}

/** A page authenticated as admin against the CSRF-on server, reused across the whole run. */
export async function csrfOnPage() {
  if (!sharedPage) {
    const browser = await chromium.launch();
    const context = await browser.newContext({ baseURL: E2E_CSRF_BASE_URL });
    sharedPage = await context.newPage();
    await login(sharedPage);

    return sharedPage;
  }

  if (!(await sessionIsAlive(sharedPage))) {
    await login(sharedPage);
  }

  return sharedPage;
}
