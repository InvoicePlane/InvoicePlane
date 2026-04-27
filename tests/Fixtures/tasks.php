<?php

/**
 * Task Fixtures.
 *
 * Reusable test data for task-related tests
 */
return [
    'open' => [
        'task_id'           => 1,
        'project_id'        => 1,
        'task_name'         => 'Design Homepage',
        'task_description'  => 'Create homepage mockup and design',
        'task_status'       => 0, // Pending/Open
        'task_price'        => '500.00',
        'task_date_created' => '2024-01-05',
        'task_date_due'     => '2024-01-15',
        'task_finish_time'  => null,
    ],

    'completed' => [
        'task_id'           => 2,
        'project_id'        => 2,
        'task_name'         => 'Setup Database',
        'task_description'  => 'Configure database schema',
        'task_status'       => 2, // Completed
        'task_price'        => '200.00',
        'task_date_created' => '2023-10-05',
        'task_date_due'     => '2023-10-10',
        'task_finish_time'  => '2023-10-09',
    ],

    'with_times' => [
        'task_id'           => 3,
        'project_id'        => 1,
        'task_name'         => 'Develop Contact Form',
        'task_description'  => 'Build functional contact form with time tracking',
        'task_status'       => 1, // In Progress
        'task_price'        => '300.00',
        'task_date_created' => '2024-01-10',
        'task_date_due'     => '2024-01-20',
        'task_finish_time'  => null,
    ],

    'valid_new_task' => [
        'project_id'       => 1,
        'task_name'        => 'New Test Task',
        'task_description' => 'Test task description',
        'task_status'      => 0,
        'task_price'       => '150.00',
        'task_date_due'    => '2024-02-15',
    ],
];
