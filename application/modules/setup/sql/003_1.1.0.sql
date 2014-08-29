ALTER TABLE `ip_email_templates`
ADD COLUMN `email_template_type` VARCHAR(255) NULL AFTER `email_template_title`,
ADD COLUMN `email_template_subject` VARCHAR(255) NULL AFTER `email_template_body`,
ADD COLUMN `email_template_from_name` VARCHAR(255) NULL AFTER `email_template_subject`,
ADD COLUMN `email_template_from_email` VARCHAR(255) NULL AFTER `email_template_from_name`,
ADD COLUMN `email_template_cc` VARCHAR(255) NULL AFTER `email_template_from_email`,
ADD COLUMN `email_template_bcc` VARCHAR(255) NULL AFTER `email_template_cc`,
ADD COLUMN `email_template_pdf_template` VARCHAR(255) NULL AFTER `email_template_bcc`;

ALTER TABLE `ip_clients`
ADD COLUMN `client_vat_id` VARCHAR(100) NOT NULL DEFAULT '' AFTER `client_web`,
ADD COLUMN `client_tax_code` VARCHAR(100) NOT NULL DEFAULT '' AFTER `client_vat_id`;

ALTER TABLE `ip_users`
ADD COLUMN `user_vat_id` VARCHAR(100) NOT NULL DEFAULT '' AFTER `user_web`,
ADD COLUMN `user_tax_code` VARCHAR(100) NOT NULL DEFAULT '' AFTER `user_vat_id`;