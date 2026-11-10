/**
 * Browser coverage for application/modules/guest/controllers/Get.php.
 * Mirrors tests/Feature/Invoices/GuestGetControllerTest.php — the guest file
 * listing / download endpoints, including the path-traversal guard.
 *
 * Upload files are written straight into uploads/customer_files/ on disk (the
 * container serves the same bind-mounted tree) and cleaned up afterwards.
 */

import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { test, expect } from '../test.js';
import { createClient, createInvoice } from '../support/fixtures.js';
import { dbExec, dbInsert } from '../support/db.js';

const UPLOAD_DIR = path.resolve('uploads/customer_files');
const MARK = '_e2eguest_';

const hexKey = () => crypto.randomBytes(16).toString('hex');

test.beforeAll(() => fs.mkdirSync(UPLOAD_DIR, { recursive: true }));
test.afterEach(() => {
  for (const file of fs.readdirSync(UPLOAD_DIR)) {
    if (file.includes(MARK)) fs.rmSync(path.join(UPLOAD_DIR, file), { force: true });
  }
});

/** A guest-visible (status 2) invoice with a 32-hex url_key. */
async function visibleInvoiceKey(page, statusId = 2) {
  const invoice = await createInvoice(page);
  const key = hexKey();
  dbExec(`UPDATE ip_invoices SET invoice_url_key = '${key}', invoice_status_id = ${statusId} WHERE invoice_id = ${invoice.id}`);

  return { key, clientId: invoice.clientId };
}

function writeUpload(name, content) {
  fs.writeFileSync(path.join(UPLOAD_DIR, name), content);
}

test.describe('Guest get — show_files', () => {
  test('it returns an empty response for show_files with no key', async ({ page }) => {
    /* Arrange + Act + Assert */
    const response = await page.request.get('/guest/get/show_files');
    expect((await response.text()).trim()).toBe('{}');
  });

  test('it returns an empty response for show_files on a draft invoice', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 1);

    /* Act + Assert */
    const response = await page.request.get(`/guest/get/show_files/${key}`);
    expect((await response.text()).trim()).toBe('{}');
  });

  test('it returns an empty response for show_files with no uploads', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 2);

    /* Act + Assert */
    const response = await page.request.get(`/guest/get/show_files/${key}`);
    expect((await response.text()).trim()).toBe('{}');
  });

  test('it lists uploaded files for a guest visible invoice', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    const invoice = await createInvoice(page, { client_id: client.id });
    const key = hexKey();
    dbExec(`UPDATE ip_invoices SET invoice_url_key = '${key}', invoice_status_id = 2 WHERE invoice_id = ${invoice.id}`);
    dbInsert('ip_uploads', {
      client_id: client.id,
      url_key: key,
      file_name_original: 'attachment.pdf',
      file_name_new: `${key}${MARK}attachment.pdf`,
      uploaded_date: new Date().toISOString().slice(0, 10),
    });
    writeUpload(`${key}${MARK}attachment.pdf`, 'attachment-bytes');

    /* Act */
    const response = await page.request.get(`/guest/get/show_files/${key}`);

    /* Assert */
    expect(await response.text()).toContain('attachment.pdf');
  });
});

test.describe('Guest get — get_file', () => {
  test('it returns 400 for get_file with no filename', async ({ page }) => {
    expect((await page.request.get('/guest/get/get_file')).status()).toBe(400);
  });

  test('it returns 404 for get_file with a malformed url key prefix', async ({ page }) => {
    expect((await page.request.get('/guest/get/get_file/not-a-valid-key_file.pdf')).status()).toBe(404);
  });

  test('it returns 404 for get_file whose url key is not guest visible', async ({ page }) => {
    expect((await page.request.get(`/guest/get/get_file/${hexKey()}_file.pdf`)).status()).toBe(404);
  });

  test('it returns 404 for get_file whose url key belongs to a draft invoice', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 1);

    /* Act + Assert */
    expect((await page.request.get(`/guest/get/get_file/${key}_file.pdf`)).status()).toBe(404);
  });

  test('it returns 404 for a visible invoice whose file does not exist on disk', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 2);

    /* Act + Assert */
    expect((await page.request.get(`/guest/get/get_file/${key}_missing.pdf`)).status()).toBe(404);
  });

  test('it downloads an existing file for a guest visible invoice', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 2);
    const filename = `${key}${MARK}download.pdf`;
    writeUpload(filename, 'pdf-bytes');

    /* Act */
    const response = await page.request.get(`/guest/get/get_file/${filename}`);

    /* Assert */
    expect(await response.text()).toBe('pdf-bytes');
  });

  test('it rejects a path traversal attempt in the filename', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 2);

    /* Act */
    const response = await page.request.get(
      `/guest/get/get_file/${encodeURIComponent(`${key}_../../../../etc/passwd`)}`,
    );

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it serves attachment route the same as get_file', async ({ page }) => {
    /* Arrange */
    const { key } = await visibleInvoiceKey(page, 2);
    const filename = `${key}${MARK}attach2.pdf`;
    writeUpload(filename, 'attachment-bytes');

    /* Act */
    const response = await page.request.get(`/guest/get/attachment/${filename}`);

    /* Assert */
    expect(await response.text()).toBe('attachment-bytes');
  });
});
