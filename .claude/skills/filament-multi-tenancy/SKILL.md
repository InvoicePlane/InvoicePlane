---
name: filament-multi-tenancy
description: "Handles Filament multi-tenancy: tenant scoping, TenantAware trait, observer behaviour, isScopedToTenant, and tenant switching. Activates when adding tenant-aware models, fixing company_id scoping, working with Filament::getTenant, debugging tenant isolation, or when the user mentions company scope, tenant, multi-tenancy, or company_id."
license: MIT
metadata:
  author: project
---

# Filament Multi-Tenancy

The tenant model is `Company`. Every per-company record carries `company_id`.

## TenantAware Trait

Models that belong to a company use the `TenantAware` trait:

```php
use Modules\Core\Traits\TenantAware;

class Invoice extends Model
{
    use TenantAware;
}
```

The trait registers a `creating` observer that sets `company_id` from
`Filament::getTenant()` **only when `company_id` is empty**:

```php
static::creating(function ($model) {
    if (empty($model->company_id)) {
        $tenant = Filament::getTenant();
        if ($tenant) {
            $model->company_id = $tenant->id;
        }
    }
});
```

## BaseResource Automatic Filtering

All module resources extend `BaseResource`, which scopes the Eloquent query to
the current tenant and injects `company_id` on create:

```php
// Modules/core/src/Filament/Resources/BaseResource.php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->when(Filament::getTenant(), fn ($q, $t) => $q->where('company_id', $t->id));
}
```

Do NOT add manual `company_id` filtering in resources that extend `BaseResource` —
it is already handled.

## The Company Resource Exception

`Company` IS the tenant. It must NOT be scoped to itself:

```php
class CompanyResource extends Resource
{
    protected static bool $isScopedToTenant = false;
    protected static ?string $tenantOwnershipRelationshipName = null;
}
```

Any model that should NOT be tenant-scoped (global settings, email templates, etc.)
also sets `$isScopedToTenant = false`.

## observeTenancyModelCreation Trap

Filament's `observeTenancyModelCreation` walks every `BelongsTo` relationship on a
model and calls `->associate($tenant)` when creating. This means:

- If a model has a `BelongsTo` pointing to `Company` (even indirectly), Filament
  will set that FK to the current tenant's id.
- A self-referential `BelongsTo` on the Company model itself will cause
  `UNIQUE constraint failed: companies.id` because Filament sets `id = currentTenant->id`
  on every new Company.

**Fix:** Remove bogus self-referential relationships and set `$isScopedToTenant = false`
on the offending resource.

## Tenant Switching in Tests

When a test creates records for multiple tenants, switch the active tenant before
creating each set — otherwise `TenantAware` assigns all records to the first tenant:

```php
$companyA = $this->company; // already set in setUp
$companyB = Company::factory()->create();

// Create companyA records (tenant already set to companyA)
$invoiceA = Invoice::factory()->create(['company_id' => $companyA->id, ...]);

// Switch tenant before creating companyB records
Filament::setTenant($companyB, isQuiet: true);
$invoiceB = Invoice::factory()->create(['company_id' => $companyB->id, ...]);

// Restore original tenant
Filament::setTenant($companyA, isQuiet: true);
```

## Tenant Middleware Stack

See the `tenant-middleware` skill for the full middleware chain. In short: three
persistent middlewares run on every company panel request in this order:
`SetTenantFromQueryString` → `ConfigureTenant` → `EnsureUserCanAccessCompany`.

## Tenant in Tests Setup

```php
protected function setUp(): void
{
    parent::setUp();
    Filament::setCurrentPanel(Filament::getPanel('company'));
    Filament::bootCurrentPanel();

    $this->company = Company::factory()->create();
    Filament::setTenant($this->company, isQuiet: true);

    $this->user = User::factory()->create();
    $this->user->companies()->syncWithoutDetaching([$this->company->id]);
}
```

## Services

Services must never assign company_id themselves when operating inside the
Filament company panel.

company_id is supplied by:

- TenantAware
- BaseResource
- explicit caller input

Services should only normalize or validate incoming values.

Hardcoding tenant assignment inside services creates hidden coupling.


## Fix-One-Fix-All

If one tenant-aware resource requires adjustment,
review every tenant-aware resource for the same pattern.

Tenant scoping inconsistencies are data isolation defects.
