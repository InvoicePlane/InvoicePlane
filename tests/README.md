# InvoicePlane Tests

This directory contains unit tests for InvoicePlane.

## Setup

To run the tests, you first need to install PHPUnit:

```bash
composer require --dev phpunit/phpunit
```

## Running Tests

Run all tests:

```bash
vendor/bin/phpunit
```

Run specific test file:

```bash
vendor/bin/phpunit tests/Unit/Helpers/FileSecurityHelperTest.php
```

Run tests with coverage:

```bash
vendor/bin/phpunit --coverage-html coverage/
```

## Test Structure

```
tests/
├── bootstrap.php                    # Bootstrap file for PHPUnit
├── Unit/                            # Unit tests
│   └── Helpers/                     # Helper function tests
│       └── FileSecurityHelperTest.php
└── README.md                        # This file
```

## Writing Tests

Follow these guidelines when writing tests:

1. **Naming**: Test files should be named `*Test.php`
2. **Structure**: Tests should extend `PHPUnit\Framework\TestCase`
3. **Method naming**: Test methods should start with `test_` and use snake_case
4. **Annotations**: Use PHPUnit 10+ attributes where possible
5. **Arrange-Act-Assert**: Structure tests in the AAA pattern

## Current Test Coverage

- **File Security Helper**: Tests for `validate_db_filename()` function covering:
  - Valid filename acceptance
  - Path traversal rejection
  - Absolute path rejection
  - Null byte rejection
  - Basename normalization
  - Multiple separator handling
  - Base directory edge cases
  - Symlink escape prevention
  - Hash preservation
  - Non-existent file handling

## Future Tests

Additional test coverage should be added for:

- Other file security helper functions
- Settings controller logo removal
- Upload model file operations
- Guest view attachment handling
