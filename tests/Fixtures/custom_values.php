<?php

/**
 * Custom Values Fixtures
 * 
 * Reusable test data for custom values (dropdown options) tests
 */
return [
    'industry_technology' => [
        'custom_values_id' => 1,
        'custom_field_id' => 2, // Refers to client_dropdown_field
        'custom_values_value' => 'Technology',
    ],
    
    'industry_healthcare' => [
        'custom_values_id' => 2,
        'custom_field_id' => 2,
        'custom_values_value' => 'Healthcare',
    ],
    
    'industry_finance' => [
        'custom_values_id' => 3,
        'custom_field_id' => 2,
        'custom_values_value' => 'Finance',
    ],
    
    'industry_education' => [
        'custom_values_id' => 4,
        'custom_field_id' => 2,
        'custom_values_value' => 'Education',
    ],
    
    'valid_new_value' => [
        'custom_field_id' => 2,
        'custom_values_value' => 'Manufacturing',
    ],
];
