/*  Customize quote / invoice groups #5 */
ALTER TABLE `ip_invoice_groups`
ADD COLUMN `invoice_group_identifier_format` VARCHAR(255) NOT NULL AFTER `invoice_group_name`;
/* convert fields to format string
 * Schema: Prefix{{{YEAR}}}{{{MONTH}}}{{{ID}}}
 * (based on existing schema)
 */
UPDATE ip_invoice_groups
SET invoice_group_identifier_format = CONCAT(
    invoice_group_prefix,
    CASE invoice_group_prefix_year
    WHEN 1 THEN '{{{year}}}'
    ELSE ''
    END,
    CASE invoice_group_prefix_month
    WHEN 1 THEN '{{{month}}}'
    ELSE ''
    END,
    '{{{id}}}'
);
ALTER TABLE invoiceplane.ip_invoice_groups
DROP invoice_group_prefix,
DROP invoice_group_prefix_month,
DROP invoice_group_prefix_year
;