---
mode: testing
url: http://localhost:4895/
max_steps: 40
---

# Add a client

## Log in
Open http://localhost:4895/.
Enter "admin@example.com" as the email and "Invoice-Test-2026" as the password, and click "Login".
Verify the dashboard is shown with "Quick Actions", waiting for it to load if needed.

## Add the client
Click "Add Client" in the "Quick Actions" panel.
Enter "Acme" as the client name and "Corporation" as the client surname.
Enter "Springfield" as the city and "billing@acme.example.com" as the email.
Click "Save".
Verify the client page for "Acme Corporation" is shown, waiting for it to load if needed.

## Check the client list
Open http://localhost:4895/clients/status/active.
Verify the clients list shows "Acme Corporation" with the email "billing@acme.example.com", waiting for it to update if needed.
