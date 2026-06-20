---
name: non-standard-pks
description: "Works with models that have non-standard primary key names. Activates when writing factories, tests, relationships, or seeders for models that use a custom primary key instead of id."
license: MIT
metadata:
  author: project
---

# Non-Standard Primary Keys

Most models in this app use `id` as their primary key (Laravel default). Only a
handful declare a custom `$primaryKey`. **Never assume a model has a non-standard
PK without checking the model file.**

## Confirmed Non-Standard PKs

| Model | Table | Primary Key |
|-------|-------|-------------|
| `ClientCustom` | `client_custom` | `client_custom_id` |
| `Import` | `imports` | `import_id` |

All other models should be assumed to use `id` unless their model file explicitly
declares `protected $primaryKey = '...'`.

## Accessing the PK Safely

Use `$model->getKey()` for generic access. Use the named attribute only when
you know the model's actual PK:

```php
$custom->client_custom_id   // ✓ typed access for ClientCustom
$custom->getKey()           // ✓ generic access
$custom->id                 // ✗ returns null — ClientCustom uses client_custom_id
```

## Factories: Pass the FK by Name

When creating related records that reference a non-standard PK, pass the FK
column explicitly:

```php
// ClientCustom's PK is client_custom_id, not id
SomeRelated::factory()->create([
    'client_custom_id' => $custom->client_custom_id,
]);
```

## Filament Edit Page

The `record` parameter expects the PK value:

```php
Livewire::actingAs($this->user)
    ->test(EditClientCustom::class, [
        'record' => $custom->client_custom_id,  // not $custom->id
        'tenant' => $this->company->search_code,
    ])
```

## Model Definition

Always declare `$primaryKey` explicitly for non-standard models:

```php
class ClientCustom extends Model
{
    protected $table      = 'client_custom';
    protected $primaryKey = 'client_custom_id';
    public    $timestamps = false;
}
```

## Adding a New Non-Standard PK

When you introduce a model with a non-standard PK, update this skill's
**Confirmed Non-Standard PKs** table immediately.

## Timestamps

Almost all models in this app have `$timestamps = false` — they manage date
columns manually. Do not assume `created_at`/`updated_at` exist.
