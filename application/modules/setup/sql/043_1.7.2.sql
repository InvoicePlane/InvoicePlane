# Added for versioning
-- Treat empty client birthdates as NULL.
UPDATE `ip_clients`
SET `client_birthdate` = NULL
WHERE `client_birthdate` = '0000-00-00';

ALTER TABLE `ip_clients`
MODIFY `client_birthdate` DATE NULL DEFAULT NULL;
