# End-to-end (e2e) tests for web

Browser end-to-end tests for InvoicePlane, authored with [KaneCLI](https://www.testmuai.com/kane-cli/) by TestMu AI. Each test is a plain-English `_test.md` file; KaneCLI runs it in a real Chrome browser and records each step so later runs replay without re-authoring.

| Test | What it covers |
|---|---|
| `client_test.md` | Logging in, adding a client from the dashboard's Quick Actions, and checking it in the active clients list |
| `invoice_test.md` | Logging in, adding a client, creating an invoice for it, adding a line item (2 × 250), and checking the item and the $500.00 total |

They complement the PHPUnit suite in `tests/` by going through the whole app in a browser. The Docker image only copies the app folders, so nothing here ends up in it.

## Run the tests

The tests expect InvoicePlane at `http://localhost:4895` on a fresh database, with the admin user `admin@example.com` / `Invoice-Test-2026`. With `compose.yml`, set the default admin through an override file, for example `compose.e2e.yml`:

```yaml
services:
  app:
    environment:
      DEFAULT_ADMIN_EMAIL: admin@example.com
      DEFAULT_ADMIN_PASSWORD: Invoice-Test-2026
      DEFAULT_ADMIN_NAME: Alex Morgan
```

```bash
docker compose -f compose.yml -f compose.e2e.yml up -d --build
```

Both tests create their own records, so reset the database (for example `docker compose -f compose.yml down -v`) before running them again.

### With KaneCLI

```bash
npm install -g @testmuai/kane-cli
kane-cli login
kane-cli testmd run e2e/kane/client_test.md --agent
kane-cli testmd run e2e/kane/invoice_test.md --agent
```

### With the exported Playwright code

Each test also has an exported Playwright (JavaScript) version under `output-<test>/playwright-javascript-code/`:

```bash
cd e2e/kane/output-invoice/playwright-javascript-code
npm install
npx playwright install chromium
node test.js
```

Copy `.env.example` to `.env` and fill in your TestMu AI (LambdaTest) credentials first.

## Editing a test

Edit the step text in the `_test.md` file and run it again with `kane-cli testmd run`. Changed steps (and the steps after them) are re-recorded; unchanged steps replay from `output-<test>/`.
