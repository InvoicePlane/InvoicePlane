<?php

/**
 * Client Fixtures.
 *
 * Reusable test data for client-related tests
 */
return [
    'active_client' => [
        'client_id'            => 1,
        'client_name'          => 'Active Client Corp',
        'client_email'         => 'contact@activeclient.com',
        'client_phone'         => '555-0101',
        'client_active'        => 1,
        'client_date_created'  => '2024-01-01 10:00:00',
        'client_date_modified' => '2024-01-01 10:00:00',
    ],

    'inactive_client' => [
        'client_id'            => 2,
        'client_name'          => 'Inactive Client LLC',
        'client_email'         => 'contact@inactiveclient.com',
        'client_phone'         => '555-0102',
        'client_active'        => 0,
        'client_date_created'  => '2024-01-01 10:00:00',
        'client_date_modified' => '2024-01-01 10:00:00',
    ],

    'valid_new_client' => [
        'client_name'   => 'New Client Inc',
        'client_email'  => 'contact@newclient.com',
        'client_phone'  => '555-0103',
        'client_active' => 1,
    ],
];
