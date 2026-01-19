# Added for versioning and new fields for client_number, invoice fields, and quote fields

# Add client_number to ip_clients
ALTER TABLE `ip_clients`
    ADD COLUMN client_number VARCHAR(255) DEFAULT NULL AFTER `client_id`;

# Add invoice_quote_number, invoice_work_order, and invoice_agreement to ip_invoices
ALTER TABLE `ip_invoices`
    ADD COLUMN invoice_quote_number TEXT DEFAULT NULL AFTER `invoice_number`,
    ADD COLUMN invoice_work_order VARCHAR(255) DEFAULT NULL AFTER `invoice_quote_number`,
    ADD COLUMN invoice_agreement VARCHAR(255) DEFAULT NULL AFTER `invoice_work_order`;

# Add quote_work_order and quote_agreement to ip_quotes
ALTER TABLE `ip_quotes`
    ADD COLUMN quote_work_order VARCHAR(255) DEFAULT NULL AFTER `quote_number`,
    ADD COLUMN quote_agreement VARCHAR(255) DEFAULT NULL AFTER `quote_work_order`;

