#Adding suport for custom templates for each invoice group
ALTER TABLE ip_invoice_groups ADD COLUMN invoice_group_pdf_template varchar(255);

# IP-254 Receipts
ALTER TABLE `ip_payment_methods`
ADD COLUMN `receipt_group_id` INT(11) NULL;
ALTER TABLE `ip_payments`
ADD COLUMN `receipt_number` VARCHAR(100) NULL;