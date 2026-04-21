<?php

/**
 * Email Templates Fixtures.
 *
 * Reusable test data for email template tests
 */
return [
    'invoice_template' => [
        'email_template_id'           => 1,
        'email_template_title'        => 'Default Invoice Template',
        'email_template_type'         => 'invoice',
        'email_template_subject'      => 'Invoice #{{invoice_number}} from {{company_name}}',
        'email_template_body'         => 'Dear {{client_name}},<br><br>Please find attached invoice #{{invoice_number}}.<br><br>Thank you for your business!',
        'email_template_from_name'    => 'Test Company',
        'email_template_from_email'   => 'billing@example.com',
        'email_template_cc'           => '',
        'email_template_bcc'          => '',
        'email_template_pdf_template' => 'default',
    ],

    'quote_template' => [
        'email_template_id'           => 2,
        'email_template_title'        => 'Default Quote Template',
        'email_template_type'         => 'quote',
        'email_template_subject'      => 'Quote #{{quote_number}} from {{company_name}}',
        'email_template_body'         => 'Dear {{client_name}},<br><br>We are pleased to provide you with quote #{{quote_number}}.<br><br>Best regards,<br>{{company_name}}',
        'email_template_from_name'    => 'Test Company',
        'email_template_from_email'   => 'sales@example.com',
        'email_template_cc'           => '',
        'email_template_bcc'          => '',
        'email_template_pdf_template' => 'default',
    ],

    'overdue_reminder_template' => [
        'email_template_id'           => 3,
        'email_template_title'        => 'Overdue Invoice Reminder',
        'email_template_type'         => 'invoice',
        'email_template_subject'      => 'Reminder: Invoice #{{invoice_number}} is overdue',
        'email_template_body'         => 'Dear {{client_name}},<br><br>This is a friendly reminder that invoice #{{invoice_number}} is now overdue.<br><br>Please remit payment at your earliest convenience.',
        'email_template_from_name'    => 'Test Company',
        'email_template_from_email'   => 'billing@example.com',
        'email_template_cc'           => 'accounting@example.com',
        'email_template_bcc'          => '',
        'email_template_pdf_template' => 'default',
    ],

    'valid_new_template' => [
        'email_template_title'        => 'New Custom Template',
        'email_template_type'         => 'invoice',
        'email_template_subject'      => 'New Invoice Subject',
        'email_template_body'         => 'New invoice body content with {{variables}}',
        'email_template_from_name'    => 'Test Company',
        'email_template_from_email'   => 'info@example.com',
        'email_template_cc'           => '',
        'email_template_bcc'          => '',
        'email_template_pdf_template' => 'default',
    ],
];
