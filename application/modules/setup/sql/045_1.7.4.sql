# Feature: Add generate_if_unpaid column to control recurring invoice generation behavior
# This allows users to control whether recurring invoices should continue to be generated
# when previous invoices remain unpaid (default: 1 = continue generating)
ALTER TABLE `ip_invoices_recurring`
    ADD COLUMN `generate_if_unpaid` TINYINT(1) NOT NULL DEFAULT 1 AFTER `recur_next_date`;

# Add invoice_recurring_id to ip_invoices to track which recurring template generated each invoice
ALTER TABLE `ip_invoices`
    ADD COLUMN `invoice_recurring_id` INT(11) DEFAULT NULL AFTER `invoice_id`,
    ADD KEY `invoice_recurring_id` (`invoice_recurring_id`);
