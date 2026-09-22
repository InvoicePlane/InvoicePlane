# Playwright E2E Tests

End-to-end tests for InvoicePlane using Playwright. These tests verify the frontend UI and user workflows.

## Prerequisites

- Node.js (see `.node-version` for required version)
- The InvoicePlane application running and accessible
- Test database seeded with initial data
- Admin user account: `admin@test.local` / `password`

## Installation

```bash
npm install
npx playwright install
```

## Running Tests

### All tests
```bash
npm run test:e2e
```

### Specific test file
```bash
npx playwright test tests/e2e/reminders.spec.ts
```

### With UI mode (interactive)
```bash
npm run test:e2e:ui
```

### Headed mode (see browser)
```bash
npm run test:e2e:headed
```

### Debug mode
```bash
npm run test:e2e:debug
```

### View test report
```bash
npm run test:e2e:report
```

## Configuration

### Base URL

Tests connect to `http://localhost` by default. Override with:

```bash
BASE_URL=http://invoiceplane.local npx playwright test
```

### Server Configuration

The test server is configured in `playwright.config.ts`. For local development:

```bash
# Start the PHP built-in server
php -S localhost:8000

# In another terminal, run tests
npm run test:e2e
```

For CI/CD, set:
```bash
START_SERVER="docker-compose up -d" npm run test:e2e
```

## Test Structure

- `tests/e2e/fixtures/auth.ts` - Authentication fixture for login
- `tests/e2e/reminders.spec.ts` - Invoice reminders feature tests

## Writing New Tests

1. Create a new file: `tests/e2e/feature-name.spec.ts`
2. Use Playwright's test syntax
3. Import fixtures if authentication is needed:

```typescript
import { test, expect } from './fixtures/auth';

test.describe('Feature Name', () => {
  test('specific scenario', async ({ authenticatedPage: page }) => {
    await page.goto('/path/to/page');
    // assertions...
  });
});
```

## Authentication

Tests include authentication state persistence:
- First login creates `.auth/admin.json` with session state
- Subsequent tests reuse the stored auth state
- Clear `.auth/` folder to force re-login

## Debugging

### Screenshots on failure
Enabled by default in config. Check `playwright-report/` after test run.

### Trace files
Enabled on retry. Open with:
```bash
npx playwright show-trace tests/e2e/trace.zip
```

### Step through with Inspector
```bash
npx playwright test --debug
```

## CI/CD Integration

Tests are configured to run in CI with:
- Retries: 2 (configurable via `CI` env var)
- Workers: 1 (single process for stability)
- Screenshots: failure only
- Traces: on first retry

Set `CI=true` before running for CI behavior:
```bash
CI=true npm run test:e2e
```

## Test Database

For CI environments, the test database must be:
1. Fresh with migrations applied
2. Seeded with basic data (clients, invoices, etc.)
3. Admin user pre-created

## Known Issues

- Some tests may require specific invoice/client setup in the test database
- If tests consistently fail at login, verify credentials and base URL
- Browser context isolation may prevent auth state sharing; clear `.auth/` folder if needed

## Best Practices

1. Use explicit waits (`waitForURL`, `waitForLoadState`) over arbitrary delays
2. Prefer fixture-based setup over in-test data creation
3. Test user workflows, not implementation details
4. Keep tests isolated - each should be runnable independently
5. Use descriptive test names that explain what's being tested
