<?php

return [
    'draft_invoice' => [
        'user_id'                  => 1,
        'client_id'                => 1,
        'invoice_group_id'         => 1,
        'invoice_status_id'        => 1,
        'invoice_date_created'     => '2024-01-01',
        'invoice_date_due'         => '2024-01-31',
        'invoice_number'           => 'INV-0001',
        'invoice_discount_amount'  => '0.00',
        'invoice_discount_percent' => '0.00',
        'invoice_terms'            => '',
        'invoice_url_key'          => 'abc123',
        'payment_method'           => 1,
    ],

    'paid_invoice' => [
        'user_id'                  => 1,
        'client_id'                => 1,
        'invoice_group_id'         => 1,
        'invoice_status_id'        => 4,
        'invoice_date_created'     => '2024-01-01',
        'invoice_date_due'         => '2024-01-31',
        'invoice_number'           => 'INV-0002',
        'invoice_discount_amount'  => '0.00',
        'invoice_discount_percent' => '0.00',
        'invoice_terms'            => '',
        'invoice_url_key'          => 'def456',
        'payment_method'           => 1,
    ],
];
