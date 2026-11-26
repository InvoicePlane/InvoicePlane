/**
 * Test-data builders.
 *
 * The E2E database is seeded with only the baseline (one admin, settings, a
 * default invoice group) — every spec creates the rows it needs. There is no
 * per-test DB rollback here (unlike the PHPUnit suite), so every fixture name
 * is made unique with `uniq()` and specs assert on their own unique strings
 * rather than on absolute counts.
 */

import { expect } from '../test.js';
import { idFromUrl } from './app.js';
import { dbExec, dbInsert, dbQuery } from './db.js';

let counter = 0;

/** A short value that is unique within a run (and readable in the UI / a failure screenshot). */
export function uniq(prefix = 'E2E') {
  counter += 1;

  return `${prefix}-${Date.now().toString(36)}-${counter}`;
}

/**
 * Create a client through the real form and return `{ id, name, surname }`.
 * `client_name` is the only required field; pass overrides for anything else.
 */
export async function createClient(page, overrides = {}) {
  const name = overrides.client_name ?? uniq('Client');
  const surname = overrides.client_surname ?? '';

  await page.goto('/clients/form');
  await page.fill('#client_name', name);
  if (surname) await page.fill('#client_surname', surname);

  for (const [key, value] of Object.entries(overrides)) {
    if (key === 'client_name' || key === 'client_surname') continue;
    const field = page.locator(`[name="${key}"]`).first();
    const type = await field.evaluate((el) => el.getAttribute('type'));
    if (type === 'checkbox' || type === 'radio') {
      await field.setChecked(value === true || value === 1 || value === '1');
    } else {
      await field.fill(String(value));
    }
  }

  await Promise.all([page.waitForURL(/\/clients\/view\/\d+/), page.click('#btn-submit')]);

  const id = idFromUrl(page.url());
  expect(id, 'a created client should redirect to /clients/view/{id}').not.toBeNull();

  return { id, name, surname };
}

/**
 * Create a non-admin (user_type 2) user via a direct authenticated POST to the
 * user form and return `{ id, email, name }`. Driving the whole multi-field
 * user form by hand in every spec that just needs "some other user" is noise;
 * the user form itself is covered by UsersController.spec.js.
 */
export async function createSecondaryUser(page, overrides = {}) {
  const email = overrides.user_email ?? `${uniq('user').toLowerCase()}@test.local`;
  const name = overrides.user_name ?? uniq('User');
  const password = 'password-123';

  const response = await page.request.post('/users/form', {
    form: {
      user_type: '2',
      user_name: name,
      user_email: email,
      user_password: password,
      user_passwordv: password,
      user_language: 'system',
      btn_submit: '1',
      ...overrides,
    },
    maxRedirects: 0,
  });
  expect(response.status(), 'creating the secondary user should redirect').toBe(303);

  // The save redirects to the list, not the edit form, so recover the id from
  // the row the new email now appears in.
  const listHtml = await (await page.request.get('/users')).text();
  const row = listHtml.split(/<tr[ >]/).find((chunk) => chunk.includes(email));
  const id = Number(row?.match(/users\/(?:form|view)\/(\d+)/)?.[1]);
  expect(id, `the new user (${email}) should appear in the user list`).toBeGreaterThan(0);

  return { id, email, name, password };
}

/**
 * Create a record whose form save redirects to the module's index (not an
 * edit/view page carrying an id) — families, units, products, tax rates, ….
 * Posts the form as the authenticated admin, then recovers the id from the
 * index row that now contains `marker` via its `{editSegment}/{id}` link.
 */
export async function createByForm(page, { formPath, indexPath, editSegment, marker, fields }) {
  const response = await page.request.post(formPath, {
    form: { is_update: '0', btn_submit: '1', ...fields },
    maxRedirects: 0,
  });
  expect(response.status(), `${formPath} should accept a valid create`).toBe(303);

  const html = await (await page.request.get(indexPath)).text();
  const row = html.split(/<tr[\s>]/).find((chunk) => chunk.includes(marker));
  const id = Number(row?.match(new RegExp(`${editSegment}/(\\d+)`))?.[1]);
  expect(id, `the new record (${marker}) should appear at ${indexPath}`).toBeGreaterThan(0);

  return id;
}

