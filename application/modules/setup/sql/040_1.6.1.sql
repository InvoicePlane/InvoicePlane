# Feature Request IP-942 Add BIC & Remittance info to ip_users table
ALTER TABLE `ip_users`
  ADD COLUMN user_bic VARCHAR(11) DEFAULT NULL AFTER `user_iban`,
  ADD COLUMN user_remittance_tmpl VARCHAR(100) DEFAULT NULL AFTER `user_bic`;
