# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: clients/clients-comprehensive.spec.js >> Clients — comprehensive workflow >> it creates, edits, validates, and filters clients through a complete workflow
- Location: tests/E2E/clients/clients-comprehensive.spec.js:20:7

# Error details

```
Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php query SELECT client_name, client_surname, client_email, client_phone FROM ip_clients WHERE client_id = 1
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
    - generic [ref=f1e61]:
      - generic [ref=f1e62]:
        - heading "TechCorp International-mvrx5w1e-19 Division A" [level=1] [ref=f1e63]
        - generic [ref=f1e65]:
          - link " Create Quote" [ref=f1e66] [cursor=pointer]:
            - /url: "#"
            - generic [ref=f1e67]: 
            - text: Create Quote
          - link " Create Invoice" [ref=f1e68] [cursor=pointer]:
            - /url: "#"
            - generic [ref=f1e69]: 
            - text: Create Invoice
          - link " Edit" [ref=f1e70] [cursor=pointer]:
            - /url: http://172.19.0.5:8000/index.php/clients/form/1
            - generic [ref=f1e71]: 
            - text: Edit
          - button " Delete" [ref=f1e73] [cursor=pointer]:
            - generic [ref=f1e74]: 
            - text: Delete
      - list [ref=f1e75]:
        - listitem [ref=f1e76]:
          - link "Details" [ref=f1e77]:
            - /url: "#client-details"
        - listitem [ref=f1e78]:
          - link "Quotes" [ref=f1e79] [cursor=pointer]:
            - /url: "#client-quotes"
        - listitem [ref=f1e80]:
          - link "Invoices" [ref=f1e81] [cursor=pointer]:
            - /url: "#client-invoices"
        - listitem [ref=f1e82]:
          - link "Payments" [ref=f1e83] [cursor=pointer]:
            - /url: "#client-payments"
      - generic [ref=f1e85]:
        - generic [ref=f1e86]:
          - alert [ref=f1e87]:
            - button "Close" [ref=f1e88] [cursor=pointer]: ×
            - generic [ref=f1e89]: 
            - text: Record successfully created
          - generic [ref=f1e90]:
            - generic [ref=f1e91]:
              - heading "TechCorp International-mvrx5w1e-19 Division A" [level=3] [ref=f1e92]
              - paragraph
            - table [ref=f1e94]:
              - rowgroup [ref=f1e95]:
                - row [ref=f1e96]:
                  - rowheader "Language" [ref=f1e97]
                  - cell "System" [ref=f1e98]
                - row [ref=f1e99]:
                  - rowheader "Total Billed" [ref=f1e100]
                  - cell "$0" [ref=f1e101]
                - row [ref=f1e102]:
                  - columnheader "Total Paid" [ref=f1e103]
                  - columnheader "$0" [ref=f1e104]
                - row [ref=f1e105]:
                  - rowheader "Total Balance" [ref=f1e106]
                  - cell "$0" [ref=f1e107]
          - separator [ref=f1e108]
          - generic [ref=f1e109]:
            - generic [ref=f1e111]:
              - generic [ref=f1e112]: Contact Information
              - table [ref=f1e114]:
                - rowgroup [ref=f1e115]:
                  - row [ref=f1e116]:
                    - rowheader "Email" [ref=f1e117]
                    - cell [ref=f1e118]:
                      - link "contact-techcorp-mvrx5w1e-20@example.com" [ref=f1e119] [cursor=pointer]:
                        - /url: mailto:contact-techcorp-mvrx5w1e-20@example.com
                  - row [ref=f1e120]:
                    - rowheader "Phone" [ref=f1e121]
                    - cell "+1-555-0123" [ref=f1e122]
            - generic [ref=f1e124]:
              - generic [ref=f1e125]: Taxes Information
              - table [ref=f1e127]:
                - rowgroup [ref=f1e128]:
                  - row [ref=f1e129]:
                    - columnheader "Company" [ref=f1e130]
                    - cell [ref=f1e131]
          - separator [ref=f1e132]
          - generic [ref=f1e135]:
            - generic [ref=f1e136]: Personal Information
            - table [ref=f1e138]:
              - rowgroup [ref=f1e139]:
                - row [ref=f1e140]:
                  - columnheader "Birthdate" [ref=f1e141]
                  - cell [ref=f1e142]
                - row [ref=f1e143]:
                  - rowheader "Gender" [ref=f1e144]
                  - cell "Male" [ref=f1e145]
          - separator [ref=f1e146]
          - generic [ref=f1e149]:
            - generic [ref=f1e150]: Notes
            - generic [ref=f1e152]:
              - textbox [ref=f1e153]
              - generic [ref=f1e154] [cursor=pointer]: Add Note
        - text:            
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
> 40 |   const out = execFileSync('php', [SQL_SCRIPT, 'query', sql], {
     |                           ^ Error: Command failed: php /data/Projects/invoiceplane/ivpl-devcheck/tests/Support/e2e-sql.php query SELECT client_name, client_surname, client_email, client_phone FROM ip_clients WHERE client_id = 1
  41 |     stdio: ['ignore', 'pipe', 'pipe'],
  42 |     env: dbEnv(),
  43 |   });
  44 | 
  45 |   return JSON.parse(out.toString() || '[]');
  46 | }
  47 | 
  48 | /** Run an UPDATE/DELETE/INSERT statement; returns the affected-row count. */
  49 | export function dbExec(sql) {
  50 |   const out = execFileSync('php', [SQL_SCRIPT, 'exec', sql], {
  51 |     stdio: ['ignore', 'pipe', 'pipe'],
  52 |     env: dbEnv(),
  53 |   });
  54 | 
  55 |   return Number(out.toString().trim() || 0);
  56 | }
  57 | 
```