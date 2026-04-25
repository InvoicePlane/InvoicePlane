# Performance: Add database indexes to improve query speed
# These indexes significantly improve performance when dealing with large datasets
# (e.g., 500+ clients and 6500+ invoices)
# Before: 7-8 seconds on viewing clients list
# After: 0.5 seconds on viewing clients list

# Clients active status index
CREATE INDEX idx_clients_active 
  ON ip_clients (client_active);

# Invoices client relationship index
CREATE INDEX idx_invoices_client_id
  ON ip_invoices (client_id);

# Invoice amounts relationship index
CREATE INDEX idx_invoice_amounts_invoice_id
  ON ip_invoice_amounts (invoice_id);

# Clients primary key index (if not already exists)
CREATE INDEX idx_clients_id ON ip_clients (client_id);

# Invoices user relationship index
CREATE INDEX idx_invoices_user_id
  ON ip_invoices (user_id);

# Recurring invoices compound index for subquery optimization
CREATE INDEX idx_invoices_recurring_invoice_id
  ON ip_invoices_recurring (invoice_id, recur_next_date);

# Quotes invoice relationship index
CREATE INDEX idx_quotes_invoice_id
  ON ip_quotes (invoice_id);

# Sumex invoice relationship index
CREATE INDEX idx_invoice_sumex_invoice
  ON ip_invoice_sumex (sumex_invoice);

# Security: Add password reset token expiration
# This adds a timestamp column to track when password reset tokens were created,
# allowing the system to enforce a strict expiration time (default: 15 minutes)
# to prevent indefinite token validity and reduce account takeover risk.
ALTER TABLE `ip_users`
    ADD COLUMN `user_passwordreset_token_expiry` DATETIME DEFAULT NULL AFTER `user_passwordreset_token`;
