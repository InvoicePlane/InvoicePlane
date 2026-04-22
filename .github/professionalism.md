# Bootstrap Professionalism Guide

## Problem

`vendor/bin/phpunit` (and any CLI entry-point) touches `index.php` indirectly
because the current bootstrap layer is a set of procedural flat files that repeat
constant and env setup logic across `bootstrap/kernel.php`, `bootstrap/app.php`,
`bootstrap/constants.php`, and `bootstrap/bootstrap.php`.

The result is:

- Constants like `FCPATH`, `APPPATH`, `BASEPATH` are defined in **four** places.
- `env()` / `env_bool()` helper functions are declared in **three** files.
- Boot order (CI core → MX → modules) is implicit and scattered.
- `index.php` knows about `BASEPATH` and CodeIgniter internals — coupling the
  HTTP entry-point to the framework's implementation details.
- Tests re-bootstrap on every run because there is no single, idempotent boot gate.

---

## Design Goals

| Principle | How it applies here |
|-----------|---------------------|
| **DRY** | Constants, env helpers, and path definitions live in exactly one place. |
| **SOLID – SRP** | Each class / file has one reason to change (env loading, constant definition, CI boot, etc.). |
| **SOLID – OCP** | Boot providers are registered as a list; adding a new bootstrap step means adding a provider, not editing a procedural file. |
| **SOLID – DIP** | `index.php` depends on the `Application` abstraction, not on CodeIgniter internals. |
| **Early Returns** | Guards (`if (defined / booted) return;`) are placed at the top of every method. |
| **Dynamic Programming** | Expensive work (path resolution, provider loading) is memoised so it only runs once per request/test run, regardless of how many times `boot()` is called. |

---

## Proposed Architecture

### 1. `Application` class — `bootstrap/Application.php`

```php
<?php



namespace Bootstrap;

/**
 * Lightweight application container that mimics Laravel's Application bootstrap
 * lifecycle without requiring the Laravel framework.
 *
 * Follows:
 *  - SRP  : owns only bootstrapping + path resolution
 *  - OCP  : new bootstrap steps are registered as providers
 *  - DIP  : index.php depends on this abstraction, not on CI3 internals
 *  - DRY  : constants / env helpers defined once, behind `defined()` guards
 *  - Early returns: every method bails at the top if already initialised
 */
final class Application
{
    private bool $booted = false;

    /** @var list<callable> */
    private array $providers = [];

    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    // -------------------------------------------------------------------------
    // Factory methods
    // -------------------------------------------------------------------------

    public static function createForHttp(string $basePath): self
    {
        return (new self($basePath))
            ->withProvider(new Providers\EnvProvider())
            ->withProvider(new Providers\ConstantsProvider())
            ->withProvider(new Providers\CiCoreProvider())
            ->withProvider(new Providers\MxProvider());
    }

    public static function createForConsole(string $basePath): self
    {
        return (new self($basePath))
            ->withProvider(new Providers\EnvProvider())
            ->withProvider(new Providers\ConstantsProvider());
    }

    // -------------------------------------------------------------------------
    // Provider registration (OCP)
    // -------------------------------------------------------------------------

    public function withProvider(BootProvider $provider): self
    {
        $this->providers[] = $provider;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Boot — idempotent (Dynamic Programming / early return)
    // -------------------------------------------------------------------------

    public function boot(): self
    {
        if ($this->booted) {
            return $this;   // already initialised — skip all work
        }

        foreach ($this->providers as $provider) {
            $provider->register($this);
        }

        $this->booted = true;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Path helpers (memoised — Dynamic Programming)
    // -------------------------------------------------------------------------

    public function basePath(string $path = ''): string
    {
        return $path === '' ? $this->basePath : $this->basePath . '/' . ltrim($path, '/');
    }

    public function appPath(string $path = ''): string
    {
        return $this->basePath('application' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }

    public function vendorPath(string $path = ''): string
    {
        return $this->basePath('vendor' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }

    // -------------------------------------------------------------------------
    // Entry-point delegation
    // -------------------------------------------------------------------------

    public function handleHttp(): void
    {
        $this->boot();
        // CI3 executes the HTTP cycle inside CodeIgniter.php via get_instance()
    }
}
```

### 2. `BootProvider` interface — `bootstrap/BootProvider.php`

```php
<?php



namespace Bootstrap;

interface BootProvider
{
    public function register(Application $app): void;
}
```

### 3. Example providers

**`bootstrap/Providers/EnvProvider.php`** — loads `ipconfig.php` once:

```php
<?php



namespace Bootstrap\Providers;

use Bootstrap\Application;
use Bootstrap\BootProvider;
use Dotenv\Dotenv;

final class EnvProvider implements BootProvider
{
    public function register(Application $app): void
    {
        $config = $app->basePath('ipconfig.php');

        if (! file_exists($config)) {
            return;   // early return — no config file in CI/testing environments
        }

        Dotenv::createImmutable($app->basePath(), 'ipconfig.php')->safeLoad();
    }
}
```

