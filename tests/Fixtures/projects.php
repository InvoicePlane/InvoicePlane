<?php

/**
 * Project Fixtures.
 *
 * Reusable test data for project-related tests
 */
return [
    'active' => [
        'project_id'          => 1,
        'client_id'           => 1,
        'project_name'        => 'Website Redesign',
        'project_description' => 'Complete website redesign project',
        'project_status_id'   => 1, // Active
        'project_budget'      => '10000.00',
        'project_date_start'  => '2024-01-01',
        'project_date_due'    => '2024-03-31',
    ],

    'completed' => [
        'project_id'             => 2,
        'client_id'              => 1,
        'project_name'           => 'Mobile App Development',
        'project_description'    => 'iOS and Android mobile app',
        'project_status_id'      => 3, // Completed
        'project_budget'         => '25000.00',
        'project_date_start'     => '2023-10-01',
        'project_date_due'       => '2024-01-15',
        'project_date_completed' => '2024-01-10',
    ],

    'valid_new_project' => [
        'client_id'           => 1,
        'project_name'        => 'New Test Project',
        'project_description' => 'A new project for testing',
        'project_status_id'   => 1,
        'project_budget'      => '5000.00',
        'project_date_start'  => '2024-02-01',
        'project_date_due'    => '2024-04-30',
    ],
];
