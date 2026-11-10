/**
 * Browser coverage for application/modules/filter/controllers/Ajax.php.
 * Mirrors tests/Feature/Core/FilterAjaxControllerTest.php — the search box on
 * every index table posts to /filter/ajax/filter_<thing> and gets back a
 * filtered table partial. `$ajax_controller = true`.
 */

import { test, expect } from '../test.js';
import {
  createClient, createFamily, createInvoice, createProduct, createProject,
  createQuote, createTask, seedCustomField, seedUser, uniq,
} from '../support/fixtures.js';
import { dbExec } from '../support/db.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };
const filter = (page, route, query) =>
  page.request.post(`/filter/ajax/${route}`, { headers: XHR, form: query === undefined ? {} : { filter_query: query } });
const noPhpError = (text) => expect(text).not.toMatch(/Fatal error|A PHP Error was encountered/i);

test.describe('Filter AJAX — matches its own record', () => {
  test('it filters invoices by query', async ({ page }) => {
    /* Arrange: give each invoice a distinctive number to filter on */
    const invoice = await createInvoice(page);
    const other = await createInvoice(page, { client_id: invoice.clientId });
    const marker = uniq('FILTERMATCH');
    dbExec(`UPDATE ip_invoices SET invoice_number = '${marker}' WHERE invoice_id = ${invoice.id}`);
    dbExec(`UPDATE ip_invoices SET invoice_number = '${uniq('OTHER')}' WHERE invoice_id = ${other.id}`);

    /* Act */
    const body = await (await filter(page, 'filter_invoices', marker)).text();

    /* Assert: the matching invoice is in the filtered table, the other is not */
    expect(body).toContain(`invoices/view/${invoice.id}`);
    expect(body).not.toContain(`invoices/view/${other.id}`);
  });

  test('it filters quotes by query', async ({ page }) => {
    /* Arrange */
    const quote = await createQuote(page);

    /* Act + Assert */
    expect(await (await filter(page, 'filter_quotes', quote.number)).text()).toContain(quote.number);
  });

  test('it filters clients by query', async ({ page }) => {
    /* Arrange */
    const match = await createClient(page, { client_name: uniq('FilterClientMatch') });
    const other = await createClient(page, { client_name: uniq('OtherClient') });

    /* Act */
    const body = await (await filter(page, 'filter_clients', match.name)).text();

    /* Assert */
    expect(body).toContain(match.name);
    expect(body).not.toContain(other.name);
  });

  test('it filters custom fields by query', async ({ page }) => {
    /* Arrange */
    const field = seedCustomField({ custom_field_label: uniq('FilterFieldMatch') });

    /* Act + Assert */
    expect(await (await filter(page, 'filter_custom_fields', field.label)).text()).toContain(field.label);
  });

  test('it filters custom values by query', async ({ page }) => {
    /* Arrange */
    const field = seedCustomField({ custom_field_type: 'MULTIPLE-CHOICE' });
    const value = uniq('FilterValueMatch');
    dbExec(`INSERT INTO ip_custom_values (custom_values_field, custom_values_value) VALUES (${field.id}, '${value}')`);

    /* Act + Assert */
    noPhpError(await (await filter(page, 'filter_custom_values', value)).text());
  });

  test('it filters custom values field by query', async ({ page }) => {
    /* Arrange */
    const field = seedCustomField({ custom_field_type: 'MULTIPLE-CHOICE', custom_field_label: uniq('FilterCVField') });

    /* Act + Assert */
    noPhpError(await (await filter(page, 'filter_custom_values_field', field.label)).text());
  });

  test('it filters projects by query', async ({ page }) => {
    /* Arrange */
    const project = await createProject(page, { project_name: uniq('FilterProjectMatch') });

    /* Act + Assert */
    expect(await (await filter(page, 'filter_projects', project.name)).text()).toContain(project.name);
  });

  test('it filters tasks by query', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('FilterTaskMatch') });

    /* Act + Assert */
    expect(await (await filter(page, 'filter_tasks', task.name)).text()).toContain(task.name);
  });

  test('it filters products by query', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('FilterProductMatch') });

    /* Act + Assert */
    expect(await (await filter(page, 'filter_products', product.name)).text()).toContain(product.name);
  });

  test('it filters users by query', async ({ page }) => {
    /* Arrange */
    const user = seedUser({ user_name: uniq('FilterUserMatch') });

    /* Act + Assert */
    expect(await (await filter(page, 'filter_users', user.name)).text()).toContain(user.name);
  });

  test('it filters families by query', async ({ page }) => {
    /* Arrange */
    const family = await createFamily(page, uniq('FilterFamilyMatch'));

    /* Act + Assert */
    expect(await (await filter(page, 'filter_families', family.name)).text()).toContain(family.name);
  });

  test('it filters payments by query', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const note = uniq('FilterPaymentMatch');
    dbExec(
      `INSERT INTO ip_payments (invoice_id, payment_method_id, payment_date, payment_amount, payment_note)`
      + ` VALUES (${invoice.id}, 0, CURDATE(), 10.00, '${note}')`,
    );

    /* Act + Assert */
    expect(await (await filter(page, 'filter_payments', note)).text()).toContain(note);
  });
});

test.describe('Filter AJAX — renders without error for empty / exotic tables', () => {
  for (const route of ['filter_invoices_recuring', 'filter_online_logs', 'filter_archives']) {
    test(`it filters ${route.replace('filter_', '')} by query`, async ({ page }) => {
      /* Arrange + Act + Assert */
      noPhpError(await (await filter(page, route, 'anything')).text());
    });
  }

  test('it does not expose php errors when filtering invoices without a query', async ({ page }) => {
    /* Arrange + Act + Assert */
    noPhpError(await (await filter(page, 'filter_invoices')).text());
  });

  test('it treats filter invoices query as a literal search term', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act + Assert: a SQLi-shaped query is a literal string, no crash */
    const body = await (await filter(page, 'filter_invoices', "' OR '1'='1")).text();
    noPhpError(body);
    expect(body).not.toContain(`invoices/view/${invoice.id}`);
  });
});

test.describe('Filter AJAX — guard', () => {
  test('it requires an ajax request', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/filter/ajax/filter_invoices', { form: { filter_query: 'x' } });

    /* Assert */
    expect((await response.text()).trim()).toBe('');
  });
});
