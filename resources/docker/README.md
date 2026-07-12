# Docker Setup

This repository ships two different ways to run InvoicePlane in Docker. They are not
interchangeable — pick the one that matches what you're trying to do.

## `compose.yml` — self-contained (recommended)

```bash
docker compose up -d --build
```

Builds a single image from [`resources/docker/Containerfile`](Containerfile): a multi-stage
build that runs `composer install` and `yarn build` for you, then serves the app with Apache.
Configuration is entirely through environment variables — no `ipconfig.php` needed. See the
[Container Deployment](../../README.md#container-deployment) section of the main README for
the full list of variables.

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

Four separate services — `php` ([php-fpm](php-fpm/Dockerfile)), `nginx`
([nginx](nginx/Dockerfile)), `db` ([mariadb](mariadb/Dockerfile)), and `phpmyadmin`
([phpmyadmin](phpmyadmin/Dockerfile)) — that bind-mount the working tree into the containers
rather than building the app into the image. Because of that, PHP dependencies and frontend
assets must already be installed on the host (`composer install`, `yarn install && yarn build`)
and `ipconfig.php` must already exist before starting it — the containers don't do any of that
for you.

Default database credentials baked into the compose file: `DB_HOSTNAME=db`,
`DB_USERNAME=ipdevdb`, `DB_PASSWORD=ipdevdb`, `DB_DATABASE=invoiceplane_db`. phpMyAdmin is
available at `http://localhost:8081`.

This predates the Containerfile-based approach above. It's kept because the individual
Dockerfiles are still useful for developing against a specific service in isolation, and it's
also tested in CI (see `.github/workflows/docker.yml`) — but for a normal "just run the app"
workflow, use `compose.yml` instead.
