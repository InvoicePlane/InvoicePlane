/**
 * Raw HTTP helpers for the paths a browser can't (or shouldn't) drive by
 * clicking: POST-only destroy routes, CSRF negative cases, AJAX endpoints.
 *
 * `page.request` reuses the browsing context's cookie jar, so these calls are
 * authenticated with the same admin session the page has.
 */

/** Read the CSRF hash the app embedded in the form at `formPath` (empty when CSRF is off). */
export async function readCsrfToken(page, formPath) {
  await page.goto(formPath);

  return page.locator('input[name="_ip_csrf"]').first().inputValue();
}

/**
 * POST a form-encoded body to `path`. Does not follow redirects, so the caller
 * can assert on the raw status (303 = accepted, 200 = re-rendered/refused).
 */
export function postForm(page, path, fields = {}) {
  return page.request.post(path, { form: fields, maxRedirects: 0 });
}

/** GET `path` as an XHR would (X-Requested-With), returning the parsed JSON body. */
export async function getJson(page, path) {
  const response = await page.request.get(path, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  });

  return { status: response.status(), body: await response.json() };
}
