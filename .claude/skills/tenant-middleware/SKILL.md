---
name: tenant-middleware
description: "Understands and modifies the tenant resolution middleware chain. Activates when debugging tenant switching, company access, session-based tenant resolution, URL-based tenant identification, or when the user mentions ConfigureTenant, EnsureUserCanAccessCompany, SetTenantFromQueryString, search_code, or company switching."
license: MIT
metadata:
  author: project
---

# Tenant Middleware Chain

Three middlewares run in order on every company panel request. They are registered
as persistent tenant middleware in `CompanyPanelProvider`.

## 1. SetTenantFromQueryString

**Purpose:** Handle explicit `?tenant=<search_code>` in the URL (used when switching company).

- Reads the `tenant` query parameter (expects a `search_code` string)
- Looks up the company by `search_code`
- Checks the user has access (elevated role OR company membership)
- Sets Filament tenant and writes `company_id` to session
- Updates the `tenant` route parameter to the lowercase `search_code`

## 2. ConfigureTenant

**Purpose:** Resolve the active tenant from multiple sources and persist it.

Resolution order:
1. Route parameter (`{tenant}`)
2. Query string `?tenant=`
3. Session `current_company_id`
4. User's first company (fallback)

Writes resolved company to session and shares it with views.

## 3. EnsureUserCanAccessCompany

**Purpose:** Enforce that the resolved tenant is accessible to the authenticated user.

- Elevated roles (`super_admin`, `admin`, `assist`) bypass — they can access all companies.
- Regular users must have the company in their `companies()` pivot relationship.
- Aborts 403 if the user has no access.

## Company Identification

Tenants are identified in URLs by `search_code` (a short alphanumeric string),
not by numeric `id`. The session stores the numeric `id` (`current_company_id`).

```php
// URL: /company/invoices?tenant=ivplv2
// Session: current_company_id = 22
// Model: Company::where('search_code', 'ivplv2')->first() → id=22
```

## Switching Companies

The "Switch Company" user menu action redirects with `?tenant=<search_code>`:

```php
Action::make('switch-company')
    ->modalContent(fn () => view('filament.company.widgets.switch-company-table'))
```

The Livewire component inside that modal dispatches a redirect to the new tenant's URL.

## Testing Tenant Switching

```php
Livewire::actingAs($this->user)
    ->test(SwitchCompanyComponent::class)
    ->callAction('switch', ['company_id' => $otherCompany->id])
    ->assertRedirect(route('filament.company.home', ['tenant' => $otherCompany->search_code]));
```


## Single Source of Truth

Tenant resolution belongs exclusively in the tenant middleware chain.

Controllers, Resources, Pages, Services, and Models must never independently
resolve the active tenant from the request, session, or URL.

They must rely on:

- Filament::getTenant()
- injected Company model
- resolved route parameter

Duplicating tenant resolution logic is an architectural defect.

## Fix-One-Fix-All

If one middleware requires modification due to a tenant resolution bug,
review all three tenant middlewares for equivalent logic and consistency.

Tenant resolution behavior must remain uniform across the entire middleware chain.
