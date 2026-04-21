.PHONY: test test-filter test-stop test-unit test-feature

test:
	vendor/bin/phpunit

test-filter:
	@if [ -z "$(FILTER)" ]; then \
		echo "Usage: make test-filter FILTER=TestName"; \
		exit 1; \
	fi
	vendor/bin/phpunit --filter "$(FILTER)"

test-stop:
	@if [ -z "$(FILTER)" ]; then \
		echo "Usage: make test-stop FILTER=TestName"; \
		exit 1; \
	fi
	vendor/bin/phpunit --filter "$(FILTER)" --stop-on-failure --stop-on-error

test-unit:
	vendor/bin/phpunit tests/Unit

test-feature:
	vendor/bin/phpunit tests/Feature
