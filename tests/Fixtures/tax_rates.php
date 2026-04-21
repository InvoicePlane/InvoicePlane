<?php

/**
 * Tax Rate Fixtures.
 *
 * Reusable test data for tax rate tests
 */
return [
    'standard_tax' => [
        'tax_rate_id'      => 1,
        'tax_rate_name'    => 'Standard VAT',
        'tax_rate_percent' => '20.00',
    ],

    'reduced_tax' => [
        'tax_rate_id'      => 2,
        'tax_rate_name'    => 'Reduced Rate',
        'tax_rate_percent' => '10.00',
    ],

    'zero_tax' => [
        'tax_rate_id'      => 3,
        'tax_rate_name'    => 'Zero Rate',
        'tax_rate_percent' => '0.00',
    ],

    'valid_new_tax' => [
        'tax_rate_name'    => 'New Tax Rate',
        'tax_rate_percent' => '15.00',
    ],
];
