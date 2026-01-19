# Add monthly reset functionality for invoice groups
ALTER TABLE `ip_invoice_groups` ADD `invoice_group_reset_monthly` TINYINT(1) NOT NULL DEFAULT '0' AFTER `invoice_group_left_pad`;
