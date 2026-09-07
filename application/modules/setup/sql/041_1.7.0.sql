# Add payment_external_id column to support payment gateway deduplication
ALTER TABLE `ip_payments`
    ADD COLUMN `payment_external_id` VARCHAR(255) NULL DEFAULT NULL,
    ADD INDEX `idx_payment_external_id` (`payment_external_id`);
