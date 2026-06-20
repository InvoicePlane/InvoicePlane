---
name: user-auth-fields
description: "Works with the User model's non-standard authentication fields. Activates when writing queries, factories, tests, or seeders that reference the user's email, name, or password; or when the user mentions user_email, user_name, user_password, authentication, login, or the User model."
license: MIT
metadata:
  author: project
---

# User Authentication Fields

This app's `users` table does NOT use Laravel's default `name`, `email`, and
`password` column names. All three are prefixed with `user_`:

| Laravel default | This app |
|-----------------|----------|
| `name` | `user_name` |
| `email` | `user_email` |
| `password` | `user_password` |

## Model Overrides

The `User` model overrides the auth contract methods:

```php
public function getAuthIdentifierName(): string
{
    return 'user_name';
}

public function getAuthPassword(): string
{
    return 'user_password';
}
```

## Never Use the Default Column Names

```php
// ✗ WRONG — will cause "Column not found" on MySQL
User::factory()->create(['name' => 'Test', 'email' => 'test@example.com']);

// ✓ CORRECT
User::factory()->create(['user_name' => 'Test', 'user_email' => 'test@example.com']);
```

This includes seeders, tests, and any `User::create()` call.

## Factory Definition

```php
public function definition(): array
{
    return [
        'user_name'          => fake()->name(),
        'user_email'         => fake()->unique()->safeEmail(),
        'user_password'      => Hash::make('password'),
        'user_active'        => fake()->boolean(90),
        'user_all_clients'   => fake()->boolean(90),
        'user_date_created'  => now(),
        'user_date_modified' => now(),
    ];
}
```

## Additional Non-Standard Fields

| Standard concept | This app's column |
|------------------|-------------------|
| Timestamps | Manual: `user_date_created`, `user_date_modified` |
| Active flag | `user_active` (boolean) |
| `$timestamps` | `false` — managed manually |

## Filament Name Display

Filament uses `getFilamentName()` not `name`:

```php
public function getFilamentName(): string
{
    return $this->user_name ?? $this->user_email ?? 'User';
}
```

---

## Authentication Queries

Never query using:

email
name
password

Always use:

user_email
user_name
user_password

including:

- validation rules
- login logic
- factories
- tests
- seeders
- authentication providers

---

## Fix-One-Fix-All

If one occurrence of `email`, `name`, or `password` is corrected to the
application's custom fields, search for equivalent usages throughout the
repository and update them consistently.
