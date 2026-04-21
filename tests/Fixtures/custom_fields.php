<?php

/**
 * Custom Fields Fixtures.
 *
 * Reusable test data for custom fields tests
 */
return [
    'invoice_text_field' => [
        'custom_field_id'       => 1,
        'custom_field_table'    => 'ip_invoices',
        'custom_field_label'    => 'Project Reference',
        'custom_field_type'     => 'TEXT',
        'custom_field_location' => 'AFTER',
        'custom_field_order'    => 1,
    ],

    'client_dropdown_field' => [
        'custom_field_id'       => 2,
        'custom_field_table'    => 'ip_clients',
        'custom_field_label'    => 'Industry',
        'custom_field_type'     => 'SELECT',
        'custom_field_location' => 'AFTER',
        'custom_field_order'    => 2,
    ],

    'quote_textarea_field' => [
        'custom_field_id'       => 3,
        'custom_field_table'    => 'ip_quotes',
        'custom_field_label'    => 'Special Instructions',
        'custom_field_type'     => 'TEXTAREA',
        'custom_field_location' => 'AFTER',
        'custom_field_order'    => 3,
    ],

    'user_checkbox_field' => [
        'custom_field_id'       => 4,
        'custom_field_table'    => 'ip_users',
        'custom_field_label'    => 'Newsletter Subscription',
        'custom_field_type'     => 'CHECKBOX',
        'custom_field_location' => 'AFTER',
        'custom_field_order'    => 4,
    ],

    'valid_new_field' => [
        'custom_field_table'    => 'ip_invoices',
        'custom_field_label'    => 'New Custom Field',
        'custom_field_type'     => 'TEXT',
        'custom_field_location' => 'AFTER',
        'custom_field_order'    => 99,
    ],
];
