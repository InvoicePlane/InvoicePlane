import { resetDatabase } from './support/db.js';
import { refreshAdminSession } from './support/auth.js';

/**
 * Once per run: start from the same clean baseline every per-test reset
 * produces, then log in and persist the admin session so every spec starts
 * authenticated via playwright.config.js's `storageState`. The per-test fixture
 * (test.js) keeps that session valid from there.
 */
export default async function globalSetup() {
  resetDatabase();
  await refreshAdminSession();
}
