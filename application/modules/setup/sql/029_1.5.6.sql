#Alter custom fields id like in the models

ALTER TABLE ip_client_custom CHANGE client_custom_id client_custom_fieldid INT(11);

ALTER TABLE ip_invoice_custom CHANGE invoice_custom_id invoice_custom_fieldid INT(11);
ALTER TABLE ip_invoice_custom CHANGE invoice_custom_notes invoice_custom_fieldvalue VARCHAR(2000);

ALTER TABLE ip_quote_custom CHANGE quote_custom_id quote_custom_fieldid INT(11);

ALTER TABLE ip_user_custom CHANGE user_custom_id user_custom_fieldid INT(11);

ALTER TABLE ip_payment_custom CHANGE payment_custom_id payment_custom_fieldid INT(11);

ALTER TABLE ip_invoice_custom
CHANGE COLUMN `invoice_custom_fieldid` `invoice_custom_fieldid` INT(11) NOT NULL ,
CHANGE COLUMN `invoice_id` `invoice_id` INT(11) NOT NULL DEFAULT NULL ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`invoice_custom_fieldid`, `invoice_id`);

