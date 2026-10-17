---
test: ../client_test.md
status: passed
started: 2026-10-17T09:15:49.784Z
duration_s: 117
session_id: da11dfb7-62a5-4163-8e3b-fc54854be5af
---

# Add a client — Result

## Log in ✓ passed (56.4s)
md5: 46820fb54473925f6ce89fe2926cd1a0
Open http://localhost:4895/.
Enter "admin@example.com" as the email and "Invoice-Test-2026" as the password, and click "Login".
Verify the dashboard is shown with "Quick Actions", waiting for it to load if needed.

## Add the client ✓ passed (35.9s)
md5: 6a9dc6df5aec7c785f3694f1e4c7dcde
Click "Add Client" in the "Quick Actions" panel.
Enter "Acme" as the client name and "Corporation" as the client surname.
Enter "Springfield" as the city and "billing@acme.example.com" as the email.
Click "Save".
Verify the client page for "Acme Corporation" is shown, waiting for it to load if needed.

## Check the client list ✓ passed (17.9s)
md5: d24b4b58ee583664e2437055b994f68b
Open http://localhost:4895/clients/status/active.
Verify the clients list shows "Acme Corporation" with the email "billing@acme.example.com", waiting for it to update if needed.
