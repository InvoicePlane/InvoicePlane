/**
 * Browser coverage for application/modules/email_templates/controllers/Email_templates.php.
 * Mirrors tests/Feature/Core/EmailTemplatesControllerTest.php.
 * Required field (Mdl_Email_templates::validation_rules): email_template_title.
 */

import { test, expect } from '../test.js';
import { seedEmailTemplate, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired } from '../support/forms.js';
import { postForm } from '../support/http.js';

const body = (over = {}) => ({
  email_template_title: uniq('Template'),
  email_template_type: 'invoice',
  email_template_body: 'Body text.',
  btn_submit: '1',
  ...over,
});

test.describe('Email templates — list', () => {
  test('it lists every email template', async ({ page }) => {
    /* Arrange */
    const a = seedEmailTemplate({ email_template_title: uniq('InvoiceReminder') });
    const b = seedEmailTemplate({ email_template_title: uniq('QuoteFollowUp') });

    /* Act */
    await page.goto('/email_templates');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.title);
    await expect(page.locator('#content')).toContainText(b.title);
  });
});

test.describe('Email templates — create', () => {
  test('it creates an email template', async ({ page }) => {
    /* Arrange */
    const title = uniq('PaymentReceived');

    /* Act */
    await page.goto('/email_templates/form');
    await page.fill('#email_template_title', title);
    await page.check('#email_template_type_invoice');
    await page.fill('#email_template_body', 'Thank you for your payment.');
    await Promise.all([page.waitForURL(/\/email_templates(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(title);
  });

  test('it fails to create without email_template_title', async ({ page }) => {
    /* Arrange */
    await page.goto('/email_templates/form');
    await page.check('#email_template_type_invoice');
    await page.fill('#email_template_body', 'Body with no title.');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#email_template_title');
  });
});

test.describe('Email templates — update', () => {
  test('it renders the edit form for the requested email template only', async ({ page }) => {
    /* Arrange */
    const target = seedEmailTemplate({ email_template_title: uniq('EditableTemplate') });
    const other = seedEmailTemplate({ email_template_title: uniq('OtherTemplate') });

    /* Act */
    await page.goto(`/email_templates/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#email_template_title')).toHaveValue(target.title);
    await expect(page.locator('body')).not.toContainText(other.title);
  });

  test('it updates an email template', async ({ page }) => {
    /* Arrange */
    const template = seedEmailTemplate({ email_template_title: uniq('OriginalTemplate') });
    const renamed = uniq('RenamedTemplate');

    /* Act */
    const response = await postForm(page, `/email_templates/form/${template.id}`, body({
      email_template_title: renamed,
      email_template_body: 'Updated body.',
    }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT email_template_title FROM ip_email_templates WHERE email_template_id = ${template.id}`))
      .toEqual([{ email_template_title: renamed }]);
  });

  test('it fails to update without email_template_title', async ({ page }) => {
    /* Arrange */
    const template = seedEmailTemplate({ email_template_title: uniq('KeepThisTemplate') });

    /* Act + Assert */
    await page.goto(`/email_templates/form/${template.id}`);
    await page.fill('#email_template_title', '');
    await expectBlockedByRequired(page, '#email_template_title');
  });
});

test.describe('Email templates — delete', () => {
  test('it deletes an email template', async ({ page }) => {
    /* Arrange */
    const doomed = seedEmailTemplate({ email_template_title: uniq('DeletableTemplate') });
    const kept = seedEmailTemplate({ email_template_title: uniq('KeptTemplate') });

    /* Act */
    await page.goto('/email_templates');
    const row = page.locator('tr', { hasText: doomed.title });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT email_template_id FROM ip_email_templates WHERE email_template_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT email_template_id FROM ip_email_templates WHERE email_template_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes an email template when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete an email template when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Email templates — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no email template', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/email_templates');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Template');
  });
});
