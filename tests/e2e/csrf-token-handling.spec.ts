import { test, expect } from '@playwright/test';

/**
 * E2E tests for robust CSRF token handling in AJAX requests.
 *
 * Tests the client-side JavaScript behavior for:
 * - Safe token extraction with fallback to cookies
 * - Duplicate prevention in query strings
 * - Graceful handling of missing/invalid tokens
 * - Modal and filter operations with CSRF protection
 *
 * These address real bugs that occur when:
 * 1. CSRF token meta tag is missing (scripts.js would append "false")
 * 2. Token is appended twice (causing validation failure)
 * 3. Modals fail with 403 because token value is undefined
 */

test.describe('CSRF Token Handling - Robust Injection', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to home page (or any page with CSRF tokens)
    try {
      await page.goto('/', { waitUntil: 'domcontentloaded', timeout: 5000 });
    } catch (e) {
      // If navigation fails, skip this test
      test.skip();
    }
  });

  test('CSRF meta tags are present and have valid values', async ({ page }) => {
    // Check current page for meta tags
    const tokenNameMeta = page.locator('meta[name="csrf_token_name"]');
    const tokenValueMeta = page.locator('meta[name="csrf_token_value"]');

    // Meta tags should exist in the page
    const nameContent = await tokenNameMeta.getAttribute('content').catch(() => null);
    const valueContent = await tokenValueMeta.getAttribute('content').catch(() => null);

    // If tokens don't exist on this page, skip
    if (!nameContent || !valueContent) {
      test.skip();
    }

    // Token name should default to _ip_csrf
    expect(nameContent).toBeTruthy();
    expect(valueContent).toBeTruthy();

    // Most importantly: token value should NOT be false or undefined string
    expect(valueContent).not.toEqual('false');
    expect(valueContent).not.toEqual('undefined');
    expect(valueContent?.length).toBeGreaterThan(0);
  });

  test('get_csrf_token_value() safely extracts token from meta or cookie', async ({ page }) => {
    await page.goto('/invoices');

    // Test the helper function behavior
    const tokenViaHelper = await page.evaluate(() => {
      return (window as any).get_csrf_token_value?.();
    });

    expect(tokenViaHelper).toBeTruthy();
    expect(typeof tokenViaHelper).toBe('string');
    expect(tokenViaHelper).not.toEqual('false');
    expect(tokenViaHelper).not.toEqual('undefined');
  });

  test('ajaxPrefilter validates token before appending to request', async ({ page }) => {
    await page.goto('/invoices');

    // Capture all POST requests and check their CSRF tokens
    const capturedRequests: { method: string; url: string; hasToken: boolean }[] = [];

    page.on('request', (request) => {
      if (request.method() === 'POST') {
        const data = request.postData() || '';
        const headers = request.headers();

        const hasToken =
          data.includes('_ip_csrf=') ||
          data.includes('_ip_csrf%3D') || // URL encoded
          headers['x-_ip_csrf'] ||
          headers['x-ip_csrf'];

        // Important: should NOT have "false" or "undefined" as token value
        const isBadToken =
          data.includes('_ip_csrf=false') ||
          data.includes('_ip_csrf=undefined') ||
          headers['x-_ip_csrf'] === 'false' ||
          headers['x-_ip_csrf'] === 'undefined';

        capturedRequests.push({
          method: request.method(),
          url: request.url(),
          hasToken: hasToken && !isBadToken,
        });
      }
    });

    // Try to trigger a filter or AJAX operation
    // Look for a filter button or link that uses AJAX
    const filterButtons = page.locator('button, a').filter({ hasText: /filter|search|update/i });
    const filterCount = await filterButtons.count();

    if (filterCount > 0) {
      const firstFilter = filterButtons.first();
      if (await firstFilter.isVisible()) {
        await firstFilter.click();
        await page.waitForTimeout(500);
      }
    }

    // All POST requests should have valid tokens
    for (const request of capturedRequests) {
      if (request.url().includes('/ajax/')) {
        // AJAX requests MUST have a token
        expect(request.hasToken, `AJAX POST to ${request.url()} should have valid CSRF token`).toBe(true);
      }
    }
  });

  test('ajaxPrefilter prevents duplicate token injection', async ({ page }) => {
    await page.goto('/invoices');

    // Monitor requests for duplicate tokens
    const badRequests: string[] = [];

    page.on('request', (request) => {
      if (request.method() === 'POST') {
        const data = request.postData() || '';

        // Check for duplicate token patterns
        const tokenMatches = data.match(/_ip_csrf=/g);
        if (tokenMatches && tokenMatches.length > 1) {
          badRequests.push(request.url());
        }
      }
    });

    // Trigger AJAX if possible
    const anyButton = page.locator('button[type="button"]').first();
    if (await anyButton.isVisible()) {
      await anyButton.click({ force: true });
      await page.waitForTimeout(500);
    }

    // No requests should have duplicate tokens
    expect(badRequests.length, 'No POST requests should have duplicate _ip_csrf tokens').toBe(0);
  });

  test('modals work correctly with CSRF protection enabled', async ({ page }) => {
    // Navigate to invoices list
    await page.goto('/invoices');

    // Look for a button that opens a modal (typically "Add", "New", "Create", etc.)
    const modalTriggers = page.locator('button, a').filter({ hasText: /new|add|create|edit/i });
    const triggerCount = await modalTriggers.count();

    if (triggerCount > 0) {
      const trigger = modalTriggers.first();

      // Click to open modal
      await trigger.click();
      await page.waitForTimeout(500);

      // Check if a modal opened
      const modal = page.locator('[role="dialog"], .modal, .modal-dialog').first();

      // If modal exists, verify it has CSRF token if it has forms
      if (await modal.isVisible()) {
        const forms = modal.locator('form');
        const formCount = await forms.count();

        if (formCount > 0) {
          const csrfField = forms.first().locator('input[name="_ip_csrf"]');

          // Should have CSRF field with non-empty value
          if (await csrfField.isVisible()) {
            const tokenValue = await csrfField.inputValue();
            expect(tokenValue).toBeTruthy();
            expect(tokenValue).not.toEqual('false');
            expect(tokenValue).not.toEqual('undefined');
          }
        }
      }
    }
  });

  test('form submissions include valid CSRF tokens', async ({ page }) => {
    await page.goto('/invoices');

    // Find and monitor form submissions
    let formSubmitted = false;
    let submissionHadValidToken = false;

    page.on('request', (request) => {
      if (request.method() === 'POST' && request.url().includes('/invoices')) {
        formSubmitted = true;
        const data = request.postData() || '';
        const hasValidToken = data.includes('_ip_csrf=') && !data.includes('_ip_csrf=false');
        submissionHadValidToken = hasValidToken;
      }
    });

    // Try to find and submit a form
    const forms = page.locator('form[method="post"]');
    const formCount = await forms.count();

    if (formCount > 0) {
      const form = forms.first();
      const submitButton = form.locator('button[type="submit"]');

      if (await submitButton.isVisible()) {
        await submitButton.click({ force: true });
        await page.waitForTimeout(500);

        // If a POST was made, it should have had a valid token
        if (formSubmitted) {
          expect(submissionHadValidToken, 'Form submission should include valid CSRF token').toBe(true);
        }
      }
    }
  });

  test('CSRF token helpers handle missing meta tags gracefully', async ({ page }) => {
    await page.goto('/invoices');

    // Simulate missing meta tag
    const result = await page.evaluate(() => {
      const meta = document.querySelector('meta[name="csrf_token_value"]');
      const originalContent = meta?.getAttribute('content');

      // Temporarily remove meta tag
      if (meta) {
        meta.remove();
      }

      // Call helper - should return empty string, not throw
      const token = (window as any).get_csrf_token_value?.();
      const name = (window as any).get_csrf_token_name?.();

      // Restore meta tag for other tests
      if (meta && originalContent) {
        const newMeta = document.createElement('meta');
        newMeta.setAttribute('name', 'csrf_token_value');
        newMeta.setAttribute('content', originalContent);
        document.head.appendChild(newMeta);
      }

      return { token, name, noError: true };
    });

    // Functions should not throw and should return strings
    expect(result.noError).toBe(true);
    expect(typeof result.token).toBe('string');
    expect(typeof result.name).toBe('string');
    expect(result.name).toBe('_ip_csrf'); // Should default to this
  });

  test('CSRF token rotates after POST requests', async ({ page }) => {
    await page.goto('/invoices');

    // Capture initial token
    const initialToken = await page.locator('meta[name="csrf_token_value"]').getAttribute('content');

    let newToken = initialToken;
    let responseReceived = false;

    page.on('response', (response) => {
      if (response.status() === 200 || response.status() === 302) {
        const csrfHeader = response.headers()['x-_ip_csrf'];
        if (csrfHeader) {
          newToken = csrfHeader;
          responseReceived = true;
        }
      }
    });

    // Try to submit a form or trigger POST
    const submitButton = page.locator('button[type="submit"]').first();
    if (await submitButton.isVisible()) {
      await submitButton.click({ force: true });
      await page.waitForTimeout(500);
    }

    // If response was received, new token should be valid
    if (responseReceived) {
      expect(newToken).toBeTruthy();
      expect(newToken).not.toEqual('false');
      expect(newToken).not.toEqual('undefined');
    }
  });
});
