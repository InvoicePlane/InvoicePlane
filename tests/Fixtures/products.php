<?php

/**
 * Product Fixtures.
 *
 * Reusable test data for product-related tests
 */
return [
    'standard_product' => [
        'product_id'          => 1,
        'product_sku'         => 'PROD-001',
        'product_name'        => 'Standard Product',
        'product_description' => 'A standard test product',
        'product_price'       => '99.99',
        'product_unit_id'     => 1,
        'product_family_id'   => 1,
        'product_tax_rate_id' => 1,
    ],

    'service_product' => [
        'product_id'          => 2,
        'product_sku'         => 'SRV-001',
        'product_name'        => 'Consulting Service',
        'product_description' => 'Hourly consulting service',
        'product_price'       => '150.00',
        'product_unit_id'     => 2, // Hours
        'product_family_id'   => 2, // Services
        'product_tax_rate_id' => 1,
    ],

    'valid_new_product' => [
        'product_sku'         => 'NEWPROD-001',
        'product_name'        => 'New Test Product',
        'product_description' => 'New product for testing',
        'product_price'       => '49.99',
        'product_unit_id'     => 1,
        'product_family_id'   => 1,
    ],
];
