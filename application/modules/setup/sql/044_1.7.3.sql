# Added for versioning
-- Security (CWE-89): remove any custom field whose custom_field_table is not one
-- of the five static custom tables. Vulnerable versions let an authenticated
-- admin store an arbitrary table identifier that was later interpolated into a
-- raw query, enabling time-based blind SQL injection through the custom-field
-- workflow. The application now rejects such values on save; this clears any
-- rows persisted before the fix.
DELETE FROM `ip_custom_fields`
WHERE `custom_field_table` NOT IN (
    'ip_client_custom',
    'ip_invoice_custom',
    'ip_payment_custom',
    'ip_quote_custom',
    'ip_user_custom'
);