/** Raw ip_users row (like the PHPUnit seedUser helper) — for arrange-only users. */
export function seedUser(overrides = {}) {
  const name = overrides.user_name ?? uniq('User');
  const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  const id = dbInsert('ip_users', {
    user_type: 2,
    user_name: name,
    user_email: overrides.user_email ?? `${uniq('seed').toLowerCase()}@test.local`,
    user_password: 'x',
    user_psalt: 'e2e',
    user_language: 'system',
    user_active: 1,
    user_date_created: now,
    user_date_modified: now,
    ...overrides,
  });

  return { id, name };
}

/** Raw ip_email_templates row (like the PHPUnit seedTemplate helper). */
export function seedEmailTemplate(overrides = {}) {
  const title = overrides.email_template_title ?? uniq('Template');
  const id = dbInsert('ip_email_templates', {
    email_template_title: title,
    email_template_type: 'invoice',
    email_template_body: 'Hello {{{client_name}}}',
    ...overrides,
  });

  return { id, title };
}

/** Raw ip_custom_fields row (like the PHPUnit seedField helper). */
export function seedCustomField(overrides = {}) {
  const label = overrides.custom_field_label ?? uniq('Field');
  const id = dbInsert('ip_custom_fields', {
    custom_field_table: 'ip_client_custom',
    custom_field_label: label,
    custom_field_type: 'TEXT',
    ...overrides,
  });

  return { id, label };
}

export async function createTaxRate(page, overrides = {}) {
  const name = overrides.tax_rate_name ?? uniq('Tax');
  const percent = overrides.tax_rate_percent ?? '10.00';
  const id = await createByForm(page, {
    formPath: '/tax_rates/form',
    indexPath: '/tax_rates',
    editSegment: 'tax_rates/form',
    marker: name,
    fields: { tax_rate_name: name, tax_rate_percent: percent },
  });

  return { id, name, percent };
}

export async function createFamily(page, name = uniq('Family')) {
  const id = await createByForm(page, {
    formPath: '/families/form',
    indexPath: '/families',
    editSegment: 'families/form',
    marker: name,
    fields: { family_name: name },
  });

  return { id, name };
}

export async function createUnit(page, overrides = {}) {
  const name = overrides.unit_name ?? uniq('Unit');
  const plural = overrides.unit_name_plrl ?? `${name}s`;
  const id = await createByForm(page, {
    formPath: '/units/form',
    indexPath: '/units',
    editSegment: 'units/form',
    marker: name,
    fields: { unit_name: name, unit_name_plrl: plural },
  });

  return { id, name, plural };
}

/**
 * Create a quote through the real "new quote" AJAX endpoint (the modal's
 * $.post target) and return `{ id, number, clientId }`. Required fields
 * (Mdl_Quotes::validation_rules): client_id, quote_date_created,
 * invoice_group_id — group 1 is the baseline "Default" group.
 */
export async function createQuote(page, overrides = {}) {
  const clientId = overrides.client_id ?? (await createClient(page)).id;

  const response = await page.request.post('/quotes/ajax/create', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    form: {
      client_id: String(clientId),
      quote_date_created: new Date().toISOString().slice(0, 10),
      invoice_group_id: '1',
      user_id: '1',
      ...overrides,
    },
  });
  const json = await response.json();
  expect(json.success, `quote create failed: ${JSON.stringify(json)}`).toBe(1);

  const [row] = dbQuery(`SELECT quote_number FROM ip_quotes WHERE quote_id = ${Number(json.quote_id)}`);

  return { id: Number(json.quote_id), number: row?.quote_number ?? '', clientId };
}

export async function createInvoiceGroup(page, overrides = {}) {
  const name = overrides.invoice_group_name ?? uniq('Group');
  const id = await createByForm(page, {
    formPath: '/invoice_groups/form',
    indexPath: '/invoice_groups',
    editSegment: 'invoice_groups/form',
    marker: name,
    fields: {
      invoice_group_name: name,
      invoice_group_identifier_format: overrides.invoice_group_identifier_format ?? '{{{id}}}',
      invoice_group_next_id: overrides.invoice_group_next_id ?? '1',
      invoice_group_left_pad: overrides.invoice_group_left_pad ?? '0',
    },
  });

  return { id, name };
}

/**
 * Create an invoice through the real "new invoice" AJAX endpoint and return
 * `{ id, number, clientId }`. Required fields (Mdl_Invoices::validation_rules):
 * client_id, invoice_date_created, invoice_time_created, invoice_group_id,
 * user_id.
 */
