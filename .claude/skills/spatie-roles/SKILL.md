---
name: spatie-roles
description: "Implements role-based authorization using Spatie Laravel Permission. Activates when assigning roles, checking permissions, seeding roles, writing canAccessPanel logic, or when the user mentions roles, permissions, super_admin, client_admin, UserRole, assignRole, hasRole, or Spatie."
license: MIT
metadata:
  author: project
---

# Spatie Roles

## UserRole Enum

All roles are defined in `Modules\Core\Enums\UserRole`:

```php
enum UserRole: string
{
    case SUPER_ADMIN    = 'super_admin';   // global — no company required
    case ADMIN          = 'admin';          // elevated
    case ASSIST         = 'assist';         // elevated, limited
    case CUSTOMER_ADMIN = 'client_admin';   // company admin
    case CUSTOMER       = 'client';         // regular user
}
```

Helper methods:
- `UserRole::elevated()` → `['super_admin', 'admin', 'assist']`
- `UserRole::nonAdmin()` → `['client_admin', 'client']`
- `UserRole::values()` → all values

**Always use the enum**, never hardcode the string value.

## Panel Access Logic

`User::canAccessPanel(Panel $panel)` is the Filament gate:

```php
public function canAccessPanel(Panel $panel): bool
{
    // Elevated roles can access any panel
    if ($this->hasRole(UserRole::SUPER_ADMIN->value)
        || $this->hasRole(UserRole::ADMIN->value)
        || $this->hasRole(UserRole::ASSIST->value)) {
        return true;
    }

    // Company-level users only see the company panel
    if ($panel->getId() === 'company') {
        return $this->hasRole(UserRole::CUSTOMER_ADMIN->value)
            || $this->hasRole(UserRole::CUSTOMER->value);
    }

    return false;
}
```

## Seeding Roles

Always seed roles before assigning them. `Role::firstOrCreate` is idempotent:

```php
foreach (UserRole::cases() as $role) {
    Role::firstOrCreate(
        ['name' => $role->value],
        ['guard_name' => 'web'],
    );
}
```

## Assigning Roles

```php
$user->assignRole(UserRole::SUPER_ADMIN->value);
$user->assignRole(UserRole::CUSTOMER_ADMIN->value);
```

## Checking Roles

```php
$user->hasRole(UserRole::SUPER_ADMIN->value);
$user->isSuperAdmin(); // shorthand defined on User model
```

## Super Admin

The super admin is a single global user, not tied to any company. Created in the
seeder as:

```php
$superAdmin = User::factory()->create([
    'user_name'   => 'Super Admin',
    'user_email'  => 'superadmin@example.com',
    'user_active' => true,
]);
$superAdmin->assignRole(UserRole::SUPER_ADMIN->value);
```

Super admins bypass `canAccessTenant()` via `isSuperAdmin()`:

```php
public function canAccessTenant(Model $tenant): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }
    return $this->companies()->whereKey($tenant->getKey())->exists();
}
```

## Company Users

Per company: 2 `client_admin` + 8 `client` (set by `UsersSeeder`).
Company admins are regular users who have elevated access within their company.
They do NOT have cross-company access.

## Guard Name

The Spatie permission guard is `web`. Always pass `guard_name: 'web'` when creating
roles/permissions programmatically.

---

## Authorization

Never authorize based on role strings directly when a policy,
permission, or helper method already exists.

Prefer:

- can()
- policies
- helper methods
- enum methods

over repeated role checks.

Duplicate authorization logic is a security risk.

---

## Enum Rule

Never compare:

'user_role' == 'admin'

Always compare against:

UserRole::ADMIN->value
