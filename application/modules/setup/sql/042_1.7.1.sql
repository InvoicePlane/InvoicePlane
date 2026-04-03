# Feature Request IP-1502 Add invoice email address for clients
ALTER TABLE `ip_clients`
    ADD COLUMN client_invoice_email VARCHAR(100) DEFAULT NULL AFTER `client_email`,