-- Add payment_external_id column for gateway deduplication (Stripe/PayPal callbacks)

ALTER TABLE `ip_payments` ADD COLUMN `payment_external_id` VARCHAR(255) NULL AFTER `payment_id`;
ALTER TABLE `ip_payments` ADD INDEX `idx_payment_external_id` (`payment_external_id`);
