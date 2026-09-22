/**
 * Browser coverage aligned with tests/Feature/Security/DevelopMergeSecurityTest.php.
 *
 * Two of its three checks are filesystem/config assertions with no browser
 * surface (the SUMEX storage folder lives outside the web root; the SUMEX XML is
 * written there) — those stay test.fixme. The third — that the uploads/import
 * directory is not directly web-accessible — is a real browser check.
 */

import { test, expect } from '../test.js';

test.describe('Develop-merge security', () => {
  test('it denies direct web access to the uploads import directory', async ({ page }) => {
    /* Arrange + Act */
    const dirListing = await page.request.get('/uploads/import/', { maxRedirects: 0 });
    const knownFile = await page.request.get('/uploads/import/clients.csv', { maxRedirects: 0 });

    /* Assert: no directory listing, no direct file serving */
    expect(dirListing.status()).not.toBe(200);
    expect([403, 404]).toContain(knownFile.status());
  });

  test('it defines the sumex storage folder outside the public web root', () => {
    test.fixme(true, 'filesystem/config assertion, no browser surface — covered by DevelopMergeSecurityTest');
  });

  test('it writes the sumex xml to the non web accessible storage folder', () => {
    test.fixme(true, 'needs the SUMEX generation flow — covered by DevelopMergeSecurityTest');
  });
});
