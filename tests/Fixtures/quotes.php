<?php

/**
 * Quote Fixtures.
 *
 * Reusable test data for quote-related tests
 */
return [
    'draft' => [
        'quote_id'           => 1,
        'client_id'          => 1,
        'quote_status_id'    => 1, // Draft
        'quote_number'       => 'QUO-2024-001',
        'quote_date_created' => '2024-01-01',
        'quote_date_expires' => '2024-01-31',
        'quote_subtotal'     => '1000.00',
        'quote_tax_total'    => '100.00',
        'quote_total'        => '1100.00',
        'quote_amount'       => '1100.00',
        'user_id'            => 1,
    ],

    'sent' => [
        'quote_id'           => 2,
        'client_id'          => 1,
        'quote_status_id'    => 2, // Sent
        'quote_number'       => 'QUO-2024-002',
        'quote_date_created' => '2024-01-02',
        'quote_date_expires' => '2024-02-01',
        'quote_subtotal'     => '2000.00',
        'quote_tax_total'    => '200.00',
        'quote_total'        => '2200.00',
        'quote_amount'       => '2200.00',
        'user_id'            => 1,
    ],

    'approved' => [
        'quote_id'           => 3,
        'client_id'          => 1,
        'quote_status_id'    => 4, // Approved
        'quote_number'       => 'QUO-2024-003',
        'quote_date_created' => '2024-01-03',
        'quote_date_expires' => '2024-02-02',
        'quote_subtotal'     => '1500.00',
        'quote_tax_total'    => '150.00',
        'quote_total'        => '1650.00',
        'quote_amount'       => '1650.00',
        'user_id'            => 1,
    ],

    'valid_new_quote' => [
        'client_id'          => 1,
        'quote_number'       => 'QUO-2024-NEW',
        'quote_date_created' => '2024-01-15',
        'quote_date_expires' => '2024-02-15',
        'quote_status_id'    => 1,
        'quote_amount'       => '1000.00',
    ],
];
