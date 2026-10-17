---
test: ../invoice_test.md
status: passed
started: 2026-10-17T09:18:09.899Z
duration_s: 202
session_id: a49158a5-92c5-4c63-8964-f74f3178ba68
---

# Create an invoice for a client — Result

## Log in ✓ passed (54.6s)
md5: 46820fb54473925f6ce89fe2926cd1a0
Open http://localhost:4895/.
Enter "admin@example.com" as the email and "Invoice-Test-2026" as the password, and click "Login".
Verify the dashboard is shown with "Quick Actions", waiting for it to load if needed.

## Add the client ✓ passed (29.8s)
md5: 54d3864ebe5592f1125b891c86514c7d
Click "Add Client" in the "Quick Actions" panel.
Enter "Globex" as the client name and click "Save".
Verify the client page for "Globex" is shown, waiting for it to load if needed.

## Create the invoice ✓ passed (72.8s)
md5: 6f8007a26035e33294079641b535facc
Open http://localhost:4895/dashboard and click "Create Invoice" in the "Quick Actions" panel.
In the "Create Invoice" dialog, select "Globex" as the client and keep the other values.
Click "Submit" in the dialog.
Verify the invoice edit page is shown, waiting for it to load if needed.

## Add a line item ✓ passed (39.6s)
md5: 751e0472032d4f0ea1ada892b8dbf21b
In the first item row, enter "Website design" as the item name, 2 as the quantity and 250 as the price.
Click "Save".
Verify the invoice shows the item "Website design" and a total of $500.00, waiting for it to update if needed.
