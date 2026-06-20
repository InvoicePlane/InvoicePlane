# InvoicePlane v1

## What this project is

InvoicePlane v1 is a self-hosted open-source invoicing application built on
**CodeIgniter 3** (CI3) with the **WireDesignz HMVC** extension. It is **not
Laravel**. The ambition is to apply Laravel-quality standards — clean typed PHP,
PHPUnit test coverage, structured bootstrap, strict code style — while remaining
on the CI3 runtime.

## Directory structure

```
/
├── public/                  # Webroot — point Apache/Nginx here
│   ├── index.php            # Front controller
│   ├── .htaccess            # CI3 URL rewriting
│   └── assets/              # Built output only (never edit by hand)
├── resources/
│   └── assets/              # Source assets (SCSS, hand-written JS)
├── application/
│   ├── config/              # CI3 config (database, routes, autoload, …)
│   ├── modules/             # HMVC feature modules
│   ├── third_party/MX/      # WireDesignz HMVC extension
│   └── views/               # Shared views (non-module)
├── bootstrap/
│   ├── kernel.php           # Minimal shared bootstrap (web + test)
│   ├── app.php              # Full CI3 + MX boot for integration tests
│   └── bootstrap.php        # PHPUnit-specific bootstrap
├── vendor/
│   └── pocketarc/codeigniter/system/   # CI3 core (BASEPATH)
└── tests/
```

## Key constants

| Constant   | Value                                  |
|------------|----------------------------------------|
| `FCPATH`   | absolute path to `public/` with `/`   |
| `APPPATH`  | absolute path to `application/` with `/` |
| `BASEPATH` | `vendor/pocketarc/codeigniter/system/` with `/` |
| `VIEWPATH` | `APPPATH . 'views/'`                   |

`public/index.php` defines `FCPATH` before loading `bootstrap/kernel.php`.
The kernel uses `defined()` guards so it never overrides what the entry point set.

## Bootstrap

Web: `public/index.php` → `bootstrap/kernel.php` → `BASEPATH/core/CodeIgniter.php`

Tests: `tests/bootstrap.php` → `bootstrap/kernel.php` → `tests/Integration/bootstrap.php`
→ `bootstrap/app.php` (full CI3 + MX boot)

## Assets

Source files live in `resources/assets/`. The Grunt pipeline compiles them into
`public/assets/` which is the webroot asset path.

```
resources/assets/**/sass/*.scss  →  grunt sass  →  public/assets/**/css/*.css
resources/assets/core/js/        →  grunt concat/uglify  →  public/assets/core/js/
node_modules/font-awesome/       →  grunt copy  →  public/assets/core/fonts/
```

- `grunt build` (default) — production: compressed CSS, minified JS
- `grunt dev-build` — development: expanded CSS, unminified JS
- `grunt dev` — same as dev-build + watch

## Modules (HMVC)

Each feature lives under `application/modules/{name}/`:

```
application/modules/invoices/
├── controllers/
├── models/
├── views/
└── libraries/
```

Routes resolve automatically: `GET /invoices/index` → `Invoices::index()`.
Cross-module: `$this->load->module('quotes')`.

## Integrations module

Provider integrations are in `application/modules/integrations/`. Each provider
has its own library class. All HTTP is routed through the `RequestMethod` enum
(see `.claude/skills/api-client-conventions/`). Responses are stored in
`ip_merchant_responses`.

## Tests

PHPUnit 11. Test methods are named `it_{verb}_{object}` and carry the
`#[\PHPUnit\Framework\Attributes\Test]` attribute. Every test uses
`/* Arrange */` / `/* Act */` / `/* Assert */` comment structure.
See `.claude/skills/testing-conventions/`.

## Skills

All project-specific Claude Code skills are in `.claude/skills/`:

- `invoiceplane-architecture` — this project's structure (full detail)
- `api-client-conventions` — `RequestMethod` enum, no named HTTP wrappers
- `testing-conventions` — `it_` naming, Arrange/Act/Assert
- `no-conflict-markers` — never commit `<<<<<<<` markers
