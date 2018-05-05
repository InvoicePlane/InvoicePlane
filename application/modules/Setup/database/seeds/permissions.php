<?php

/**
 * Permission Seeder
 *
 * Inserts all permissions needed to run InvoicePlane
 */

// Admin-level permissions
$admin_permissions = [
  'access_system_settings',
];

// Default operations for basic users of the system
$default_permissions = [
  'access_dashboard',
];

// Permissions for any client-side operations
$client_permissions = [
  'access_clientarea',
];

// Save the permissions to the database
$permissions = array_merge($admin_permissions, $default_permissions, $client_permissions);

foreach ($permissions as $permission) {
  $this->aauth->create_perm($permission);
}
