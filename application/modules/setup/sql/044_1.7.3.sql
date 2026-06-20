# Added for versioning
-- Add user_siren column for French e-invoicing (Factur-X / EN16931 / PDP) support.
ALTER TABLE `ip_users`
ADD COLUMN `user_siren` VARCHAR(9) NULL DEFAULT NULL AFTER `user_company`;