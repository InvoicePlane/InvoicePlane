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
Docker setup (see `.github/workflows/compose-yml-test.yml`).

## `docker-compose.yml` — separated dev services (legacy)

```bash
composer install
yarn install && yarn build
cp ipconfig.php.example ipconfig.php
# edit ipconfig.php: set IP_URL=http://ivpl.local and DB_* to match the values below

echo "127.0.0.1 ivpl.local" | sudo tee -a /etc/hosts

docker compose -f docker-compose.yml up -d --build
```

Four separate services — `php` ([php-fpm](php-fpm/Dockerfile), **PHP 8.2**), `nginx`
([nginx](nginx/Dockerfile)), `db` ([mariadb](mariadb/Dockerfile)), and `phpmyadmin`
([phpmyadmin](phpmyadmin/Dockerfile)) — that bind-mount the working tree into the containers
rather than building the app into the image. Because of that, PHP dependencies and frontend
assets must already be installed on the host (`composer install`, `yarn install && yarn build`)
and `ipconfig.php` must already exist before starting it — the containers don't do any of that
for you. There's also no automated migration step here; run the setup wizard at
`http://ivpl.local/index.php/setup` to initialize the database.

> **Hostname:** [`nginx/invoiceplane.conf`](nginx/invoiceplane.conf) sets `server_name
> ivpl.local` — add `127.0.0.1 ivpl.local` to `/etc/hosts` and use that hostname (not
> `localhost`) in your browser and in `IP_URL`. `.local` names don't resolve without an explicit
> hosts entry or mDNS. Since this is the only nginx server block, requests to `localhost` do
> still get routed here — but `IP_URL` (and anything InvoicePlane derives from it: redirects,
> generated links, cookies) will be wrong unless you access it as `ivpl.local`.

Default database credentials baked into the compose file: `DB_HOSTNAME=db`,
`DB_USERNAME=ipdevdb`, `DB_PASSWORD=ipdevdb`, `DB_DATABASE=invoiceplane_db`. phpMyAdmin is
available at `http://localhost:8081`. The `invoiceplane-db` volume persists database data across
restarts; `uploads`/`storage` are bind-mounted from the working tree, so they persist there too.

This predates the Containerfile-based approach above. It's kept because the individual
Dockerfiles are still useful for developing against a specific service in isolation, and it's
also tested in CI (see `.github/workflows/docker-compose-yml-test.yml`) — but for a normal
"just run the app" workflow, use `compose.yml` instead.
