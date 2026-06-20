# Added for versioning
-- Treat empty client birthdates as NULL.
UPDATE `ip_clients`
SET `client_birthdate` = NULL
WHERE `client_birthdate` = '0000-00-00';

ALTER TABLE `ip_clients`
MODIFY `client_birthdate` DATE NULL DEFAULT NULL;

-- NOTE: invoice_password and quote_password are now encrypted at rest using
-- the application ENCRYPTION_KEY. Existing plaintext values remain readable
-- (the application falls back to returning them as-is on decrypt failure) and
-- are transparently re-encrypted the next time each record is saved.
