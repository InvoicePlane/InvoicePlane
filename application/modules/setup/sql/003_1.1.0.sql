ALTER TABLE `ip_email_templates`
ADD COLUMN `email_template_type` VARCHAR(255) NULL AFTER `email_template_title`,
ADD COLUMN `email_template_subject` VARCHAR(255) NULL AFTER `email_template_body`,
ADD COLUMN `email_template_from_name` VARCHAR(255) NULL AFTER `email_template_subject`,
ADD COLUMN `email_template_from_email` VARCHAR(255) NULL AFTER `email_template_from_name`,
ADD COLUMN `email_template_cc` VARCHAR(255) NULL AFTER `email_template_from_email`,
ADD COLUMN `email_template_bcc` VARCHAR(255) NULL AFTER `email_template_cc`,
ADD COLUMN `email_template_pdf_template` VARCHAR(255) NULL AFTER `email_template_bcc`;
