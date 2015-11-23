# Add column custom_field_type to table ip_custom_fields
ALTER TABLE `ip_custom_fields`
ADD COLUMN `custom_field_type` VARCHAR(64) NOT NULL
AFTER `custom_field_table`;

# Feature IP-162
# Allow invoice item to refer to a table `products` or to table `tasks`
ALTER TABLE `ip_invoice_items`
ADD COLUMN `item_task_id` INT(11) DEFAULT NULL
AFTER `item_date_added`;

# Add default hourly rate for tasks
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('default_hourly_rate', '0.00');

# Add tax rate to tasks
ALTER TABLE `ip_tasks`
ADD COLUMN `tax_rate_id` INT(11) NOT NULL
AFTER `task_status`;

# Add email_template_to_email, email_template_send_pdf, email_template_send_attachments to table ip_email_templates
ALTER TABLE `ip_email_templates`
ADD COLUMN `email_template_to_email` VARCHAR(255) NOT NULL,
ADD COLUMN `email_template_send_pdf` INT(11) NOT NULL,
ADD COLUMN `email_template_send_attachments` INT(11) NOT NULL
AFTER `email_template_pdf_template`;

# Add recur_invoices_due_after, recur_email_invoice_template to table ip_invoices_recurring
ALTER TABLE `ip_invoices_recurring`
ADD COLUMN `recur_invoices_due_after` INT(11) DEFAULT 30 NOT NULL,
ADD COLUMN `recur_email_invoice_template` INT(11) NOT NULL
AFTER `recur_next_date`;
