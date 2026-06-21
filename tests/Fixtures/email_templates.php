<?php

return [
    'invoice_email' => [
        'email_template_subject' => 'Your invoice #{{{invoice_number}}}',
        'email_template_body'    => 'Please find your invoice attached.',
        'email_template_pdf'     => 1,
    ],

    'quote_email' => [
        'email_template_subject' => 'Your quote #{{{quote_number}}}',
        'email_template_body'    => 'Please review the attached quote.',
        'email_template_pdf'     => 1,
    ],
];
