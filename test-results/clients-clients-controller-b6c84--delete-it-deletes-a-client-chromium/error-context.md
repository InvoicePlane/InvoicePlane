# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: clients/clients-controller.spec.js >> Clients — delete >> it deletes a client
- Location: tests/E2E/clients/clients-controller.spec.js:121:7

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('.alert-success[role="alert"]')
Expected: visible
Timeout: 5000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('.alert-success[role="alert"]') with timeout 5000ms
  - waiting for locator('.alert-success[role="alert"]')

```

```yaml
- navigation:
  - list:
    - listitem:
      - link "Dashboard":
        - /url: http://172.19.0.5:8000/index.php/dashboard
    - listitem:
      - link " Clients":
        - /url: "#"
    - listitem:
      - link " Quotes":
        - /url: "#"
    - listitem:
      - link " Invoices":
        - /url: "#"
    - listitem:
      - link " Payments":
        - /url: "#"
    - listitem:
      - link " Products":
        - /url: "#"
    - listitem:
      - link " Reports":
        - /url: "#"
  - search:
    - textbox "Filter Clients"
  - list:
    - listitem:
      - link "":
        - /url: https://wiki.invoiceplane.com/
    - listitem:
      - link "":
        - /url: "#"
    - listitem:
      - link "":
        - /url: http://172.19.0.5:8000/index.php/users/form/1
    - listitem:
      - button ""
- list:
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/clients/index
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/quotes/index
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/invoices/index
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/payments/index
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/products/index
  - listitem:
    - link "":
      - /url: http://172.19.0.5:8000/index.php/settings
- heading "Clients" [level=1]
- link " New":
  - /url: http://172.19.0.5:8000/index.php/clients/form
- link "":
  - /url: "#"
- link "":
  - /url: "#"
- link "":
  - /url: "#"
- link "":
  - /url: "#"
- link "Active":
  - /url: http://172.19.0.5:8000/index.php/clients/status/active
- link "Inactive":
  - /url: http://172.19.0.5:8000/index.php/clients/status/inactive
- link "All":
  - /url: http://172.19.0.5:8000/index.php/clients/status/all
- table:
  - rowgroup:
    - row "Active Client Name Email Address Phone Number Balance Options":
      - columnheader "Active"
      - columnheader "Client Name"
      - columnheader "Email Address"
      - columnheader "Phone Number"
      - columnheader "Balance"
      - columnheader "Options"
  - rowgroup:
    - row "Yes Kept-mvrx66vj-11 $0  Options":
      - cell "Yes"
      - cell "Kept-mvrx66vj-11":
        - link "Kept-mvrx66vj-11":
          - /url: http://172.19.0.5:8000/index.php/clients/view/2
      - cell
      - cell
      - cell "$0"
      - cell " Options":
        - link " Options":
          - /url: "#"
