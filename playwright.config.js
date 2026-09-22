import { defineConfig, devices } from '@playwright/test';
import path from 'path';
import { E2E_BASE_URL } from './tests/E2E/config.js';

// Start our own PHP built-in server only for local runs that did not point
// E2E_BASE_URL at an already-running instance. CI starts the server itself
// (see .github/workflows/e2e-tests.yml) and sets E2E_BASE_URL.
const manageServer = !process.env.CI && !process.env.E2E_BASE_URL;

export default defineConfig({
  testDir: './tests/E2E',
  testMatch: '**/*.spec.js',
  // Serial by design: one shared app instance (PHP's built-in server handles
  // one request at a time) backed by one database that every test truncates and
  // reseeds before it runs (tests/E2E/test.js). Parallel workers would race on
  // both. Keep this in step with the PHPUnit Feature suite's per-test reset.
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1,
  reporter: [
    ['html', { open: 'never' }],
    ['list'],
    ...(process.env.CI ? [['github']] : []),
    ['./tests/E2E/error-summary-reporter.js'],
  ],
  globalSetup: path.resolve('./tests/E2E/global-setup.js'),
  use: {
    baseURL: E2E_BASE_URL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    storageState: 'tests/E2E/.auth/admin.json',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: manageServer
    ? {
        // `-d variables_order=EGPCS`: this build's php.ini omits `E`, so without
        // it exported vars never reach $_ENV and InvoicePlane's env() (which
        // reads $_ENV only) can't see DB_HOSTNAME=127.0.0.1 — the app then tries
        // the Docker-only `mariadb` host from ipconfig.php and fails to boot.
        command:
          'DB_HOSTNAME=${DB_HOSTNAME:-127.0.0.1} php -d variables_order=EGPCS -S localhost:8000 -t . tests/E2E/router.php',
        url: E2E_BASE_URL,
        reuseExistingServer: true,
        timeout: 120 * 1000,
      }
    : undefined,
});
