<?php

/**
 * Role Seeder
 * Creates new basic roles and assigns basic permissions
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */

// Attach the default permissions to the default role
foreach ($default_permissions as $permission) {
    $this->aauth->allow_group('default', $permission);
}

// Create the client role
$client_group = $this->aauth->create_group('client');

// Attach the client permissions to the client role
foreach ($client_permissions as $permission) {
    $this->aauth->allow_group($client_group, $permission);
}
