# Security: Add payment_external_id to prevent duplicate payment processing
# This column stores external payment identifiers (e.g., PayPal capture IDs and
# Stripe payment_intent IDs) to prevent duplicate payment callbacks from being
# processed multiple times. It was originally added in 042_1.7.1.sql but was
# lost when that migration was renumbered/replaced; the guest gateway
# controllers (Paypal.php, Stripe.php) have depended on it ever since.
ALTER TABLE `ip_payments`
    ADD COLUMN `payment_external_id` VARCHAR(255) DEFAULT NULL AFTER `payment_note`,
    ADD UNIQUE INDEX `idx_payment_external_id` (`payment_external_id`);
