/**
 * Browser coverage for application/modules/guest/controllers/Payments.php.
 * Mirrors tests/Feature/Payments/GuestPaymentsControllerTest.php — the guest
 * "my payments" list, scoped to the guest user's assigned client(s).
 */

import { test, expect } from '../test.js';
import { createClient, createInvoiceWithBalance, createPayment, createSecondaryUser, uniq } from '../support/fixtures.js';
import { dbInsert } from '../support/db.js';
import { loginAs } from '../support/auth.js';
import { E2E_BASE_URL } from '../config.js';

/** A guest (type-2) user assigned to `clientId`, in its own session. */
async function guestFor(page, browser, clientId) {
  const user = await createSecondaryUser(page);
  dbInsert('ip_user_clients', { user_id: user.id, client_id: clientId });

  return loginAs(browser, user.email, user.password);
}

test.describe('Guest payments — access control', () => {
  test('it redirects an unauthenticated request to login', async ({ browser }) => {
    /* Arrange */
    const anon = await browser.newContext({ baseURL: E2E_BASE_URL });

    /* Act */
    const response = await anon.request.get('/guest/payments', { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
    await anon.close();
  });

  test('it denies an admin session guest type access', async ({ page }) => {
    /* Arrange + Act: the admin (type 1) hits a type-2 controller */
    const response = await page.request.get('/guest/payments', { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it returns 403 for a guest user with no assigned clients', async ({ page, browser }) => {
    /* Arrange: a real guest user, never linked to any client */
    const user = await createSecondaryUser(page);
    const { context, page: guest } = await loginAs(browser, user.email, user.password);

    /* Act */
    const response = await guest.request.get('/guest/payments', { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(403);
    await context.close();
  });
});

test.describe('Guest payments — scoping', () => {
  test('it lists only payments for the guests own client', async ({ page, browser }) => {
    /* Arrange */
    const own = await createClient(page, { client_name: uniq('OwnClient') });
    const other = await createClient(page, { client_name: uniq('OtherClient') });
    const ownInvoice = await createInvoiceWithBalance(page, '100.00', { client_id: own.id });
    const otherInvoice = await createInvoiceWithBalance(page, '100.00', { client_id: other.id });
    const ownMarker = uniq('ownpay');
    const otherMarker = uniq('otherpay');
    await createPayment(page, ownInvoice.id, { payment_amount: '11.00', payment_note: ownMarker });
    await createPayment(page, otherInvoice.id, { payment_amount: '22.00', payment_note: otherMarker });
    const { context, page: guest } = await guestFor(page, browser, own.id);

    /* Act: the guest payments list renders the payment note per row */
    const body = await (await guest.request.get('/guest/payments')).text();

    /* Assert */
    expect(body).toContain(ownMarker);
    expect(body).not.toContain(otherMarker);
    await context.close();
  });

  test('it does not expose php errors', async ({ page, browser }) => {
    /* Arrange */
    const client = await createClient(page);
    const { context, page: guest } = await guestFor(page, browser, client.id);

    /* Act */
    const body = await (await guest.request.get('/guest/payments')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|A PHP Error was encountered/i);
    await context.close();
  });
});
