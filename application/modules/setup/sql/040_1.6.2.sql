# Feature Request IP-939 Add BIC & Remittance info tmpl to ip_users table and Add contact & UBL-CII_version to ip_clients table
ALTER TABLE `ip_users`
  ADD COLUMN user_bank VARCHAR(50) DEFAULT NULL AFTER `user_subscribernumber`,
  ADD COLUMN user_bic VARCHAR(11) DEFAULT NULL AFTER `user_iban`,
  ADD COLUMN user_remittance_tmpl VARCHAR(105) DEFAULT NULL AFTER `user_bic`,
  ADD COLUMN user_invoicing_contact VARCHAR(50) DEFAULT NULL AFTER `user_country`;  # So we have a contact for a user? terrific

ALTER TABLE `ip_clients`
  ADD COLUMN client_invoicing_contact VARCHAR(50) DEFAULT NULL AFTER client_id,
  ADD COLUMN client_einvoice_version VARCHAR(25) DEFAULT NULL AFTER client_invoicing_contact;
