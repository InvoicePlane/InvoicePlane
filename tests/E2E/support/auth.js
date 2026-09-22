/**
 * Keeps the shared admin session in tests/E2E/.auth/admin.json valid.
 *
 * `global-setup.js` writes it once. It normally survives the whole run (a DB
 * reseed keeps the admin at user_id 1), but a test in which User_Controller
 * calls the raw `session_destroy()` — a non-admin reaching an admin route —
 * tears the shared session down under the single-process dev server. Rather
 * than make every such spec clean up after itself, the per-test fixture
 * (test.js) calls `ensureAdminSession()` first: a one-request probe, and a full
 * re-login only when the probe shows the session is gone.
 */

import { chromium } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { E2E_BASE_URL, E2E_EMAIL, E2E_PASSWORD, LOGIN_PATH } from '../config.js';

const authFile = path.resolve('tests/E2E/.auth/admin.json');

function storedSessionCookie() {
  try {
    const state = JSON.parse(fs.readFileSync(authFile, 'utf8'));
    return state.cookies?.map((c) => `${c.name}=${c.value}`).join('; ') ?? '';
  } catch {
    return '';
  }
}

async function sessionIsAlive() {
  const cookie = storedSessionCookie();
  if (!cookie) return false;

  try {
    const response = await fetch(`${E2E_BASE_URL}/dashboard`, {
      headers: { Cookie: cookie },
      redirect: 'manual',
    });
    if (response.status >= 300 && response.status < 400) return false;

    return !(await response.text()).includes('InvoicePlane - Login');
  } catch {
    return false;
  }
}

/** Log in through the browser and (re)write the storageState file. */
export async function refreshAdminSession() {
  fs.mkdirSync(path.dirname(authFile), { recursive: true });

  const browser = await chromium.launch();
  const context = await browser.newContext({ baseURL: E2E_BASE_URL });
  const page = await context.newPage();

  await page.goto(LOGIN_PATH);
  await page.fill('input[name="email"]', E2E_EMAIL);
  await page.fill('input[name="password"]', E2E_PASSWORD);
  await Promise.all([
    page.waitForURL((url) => !url.pathname.includes(LOGIN_PATH), { timeout: 60000 }),
    page.click('form button[type="submit"]'),
  ]);

  await context.storageState({ path: authFile });
  await browser.close();
}

/** Probe the stored session; re-login only if it is no longer valid. */
export async function ensureAdminSession() {
  if (!(await sessionIsAlive())) {
    await refreshAdminSession();
  }
}

/**
 * Log in as an arbitrary user in a brand-new browser context (its own cookie
 * jar) and return `{ context, page }`. The caller owns `context` and must
 * `close()` it. Used for the guest-session and non-admin authorization tests.
 */
export async function loginAs(browser, email, password) {
  const context = await browser.newContext({ baseURL: E2E_BASE_URL });
  const page = await context.newPage();

  await page.goto(LOGIN_PATH);
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await Promise.all([
    page.waitForURL((url) => !url.pathname.includes(LOGIN_PATH)),
    page.click('form button[type="submit"]'),
  ]);

  return { context, page };
}
