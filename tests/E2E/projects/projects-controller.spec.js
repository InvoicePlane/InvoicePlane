/**
 * Browser coverage for application/modules/projects/controllers/Projects.php.
 * Mirrors tests/Feature/Projects/ProjectsControllerTest.php.
 * Required field (Mdl_Projects::validation_rules): project_name.
 */

import { test, expect } from '../test.js';
import { createProject, createTask, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired } from '../support/forms.js';

test.describe('Projects — list', () => {
  test('it lists every project', async ({ page }) => {
    /* Arrange */
    const a = await createProject(page, { project_name: uniq('ProjectAlpha') });
    const b = await createProject(page, { project_name: uniq('ProjectBeta') });

    /* Act */
    await page.goto('/projects');

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('Projects — create', () => {
  test('it creates a project', async ({ page }) => {
    /* Arrange */
    const name = uniq('NewProject');

    /* Act */
    await page.goto('/projects/form');
    await page.fill('#project_name', name);
    await Promise.all([page.waitForURL(/\/projects(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name })).toBeVisible();
  });

  test('it fails to create without project_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/projects/form');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#project_name');
  });
});

test.describe('Projects — update', () => {
  test('it renders the edit form for the requested project only', async ({ page }) => {
    /* Arrange */
    const target = await createProject(page, { project_name: uniq('EditableProject') });
    const other = await createProject(page, { project_name: uniq('OtherProject') });

    /* Act */
    await page.goto(`/projects/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#project_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a project', async ({ page }) => {
    /* Arrange */
    const project = await createProject(page, { project_name: uniq('OriginalProject') });
    const renamed = uniq('RenamedProject');

    /* Act */
    await page.goto(`/projects/form/${project.id}`);
    await page.fill('#project_name', renamed);
    await Promise.all([page.waitForURL(/\/projects(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name: renamed })).toBeVisible();
    await expect(page.getByRole('link', { name: project.name, exact: true })).toHaveCount(0);
  });

  test('it fails to update without project_name', async ({ page }) => {
    /* Arrange */
    const project = await createProject(page, { project_name: uniq('KeepThisProject') });

    /* Act + Assert */
    await page.goto(`/projects/form/${project.id}`);
    await page.fill('#project_name', '');
    await expectBlockedByRequired(page, '#project_name');
  });
});

test.describe('Projects — delete', () => {
  test('it deletes a project', async ({ page }) => {
    /* Arrange */
    const doomed = await createProject(page, { project_name: uniq('DeletableProject') });
    const kept = await createProject(page, { project_name: uniq('KeptProject') });

    /* Act */
    await page.goto('/projects');
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    await page.goto('/projects');
    await expect(page.getByRole('link', { name: doomed.name })).toHaveCount(0);
    await expect(page.getByRole('link', { name: kept.name })).toBeVisible();
  });

  test('it orphans rather than deletes the tasks of a deleted project', async ({ page }) => {
    /* Arrange */
    const project = await createProject(page, { project_name: uniq('DoomedProject') });
    const task = await createTask(page, { task_name: uniq('OrphanTask') });
    await page.request.post(`/tasks/form/${task.id}`, {
      form: {
        task_name: task.name,
        task_price: '100.00',
        task_finish_date: '2026-12-31',
        task_status: '1',
        project_id: String(project.id),
        btn_submit: '1',
      },
      maxRedirects: 0,
    });

    /* Act */
    await page.goto('/projects');
    const row = page.locator('tr', { has: page.getByRole('link', { name: project.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    const [taskRow] = dbQuery(`SELECT project_id FROM ip_tasks WHERE task_id = ${task.id}`);
    expect(taskRow, 'the task itself survives the project deletion').toBeTruthy();
    expect(Number(taskRow.project_id ?? 0)).toBe(0);
  });

  test('it still deletes a project when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a project when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Projects — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no project', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/projects');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Project');
  });
});
