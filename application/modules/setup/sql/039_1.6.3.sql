# Feature Request IP-939 Add e-invoice flow system
ALTER TABLE `ip_users`
    ADD COLUMN user_ubl_eas_code VARCHAR(50) DEFAULT NULL AFTER `user_tax_code`,
    ADD COLUMN user_bank VARCHAR(50) DEFAULT NULL AFTER `user_subscribernumber`,
    ADD COLUMN user_bic VARCHAR(11) DEFAULT NULL AFTER `user_iban`,
    ADD COLUMN user_remittance_tmpl VARCHAR(105) DEFAULT NULL AFTER `user_bic`,
    ADD COLUMN user_invoicing_contact VARCHAR(50) DEFAULT NULL AFTER `user_country`;

ALTER TABLE `ip_clients`
    ADD COLUMN client_ubl_eas_code VARCHAR(50) DEFAULT NULL AFTER `client_tax_code`,
    ADD COLUMN client_company VARCHAR(150) DEFAULT NULL AFTER `client_name`,
    ADD COLUMN client_invoicing_contact VARCHAR(50) DEFAULT NULL AFTER `client_surname`,
    ADD COLUMN client_einvoicing_active TINYINT(1) NOT NULL DEFAULT '0' AFTER `client_title`,
    ADD COLUMN client_einvoicing_version VARCHAR(25) DEFAULT NULL AFTER `client_einvoicing_active`;

DELETE FROM `ip_settings` WHERE `setting_key` = 'include_zugferd';