```

# Test source

```ts
  1   | /**
  2   |  * Form-interaction helpers shared by every CRUD spec.
  3   |  *
  4   |  * InvoicePlane's admin forms are plain server-rendered Bootstrap 3. The
  5   |  * conventions these helpers bake in, all verified against the live markup:
  6   |  *
  7   |  *   - the primary submit control is `<button id="btn-submit" name="btn_submit">`
  8   |  *     (application/modules/layout/views/header_buttons.php);
  9   |  *   - a saved record flashes `<div class="alert alert-success …">` and a
  10  |  *     rejected one flashes / renders `<div class="alert alert-danger …">`
  11  |  *     (application/modules/layout/views/alerts.php);
  12  |  *   - required fields carry a native HTML `required` attribute, so an omitted
  13  |  *     one is stopped by the browser before any request is sent.
  14  |  */
  15  | 
  16  | import { expect } from '../test.js';
  17  | 
  18  | /**
  19  |  * Fill a set of fields keyed by their `name` attribute. Selects use
  20  |  * selectOption, checkboxes take a boolean, everything else is `fill()`.
  21  |  * Missing fields throw — a silently skipped field is how a "valid except X"
  22  |  * submission accidentally becomes "valid except X and Y".
  23  |  */
  24  | export async function fillByName(page, values) {
  25  |   for (const [name, value] of Object.entries(values)) {
  26  |     const field = page.locator(`[name="${name}"]`).first();
  27  |     await expect(field, `form field [name="${name}"] should exist`).toHaveCount(1);
  28  | 
  29  |     const tag = await field.evaluate((el) => el.tagName.toLowerCase());
  30  |     const type = await field.evaluate((el) => el.getAttribute('type'));
  31  | 
  32  |     if (tag === 'select') {
  33  |       await field.selectOption(String(value));
  34  |     } else if (type === 'checkbox' || type === 'radio') {
  35  |       await field.setChecked(Boolean(value));
  36  |     } else {
  37  |       await field.fill(String(value));
  38  |     }
  39  |   }
  40  | }
  41  | 
  42  | /** Click the standard Save button and wait for the resulting navigation to settle. */
  43  | export async function save(page) {
  44  |   await Promise.all([
  45  |     page.waitForLoadState('load'),
  46  |     page.locator('#btn-submit').click(),
  47  |   ]);
  48  | }
  49  | 
  50  | // The dismissible flash message rendered by
  51  | // application/modules/layout/views/alerts.php. Scoped with [role="alert"] so it
  52  | // never collides with the static `.alert.alert-danger` hint boxes the forms use
  53  | // for styling (there can be a dozen of those, all hidden, on one page).
  54  | const FLASH_SUCCESS = '.alert-success[role="alert"]';
  55  | const FLASH_ERROR = '.alert-danger[role="alert"]';
  56  | 
  57  | /** Assert a green "record saved" flash is shown. */
  58  | export async function expectSavedFlash(page, text) {
  59  |   const alert = page.locator(FLASH_SUCCESS);
> 60  |   await expect(alert).toBeVisible();
      |                       ^ Error: expect(locator).toBeVisible() failed
  61  |   if (text) await expect(alert).toContainText(text);
  62  | }
  63  | 
  64  | /** Assert a red flash message (controller-set flashdata) is shown. */
  65  | export async function expectErrorFlash(page, text) {
  66  |   const alert = page.locator(FLASH_ERROR);
  67  |   await expect(alert).toBeVisible();
  68  |   if (text) await expect(alert).toContainText(text);
  69  | }
  70  | 
  71  | /**
  72  |  * Assert a CI3 form-validation summary is shown. alerts.php renders these as a
  73  |  * plain `<div class="alert alert-danger">` (no role) via validation_errors(),
  74  |  * so this is deliberately broader than expectErrorFlash().
  75  |  */
  76  | export async function expectValidationError(page, text) {
  77  |   const alert = page.locator('#content .alert.alert-danger').first();
  78  |   await expect(alert).toBeVisible();
  79  |   if (text) await expect(alert).toContainText(text);
  80  | }
  81  | 
  82  | /**
  83  |  * Assert that submitting the form is blocked by the browser's own constraint
  84  |  * validation on `selector` (an omitted required field) — i.e. no request is
  85  |  * sent and the field reports itself invalid with a user-facing message.
  86  |  * Mirrors the "native-HTML-backed required field" rejection mechanism.
  87  |  */
  88  | export async function expectBlockedByRequired(page, selector) {
  89  |   const urlBefore = page.url();
  90  |   await page.locator('#btn-submit').click();
  91  | 
  92  |   const field = page.locator(selector);
  93  |   await expect(field).toHaveJSProperty('validity.valid', false);
  94  |   const message = await field.evaluate((el) => el.validationMessage);
  95  |   expect(message, 'the browser should show a constraint-validation message').not.toBe('');
  96  | 
  97  |   // The click must not have navigated anywhere.
  98  |   expect(page.url()).toBe(urlBefore);
  99  | }
  100 | 
```