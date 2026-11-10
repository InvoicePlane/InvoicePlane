/**
 * Browser coverage for application/modules/clients/controllers/Ajax.php.
 *
 * Mirrors tests/Feature/Clients/ClientsAjaxControllerTest.php. These endpoints
 * back the client-picker search box and the client-notes widget; every action
 * is guarded by `$ajax_controller = true`, so each request carries the
 * X-Requested-With header a real XHR would send and reuses the page's
 * authenticated admin session.
 */

import { test, expect } from '../test.js';
import { createClient, uniq } from '../support/fixtures.js';
import { getJson } from '../support/http.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

test.describe('Clients AJAX — name_query', () => {
  test('it finds active clients matching the query', async ({ page }) => {
    /* Arrange */
    const needle = await createClient(page, { client_name: uniq('Needle') });
    await createClient(page, { client_name: uniq('Haystack') });

    /* Act */
    const { status, body } = await getJson(
      page,
      `/clients/ajax/name_query?query=${encodeURIComponent(needle.name)}`,
    );

    /* Assert */
    expect(status).toBe(200);
    expect(body.map((row) => row.text)).toContain(needle.name);
  });

  test('it excludes inactive clients from name_query', async ({ page }) => {
    /* Arrange */
    const name = uniq('InactiveNeedle');
    await createClient(page, { client_name: name, client_active: '0' });

    /* Act */
    const { body } = await getJson(
      page,
      `/clients/ajax/name_query?query=${encodeURIComponent(name)}`,
    );

    /* Assert */
    expect(body).toEqual([]);
  });

  test('it returns an empty result for name_query with no query', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: uniq('AnyClient') });

    /* Act */
    const { status, body } = await getJson(page, '/clients/ajax/name_query');

    /* Assert */
    expect(status).toBe(200);
    expect(body).toEqual([]);
  });

  test('it treats name_query input as a literal search term', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: uniq('RealClient') });

    /* Act */
    const { body } = await getJson(
      page,
      `/clients/ajax/name_query?query=${encodeURIComponent("x' OR '1'='1")}`,
    );

    /* Assert */
    expect(body).toEqual([]);
  });
});

test.describe('Clients AJAX — get_latest', () => {
  test('it returns up to five latest active clients', async ({ page }) => {
    /* Arrange */
    for (let i = 0; i < 7; i++) {
      await createClient(page, { client_name: uniq(`Latest${i}`) });
    }

    /* Act */
    const { status, body } = await getJson(page, '/clients/ajax/get_latest');

    /* Assert */
    expect(status).toBe(200);
    expect(body.length).toBe(5);
  });

  test('it escapes client names returned by get_latest', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: '<script>alert(1)</script>' });

    /* Act */
    const response = await page.request.get('/clients/ajax/get_latest', { headers: XHR });
    const raw = await response.text();

    /* Assert: two layers must hold — the form's input sanitiser never lets a
     * live <script> reach storage, and get_latest htmlsc()-escapes on the way
     * out — so the payload can never round-trip as executable markup. */
    expect(raw.toLowerCase()).not.toContain('<script');
    const text = JSON.parse(raw).map((row) => row.text)[0];
    expect(text).not.toMatch(/<[a-z]/i);
  });
});

test.describe('Clients AJAX — permissive-search preference', () => {
  // The saved preference surfaces as the value of #input_permissive_search_clients,
  // the hidden field the client picker reads (rendered on e.g. the project form).
  test('it saves a valid permissive search preference', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(
      '/clients/ajax/save_preference_permissive_search_clients?permissive_search_clients=1',
      { headers: XHR },
    );

    /* Assert */
    expect(response.status()).toBe(200);
    await page.goto('/projects/form');
    await expect(page.locator('#input_permissive_search_clients')).toHaveValue('1');
  });

  test('it rejects an invalid permissive search preference value', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(
      '/clients/ajax/save_preference_permissive_search_clients?permissive_search_clients=2',
      { headers: XHR },
    );

    /* Assert */
    expect(response.status()).toBe(200);
    await page.goto('/projects/form');
    await expect(page.locator('#input_permissive_search_clients')).toHaveValue('');
  });
});

test.describe('Clients AJAX — client notes', () => {
  const saveNote = (page, clientId, note) =>
    page.request.post('/clients/ajax/save_client_note', {
      headers: XHR,
      form: { client_id: String(clientId), client_note: note },
    });
  const loadNotes = (page, clientId) =>
    page.request.post('/clients/ajax/load_client_notes', {
      headers: XHR,
      form: { client_id: String(clientId) },
    });

  test('it saves a client note with all required fields', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);

    /* Act */
    const json = await (await saveNote(page, client.id, 'A note about this client')).json();

    /* Assert */
    expect(json.success).toBe(1);
    expect(await (await loadNotes(page, client.id)).text()).toContain('A note about this client');
  });

  test('it fails to save a client note without client_id', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/clients/ajax/save_client_note', {
      headers: XHR,
      form: { client_id: '', client_note: 'Orphan note' },
    });

    /* Assert */
    expect((await response.json()).success).toBe(0);
  });

  test('it fails to save a client note without client_note text', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);

    /* Act */
    const response = await saveNote(page, client.id, '');

    /* Assert */
    expect((await response.json()).success).toBe(0);
  });

  test('it deletes an existing client note', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    await saveNote(page, client.id, 'Note to delete');
    const listBefore = await (await loadNotes(page, client.id)).text();
    // partial_notes.php renders <span data-id="{client_note_id}" class="delete_client_note …">
    const noteId = listBefore.match(/data-id="(\d+)"/)?.[1];
    expect(noteId, 'the saved note id should be discoverable in the notes partial').toBeTruthy();

    /* Act */
    const response = await page.request.post('/clients/ajax/delete_client_note', {
      headers: XHR,
      form: { client_note_id: String(noteId) },
    });

    /* Assert */
    expect((await response.json()).success).toBe(1);
    expect(await (await loadNotes(page, client.id)).text()).not.toContain('Note to delete');
  });

  test('it does not delete anything for a nonexistent note id', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    await saveNote(page, client.id, 'Untouched note');

    /* Act */
    const response = await page.request.post('/clients/ajax/delete_client_note', {
      headers: XHR,
      form: { client_note_id: '999999' },
    });

    /* Assert */
    expect((await response.json()).success).toBe(0);
    expect(await (await loadNotes(page, client.id)).text()).toContain('Untouched note');
  });

  test('it loads notes for a client', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    await saveNote(page, client.id, 'Visible note marker');

    /* Act */
    const response = await loadNotes(page, client.id);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain('Visible note marker');
  });
});

test.describe('Clients AJAX — guard', () => {
  test('it requires an ajax request', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: uniq('ShouldNotAppear') });

    /* Act: no X-Requested-With header — Base_Controller's guard is a bare exit */
    const response = await page.request.get('/clients/ajax/get_latest');

    /* Assert */
    expect(response.status()).toBe(200);
    expect((await response.text()).trim()).toBe('');
  });
});
