# InvoicePlane v1.8 development Makefile
# ---------------------------------------------------------------------------
# InvoicePlane v1 is a CodeIgniter 3/HMVC application. There is no Artisan
# CLI, no Laravel test runner, and no framework migration command.
#
# Docker defaults match the local ivpldock workspace. Override them on the
# command line if your container name, user, or project path differs.

SHELL := /usr/bin/env bash
.SHELLFLAGS := -euo pipefail -c

.DEFAULT_GOAL := help

PHP               ?= php
COMPOSER          ?= composer
PHPUNIT           ?= vendor/bin/phpunit
PHPSTAN           ?= vendor/bin/phpstan
PHPSTAN_MEMORY    ?= 1G
PHPSTAN_TMPDIR    ?= .phpstan.cache/tmp

DOCKER_USER       ?= ivpldock
CONTAINER_NAME    ?= ivpldock-workspace-1
MARIADB_CONTAINER ?= mariadb
DOCKER_PROJECT_DIR ?= /var/www/projects/exprmt

DB_HOSTNAME       ?= mariadb
DB_PORT           ?= 3306
DB_DATABASE       ?= invoiceplane_test
DB_USERNAME       ?= root
DB_PASSWORD       ?= root

FILTER            ?=
SUITE             ?=

# -e XDEBUG_MODE=off: the workspace image ships Xdebug in "develop,debug" mode,
# whose step-debug "Could not connect to debugging client" notices are written to
# stdout mid-test and get surfaced by PHPUnit as spurious errors. CI runs without
# Xdebug, so turn it off here to match.
DOCKER_EXEC = docker exec -e XDEBUG_MODE=off --user=$(DOCKER_USER) $(CONTAINER_NAME)
DOCKER_EXEC_INTERACTIVE = docker exec -e XDEBUG_MODE=off -it --user=$(DOCKER_USER) $(CONTAINER_NAME)
DOCKER_ROOT_EXEC = docker exec $(CONTAINER_NAME)
MARIADB_EXEC = docker exec -i $$(docker ps -aqf "name=$(MARIADB_CONTAINER)")
MARIADB_EXEC_TTY = docker exec -t $$(docker ps -aqf "name=$(MARIADB_CONTAINER)")

PHPUNIT_ENV_CLEAN = env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD
PHPUNIT_ARGS = $(if $(FILTER),--filter "$(FILTER)") $(if $(SUITE),--testsuite "$(SUITE)")

.PHONY: help \
	install lint-php phpstan test test-filter test-suite test-custom-templates \
	db-prepare docker-db-prepare docker-test docker-test-filter docker-test-suite \
	docker-test-custom-templates docker-phpstan docker-lint-php docker-shell \
	docker-pint \
	status clean

help:
	@printf '%s\n' \
		'InvoicePlane v1.8 development targets' \
		'' \
		'Local:' \
		'  make install                         Composer install' \
		'  make test                            Run PHPUnit with the current ipconfig.php' \
		'  make test-filter FILTER=Name         Run PHPUnit with --filter' \
		'  make test-suite SUITE=Unit           Run one PHPUnit suite' \
		'  make test-custom-templates           Run custom-template allowlist/kernel tests' \
		'  make phpstan                         Run PHPStan' \
		'  make lint-php                        Syntax-check tracked PHP files' \
		'  make db-prepare                      Prepare local sandbox MariaDB' \
		'' \
		'Docker:' \
		'  make docker-db-prepare               Rebuild invoiceplane_test in MariaDB container' \
		'  make docker-test                     Prepare DB and run PHPUnit in workspace' \
		'  make docker-test-filter FILTER=Name  Prepare DB and run filtered PHPUnit' \
		'  make docker-test-suite SUITE=Feature Prepare DB and run one PHPUnit suite' \
		'  make docker-test-custom-templates    Run custom-template tests in workspace' \
		'  make docker-phpstan                  Run PHPStan in workspace' \
		'  make docker-pint                     Run Pint in workspace' \
		'  make docker-lint-php                 Syntax-check PHP files in workspace' \
		'  make docker-shell                    Open a workspace shell' \
		'' \
		'Important: PHPUnit is run with DB_* unset so bootstrap/kernel.php reads ipconfig.php.' \
		'Exported DB_* variables can mask DB-backed tests as skips in this repository.'

install:
	$(COMPOSER) install

lint-php:
	@git ls-files -z '*.php' | while IFS= read -r -d '' file; do [[ -f "$$file" ]] && $(PHP) -l "$$file"; done

phpstan:
	mkdir -p "$(PHPSTAN_TMPDIR)"
	TMPDIR="$(CURDIR)/$(PHPSTAN_TMPDIR)" $(PHPSTAN) analyse --memory-limit=$(PHPSTAN_MEMORY)

test:
	$(PHPUNIT_ENV_CLEAN) $(PHPUNIT) $(PHPUNIT_ARGS)

