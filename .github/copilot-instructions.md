# Copilot Instructions

This repository is a Laravel 12 modular integration platform. Treat `Modules/*` as the real application.

## Canonical domain language
- **Account** = customer/tenant.
- **User** belongs to many accounts.
- **Application** = integration pairing, understood here as origin + goal.
- **Workspace** = account-specific installation of an application.
- **AppAdapter** = adapter wiring at application level.
- **WorkspaceAdapter** = adapter wiring at workspace level.
- **Endpoint** -> **AppEndpoint** -> **WorkspaceEndpoint** model the syncable resources from template to tenant instance.
- **SyncConfiguration** drives origin-to-goal sync behavior per workspace/endpoint.

## How syncs should work
- Pull data from the **origin** adapter.
- Transform into DTOs / normalized internal representations.
- Persist and/or push to the **goal** adapter.
- Run orchestration in **jobs**.
- Keep auth, API access, transformation, persistence, and orchestration separate.

## Coding rules
- Follow **SOLID**, **DRY**, **dynamic composition**, and **early returns** religiously.
- Prefer strategies, interfaces, handlers, repositories, DTOs, and jobs over branching-heavy procedural code.
- Do not leak raw external payloads into controllers, UI code, or unrelated layers.
- Keep tenant/workspace scoping explicit.
- Prefer extending module-specific implementations over adding `if provider == ...` logic.

## Auth rules
- Some adapters use OAuth2.
- Keep OAuth2 behavior strategy-based and workspace-scoped.
- Do not inline token refresh logic into repositories, controllers, or jobs.
- Reuse and extend the existing auth strategy tests when changing token behavior.

## Testing rules
- PHPUnit coverage for the OAuth2 flow already exists in `Modules/core/tests/Feature/Auth/OAuth2StrategyTest.php`.
- Reuse fixture-driven tests for adapters, DTOs, transformers, repositories, and sync flows.
- For sync changes, test pagination, duplicate prevention, failure handling, logging, and timestamp updates.

## Important caution
There are current inconsistencies across some model names, migration table names, and relationship wiring. Before changing schema or relations, inspect the matching model, migration, and existing test first. Prefer clarifying the canonical domain model instead of spreading ambiguity.

For the fuller guidance, start with `.junie/guidelines.md` and the files under `.junie/guidelines/`.
