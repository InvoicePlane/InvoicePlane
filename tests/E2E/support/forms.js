/**
 * Form-interaction helpers shared by every CRUD spec.
 *
 * InvoicePlane's admin forms are plain server-rendered Bootstrap 3. The
 * conventions these helpers bake in, all verified against the live markup:
 *
 *   - the primary submit control is `<button id="btn-submit" name="btn_submit">`
 *     (application/modules/layout/views/header_buttons.php);
 *   - a saved record flashes `<div class="alert alert-success …">` and a
 *     rejected one flashes / renders `<div class="alert alert-danger …">`
 *     (application/modules/layout/views/alerts.php);
 *   - required fields carry a native HTML `required` attribute, so an omitted
 *     one is stopped by the browser before any request is sent.
 */

import { expect } from '../test.js';

/**
 * Fill a set of fields keyed by their `name` attribute. Selects use
 * selectOption, checkboxes take a boolean, everything else is `fill()`.
 * Missing fields throw — a silently skipped field is how a "valid except X"
 * submission accidentally becomes "valid except X and Y".
 */
export async function fillByName(page, values) {
  for (const [name, value] of Object.entries(values)) {
    const field = page.locator(`[name="${name}"]`).first();
    await expect(field, `form field [name="${name}"] should exist`).toHaveCount(1);

    const tag = await field.evaluate((el) => el.tagName.toLowerCase());
    const type = await field.evaluate((el) => el.getAttribute('type'));

    if (tag === 'select') {
      await field.selectOption(String(value));
    } else if (type === 'checkbox' || type === 'radio') {
      await field.setChecked(Boolean(value));
    } else {
      await field.fill(String(value));
    }
  }
}

/** Click the standard Save button and wait for the resulting navigation to settle. */
export async function save(page) {
  await Promise.all([
    page.waitForLoadState('load'),
    page.locator('#btn-submit').click(),
  ]);
}

// The dismissible flash message rendered by
// application/modules/layout/views/alerts.php. Scoped with [role="alert"] so it
// never collides with the static `.alert.alert-danger` hint boxes the forms use
// for styling (there can be a dozen of those, all hidden, on one page).
const FLASH_SUCCESS = '.alert-success[role="alert"]';
const FLASH_ERROR = '.alert-danger[role="alert"]';

/** Assert a green "record saved" flash is shown. */
export async function expectSavedFlash(page, text) {
  const alert = page.locator(FLASH_SUCCESS);
  await expect(alert).toBeVisible();
  if (text) await expect(alert).toContainText(text);
}

/** Assert a red flash message (controller-set flashdata) is shown. */
export async function expectErrorFlash(page, text) {
  const alert = page.locator(FLASH_ERROR);
  await expect(alert).toBeVisible();
  if (text) await expect(alert).toContainText(text);
}

/**
 * Assert a CI3 form-validation summary is shown. alerts.php renders these as a
 * plain `<div class="alert alert-danger">` (no role) via validation_errors(),
 * so this is deliberately broader than expectErrorFlash().
 */
export async function expectValidationError(page, text) {
  const alert = page.locator('#content .alert.alert-danger').first();
  await expect(alert).toBeVisible();
  if (text) await expect(alert).toContainText(text);
}

/**
 * Assert that submitting the form is blocked by the browser's own constraint
 * validation on `selector` (an omitted required field) — i.e. no request is
 * sent and the field reports itself invalid with a user-facing message.
 * Mirrors the "native-HTML-backed required field" rejection mechanism.
 */
export async function expectBlockedByRequired(page, selector) {
  const urlBefore = page.url();
  await page.locator('#btn-submit').click();

  const field = page.locator(selector);
  await expect(field).toHaveJSProperty('validity.valid', false);
  const message = await field.evaluate((el) => el.validationMessage);
  expect(message, 'the browser should show a constraint-validation message').not.toBe('');

  // The click must not have navigated anywhere.
  expect(page.url()).toBe(urlBefore);
}
