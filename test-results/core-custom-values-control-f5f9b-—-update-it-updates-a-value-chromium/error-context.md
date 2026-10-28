# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: core/custom-values-controller.spec.js >> Custom values — update >> it updates a value
- Location: tests/E2E/core/custom-values-controller.spec.js:80:7

# Error details

```
Error: Channel closed
```

# Test source

```ts
  1  | /**
  2  |  * Keeps the shared admin session in tests/E2E/.auth/admin.json valid.
  3  |  *
  4  |  * `global-setup.js` writes it once. It normally survives the whole run (a DB
  5  |  * reseed keeps the admin at user_id 1), but a test in which User_Controller
  6  |  * calls the raw `session_destroy()` — a non-admin reaching an admin route —
  7  |  * tears the shared session down under the single-process dev server. Rather
  8  |  * than make every such spec clean up after itself, the per-test fixture
  9  |  * (test.js) calls `ensureAdminSession()` first: a one-request probe, and a full
  10 |  * re-login only when the probe shows the session is gone.
  11 |  */
  12 | 
  13 | import { chromium } from '@playwright/test';
  14 | import fs from 'node:fs';
  15 | import path from 'node:path';
  16 | import { E2E_BASE_URL, E2E_EMAIL, E2E_PASSWORD, LOGIN_PATH } from '../config.js';
  17 | 
  18 | const authFile = path.resolve('tests/E2E/.auth/admin.json');
  19 | 
  20 | function storedSessionCookie() {
  21 |   try {
  22 |     const state = JSON.parse(fs.readFileSync(authFile, 'utf8'));
  23 |     return state.cookies?.map((c) => `${c.name}=${c.value}`).join('; ') ?? '';
  24 |   } catch {
  25 |     return '';
  26 |   }
  27 | }
  28 | 
  29 | async function sessionIsAlive() {
  30 |   const cookie = storedSessionCookie();
  31 |   if (!cookie) return false;
  32 | 
  33 |   try {
  34 |     const response = await fetch(`${E2E_BASE_URL}/dashboard`, {
  35 |       headers: { Cookie: cookie },
  36 |       redirect: 'manual',
  37 |     });
  38 |     if (response.status >= 300 && response.status < 400) return false;
  39 | 
  40 |     return !(await response.text()).includes('InvoicePlane - Login');
  41 |   } catch {
  42 |     return false;
  43 |   }
  44 | }
  45 | 
  46 | /** Log in through the browser and (re)write the storageState file. */
  47 | export async function refreshAdminSession() {
  48 |   fs.mkdirSync(path.dirname(authFile), { recursive: true });
  49 | 
  50 |   const browser = await chromium.launch();
  51 |   const context = await browser.newContext({ baseURL: E2E_BASE_URL });
  52 |   const page = await context.newPage();
  53 | 
  54 |   await page.goto(LOGIN_PATH);
  55 |   await page.fill('input[name="email"]', E2E_EMAIL);
  56 |   await page.fill('input[name="password"]', E2E_PASSWORD);
> 57 |   await Promise.all([
     |   ^ Error: Channel closed
  58 |     page.waitForURL((url) => !url.pathname.includes(LOGIN_PATH), { timeout: 60000 }),
  59 |     page.click('form button[type="submit"]'),
  60 |   ]);
  61 | 
  62 |   await context.storageState({ path: authFile });
  63 |   await browser.close();
  64 | }
  65 | 
  66 | /** Probe the stored session; re-login only if it is no longer valid. */
  67 | export async function ensureAdminSession() {
  68 |   if (!(await sessionIsAlive())) {
  69 |     await refreshAdminSession();
  70 |   }
  71 | }
  72 | 
  73 | /**
  74 |  * Log in as an arbitrary user in a brand-new browser context (its own cookie
  75 |  * jar) and return `{ context, page }`. The caller owns `context` and must
  76 |  * `close()` it. Used for the guest-session and non-admin authorization tests.
  77 |  */
  78 | export async function loginAs(browser, email, password) {
  79 |   const context = await browser.newContext({ baseURL: E2E_BASE_URL });
  80 |   const page = await context.newPage();
  81 | 
  82 |   await page.goto(LOGIN_PATH);
  83 |   await page.fill('input[name="email"]', email);
  84 |   await page.fill('input[name="password"]', password);
  85 |   await Promise.all([
  86 |     page.waitForURL((url) => !url.pathname.includes(LOGIN_PATH)),
  87 |     page.click('form button[type="submit"]'),
  88 |   ]);
  89 | 
  90 |   return { context, page };
  91 | }
  92 | 
```