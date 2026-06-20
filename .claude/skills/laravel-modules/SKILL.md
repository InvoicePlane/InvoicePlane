---
name: laravel-modules
description: "Creates and modifies code inside a modular Laravel structure. Targets internachi/modular (modules as real Composer packages with src/). Activates when adding a new module, adding a model/factory/migration/resource/service to an existing module, registering a module with Filament, or when the user mentions modules, modular, or a specific module name."
license: MIT
metadata:
  author: project
---

# Laravel Modules

## Package Standard: `internachi/modular`

New modules use [`internachi/modular`](https://github.com/InterNACHI/modular).
Each module is a **real Composer package** with its own `composer.json`, resolved
from the root via a path repository. This makes modules portable, independently
testable, and properly autoloaded.

> **InvoicePlane-v2 exception:** This project was built with `nwidart/laravel-modules`
> and has **no `src/` layer** — the module root is the PSR-4 root. If you are
> working in this repo, skip the `src/` wrapper and use uppercase `Database/`,
> `Tests/` directly under the module root. See the nwidart section at the bottom.

---

## `internachi/modular` Directory Layout

```
modules/
  {name}/                        ← lowercase, kebab-case
    src/                         ← PSR-4 root
      {Name}ServiceProvider.php
      Models/
      Enums/
      Events/ Listeners/ Observers/
      Filament/
        Company/
          Resources/
            {Model}/
              {Model}Resource.php
              Pages/
                List{Model}.php
                Create{Model}.php
                Edit{Model}.php
              Schemas/
                {Model}Form.php
              Tables/
                {Model}sTable.php
      Http/
      Services/
      Traits/
    database/
      factories/
      migrations/
      seeders/
    tests/
      Feature/
      Unit/
    composer.json
```

---

## Module `composer.json`

```json
{
    "name": "app/{name}",
    "description": "The {Name} module",
    "type": "library",
    "require": {},
    "autoload": {
        "psr-4": {
            "Modules\\{Name}\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Modules\\{Name}\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Modules\\{Name}\\{Name}ServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

---

## Root `composer.json` Wiring

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./modules/*",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "app/core":     "*",
        "app/invoices": "*"
    }
}
```

Run `composer require app/{name}:*` whenever a new module is added.

---

## Namespace Convention

```
Modules\{Name}\
Modules\{Name}\Models\
Modules\{Name}\Filament\Company\Resources\{Model}\{Model}Resource
Modules\{Name}\Database\Factories\{Model}Factory
Modules\{Name}\Database\Seeders\{Model}Seeder
Modules\{Name}\Services\{Model}Service
Modules\{Name}\Tests\Feature\{Model}Test
```

---

## Service Provider

The service provider is auto-discovered via `composer.json`. It only needs to
load migrations and register observers:

```php
namespace Modules\Invoices;

use Illuminate\Support\ServiceProvider;

class InvoicesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'invoices');
    }
}
```

No manual entry in `config/app.php` — Composer's auto-discovery handles it.

---

## Filament Resource Registration

Add `->discoverResources()` per module in `CompanyPanelProvider`:

```php
->discoverResources(
    in: base_path('modules/invoices/src/Filament/Company/Resources'),
    for: 'Modules\\Invoices\\Filament\\Company\\Resources'
)
```

---

## Test Discovery

Configure `phpunit.xml` to pick up all module test directories:

```xml
<testsuite name="Unit">
    <directory>modules/*/tests/Unit</directory>
</testsuite>
<testsuite name="Feature">
    <directory>modules/*/tests/Feature</directory>
</testsuite>
```

---

## Adding a New Module (Checklist)

1. Create `modules/{name}/` with the directory tree above.
2. Write `modules/{name}/composer.json` (copy from existing module, change name/namespace).
3. Run `composer require app/{name}:*` from the project root.
4. Add `->discoverResources(...)` to `CompanyPanelProvider`.
5. Run `php artisan migrate` to pick up the new module's migrations.

---

## nwidart/laravel-modules (InvoicePlane-v2 Legacy)

InvoicePlane-v2 uses `nwidart/laravel-modules` ≥ v12. The key differences:

| | `internachi/modular` | `nwidart` (InvoicePlane-v2) |
|---|---|---|
| Module root | `modules/{name}/` | `Modules/{Name}/` |
| PSR-4 source | `src/` | module root directly |
| Namespace | `Modules\{Name}\` | `Modules\{Name}\` |
| Tests | `tests/` (lowercase) | `Tests/` (uppercase) |
| DB files | `database/` (lowercase) | `Database/` (uppercase) |
| Discovery | Composer path repo | `module.json` + manual provider |
| Registration | `composer require` | add to `config/app.php` |

When working in InvoicePlane-v2, drop the `src/` layer and follow uppercase
`Database/`, `Tests/` conventions. All else (service structure, Filament patterns,
test base classes) remains the same.
