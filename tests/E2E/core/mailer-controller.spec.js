/**
 * Browser coverage for application/modules/mailer/controllers/Mailer.php.
 * Mirrors tests/Feature/Core/MailerControllerTest.php — the send-invoice form
 * renders for a seeded invoice and its "from" address defaults sensibly.
 */

import { test, expect } from '../test.js';
import { createInvoice } from '../support/fixtures.js';
import { dbExec } from '../support/db.js';

test.describe('Mailer — send invoice form', () => {
  test('it renders the send invoice form for a seeded invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const response = await page.goto(`/mailer/invoice/${invoice.id}`);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });

  test('it prefills the from address with the smtp mail from setting', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    dbExec(
      "INSERT INTO ip_settings (setting_key, setting_value) VALUES ('smtp_mail_from', 'noreply@example.test')"
      + ' ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
    );

    /* Act */
    const body = await (await page.goto(`/mailer/invoice/${invoice.id}`)).text();

    /* Assert */
    expect(body).toContain('noreply@example.test');
  });

  test('it falls back to the current user email when smtp mail from is empty', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    dbExec("UPDATE ip_settings SET setting_value = '' WHERE setting_key = 'smtp_mail_from'");

    /* Act */
    const body = await (await page.goto(`/mailer/invoice/${invoice.id}`)).text();

    /* Assert */
    expect(body).toContain('admin@test.local');
  });
});

test.describe('Mailer — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest away from the mailer', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/mailer/invoice/1');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
