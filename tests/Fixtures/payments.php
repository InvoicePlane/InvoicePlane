<?php

/**
 * Payment Fixtures
 * 
 * Reusable test data for payment-related tests
 */
return [
    'cash_payment' => [
        'payment_id' => 1,
        'invoice_id' => 2,
        'payment_method_id' => 1, // Cash
        'payment_amount' => '500.00',
        'payment_date' => '2024-01-10',
        'payment_note' => 'Partial payment via cash',
    ],
    
    'bank_transfer' => [
        'payment_id' => 2,
        'invoice_id' => 2,
        'payment_method_id' => 2, // Bank Transfer
        'payment_amount' => '1700.00',
        'payment_date' => '2024-01-20',
        'payment_note' => 'Final payment via bank transfer',
    ],
    
    'credit_card_payment' => [
        'payment_id' => 3,
        'invoice_id' => 3,
        'payment_method_id' => 3, // Credit Card
        'payment_amount' => '1650.00',
        'payment_date' => '2024-01-15',
        'payment_note' => 'Full payment via credit card',
    ],
    
    'valid_new_payment' => [
        'invoice_id' => 2,
        'payment_method_id' => 1,
        'payment_amount' => '1000.00',
        'payment_date' => '2024-01-25',
        'payment_note' => 'Test payment',
    ],
];
