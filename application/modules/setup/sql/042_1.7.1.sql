# Security: Add payment_external_id to prevent duplicate payment processing
# This column stores external payment identifiers (e.g., Stripe payment_intent IDs)
# to prevent duplicate payment callbacks from being processed multiple times
ALTER TABLE `ip_payments`
    ADD COLUMN `payment_external_id` VARCHAR(255) DEFAULT NULL AFTER `payment_note`,
    ADD UNIQUE INDEX `idx_payment_external_id` (`payment_external_id`);
