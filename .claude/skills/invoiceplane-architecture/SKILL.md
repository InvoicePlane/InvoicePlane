# InvoicePlane Architecture

## Stack

InvoicePlane v1 runs on **CodeIgniter 3** with the **WireDesignz HMVC** extension
(`application/third_party/MX/`). It is **not Laravel**. The ambition is to apply
Laravel-quality standards (clean code, typed PHP, PHPUnit, structured bootstrap)
while staying on the CI3 runtime.

## Directory Layout

```
/
├── public/                  # Webroot — Apache/Nginx points here
│   ├── index.php            # Front controller (defines FCPATH, boots CI)
│   ├── .htaccess            # URL rewriting to index.php
│   └── assets/              # Compiled/built-only; never edit by hand
│       └── core/
│           ├── css/         # Output of SCSS compilation
│           ├── js/          # Minified/concatenated JS
│           └── fonts/       # Copied from node_modules
├── resources/
│   └── assets/              # Source assets (edit these)
│       ├── core/
│       │   ├── sass/        # SCSS source
│       │   ├── js/          # Hand-written JS (scripts.js, jquery-ui.js, paypal.js)
│       │   └── css/         # Static CSS (custom.css, custom-pdf.css, paypal.css)
│       ├── invoiceplane/    # Theme SCSS / CSS
│       └── invoiceplane_blue/
├── application/
│   ├── config/              # CI3 config files (database, routes, autoload, …)
│   ├── modules/             # HMVC modules (see Module Structure below)
│   ├── third_party/MX/      # WireDesignz HMVC extension
│   └── views/               # Shared (non-module) views
├── bootstrap/
│   ├── kernel.php           # Shared: autoload, dotenv, CI3 constants (web + test)
│   ├── app.php              # Full CI3 + MX boot; used by integration tests
│   ├── bootstrap.php        # PHPUnit bootstrap; blocks MX autoload until CI ready
│   ├── constants.php        # Standalone constants (legacy; prefer kernel.php)
│   ├── env.php              # env() / env_bool() helpers (legacy)
│   └── helpers.php          # Misc PHP helpers autoloaded by Composer
├── vendor/
│   └── pocketarc/codeigniter/system/   # CI3 core (BASEPATH)
└── tests/
    ├── bootstrap.php        # Composer test entry point → kernel.php + integration bootstrap
    └── Integration/
```

## CI3 Constants

| Constant    | Value                                     | Set by            |
|-------------|-------------------------------------------|-------------------|
| `FCPATH`    | `public/` (absolute, trailing slash)      | `public/index.php` first; `kernel.php` fallback |
| `APPPATH`   | project root + `/application/`            | `kernel.php`      |
| `BASEPATH`  | `vendor/pocketarc/codeigniter/system/`    | `kernel.php`      |
| `VIEWPATH`  | `APPPATH . 'views/'`                      | `kernel.php`      |

`public/index.php` **must** define `FCPATH` before including `bootstrap/kernel.php`
so the `defined()` guard in kernel.php does not override it.

## Module Structure (HMVC)

Each feature lives in `application/modules/{name}/`:

```
application/modules/invoices/
├── controllers/
├── models/
├── views/
└── libraries/
```

Route: `/{module}/{method}` resolves automatically via MX Router.
Cross-module calls: `$this->load->module('quotes')`.

## Asset Pipeline

Source → compile → serve:

```
resources/assets/**/*.scss   →  grunt sass  →  public/assets/**/*.css
resources/assets/core/js/    →  grunt concat/uglify  →  public/assets/core/js/
node_modules/font-awesome/   →  grunt copy  →  public/assets/core/fonts/
```

Commands:
- `grunt dev-build` — expanded CSS, unminified JS, no watch
- `grunt dev` — same + file watcher
- `grunt build` (default) — compressed CSS, minified JS

## Bootstrap Flow (Web)

```
Apache → public/index.php
         ↳ define FCPATH = public/
         ↳ require bootstrap/kernel.php
              ↳ vendor/autoload.php
              ↳ Dotenv (ipconfig.php)
              ↳ ENVIRONMENT / APPPATH / BASEPATH / VIEWPATH constants
         ↳ require BASEPATH . core/CodeIgniter.php
```

## Bootstrap Flow (PHPUnit)

```
phpunit.xml → tests/bootstrap.php
              ↳ bootstrap/kernel.php  (FCPATH falls back to public/)
              ↳ tests/Integration/bootstrap.php
                   ↳ bootstrap/app.php  (full CI3 + MX boot)
```

## Integrations Module

Provider integrations live in `application/modules/integrations/`:
- One library class per provider (e.g. `Letspe pol_client.php`, `Qonto_client.php`)
- All HTTP calls via `RequestMethod` enum — see `api-client-conventions` skill
- Responses logged to `ip_merchant_responses` (unified log, absorbs `ip_einvoice_responses`)
- Driver enum: `application/modules/integrations/libraries/MerchantResponseDriver.php`
  covers `PayPal`, `Stripe`, `SuperPdp`, `Qonto`, `LetsPeppol`