export async function createInvoice(page, overrides = {}) {
  const clientId = overrides.client_id ?? (await createClient(page)).id;
  const now = new Date();

  const response = await page.request.post('/invoices/ajax/create', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    form: {
      client_id: String(clientId),
      invoice_date_created: now.toISOString().slice(0, 10),
      invoice_time_created: now.toTimeString().slice(0, 8),
      invoice_group_id: '1',
      user_id: '1',
      ...overrides,
    },
  });
  const json = await response.json();
  expect(json.success, `invoice create failed: ${JSON.stringify(json)}`).toBe(1);

  const [row] = dbQuery(`SELECT invoice_number FROM ip_invoices WHERE invoice_id = ${Number(json.invoice_id)}`);

  return { id: Number(json.invoice_id), number: row?.invoice_number ?? '', clientId };
}

/** Create an invoice and set its amounts row so a payment can be recorded against it. */
export async function createInvoiceWithBalance(page, balance = '100.00', overrides = {}) {
  const invoice = await createInvoice(page, overrides);
  dbExec(
    `UPDATE ip_invoice_amounts SET invoice_total = ${Number(balance)}, invoice_balance = ${Number(balance)}, invoice_paid = 0`
    + ` WHERE invoice_id = ${invoice.id}`,
  );

  return invoice;
}

/**
 * Record a payment against `invoiceId` through the real add-payment AJAX
 * endpoint and return `{ id, amount }`. The invoice needs a balance
 * (createInvoiceWithBalance).
 */
export async function createPayment(page, invoiceId, overrides = {}) {
  const amount = overrides.payment_amount ?? '25.00';
  const response = await page.request.post('/payments/ajax/add', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    form: {
      invoice_id: String(invoiceId),
      payment_date: new Date().toISOString().slice(0, 10),
      payment_amount: amount,
      ...overrides,
    },
  });
  const json = await response.json();
  expect(json.success, `payment add failed: ${JSON.stringify(json)}`).toBe(1);

  return { id: Number(json.payment_id), amount };
}

/**
 * A guest-visible invoice with a url_key and a real balance — the shape the
 * public payment / gateway flows require. Returns `{ id, key, clientId }`.
 */
export async function createPayableGuestInvoice(page, { statusId = 2, balance = '100.00' } = {}) {
  const invoice = await createInvoice(page);
  const key = uniq('pay').toLowerCase();
  dbExec(
    `UPDATE ip_invoices SET invoice_url_key = '${key}', invoice_status_id = ${statusId}, payment_method = 0`
    + ` WHERE invoice_id = ${invoice.id}`,
  );
  dbExec(
    `UPDATE ip_invoice_amounts SET invoice_total = ${Number(balance)}, invoice_balance = ${Number(balance)},`
    + ` invoice_paid = ${Number(balance) === 0 ? 0 : 0} WHERE invoice_id = ${invoice.id}`,
  );

  return { id: invoice.id, key, clientId: invoice.clientId };
}

export async function createPaymentMethod(page, name = uniq('Method')) {
  const id = await createByForm(page, {
    formPath: '/payment_methods/form',
    indexPath: '/payment_methods',
    editSegment: 'payment_methods/form',
    marker: name,
    fields: { payment_method_name: name },
  });

  return { id, name };
}

export async function createProject(page, overrides = {}) {
  const name = overrides.project_name ?? uniq('Project');
  const id = await createByForm(page, {
    formPath: '/projects/form',
    indexPath: '/projects',
    editSegment: 'projects/(?:form|view)',
    marker: name,
    fields: { project_name: name, ...overrides },
  });

  return { id, name };
}

export async function createService(page, overrides = {}) {
  const name = overrides.service_name ?? uniq('Service');
  const id = await createByForm(page, {
    formPath: '/services/form',
    indexPath: '/services',
    editSegment: 'services/form',
    marker: name,
    fields: { service_name: name, ...overrides },
  });

  return { id, name };
}

export async function createTask(page, overrides = {}) {
  const name = overrides.task_name ?? uniq('Task');
  const id = await createByForm(page, {
    formPath: '/tasks/form',
    indexPath: '/tasks',
    editSegment: 'tasks/form',
    marker: name,
    fields: {
      task_name: name,
      task_price: overrides.task_price ?? '100.00',
      task_finish_date: overrides.task_finish_date ?? '2026-12-31',
      ...overrides,
    },
  });

  return { id, name };
}

export async function createProduct(page, overrides = {}) {
  const name = overrides.product_name ?? uniq('Product');
  const price = overrides.product_price ?? '19.99';
  const id = await createByForm(page, {
    formPath: '/products/form',
    indexPath: '/products',
    editSegment: 'products/form',
    marker: name,
    fields: { product_name: name, product_price: price, ...overrides },
  });

  return { id, name, price };
}
