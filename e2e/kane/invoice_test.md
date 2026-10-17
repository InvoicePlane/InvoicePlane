---
mode: testing
url: http://localhost:4895/
max_steps: 50
---

# Create an invoice for a client

## Log in
Open http://localhost:4895/.
Enter "admin@example.com" as the email and "Invoice-Test-2026" as the password, and click "Login".
Verify the dashboard is shown with "Quick Actions", waiting for it to load if needed.

## Add the client
Click "Add Client" in the "Quick Actions" panel.
Enter "Globex" as the client name and click "Save".
Verify the client page for "Globex" is shown, waiting for it to load if needed.

## Create the invoice
Open http://localhost:4895/dashboard and click "Create Invoice" in the "Quick Actions" panel.
In the "Create Invoice" dialog, select "Globex" as the client and keep the other values.
Click "Submit" in the dialog.
Verify the invoice edit page is shown, waiting for it to load if needed.

## Add a line item
In the first item row, enter "Website design" as the item name, 2 as the quantity and 250 as the price.
Click "Save".
Verify the invoice shows the item "Website design" and a total of $500.00, waiting for it to update if needed.
