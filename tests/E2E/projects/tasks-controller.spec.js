/**
 * Browser coverage for application/modules/tasks/controllers/Tasks.php.
 * Mirrors tests/Feature/Projects/TasksControllerTest.php.
 * Required fields (Mdl_Tasks::validation_rules): task_name, task_price,
 * task_finish_date.
 */

import { test, expect } from '../test.js';
import { createTask, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired } from '../support/forms.js';

const FINISH = '2026-12-31';

async function fillRequired(page, { name, price = '100.00', finish = FINISH } = {}) {
  if (name !== undefined) await page.fill('#task_name', name);
  await page.fill('#task_price', price);
  await page.fill('#task_finish_date', finish);
}

test.describe('Tasks — list', () => {
  test('it lists every task', async ({ page }) => {
    /* Arrange */
    const a = await createTask(page, { task_name: uniq('TaskAlpha') });
    const b = await createTask(page, { task_name: uniq('TaskBeta') });

    /* Act */
    await page.goto('/tasks');

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('Tasks — create', () => {
  test('it creates a task', async ({ page }) => {
    /* Arrange */
    const name = uniq('PayloadTask');

    /* Act */
    await page.goto('/tasks/form');
    await fillRequired(page, { name });
    await Promise.all([page.waitForURL(/\/tasks(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name })).toBeVisible();
  });

  test('it fails to create without task_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/tasks/form');
    await fillRequired(page, {});

    /* Act + Assert */
    await expectBlockedByRequired(page, '#task_name');
  });

  test('it fails to create without task_price', async ({ page }) => {
    /* Arrange */
    await page.goto('/tasks/form');
    await page.fill('#task_name', uniq('NoPriceTask'));
    await page.fill('#task_finish_date', FINISH);

    /* Act + Assert */
    await expectBlockedByRequired(page, '#task_price');
  });

  test('it fails to create without task_finish_date', async ({ page }) => {
    /* Arrange */
    await page.goto('/tasks/form');
    await page.fill('#task_name', uniq('NoDateTask'));
    await page.fill('#task_price', '100.00');
    // The datepicker seeds today's date on a fresh form, so clear it first.
    await page.fill('#task_finish_date', '');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#task_finish_date');
  });
});

test.describe('Tasks — update', () => {
  test('it renders the edit form for the requested task only', async ({ page }) => {
    /* Arrange */
    const target = await createTask(page, { task_name: uniq('EditableTask') });
    const other = await createTask(page, { task_name: uniq('OtherTask') });

    /* Act */
    await page.goto(`/tasks/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#task_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a task', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('OriginalTask') });
    const renamed = uniq('RenamedTask');

    /* Act */
    await page.goto(`/tasks/form/${task.id}`);
    await fillRequired(page, { name: renamed });
    await Promise.all([page.waitForURL(/\/tasks(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name: renamed })).toBeVisible();
    await expect(page.getByRole('link', { name: task.name, exact: true })).toHaveCount(0);
  });

  test('it fails to update without task_name', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('KeepNameTask') });

    /* Act + Assert */
    await page.goto(`/tasks/form/${task.id}`);
    await page.fill('#task_name', '');
    await expectBlockedByRequired(page, '#task_name');
  });

  test('it fails to update without task_price', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('KeepPriceTask') });

    /* Act + Assert */
    await page.goto(`/tasks/form/${task.id}`);
    await page.fill('#task_price', '');
    await expectBlockedByRequired(page, '#task_price');
  });

  test('it fails to update without task_finish_date', async ({ page }) => {
    /* Arrange */
    const task = await createTask(page, { task_name: uniq('KeepDateTask') });

    /* Act + Assert */
    await page.goto(`/tasks/form/${task.id}`);
    await page.fill('#task_finish_date', '');
    await expectBlockedByRequired(page, '#task_finish_date');
  });
});

test.describe('Tasks — delete', () => {
  test('it deletes a task', async ({ page }) => {
    /* Arrange */
    const doomed = await createTask(page, { task_name: uniq('DeletableTask') });
    const kept = await createTask(page, { task_name: uniq('KeptTask') });

    /* Act */
    await page.goto('/tasks');
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT task_id FROM ip_tasks WHERE task_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT task_id FROM ip_tasks WHERE task_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes a task when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a task when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Tasks — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no task', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/tasks');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Task');
  });
});
