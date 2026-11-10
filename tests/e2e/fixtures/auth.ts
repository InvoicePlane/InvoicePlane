import { test as base, expect } from '@playwright/test';
import path from 'path';

/**
 * Playwright fixtures for authentication.
 *
 * Provides reusable login functionality for e2e tests.
 * Implements persistent authentication state to avoid re-logging-in on every test.
 */

const authFile = path.join(__dirname, '.auth', 'admin.json');

export const test = base.extend({
  authenticatedPage: async ({ page }, use) => {
    // Check if we have a valid auth state already
    const stateFile = path.join(__dirname, '.auth', 'admin.json');

    try {
      // Try to load existing auth state
      await page.context().addInitScript(() => {
        const existingState = localStorage.getItem('auth_state');
        if (existingState) {
          // Auth state exists, page should load with session
        }
      });

      // Load the stored auth state if it exists
      const fs = require('fs');
      if (fs.existsSync(stateFile)) {
        // Auth state exists, re-use it
        const state = JSON.parse(fs.readFileSync(stateFile, 'utf-8'));
        await page.context().addCookies(state.cookies);
        await page.goto('/');
        await page.waitForURL('**/dashboard');
      } else {
        // Need to login fresh
        await page.goto('/');
        await page.fill('input[name="email"]', 'admin@test.local');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard');

        // Save auth state for future tests
        const cookies = await page.context().cookies();
        const state = { cookies };
        const fs = require('fs');
        const dir = path.dirname(stateFile);
        if (!fs.existsSync(dir)) {
          fs.mkdirSync(dir, { recursive: true });
        }
        fs.writeFileSync(stateFile, JSON.stringify(state, null, 2));
      }
    } catch (error) {
      // Fallback: login normally if state load fails
      await page.goto('/');
      await page.fill('input[name="email"]', 'admin@test.local');
      await page.fill('input[name="password"]', 'password');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/dashboard');
    }

    await use(page);
  },
});

export { expect };
