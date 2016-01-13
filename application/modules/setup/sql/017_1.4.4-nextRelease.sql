#Adding suport for custom templates for each invoice group
ALTER TABLE ip_invoice_groups ADD COLUMN invoice_group_pdf_template varchar(255);
