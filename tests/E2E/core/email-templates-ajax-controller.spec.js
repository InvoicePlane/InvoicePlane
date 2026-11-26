/**
 * Browser smoke coverage for application/modules/email_templates/controllers/Ajax.php.
 * Mirrors tests/Feature/Core/EmailTemplatesAjaxControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Email templates AJAX — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/email_templates');

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});
