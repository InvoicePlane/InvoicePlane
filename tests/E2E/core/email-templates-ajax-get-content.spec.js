/**
 * Browser coverage for application/modules/email_templates/controllers/Ajax.php
 * ::get_content(). Mirrors tests/Feature/Core/EmailTemplatesAjaxGetContentTest.php
 * — the endpoint the template picker calls to load a body.
 */

import { test, expect } from '../test.js';
import { seedEmailTemplate } from '../support/fixtures.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

test.describe('Email templates AJAX — get_content', () => {
  test('it gets the content of an existing template', async ({ page }) => {
    /* Arrange */
    const template = seedEmailTemplate({ email_template_body: 'Marker body content' });

    /* Act */
    const response = await page.request.post('/email_templates/ajax/get_content', {
      headers: XHR,
      form: { email_template_id: String(template.id) },
    });

    /* Assert */
    expect(await response.text()).toContain('Marker body content');
  });

  test('it returns null for an unknown template id', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/email_templates/ajax/get_content', {
      headers: XHR,
      form: { email_template_id: '999999' },
    });

    /* Assert */
    expect((await response.text()).trim()).toBe('null');
  });

  test('it requires an ajax request', async ({ page }) => {
    /* Arrange */
    const template = seedEmailTemplate();

    /* Act */
    const response = await page.request.post('/email_templates/ajax/get_content', {
      form: { email_template_id: String(template.id) },
    });

    /* Assert */
    expect((await response.text()).trim()).toBe('');
  });
});
