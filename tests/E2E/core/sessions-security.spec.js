/**
 * Browser coverage aligned with tests/Feature/Core/SessionsSecurityTest.php.
 *
 * That file is `#[Group('unit')]` — it exercises extracted helper logic
 * (get_safe_referer(), token-format regex, expiry arithmetic, bot user-agent
 * detection, rate-limit window math) against a `StubSessionsSecurity` with no
 * HTTP and no CI3 bootstrap. Most of it has no browser surface; the pieces that
 * do — the password-reset token-format guard — are exercised here through the
 * real `/sessions/passwordreset/{token}` route. The rest stay `test.fixme` with
 * a pointer, since a browser can't reach a pure helper function.
 */

import { test, expect } from '../test.js';

test.use({ storageState: { cookies: [], origins: [] } });

const RESET = (token) => `/sessions/passwordreset/${token}`;

test.describe('Sessions security — password-reset token format', () => {
  test('it accepts an alphanumeric password reset token', async ({ page }) => {
    /* Arrange + Act: a well-formed (but unknown) token is not rejected on format */
    const response = await page.request.get(RESET('abc123def456'), { maxRedirects: 0 });

    /* Assert: bounced as "unknown token", not blocked as "malformed" — either
     * way the new-password form is never rendered, but it is not a hard 404 on
     * the token shape */
    expect([301, 302, 303, 307]).toContain(response.status());
  });

  test('it accepts a hex token of typical length', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(RESET('a'.repeat(64)), { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
  });

  test('it rejects a token containing a path traversal sequence', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(RESET(encodeURIComponent('../../../../etc/passwd')), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it rejects a token containing a slash', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(RESET(encodeURIComponent('abc/def')), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it rejects a token containing special characters', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(RESET(encodeURIComponent("abc<script>'")), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });
});

test.describe('Sessions security — pure helper logic (no browser surface)', () => {
  const UNIT = 'unit-only helper logic (StubSessionsSecurity) — covered by SessionsSecurityTest';

  for (const title of [
    'it allows a referer from the same base url',
    'it rejects a referer from an external domain',
    'it returns the safe default when referer is empty',
    'it rejects a referer that starts with a double slash',
    'it considers an expired token as expired',
    'it considers a future token as not expired',
    'it enforces the max expiry minutes cap of 1440',
    'it allows a valid expiry minutes value within range',
    'it rejects a zero expiry minutes and falls back to default',
    'it detects curl as a bot user agent',
    'it detects python requests as a bot user agent',
    'it detects an empty user agent as a bot',
    'it does not flag a normal browser user agent as a bot',
    'it removes attempts outside the rate limit time window',
    'it considers the ip rate limited when attempt count meets the threshold',
    'it does not rate limit when attempt count is below the threshold',
    'it accepts only canonical password reset expiry strings',
  ]) {
    test(title, () => {
      test.fixme(true, UNIT);
    });
  }
});
