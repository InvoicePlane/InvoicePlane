/**
 * Browser coverage for application/modules/tasks/controllers/Ajax.php.
 * Mirrors tests/Feature/Projects/TasksAjaxControllerTest.php — the task lookup
 * modal used on invoice item rows. `$ajax_controller = true`.
 */

import { test, expect } from '../test.js';
import { createTask, uniq } from '../support/fixtures.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

test.describe('Tasks AJAX', () => {
  test('it renders the task lookup modal with no invoice', async ({ page }) => {
    /* Arrange */
    const errors = [];
    page.on('pageerror', (e) => errors.push(e.message));

    /* Act */
    const response = await page.request.post('/tasks/ajax/modal_task_lookups', { headers: XHR });
    const body = await response.text();

    /* Assert */
    expect(response.status()).toBe(200);
    expect(body).not.toMatch(/Fatal error|Uncaught|<b>Error<\/b>/i);
    expect(errors).toEqual([]);
  });

  test('it processes a task selection', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('LookupTaskMarker') });

    /* Act */
    const response = await page.request.post('/tasks/ajax/process_task_selections', {
      headers: XHR,
      form: { 'task_ids[]': String(task.id) },
    });
    const json = await response.json();

    /* Assert */
    expect(json.map((row) => row.task_name)).toContain(task.name);
  });

  test('it returns an empty result when no task ids are selected', async ({ page }) => {
    /* Arrange */
    await createTask(page, { task_name: uniq('NotSelectedTask') });

    /* Act */
    const response = await page.request.post('/tasks/ajax/process_task_selections', { headers: XHR });

    /* Assert */
    expect(await response.json()).toEqual([]);
  });

  test('it requires an ajax request', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/tasks/ajax/process_task_selections');

    /* Assert */
    expect(response.status()).toBe(200);
    expect((await response.text()).trim()).toBe('');
  });
});
