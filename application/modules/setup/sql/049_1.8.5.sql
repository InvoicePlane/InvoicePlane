-- 1.8.5 — Associate a single service with an invoice/quote

ALTER TABLE `ip_invoices`
  ADD `service_id` INT(11) DEFAULT 0
  AFTER `creditinvoice_parent_id`;

ALTER TABLE `ip_quotes`
  ADD `service_id` INT(11) DEFAULT 0
  AFTER `notes`;
