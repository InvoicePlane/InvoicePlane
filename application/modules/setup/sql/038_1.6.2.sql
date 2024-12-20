ALTER table `ip_invoice_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
ALTER table `ip_quote_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
INSERT INTO `ip_settings` (`setting_key`,`setting_value`) VALUES ('default_item_decimals',2);
-- [IP-1003]: Add extra field title (#1101)
ALTER TABLE `ip_clients` ADD client_title VARCHAR(50) DEFAULT NULL AFTER `client_surname`;
-- Extension by chrissie
create table ip_client_extended (
    client_extended_id int auto_increment primary key,
    client_extended_client_id int,
    client_extended_salutation varchar(255),
    client_extended_customer_no varchar(255),
    client_extended_flags int,
    client_extended_contact_person varchar(255),
    client_extended_contract varchar(255),
    client_extended_direct_debit varchar(255),
    client_extended_bank_name varchar(255),
    client_extended_bank_bic varchar(255),
    client_extended_bank_iban varchar(255),
    client_extended_payment_terms varchar(255),
    client_extended_delivery_terms varchar(255)
);
-- Extension by chrissie
create table ip_documents (
    document_id int auto_increment primary key,
    client_id int, document_filename varchar(255),
    document_description varchar(255),
    document_deleted int default 0,
    document_created datetime
);