<?php

/**
 * User Fixtures
 * 
 * Reusable test data for user-related tests
 */
return [
    'admin' => [
        'user_id' => 1,
        'user_type' => 1,
        'user_name' => 'Admin User',
        'user_email' => 'admin@example.com',
        'user_password' => password_hash('AdminPass123!', PASSWORD_DEFAULT),
        'user_company' => 'Test Company',
        'user_active' => 1,
        'user_language' => 'en',
        'user_date_created' => '2024-01-01 10:00:00',
        'user_date_modified' => '2024-01-01 10:00:00',
    ],
    
    'guest' => [
        'user_id' => 2,
        'user_type' => 2,
        'user_name' => 'Guest User',
        'user_email' => 'guest@example.com',
        'user_password' => password_hash('GuestPass123!', PASSWORD_DEFAULT),
        'user_company' => 'Test Company',
        'user_active' => 1,
        'user_language' => 'en',
        'user_date_created' => '2024-01-01 10:00:00',
        'user_date_modified' => '2024-01-01 10:00:00',
    ],
    
    'inactive' => [
        'user_id' => 3,
        'user_type' => 2,
        'user_name' => 'Inactive User',
        'user_email' => 'inactive@example.com',
        'user_password' => password_hash('InactivePass123!', PASSWORD_DEFAULT),
        'user_company' => 'Test Company',
        'user_active' => 0,
        'user_language' => 'en',
        'user_date_created' => '2024-01-01 10:00:00',
        'user_date_modified' => '2024-01-01 10:00:00',
    ],
    
    'valid_new_user' => [
        'user_name' => 'New Test User',
        'user_email' => 'newuser@example.com',
        'user_password' => 'SecurePass123!',
        'user_passwordv' => 'SecurePass123!',
        'user_type' => '2',
        'user_company' => 'Test Company',
        'user_active' => '1',
    ],
];
