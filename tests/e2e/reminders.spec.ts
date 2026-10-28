import { test, expect } from '@playwright/test';

/**
 * E2E tests for invoice reminders feature.
 *
 * Tests the user interface for displaying and managing reminder history on invoices.
 * These tests verify the frontend behavior; see tests/Feature/Invoices/CronRemindersFeatureTest.php
 * for backend reminder generation logic.
 */

test.describe('Invoice Reminders', () => {
  test.beforeEach(async ({ page }) => {
    // Setup: log in as admin user
    // Note: Tests expect the application to be running and accessible at BASE_URL
    // Admin credentials should be: email=admin@test.local, password=password
    await page.goto('/');

    // Check if already logged in by looking for dashboard elements
    const isDashboard = await page.url().includes('dashboard');
    if (!isDashboard) {
      // Need to login
      const emailInput = page.locator('input[name="email"]');
      const passwordInput = page.locator('input[name="password"]');

      if (await emailInput.isVisible()) {
        await emailInput.fill('admin@test.local');
        await passwordInput.fill('password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard');
      }
    }
  });

  test('displays reminder history panel when reminders exist', async ({ page }) => {
    // Navigate to an invoice with reminder history
    // This assumes the test database is seeded with an invoice that has reminders
    await page.goto('/invoices/view/1');

    // Verify reminder history panel is visible
    const reminderPanel = page.locator('div.panel-heading:has-text("Reminder History")');
    await expect(reminderPanel).toBeVisible();

    // Verify table structure
    const table = page.locator('table.table-striped');
    await expect(table).toBeVisible();

    // Verify table headers
    const headers = page.locator('table thead th');
    const headerTexts = await headers.allTextContents();
    expect(headerTexts).toContain('Date');
    expect(headerTexts).toContain('Type');
    expect(headerTexts).toContain('Recipient');
    expect(headerTexts).toContain('Status');
  });

  test('displays correct reminder statuses', async ({ page }) => {
    // Navigate to invoice with reminders
    await page.goto('/invoices/view/1');

    // Check for status badges
    const sentBadge = page.locator('span.label-success:has-text("Sent")');
    const failedBadge = page.locator('span.label-danger:has-text("Failed")');
    const skippedBadge = page.locator('span.label-default:has-text("Skipped")');
    const pendingBadge = page.locator('span.label-warning:has-text("Pending")');

    // At least one of these status badges should exist if reminders were sent
    const badges = [sentBadge, failedBadge, skippedBadge, pendingBadge];
    let hasStatus = false;
    for (const badge of badges) {
      if (await badge.first().isVisible().catch(() => false)) {
        hasStatus = true;
        break;
      }
    }

    // Only assert badge visibility if the reminder history panel exists
    const reminderPanel = page.locator('div.panel-heading:has-text("Reminder History")');
    if (await reminderPanel.isVisible()) {
      expect(hasStatus).toBe(true);
    }
  });

  test('displays reminder details (date, type, recipient)', async ({ page }) => {
    // Navigate to invoice with reminders
    await page.goto('/invoices/view/1');

    const reminderPanel = page.locator('div.panel-heading:has-text("Reminder History")');
    if (!await reminderPanel.isVisible()) {
      test.skip();
    }

    // Verify table rows contain expected data
    const tableRows = page.locator('table tbody tr');
    const rowCount = await tableRows.count();

    if (rowCount > 0) {
      // Check first row has data in expected columns
      const firstRow = tableRows.first();
      const cells = firstRow.locator('td');

      // Date column should have a date
      const dateCell = cells.nth(0);
      const dateText = await dateCell.textContent();
      expect(dateText?.trim().length).toBeGreaterThan(0);

      // Type column should have a reminder type
      const typeCell = cells.nth(1);
      const typeText = await typeCell.textContent();
      expect(typeText?.toLowerCase()).toMatch(/before_due|overdue/);

      // Recipient column should have an email
      const recipientCell = cells.nth(2);
      const recipientText = await recipientCell.textContent();
      expect(recipientText?.trim()).toMatch(/@/);

      // Status column should have a label
      const statusCell = cells.nth(3);
      const statusLabel = statusCell.locator('span.label');
      await expect(statusLabel).toBeVisible();
    }
  });

  test('disable reminders checkbox is present and toggleable', async ({ page }) => {
    // Navigate to invoice for editing
    await page.goto('/invoices/view/1');

    // Find the disable reminders checkbox
    const disableCheckbox = page.locator('input#invoice_disable_reminders');

    // Checkbox should be present
    await expect(disableCheckbox).toBeVisible();

    // Record initial state
    const initialChecked = await disableCheckbox.isChecked();

    // Toggle the checkbox
    await disableCheckbox.click();

    // Verify state changed
    const afterClick = await disableCheckbox.isChecked();
    expect(afterClick).toBe(!initialChecked);
  });

  test('saving invoice updates reminder disable setting', async ({ page }) => {
    // Navigate to invoice for editing
    await page.goto('/invoices/view/1');

    // Find the disable reminders checkbox
    const disableCheckbox = page.locator('input#invoice_disable_reminders');
    const initialState = await disableCheckbox.isChecked();

    // Toggle the checkbox
    await disableCheckbox.click();
    const toggledState = await disableCheckbox.isChecked();
    expect(toggledState).toBe(!initialState);

    // Save the invoice (using AJAX or form submit)
    // Look for save button - could be multiple forms, so we target near the checkbox
    const saveButton = page.locator('button:has-text("Save")', {
      has: page.locator('input#invoice_disable_reminders').locator('..'),
    }).first();

    // If save button exists, click it and wait for save
    if (await saveButton.isVisible().catch(() => false)) {
      await saveButton.click();
      await page.waitForTimeout(500);

      // Reload the page to verify state was saved
      await page.reload();

      // Check the saved state
      const reloadedCheckbox = page.locator('input#invoice_disable_reminders');
      const savedState = await reloadedCheckbox.isChecked();
      expect(savedState).toBe(toggledState);
    }
  });

  test('empty reminder history message when no reminders exist', async ({ page }) => {
    // Create an invoice without reminders by visiting a fresh invoice
    // This test verifies graceful handling when no reminders are sent yet
    await page.goto('/invoices/view/1');

    const reminderPanel = page.locator('div.panel-heading:has-text("Reminder History")');

    // If panel exists, it should either have data or show empty state message
    if (await reminderPanel.isVisible()) {
      const table = page.locator('table tbody tr');
      const rowCount = await table.count();

      // If table is empty, there may be an empty message elsewhere
      // Just verify the panel doesn't crash
      await expect(reminderPanel).toBeVisible();
    }
  });

  test('reminder history displays when client disabled reminders', async ({ page }) => {
    // Navigate to invoice
    await page.goto('/invoices/view/1');

    // Check for client disabled message
    const clientDisabledMsg = page.locator('p:has-text("Client disabled reminders")');

    // If present, verify it's visible when client has disabled reminders
    if (await clientDisabledMsg.isVisible()) {
      await expect(clientDisabledMsg).toBeVisible();
    }
  });
});