test-filter:
	@test -n "$(FILTER)" || { echo 'Usage: make test-filter FILTER=CustomTemplate'; exit 2; }
	$(MAKE) test FILTER="$(FILTER)"

test-suite:
	@test -n "$(SUITE)" || { echo 'Usage: make test-suite SUITE=Unit'; exit 2; }
	$(MAKE) test SUITE="$(SUITE)"

test-custom-templates:
	$(PHPUNIT_ENV_CLEAN) $(PHPUNIT) \
		tests/Unit/Core/CoreTest.php --filter 'it_(lists_a_custom|keeps_built_in|returns_only_built_ins|rejects_a_(path_traversal|php_extension)_custom|keeps_the_valid_name|wires_all_four_allowlist|populates_the_invoice_pdf_allowlist|defines_all_four_allowlist|defines_the_env_helper)'

db-prepare:
	bash tests/Support/sandbox-mariadb.sh

docker-db-prepare:
	$(MARIADB_EXEC_TTY) mariadb -u$(DB_USERNAME) -p$(DB_PASSWORD) \
		-e "SET GLOBAL sql_mode=''; DROP DATABASE IF EXISTS \`$(DB_DATABASE)\`; CREATE DATABASE \`$(DB_DATABASE)\` CHARACTER SET utf8;"
	@for file in application/modules/setup/sql/*.sql; do \
		echo "Importing $$file"; \
		$(MARIADB_EXEC) mariadb -u$(DB_USERNAME) -p$(DB_PASSWORD) --force $(DB_DATABASE) < "$$file"; \
	done
	$(MARIADB_EXEC) mariadb -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) < tests/Support/schema_fixups.sql
	$(DOCKER_ROOT_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && \
		cp ipconfig.php.example ipconfig.php && \
		sed -i \
			-e "s|^DB_HOSTNAME=.*|DB_HOSTNAME=$(DB_HOSTNAME)|" \
			-e "s|^DB_PORT=.*|DB_PORT=$(DB_PORT)|" \
			-e "s|^DB_DATABASE=.*|DB_DATABASE=$(DB_DATABASE)|" \
			-e "s|^DB_USERNAME=.*|DB_USERNAME=$(DB_USERNAME)|" \
			-e "s|^DB_PASSWORD=.*|DB_PASSWORD=$(DB_PASSWORD)|" \
			-e "s|^SETUP_COMPLETED=.*|SETUP_COMPLETED=true|" \
			-e "s|^ENCRYPTION_KEY=.*|ENCRYPTION_KEY=0123456789abcdef0123456789abcdef|" \
			ipconfig.php && \
		printf "\nDB_DRIVER=mysqli\nCSRF_PROTECTION=false\n" >> ipconfig.php && \
		chown "$(DOCKER_USER):$(DOCKER_USER)" ipconfig.php && \
		DB_HOSTNAME="$(DB_HOSTNAME)" DB_PORT="$(DB_PORT)" DB_DATABASE="$(DB_DATABASE)" \
			DB_USERNAME="$(DB_USERNAME)" DB_PASSWORD="$(DB_PASSWORD)" \
			php tests/Support/seed-test-db.php'

docker-test: docker-db-prepare
	$(DOCKER_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && $(PHPUNIT_ENV_CLEAN) $(PHPUNIT) $(PHPUNIT_ARGS)'

docker-test-filter:
	@test -n "$(FILTER)" || { echo 'Usage: make docker-test-filter FILTER=CustomTemplate'; exit 2; }
	$(MAKE) docker-test FILTER="$(FILTER)"

docker-test-suite:
	@test -n "$(SUITE)" || { echo 'Usage: make docker-test-suite SUITE=Unit'; exit 2; }
	$(MAKE) docker-test SUITE="$(SUITE)"

docker-test-custom-templates:
	$(DOCKER_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && $(PHPUNIT_ENV_CLEAN) $(PHPUNIT) tests/Unit/Core/CoreTest.php --filter "it_(lists_a_custom|keeps_built_in|returns_only_built_ins|rejects_a_(path_traversal|php_extension)_custom|keeps_the_valid_name|wires_all_four_allowlist|populates_the_invoice_pdf_allowlist|defines_all_four_allowlist|defines_the_env_helper)"'

docker-phpstan:
	$(DOCKER_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && mkdir -p .phpstan.cache/tmp && TMPDIR="$$PWD/.phpstan.cache/tmp" $(PHPSTAN) analyse --no-progress --memory-limit=$(PHPSTAN_MEMORY)'

docker-pint:
	$(DOCKER_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && vendor/bin/pint'

docker-lint-php:
	$(DOCKER_EXEC) bash -lc 'cd "$(DOCKER_PROJECT_DIR)" && git ls-files -z "*.php" | while IFS= read -r -d "" file; do [[ -f "$$file" ]] && $(PHP) -l "$$file"; done'

docker-shell:
	$(DOCKER_EXEC_INTERACTIVE) bash

status:
	git status --short --branch

clean:
	rm -rf .phpunit.cache phpstan.json phpstan-report.md
