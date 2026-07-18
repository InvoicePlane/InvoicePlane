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
echo "127.0.0.1 ivpl.local" | sudo tee -a /etc/hosts

docker compose -f docker-compose.yml up -d --build
```

Four separate services — `php` ([php-fpm](php-fpm/Dockerfile), **PHP 8.2**), `nginx`
([nginx](nginx/Dockerfile)), `db` ([mariadb](mariadb/Dockerfile)), and `phpmyadmin`
([phpmyadmin](phpmyadmin/Dockerfile)) — that bind-mount the working tree into the containers
rather than building the app into the image. Because of that, PHP dependencies and frontend
assets must already be installed on the host (`composer install`, `yarn install && yarn build`)
before starting it — the containers don't do that for you.

Unlike `compose.yml`, `ipconfig.php` doesn't need to exist beforehand: [`php-fpm`'s
`dev-entrypoint.sh`](php-fpm/dev-entrypoint.sh) generates one from the environment variables
already set in `docker-compose.yml`, but — deliberately unlike
[`compose.yml`'s entrypoint](entrypoint.sh) — only if `ipconfig.php` doesn't already exist, so
any manual edits you make afterward are never overwritten. It also does **not** set
`DISABLE_SETUP`, so the web setup wizard at `http://ivpl.local/index.php/setup` stays reachable
— useful since this is the container you'd use to develop/test InvoicePlane itself, setup wizard
included. It still runs pending migrations automatically on every start (best-effort — it won't
stop the container from starting if the database isn't initialized yet).

If you'd rather configure by hand, `cp ipconfig.php.example ipconfig.php` and edit it *before*
`docker compose up` — the entrypoint will see it already exists and leave it alone.

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
