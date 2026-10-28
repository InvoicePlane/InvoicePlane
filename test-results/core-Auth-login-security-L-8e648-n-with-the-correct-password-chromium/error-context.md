# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: core/Auth/login-security.spec.js >> Login security — account status >> it denies login for an inactive user even with the correct password
- Location: tests/E2E/core/Auth/login-security.spec.js:65:7

# Error details

```
Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php exec UPDATE ip_users SET user_active = 0 WHERE user_email = 'user-mvrx6uqu-22@test.local'
Could not open input file: /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php

```

# Page snapshot

```yaml
- generic [active] [ref=f1e1]:
  - navigation [ref=f1e2]:
    - generic [ref=f1e3]:
      - generic: 
      - generic [ref=f1e4]:
        - list [ref=f1e5]:
          - listitem [ref=f1e6]:
            - link "Dashboard" [ref=f1e7] [cursor=pointer]:
              - /url: http://172.19.0.5:8000/index.php/dashboard
            - text: 
          - listitem [ref=f1e8]:
            - link " Clients" [ref=f1e9] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e10]: 
              - text: Clients 
          - listitem [ref=f1e11]:
            - link " Quotes" [ref=f1e12] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e13]: 
              - text: Quotes 
          - listitem [ref=f1e14]:
            - link " Invoices" [ref=f1e15] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e16]: 
              - text: Invoices 
          - listitem [ref=f1e17]:
            - link " Payments" [ref=f1e18] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e19]: 
              - text: Payments 
          - listitem [ref=f1e20]:
            - link " Products" [ref=f1e21] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e22]: 
              - text: Products 
          - text:  
          - listitem [ref=f1e23]:
            - link " Reports" [ref=f1e24] [cursor=pointer]:
              - /url: "#"
              - generic [ref=f1e25]: 
              - text: Reports 
        - list [ref=f1e26]:
          - listitem [ref=f1e27]:
            - link "" [ref=f1e28] [cursor=pointer]:
              - /url: https://wiki.invoiceplane.com/
          - listitem [ref=f1e30]:
            - link "" [ref=f1e31] [cursor=pointer]:
              - /url: "#"
          - listitem [ref=f1e33]:
            - link "" [ref=f1e34] [cursor=pointer]:
              - /url: http://172.19.0.5:8000/index.php/users/form/1
          - listitem [ref=f1e36]:
            - button "" [ref=f1e38] [cursor=pointer]
  - generic [ref=f1e40]:
    - list [ref=f1e42]:
      - listitem [ref=f1e43]:
        - link "" [ref=f1e44] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/clients/index
      - listitem [ref=f1e46]:
        - link "" [ref=f1e47] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/quotes/index
      - listitem [ref=f1e49]:
        - link "" [ref=f1e50] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/invoices/index
      - listitem [ref=f1e52]:
        - link "" [ref=f1e53] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/payments/index
      - listitem [ref=f1e55]:
        - link "" [ref=f1e56] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/products/index
      - listitem [ref=f1e58]:
        - link "" [ref=f1e59] [cursor=pointer]:
          - /url: http://172.19.0.5:8000/index.php/settings
    - generic [ref=f1e62]:
      - alert [ref=f1e63]:
        - button "Close" [ref=f1e64] [cursor=pointer]: ×
        - generic [ref=f1e65]: 
        - text: Security Warning
      - generic [ref=f1e68]:
        - generic [ref=f1e69]: Quick Actions
        - generic [ref=f1e70]:
          - link " Add Client" [ref=f1e71] [cursor=pointer]:
            - /url: http://172.19.0.5:8000/index.php/clients/form
            - generic [ref=f1e72]: 
            - text: Add Client
          - link " Create Quote" [ref=f1e73] [cursor=pointer]:
            - /url: javascript:void(0)
            - generic [ref=f1e74]: 
            - text: Create Quote
          - link " Create Invoice" [ref=f1e75] [cursor=pointer]:
            - /url: javascript:void(0)
            - generic [ref=f1e76]: 
            - text: Create Invoice
          - link " Enter Payment" [ref=f1e77] [cursor=pointer]:
            - /url: http://172.19.0.5:8000/index.php/payments/form
            - generic [ref=f1e78]: 
            - text: Enter Payment
      - generic [ref=f1e79]:
        - generic [ref=f1e81]:
          - generic [ref=f1e83]:
            - generic [ref=f1e84]: 
            - text: Quote Overview
          - table [ref=f1e85]:
            - rowgroup [ref=f1e86]:
              - row [ref=f1e87]:
                - cell [ref=f1e88]:
                  - link "Draft" [ref=f1e89] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/draft
                - cell "$0" [ref=f1e90]
              - row [ref=f1e91]:
                - cell [ref=f1e92]:
                  - link "Sent" [ref=f1e93] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/sent
                - cell "$0" [ref=f1e94]
              - row [ref=f1e95]:
                - cell [ref=f1e96]:
                  - link "Viewed" [ref=f1e97] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/viewed
                - cell "$0" [ref=f1e98]
              - row [ref=f1e99]:
                - cell [ref=f1e100]:
                  - link "Approved" [ref=f1e101] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/approved
                - cell "$0" [ref=f1e102]
              - row [ref=f1e103]:
                - cell [ref=f1e104]:
                  - link "Rejected" [ref=f1e105] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/rejected
                - cell "$0" [ref=f1e106]
              - row [ref=f1e107]:
                - cell [ref=f1e108]:
                  - link "Canceled" [ref=f1e109] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/canceled
                - cell "$0" [ref=f1e110]
        - generic [ref=f1e111]:
          - generic [ref=f1e112]:
            - generic [ref=f1e114]:
              - generic [ref=f1e115]: 
              - text: Invoice Overview
            - table [ref=f1e116]:
              - rowgroup [ref=f1e117]:
                - row [ref=f1e118]:
                  - cell [ref=f1e119]:
                    - link "Draft" [ref=f1e120] [cursor=pointer]:
                      - /url: http://172.19.0.5:8000/index.php/invoices/status/draft
                  - cell "$0" [ref=f1e121]
                - row [ref=f1e122]:
                  - cell [ref=f1e123]:
                    - link "Sent" [ref=f1e124] [cursor=pointer]:
                      - /url: http://172.19.0.5:8000/index.php/invoices/status/sent
                  - cell "$0" [ref=f1e125]
                - row [ref=f1e126]:
                  - cell [ref=f1e127]:
                    - link "Viewed" [ref=f1e128] [cursor=pointer]:
                      - /url: http://172.19.0.5:8000/index.php/invoices/status/viewed
                  - cell "$0" [ref=f1e129]
                - row [ref=f1e130]:
                  - cell [ref=f1e131]:
                    - link "Paid" [ref=f1e132] [cursor=pointer]:
                      - /url: http://172.19.0.5:8000/index.php/invoices/status/paid
                  - cell "$0" [ref=f1e133]
          - generic [ref=f1e134]: No overdue Invoice
      - generic [ref=f1e135]:
        - generic [ref=f1e137]:
          - generic [ref=f1e139]:
            - generic [ref=f1e140]: 
            - text: Recent Quotes
          - table [ref=f1e142]:
            - rowgroup [ref=f1e143]:
              - row [ref=f1e144]:
                - columnheader "Status" [ref=f1e145]
                - columnheader "Date" [ref=f1e146]
                - columnheader "Quote" [ref=f1e147]
                - columnheader "Client" [ref=f1e148]
                - columnheader "Balance" [ref=f1e149]
                - columnheader [ref=f1e150]
            - rowgroup [ref=f1e151]:
              - row [ref=f1e152]:
                - cell [ref=f1e153]:
                  - link "View All" [ref=f1e154] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/quotes/status/all
        - generic [ref=f1e156]:
          - generic [ref=f1e158]:
            - generic [ref=f1e159]: 
            - text: Recent Invoices
          - table [ref=f1e161]:
            - rowgroup [ref=f1e162]:
              - row [ref=f1e163]:
                - columnheader "Status" [ref=f1e164]
                - columnheader "Due Date" [ref=f1e165]
                - columnheader "Invoice" [ref=f1e166]
                - columnheader "Client" [ref=f1e167]
                - columnheader "Balance" [ref=f1e168]
                - columnheader [ref=f1e169]
            - rowgroup [ref=f1e170]:
              - row [ref=f1e171]:
                - cell [ref=f1e172]:
                  - link "View All" [ref=f1e173] [cursor=pointer]:
                    - /url: http://172.19.0.5:8000/index.php/invoices/status/all
  - text:    
```

