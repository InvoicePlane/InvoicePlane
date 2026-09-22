/**
 * URL / path helpers.
 *
 * This CI3 build runs with REMOVE_INDEXPHP=false, so every link the app
 * generates is prefixed with `/index.php` and redirects land on
 * `/index.php/clients/view/3` rather than `/clients/view/3`. tests/E2E/router.php
 * accepts both schemes, so `page.goto('/clients/form')` still works, but URL
 * assertions have to tolerate the optional prefix — hence `pathMatches()`.
 */

/** Strip the optional `/index.php` front-controller prefix from a pathname. */
export function normalizePath(pathname) {
  return pathname.replace(/^\/index\.php/, '') || '/';
}

/**
 * Assert-friendly predicate: does the page's current path (prefix removed)
 * match `expected` (a string for exact match, or a RegExp)?
 */
export function pathMatches(page, expected) {
  const path = normalizePath(new URL(page.url()).pathname);

  return expected instanceof RegExp ? expected.test(path) : path === expected;
}

/** The new-record id from a `.../view/123` or `.../form/123` redirect target, or null. */
export function idFromUrl(url) {
  const match = new URL(url).pathname.match(/\/(?:view|form)\/(\d+)/);

  return match ? Number(match[1]) : null;
}
