/**
 * Comprehensive E2E test: Client creation, editing, validation, and filtering.
 *
 * This demonstrates how to test a complex feature workflow through the UI:
 * - Multi-step form with validation
 * - State persistence across page loads
 * - List filtering and search
 * - Error handling and recovery
 * - Data integrity verification
 *
 * Run: npm run e2e -- clients-comprehensive
 */

import { test, expect } from '../test.js';
import { uniq, createClient } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired, expectErrorFlash, expectSavedFlash } from '../support/forms.js';

test.describe('Clients — comprehensive workflow', () => {
  test('it creates, edits, validates, and filters clients through a complete workflow', async ({ page }) => {
    /* ========== PHASE 1: Create a new client ========== */

    /* Arrange */
    const clientName = uniq('TechCorp International');
    const clientSurname = 'Division A';
    const clientEmail = `contact-${uniq('techcorp')}@example.com`;
    const clientPhone = '+1-555-0123';

    /* Act: Navigate to create form */
    await page.goto('/clients/form');
    await expect(page).toHaveURL(/\/clients\/form$/);

    /* Assert: Form is blank for new client */
    await expect(page.locator('#client_name')).toHaveValue('');
    await expect(page.locator('#client_surname')).toHaveValue('');

    /* Act: Fill in form fields */
    await page.fill('#client_name', clientName);
    await page.fill('#client_surname', clientSurname);
    await page.fill('#client_email', clientEmail);
    await page.fill('#client_phone', clientPhone);

    /* Act: Submit form */
    await Promise.all([
      page.waitForURL(/\/clients\/view\/\d+/),
      page.click('#btn-submit'),
    ]);

    /* Assert: Redirect to view page and data is visible */
    const viewUrl = page.url();
    const clientId = Number(viewUrl.match(/\/clients\/view\/(\d+)/)[1]);

    await expect(page.locator('#content')).toContainText(clientName);
    await expect(page.locator('#content')).toContainText(clientSurname);
    await expect(page.locator('#content')).toContainText(clientEmail);
    await expect(page.locator('#content')).toContainText(clientPhone);

    /* Assert: Data persisted in database */
    const [created] = dbQuery(
      `SELECT client_name, client_surname, client_email, client_phone FROM ip_clients WHERE client_id = ${clientId}`,
    );
    expect(created.client_name).toBe(clientName);
    expect(created.client_surname).toBe(clientSurname);
    expect(created.client_email).toBe(clientEmail);
    expect(created.client_phone).toBe(clientPhone);

    /* ========== PHASE 2: Edit the client with validation error ========== */

    /* Arrange: Updated values */
    const newEmail = `updated-${uniq('techcorp')}@example.com`;
    const newPhone = '+1-555-9876';

    /* Act: Load edit form */
    await page.goto(`/clients/form/${clientId}`);

    /* Assert: Existing data is pre-filled */
    await expect(page.locator('#client_name')).toHaveValue(clientName);
    await expect(page.locator('#client_email')).toHaveValue(clientEmail);

    /* Act: Try to clear required field (client_name) */
    await page.fill('#client_name', '');
    await page.fill('#client_email', newEmail);

    /* Act + Assert: the browser blocks the submit on the empty required field
     * client_name — it never reaches the server, so there is no flash to wait
     * for here (see clients-controller.spec.js's equivalent case). */
    await expectBlockedByRequired(page, '#client_name');

    /* Assert: Data was NOT persisted (name still original) */
    const [afterFailedUpdate] = dbQuery(
      `SELECT client_name, client_email FROM ip_clients WHERE client_id = ${clientId}`,
    );
    expect(afterFailedUpdate.client_name).toBe(clientName);
    expect(afterFailedUpdate.client_email).toBe(clientEmail); // NOT changed to newEmail

    /* Assert: Form still has the values we entered (email was changed, name is empty) */
    await expect(page.locator('#client_name')).toHaveValue('');
    await expect(page.locator('#client_email')).toHaveValue(newEmail);

    /* ========== PHASE 3: Fix the validation error and successfully update ========== */

    /* Act: Correct the name and submit again */
    const updatedName = uniq('TechCorp Global');
    await page.fill('#client_name', updatedName);

    await Promise.all([
      page.waitForURL(/\/clients\/view\/\d+/),
      page.click('#btn-submit'),
    ]);

    /* Assert: Redirect to view page with updated data */
    await expect(page.locator('#content')).toContainText(updatedName);
    await expect(page.locator('#content')).not.toContainText(clientName); // Old name gone
    await expect(page.locator('#content')).toContainText(newEmail);
    await expect(page.locator('#content')).toContainText(clientPhone); // Phone unchanged

    /* Assert: Database shows only the updated values */
    const [afterSuccessfulUpdate] = dbQuery(
      `SELECT client_name, client_email, client_phone FROM ip_clients WHERE client_id = ${clientId}`,
    );
    expect(afterSuccessfulUpdate.client_name).toBe(updatedName);
    expect(afterSuccessfulUpdate.client_email).toBe(newEmail);
    expect(afterSuccessfulUpdate.client_phone).toBe(clientPhone);

    /* ========== PHASE 4: Verify filtering on list page ========== */

    /* Arrange: Create a second client to test filtering */
    const otherClient = await createClient(page, {
      client_name: uniq('Acme Corp'),
    });

    /* Act: Load the active clients list */
    await page.goto('/clients/status/active');

    /* Assert: Both clients appear */
    await expect(page.getByRole('link', { name: updatedName })).toBeVisible();
    await expect(page.getByRole('link', { name: otherClient.name })).toBeVisible();

    /* ========== PHASE 5: Verify edit form loads correct client ========== */

    /* Act: Open the row's action dropdown, then click its edit link */
    const row = page.locator('tr', { has: page.locator(`a:has-text("${updatedName}")`) });
    await row.locator('.dropdown-toggle').click();
    await row.locator('a[href*="/clients/form/"]').first().click();

    /* Assert: The edit form loaded the correct client (not the other one) */
    await expect(page.locator('#client_name')).toHaveValue(updatedName);
    await expect(page.locator('#client_email')).toHaveValue(newEmail);
    await expect(page.locator('#content')).not.toContainText(otherClient.name);

    /* ========== PHASE 6: Test duplicate detection ========== */

    /* Arrange: Try to create a duplicate */
    const duplicateName = uniq('Duplicate Name');
    const duplicateSurname = 'Corp';

    /* Create first with this name */
    await page.goto('/clients/form');
    await page.fill('#client_name', duplicateName);
    await page.fill('#client_surname', duplicateSurname);
    await Promise.all([
      page.waitForURL(/\/clients\/view\/\d+/),
      page.click('#btn-submit'),
    ]);

    /* Act: Try to create another with same name + surname */
    await page.goto('/clients/form');
    await page.fill('#client_name', duplicateName);
    await page.fill('#client_surname', duplicateSurname);
    await Promise.all([
      page.waitForLoadState('load'),
      page.click('#btn-submit'),
    ]);

    /* Assert: Duplicate was rejected */
    await expectErrorFlash(page);
    await expect(page).toHaveURL(/\/clients\/form$/); // Stays on form, doesn't redirect
  });
});