**`bootstrap/Providers/ConstantsProvider.php`** — defines all global CI3 constants once:

```php
<?php



namespace Bootstrap\Providers;

use Bootstrap\Application;
use Bootstrap\BootProvider;

final class ConstantsProvider implements BootProvider
{
    public function register(Application $app): void
    {
        // Early return per constant — idempotent (Dynamic Programming)
        defined('ENVIRONMENT') || define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production');
        defined('FCPATH')      || define('FCPATH',      $app->basePath() . '/');
        defined('APPPATH')     || define('APPPATH',     $app->appPath() . '/');
        defined('BASEPATH')    || define('BASEPATH',    $app->vendorPath('pocketarc/codeigniter/system/'));
        defined('VIEWPATH')    || define('VIEWPATH',    APPPATH . 'views/');
        defined('IP_DEBUG')    || define('IP_DEBUG',    filter_var($_ENV['ENABLE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

        $this->registerHelpers();
    }

    private function registerHelpers(): void
    {
        if (! function_exists('env')) {
            function env(string $key, mixed $default = null): mixed
            {
                return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
            }
        }

        if (! function_exists('env_bool')) {
            function env_bool(string $key, bool $default = false): bool
            {
                $raw = $_ENV[$key] ?? null;

                return $raw === null
                    ? $default
                    : filter_var($raw, FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (! function_exists('base_path')) {
            // Closure captures nothing — pure function, safe to reuse
            function base_path(string $path = ''): string
            {
                return $path === '' ? FCPATH : FCPATH . ltrim($path, '/');
            }
        }
    }
}
```

**`bootstrap/Providers/CiCoreProvider.php`** — loads CI3 core in the correct order:

```php
<?php



namespace Bootstrap\Providers;

use Bootstrap\Application;
use Bootstrap\BootProvider;

final class CiCoreProvider implements BootProvider
{
    public function register(Application $app): void
    {
        if (defined('CI_VERSION')) {
            return;   // early return — CI core already loaded
        }

        require_once BASEPATH . 'core/Common.php';
        require_once BASEPATH . 'core/Controller.php';
        require_once BASEPATH . 'core/Loader.php';
        require_once BASEPATH . 'core/CodeIgniter.php';
    }
}
```

**`bootstrap/Providers/MxProvider.php`** — loads WireDesignz MX (HMVC) only after CI core:

```php
<?php



namespace Bootstrap\Providers;

use Bootstrap\Application;
use Bootstrap\BootProvider;

final class MxProvider implements BootProvider
{
    public function register(Application $app): void
    {
        if (class_exists('MX_Modules', false)) {
            return;   // early return — MX already registered
        }

        require_once APPPATH . 'third_party/MX/Modules.php';
        require_once APPPATH . 'third_party/MX/Loader.php';
        require_once APPPATH . 'third_party/MX/Controller.php';
        require_once APPPATH . 'third_party/MX/Router.php';

        \Modules::$locations = [
            APPPATH . 'modules/' => APPPATH . 'modules/',
        ];
    }
}
```

### 4. Slimmed-down `index.php`

```php
<?php

(require_once __DIR__ . '/bootstrap/app.php')->handleHttp();
```

### 5. New `bootstrap/app.php`

```php
<?php



use Bootstrap\Application;

require_once __DIR__ . '/../vendor/autoload.php';

return Application::createForHttp(dirname(__DIR__));
```

### 6. `tests/bootstrap.php` — re-uses the same Application

```php
<?php



use Bootstrap\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Application::createForConsole(dirname(__DIR__))->boot();

define('IS_TESTING', true);
```

---

## Before / After Comparison

| Concern | Before | After |
|---------|--------|-------|
| Constant definitions | 4 files | 1 (`ConstantsProvider`) |
| `env()` helper declarations | 3 files | 1 (`ConstantsProvider`) |
| Boot idempotency guard | Global `CI_KERNEL_BOOTED` constant | `Application::$booted` flag |
| `index.php` knowledge | Knows `BASEPATH`, calls `require_once` on CI3 files | One line, delegates to `Application` |
| Adding a new boot step | Edit a procedural file | Implement `BootProvider`, add to factory |
| Test bootstrap | Separate procedural file with duplicated setup | `Application::createForConsole()->boot()` |

---

## Checklist for Implementors

- [ ] Move `bootstrap/Application.php`, `bootstrap/BootProvider.php`, and the
      `bootstrap/Providers/` directory to the repository root under a `bootstrap/`
      namespace registered in `composer.json` (`"Bootstrap\\": "bootstrap/"`).
- [ ] Delete `bootstrap/kernel.php`, `bootstrap/constants.php`, `bootstrap/env.php`,
      `bootstrap/helpers.php` after their logic has been migrated into providers.
- [ ] Update `tests/bootstrap.php` to call `Application::createForConsole()->boot()`.
- [ ] Run `vendor/bin/pint` to enforce code style on new files.
- [ ] Run `vendor/bin/phpunit` to confirm tests still pass.
- [ ] Run `php -l` on all changed PHP files.
