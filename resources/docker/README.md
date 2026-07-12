# Docker Setup

This repository ships two different ways to run InvoicePlane in Docker. They are not
interchangeable — pick the one that matches what you're trying to do.

## `compose.yml` — self-contained (recommended)

```bash
docker compose up -d --build
```

Builds a single image from [`resources/docker/Containerfile`](Containerfile): a multi-stage
build that runs `composer install` and `yarn build` for you, then serves the app with Apache on
**PHP 8.2** (matching the PHP version used across this repo's CI — lint, Pint, PHPUnit, and the
release build). Configuration is entirely through environment variables — no `ipconfig.php`
needed; the [entrypoint](entrypoint.sh) generates it, disables the web setup wizard
(`DISABLE_SETUP=true`), and runs pending migrations automatically on every start. See
[CONTAINER_DEPLOYMENT.md](../../.github/docs/CONTAINER_DEPLOYMENT.md) for the full list of
variables.

Uploads, session/cache storage, and the database persist across restarts via named volumes
(`invoiceplane-uploads`, `invoiceplane-storage`, `invoiceplane-db`).

This is what's used for local development/testing and is tested in CI on every change to the
Docker setup (see `.github/workflows/docker.yml`).

## `docker-compose.yml` — separated dev services (legacy)

```bash
composer install
yarn install && yarn build
cp ipconfig.php.example ipconfig.php
# edit ipconfig.php: set IP_URL and DB_* to match the values below

docker compose -f docker-compose.yml up -d --build
```

Four separate services — `php` ([php-fpm](php-fpm/Dockerfile), **PHP 8.1**), `nginx`
([nginx](nginx/Dockerfile)), `db` ([mariadb](mariadb/Dockerfile)), and `phpmyadmin`
([phpmyadmin](phpmyadmin/Dockerfile)) — that bind-mount the working tree into the containers
rather than building the app into the image. Because of that, PHP dependencies and frontend
assets must already be installed on the host (`composer install`, `yarn install && yarn build`)
and `ipconfig.php` must already exist before starting it — the containers don't do any of that
for you. There's also no automated migration step here; run the setup wizard at
`http://localhost/index.php/setup` to initialize the database.

> **PHP 8.1 note:** this is one version behind the 8.2 used everywhere else in this repo's CI
> (lint, Pint, PHPUnit, the release build, and `compose.yml` above), and sits right at the floor
> `endroid/qr-code` requires (`^8.1`) with no headroom. It works today, but a future dependency
> bump could break it with no CI coverage to catch it. Kept as-is since this file mirrors a
> historical setup rather than active tooling — flag it if it should be bumped to 8.2 too.

Default database credentials baked into the compose file: `DB_HOSTNAME=db`,
`DB_USERNAME=ipdevdb`, `DB_PASSWORD=ipdevdb`, `DB_DATABASE=invoiceplane_db`. phpMyAdmin is
available at `http://localhost:8081`. The `invoiceplane-db` volume persists database data across
restarts; `uploads`/`storage` are bind-mounted from the working tree, so they persist there too.

This predates the Containerfile-based approach above. It's kept because the individual
Dockerfiles are still useful for developing against a specific service in isolation, and it's
also tested in CI (see `.github/workflows/docker.yml`) — but for a normal "just run the app"
workflow, use `compose.yml` instead.
