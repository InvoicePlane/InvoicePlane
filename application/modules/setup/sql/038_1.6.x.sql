CREATE TABLE `ip_services` (
  service_id int(11) NOT NULL,
  service_name varchar(255) NOT NULL,
  PRIMARY KEY (service_id, service_name)
);

ALTER TABLE `ip_invoices`
  ADD `service_id` int(11) DEFAULT 0
  AFTER `creditinvoice_parent_id`;

ALTER TABLE `ip_quotes`
  ADD `service_id` int(11) DEFAULT 0
  AFTER `notes`;
