-- Add payment_external_id for gateway deduplication (Stripe/PayPal callbacks).
--
-- The index is UNIQUE: Mdl_Payments::record_external_payment() relies on the
-- database itself rejecting a duplicate external reference, so two concurrent
-- callbacks cannot both record a payment for the same invoice. A NULL value
-- (every manually entered payment) is exempt — MariaDB/MySQL allow unlimited
-- NULLs in a UNIQUE index.

ALTER TABLE `ip_payments`
    ADD COLUMN `payment_external_id` VARCHAR(255) NULL AFTER `payment_id`;

-- If duplicate gateway payments were recorded before the index was made unique,
-- keep the earliest row's reference intact and suffix the others with
-- "-dup<payment_id>" so ADD UNIQUE INDEX cannot fail. No rows are deleted: the
-- payment history is preserved and the renamed rows stay greppable for an
-- operator to reconcile. NULLs are untouched; on a fresh install this matches
-- nothing. Gateway references (Stripe payment_intent, PayPal capture id) are far
-- shorter than 255, so the suffix never overflows the column.
UPDATE `ip_payments` AS `dup`
INNER JOIN `ip_payments` AS `keep`
        ON `dup`.`payment_external_id` = `keep`.`payment_external_id`
       AND `dup`.`payment_external_id` IS NOT NULL
       AND `dup`.`payment_id` > `keep`.`payment_id`
   SET `dup`.`payment_external_id` = CONCAT(`dup`.`payment_external_id`, '-dup', `dup`.`payment_id`);

ALTER TABLE `ip_payments`
    ADD UNIQUE INDEX `idx_payment_external_id` (`payment_external_id`);
