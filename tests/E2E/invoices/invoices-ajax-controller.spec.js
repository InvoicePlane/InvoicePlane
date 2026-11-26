/**
 * Browser coverage for application/modules/invoices/controllers/Ajax.php.
 * Mirrors tests/Feature/Invoices/InvoicesAjaxControllerTest.php — the endpoints
 * behind the invoice editor and the new-invoice / copy / credit / recurring
 * modals. `$ajax_controller = true`.
 */

import { test, expect } from '../test.js';
import { createClient, createInvoice, uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

function createPayload(clientId) {
  const now = new Date();

  return {
    client_id: String(clientId),
    invoice_date_created: now.toISOString().slice(0, 10),
    invoice_time_created: now.toTimeString().slice(0, 8),
    invoice_group_id: '1',
    user_id: '1',
  };
}

function savePayload(invoiceId) {
  const now = new Date();
  const due = new Date(now.getTime() + 30 * 864e5);

  return {
    invoice_id: String(invoiceId),
    invoice_date_created: now.toISOString().slice(0, 10),
    invoice_date_due: due.toISOString().slice(0, 10),
    invoice_time_created: now.toTimeString().slice(0, 8),
    invoice_status_id: '1',
    invoice_discount_percent: '0',
    invoice_discount_amount: '0',
    items: '[]',
  };
}

const post = (page, path, form) => page.request.post(path, { headers: XHR, form });
const json = (response) => response.json();

function seedItem(invoiceId, name) {
  return dbInsert('ip_invoice_items', {
    invoice_id: invoiceId,
    item_tax_rate_id: 0,
    item_date_added: new Date().toISOString().slice(0, 10),
    item_name: name,
    item_description: '',
    item_quantity: '1.00',
    item_price: '10.00',
    item_order: 0,
  });
}

test.describe('Invoices AJAX — create', () => {
  test('it creates an invoice with all required fields', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/create', createPayload(client.id)));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT client_id FROM ip_invoices WHERE invoice_id = ${Number(body.invoice_id)}`))
      .toEqual([{ client_id: client.id }]);
  });

  for (const field of ['client_id', 'invoice_date_created', 'invoice_group_id', 'invoice_time_created', 'user_id']) {
    test(`it fails to create an invoice without ${field}`, async ({ page }) => {
      /* Arrange */
      const client = await createClient(page);
      const payload = createPayload(client.id);
      delete payload[field];

      /* Act */
      const body = await json(await post(page, '/invoices/ajax/create', payload));

      /* Assert */
      expect(body.success).toBe(0);
      expect(dbQuery('SELECT invoice_id FROM ip_invoices')).toEqual([]);
    });
  }
});

test.describe('Invoices AJAX — save', () => {
  test('it saves an invoice with all required fields', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/save', { ...savePayload(invoice.id), invoice_terms: 'Saved terms' }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT invoice_terms FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ invoice_terms: 'Saved terms' }]);
  });

  for (const field of ['invoice_date_due', 'invoice_date_created']) {
    test(`it fails to save an invoice without ${field}`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);
      const payload = savePayload(invoice.id);
      delete payload[field];

      /* Act */
      const body = await json(await post(page, '/invoices/ajax/save', payload));

      /* Assert */
      expect(body.success).toBe(0);
    });
  }

  test('it rejects an invoice number with unsafe characters', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const before = invoice.number;

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/save', {
      ...savePayload(invoice.id),
      invoice_number: 'INV<script>alert(1)</script>',
    }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery(`SELECT invoice_number FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ invoice_number: before }]);
  });

  test('it saves an invoice on the first attempt', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/save', { ...savePayload(invoice.id), invoice_terms: 'First attempt terms' }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT invoice_terms FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ invoice_terms: 'First attempt terms' }]);
  });

  test('it persists a save that immediately follows a create', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);

    /* Act */
    const created = await json(await post(page, '/invoices/ajax/create', createPayload(client.id)));
    const invoiceId = Number(created.invoice_id);
    const saved = await json(await post(page, '/invoices/ajax/save', {
      ...savePayload(invoiceId),
      invoice_terms: 'Terms set on the follow-up save',
    }));

    /* Assert */
    expect(saved.success).toBe(1);
    expect(dbQuery(`SELECT invoice_terms FROM ip_invoices WHERE invoice_id = ${invoiceId}`))
      .toEqual([{ invoice_terms: 'Terms set on the follow-up save' }]);
  });
});

test.describe('Invoices AJAX — change owner', () => {
  test('it changes the invoices user', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const newUserId = dbInsert('ip_users', {
      user_name: 'New Owner',
      user_email: `owner-${uniq('u').toLowerCase()}@test.local`,
      user_password: 'x',
      user_psalt: 'e2e',
      user_type: 1,
      user_active: 1,
      user_date_created: new Date().toISOString().slice(0, 19).replace('T', ' '),
      user_date_modified: new Date().toISOString().slice(0, 19).replace('T', ' '),
    });

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/change_user', { user_id: String(newUserId), invoice_id: String(invoice.id) }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT user_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`)).toEqual([{ user_id: newUserId }]);
  });

  test('it fails to change the invoices user for an unknown user id', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/change_user', { user_id: '999999', invoice_id: String(invoice.id) }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery(`SELECT user_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`)).toEqual([{ user_id: 1 }]);
  });

  test('it changes the invoices client', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const newClient = await createClient(page, { client_name: uniq('NewOwnerClient') });

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/change_client', { client_id: String(newClient.id), invoice_id: String(invoice.id) }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT client_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`)).toEqual([{ client_id: newClient.id }]);
  });

  test('it fails to change the invoices client for an unknown client id', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/change_client', { client_id: '999999', invoice_id: String(invoice.id) }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery(`SELECT client_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ client_id: invoice.clientId }]);
  });
});

