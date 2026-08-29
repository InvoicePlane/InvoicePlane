-- 1.8.6 — Add the user's own e-invoicing electronic address.
--
-- Mdl_users::validation_rules() already accepts and persists
-- user_einvoice_identifier (form.php, Users.php), but no migration ever
-- created the column, so saving a user with this field populated failed
-- with an unknown-column database error.

ALTER TABLE `ip_users`
  ADD COLUMN `user_einvoice_identifier` VARCHAR(100) NULL
    COMMENT 'Sender Peppol electronic address / e-invoicing identifier, e.g. {ICD}:{identifier}'
    AFTER `user_company`;
