<?php

/**
 * Invoice Fixtures.
 *
 * Reusable test data for invoice-related tests
 */
return [
    'draft_invoice' => [
        'invoice_id'           => 1,
        'client_id'            => 1,
        'invoice_status_id'    => 1, // Draft
        'invoice_number'       => 'INV-2024-001',
        'invoice_date_created' => '2024-01-01',
        'invoice_date_due'     => '2024-01-31',
        'invoice_subtotal'     => '1000.00',
        'invoice_tax_total'    => '100.00',
        'invoice_total'        => '1100.00',
        'invoice_balance'      => '1100.00',
        'invoice_terms'        => 'Net 30',
        'user_id'              => 1,
    ],

    'sent_invoice' => [
        'invoice_id'           => 2,
        'client_id'            => 1,
        'invoice_status_id'    => 2, // Sent
        'invoice_number'       => 'INV-2024-002',
        'invoice_date_created' => '2024-01-02',
        'invoice_date_due'     => '2024-02-01',
        'invoice_subtotal'     => '2000.00',
        'invoice_tax_total'    => '200.00',
        'invoice_total'        => '2200.00',
        'invoice_balance'      => '2200.00',
        'invoice_terms'        => 'Net 30',
        'user_id'              => 1,
    ],

    'paid_invoice' => [
        'invoice_id'           => 3,
        'client_id'            => 1,
        'invoice_status_id'    => 4, // Paid
        'invoice_number'       => 'INV-2024-003',
        'invoice_date_created' => '2024-01-03',
        'invoice_date_due'     => '2024-02-02',
        'invoice_subtotal'     => '1500.00',
        'invoice_tax_total'    => '150.00',
        'invoice_total'        => '1650.00',
        'invoice_balance'      => '0.00',
        'invoice_terms'        => 'Net 30',
        'user_id'              => 1,
    ],

    'valid_new_invoice' => [
        'client_id'            => 1,
        'invoice_date_created' => '2024-01-15',
        'invoice_date_due'     => '2024-02-15',
        'invoice_terms'        => 'Net 30',
        'invoice_status_id'    => 1,
    ],
];