test.describe('Invoices AJAX — items', () => {
  test('it deletes an existing invoice item', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const itemId = seedItem(invoice.id, 'Deletable');

    /* Act */
    const body = await json(await post(page, `/invoices/ajax/delete_item/${invoice.id}`, { item_id: String(itemId) }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT item_id FROM ip_invoice_items WHERE item_id = ${itemId}`)).toEqual([]);
  });

  test('it does not delete anything for a nonexistent item id', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const itemId = seedItem(invoice.id, 'Untouched');

    /* Act */
    const body = await json(await post(page, `/invoices/ajax/delete_item/${invoice.id}`, { item_id: '999999' }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery(`SELECT item_id FROM ip_invoice_items WHERE item_id = ${itemId}`)).toHaveLength(1);
  });

  test('it gets an item', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const itemId = seedItem(invoice.id, 'Get Me');

    /* Act */
    const response = await post(page, '/invoices/ajax/get_item', { item_id: String(itemId) });

    /* Assert */
    expect(await response.text()).toContain('Get Me');
  });
});

test.describe('Invoices AJAX — tax rate', () => {
  test('it fails to save an invoice tax rate without invoice_id', async ({ page }) => {
    /* Arrange + Act */
    const body = await json(await post(page, '/invoices/ajax/save_invoice_tax_rate', { tax_rate_id: '1', include_item_tax: '0' }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery('SELECT invoice_tax_rate_id FROM ip_invoice_tax_rates')).toEqual([]);
  });
});

test.describe('Invoices AJAX — recurring', () => {
  test('it creates a recurring invoice with all required fields', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/create_recurring', {
      invoice_id: String(invoice.id),
      recur_start_date: new Date().toISOString().slice(0, 10),
      recur_frequency: '1D',
    }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT invoice_recurring_id FROM ip_invoices_recurring WHERE invoice_id = ${invoice.id}`)).toHaveLength(1);
  });

  test('it fails to create a recurring invoice without recur_start_date', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/create_recurring', {
      invoice_id: String(invoice.id),
      recur_frequency: '1D',
    }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery('SELECT invoice_recurring_id FROM ip_invoices_recurring')).toEqual([]);
  });

  test('it gets a recur start date', async ({ page }) => {
    /* Arrange */
    const today = new Date().toISOString().slice(0, 10);

    /* Act */
    const response = await post(page, '/invoices/ajax/get_recur_start_date', {
      invoice_date: today,
      recur_frequency: '1D',
    });
    const bodyText = (await response.text()).trim();

    /* Assert */
    expect(bodyText).toMatch(/^\d{4}-\d{2}-\d{2}$|^\d{2}\/\d{2}\/\d{4}$/);
    expect(bodyText).not.toBe(today);
  });

  test('it requires an ajax request', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/invoices/ajax/get_recur_start_date');

    /* Assert */
    expect((await response.text()).trim()).toBe('');
  });
});

test.describe('Invoices AJAX — copy & credit', () => {
  test('it copies an invoice', async ({ page }) => {
    /* Arrange */
    const source = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/copy_invoice', {
      ...createPayload(source.clientId),
      invoice_id: String(source.id),
    }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(Number(body.invoice_id)).not.toBe(source.id);
  });

  test('it fails to copy an invoice without client_id', async ({ page }) => {
    /* Arrange */
    const source = await createInvoice(page);
    const payload = createPayload(source.clientId);
    delete payload.client_id;

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/copy_invoice', { ...payload, invoice_id: String(source.id) }));

    /* Assert */
    expect(body.success).toBe(0);
    expect(dbQuery('SELECT invoice_id FROM ip_invoices')).toHaveLength(1);
  });

  test('it creates a credit invoice', async ({ page }) => {
    /* Arrange */
    const source = await createInvoice(page);

    /* Act */
    const body = await json(await post(page, '/invoices/ajax/create_credit', {
      ...createPayload(source.clientId),
      invoice_id: String(source.id),
    }));

    /* Assert */
    expect(body.success).toBe(1);
    expect(dbQuery(`SELECT creditinvoice_parent_id FROM ip_invoices WHERE invoice_id = ${Number(body.invoice_id)}`))
      .toEqual([{ creditinvoice_parent_id: source.id }]);
    expect(dbQuery(`SELECT is_read_only FROM ip_invoices WHERE invoice_id = ${source.id}`))
      .toEqual([{ is_read_only: 1 }]);
  });
});

test.describe('Invoices AJAX — modals', () => {
  test('it renders the create invoice modal', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page, { client_name: uniq('ModalCreateClient') });

    /* Act */
    const body = await (await post(page, '/invoices/ajax/modal_create_invoice', { client_id: String(client.id) })).text();

    /* Assert */
    expect(body).toContain('name="invoice_group_id"');
    expect(body).toContain('id="create_invoice_client_id"');
  });

  test('it renders the create recurring modal', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await (await post(page, '/invoices/ajax/modal_create_recurring', { invoice_id: String(invoice.id) })).text();

    /* Assert */
    expect(body).toContain('name="recur_frequency"');
    expect(body).toContain('name="recur_start_date"');
  });

  test('it renders the create credit modal', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const body = await (await post(page, '/invoices/ajax/modal_create_credit', { invoice_id: String(invoice.id) })).text();

    /* Assert */
    expect(body).toContain('name="parent_id"');
    expect(body).toContain(`value="${invoice.id}"`);
  });
});
