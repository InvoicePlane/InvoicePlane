# Add column custom_field_type to table ip_custom_fields
ALTER TABLE `ip_custom_fields`
ADD COLUMN `custom_field_type` VARCHAR(64) NOT NULL
AFTER `custom_field_table`;