# Test source

```ts
  1  | /**
  2  |  * Per-test database reset.
  3  |  *
  4  |  * Runs tests/Support/reset-test-db.php (truncate + reseed baseline) so every
  5  |  * browser test starts from the same clean state the PHPUnit Feature suite gets.
  6  |  * Without this the shared app database accumulates rows across the run and
  7  |  * list/pagination assertions become order-dependent and flaky.
  8  |  *
  9  |  * The DB_* environment is passed straight through; a local host run needs
  10 |  * `DB_HOSTNAME=127.0.0.1` (the value in ipconfig.php, `mariadb`, only resolves
  11 |  * inside the Docker network). CI already exports DB_* for the job.
  12 |  */
  13 | 
  14 | import { execFileSync } from 'node:child_process';
  15 | import path from 'node:path';
  16 | 
  17 | const RESET_SCRIPT = path.resolve('tests/Support/reset-test-db.php');
  18 | const SQL_SCRIPT = path.resolve('tests/Support/e2e-sql.php');
  19 | const dbEnv = () => ({ ...process.env, DB_HOSTNAME: process.env.DB_HOSTNAME || '127.0.0.1' });
  20 | 
  21 | export function resetDatabase() {
  22 |   execFileSync('php', [RESET_SCRIPT], { stdio: 'pipe', env: dbEnv() });
  23 | }
  24 | 
  25 | /**
  26 |  * Insert a row and return its id — the E2E equivalent of the PHPUnit suite's
  27 |  * databaseInsert(), for setup rows with no clean UI path.
  28 |  */
  29 | export function dbInsert(table, row) {
  30 |   const out = execFileSync('php', [SQL_SCRIPT, 'insert', table, JSON.stringify(row)], {
  31 |     stdio: ['ignore', 'pipe', 'pipe'],
  32 |     env: dbEnv(),
  33 |   });
  34 | 
  35 |   return Number(out.toString().trim());
  36 | }
  37 | 
  38 | /** Run a SELECT and return the rows as an array of plain objects. */
  39 | export function dbQuery(sql) {
  40 |   const out = execFileSync('php', [SQL_SCRIPT, 'query', sql], {
  41 |     stdio: ['ignore', 'pipe', 'pipe'],
  42 |     env: dbEnv(),
  43 |   });
  44 | 
  45 |   return JSON.parse(out.toString() || '[]');
  46 | }
  47 | 
  48 | /** Run an UPDATE/DELETE/INSERT statement; returns the affected-row count. */
  49 | export function dbExec(sql) {
> 50 |   const out = execFileSync('php', [SQL_SCRIPT, 'exec', sql], {
     |                           ^ Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php exec UPDATE ip_users SET user_active = 0 WHERE user_email = 'user-mvrx6uqu-22@test.local'
  51 |     stdio: ['ignore', 'pipe', 'pipe'],
  52 |     env: dbEnv(),
  53 |   });
  54 | 
  55 |   return Number(out.toString().trim() || 0);
  56 | }
  57 | 
```