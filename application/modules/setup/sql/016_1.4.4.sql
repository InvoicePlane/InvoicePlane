# IP-355 - Overhaul for PDF files, change the old file names to the new ones
UPDATE ip_settings
SET setting_value = 'InvoicePlane'
WHERE setting_key = 'pdf_invoice_template' AND
      (setting_value = 'default' OR setting_value = 'default-payment' OR setting_value = 'red' OR
       setting_value = 'green');

UPDATE ip_settings
SET setting_value = 'InvoicePlane - paid'
WHERE setting_key = 'pdf_invoice_template_paid' AND
      setting_value = 'default-paid';

UPDATE ip_settings
SET setting_value = 'InvoicePlane - overdue'
WHERE setting_key = 'pdf_invoice_template_overdue' AND
      setting_value = 'default-overdue';

UPDATE ip_settings
SET setting_value = 'InvoicePlane'
WHERE setting_key = 'pdf_quote_template' AND
      (setting_value = 'default' OR setting_value = 'blue' OR setting_value = 'red' OR setting_value = 'green');