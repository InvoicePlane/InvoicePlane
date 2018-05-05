<?php

/**
 * Role Seeder
 *
 * Creates new basic roles and assigns basic permissions
 */

// Attach the default permissions to the default role
foreach ($default_permissions as $permission) {
    $this->aauth->allow_group('default', $permission);
    // @TODO Not working because Aauth caches permissions in the construction method. See https://github.com/magefly/CodeIgniter-Aauth/issues/227 for details. [Kovah 2018-05-05]
}

// Create the client role
$client_group = $this->aauth->create_group('client');

// Attach the client permissions to the client role
foreach ($client_permissions as $permission) {
    $this->aauth->allow_group($client_group, $permission);
}
