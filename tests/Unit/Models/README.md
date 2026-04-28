# Model Testing Guide

This guide documents the pattern for creating unit tests for InvoicePlane models.

## Overview

All models in InvoicePlane extend either `Response_Model` or `CI_Model` and reside in `application/modules/*/models/Mdl_*.php`.

Unit tests for models should be placed in `tests/Unit/Models/` and follow the naming convention `Mdl_<model_name>Test.php`.

## Test Template

```php
<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_<model_name> model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_<ModelName>::class)]
class Mdl_<model_name>Test extends CiTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->CI->load->model('<module>/mdl_<model_name>');
        $this->model = $this->CI->mdl_<model_name>;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_<table_name>', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_<table_name>.<pk_field>', $this->model->primary_key);
    }

    #[Test]
    public function it_has_validation_rules(): void
    {
        $this->assertTrue(method_exists($this->model, 'validation_rules'));
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        // Add specific field assertions as needed
        // $this->assertArrayHasKey('field_name', $rules);
    }

    #[Test]
    public function it_extends_correct_base_class(): void
    {
        // Use either 'Response_Model' or 'CI_Model' based on the model
        $this->assertInstanceOf('Response_Model', $this->model);
    }
}
```

## Sample Tests Created

The following sample tests have been created to demonstrate the pattern:

- `Mdl_clientsTest.php` - Tests for clients model
- `Mdl_settingsTest.php` - Tests for settings model  
- `Mdl_usersTest.php` - Tests for users model
- `Mdl_invoice_tax_ratesTest.php` - Tests for invoice tax rates model
- `Mdl_templatesTest.php` - Tests for templates model

## Models Still Needing Tests

The following 36 models still need unit tests created following the above pattern:

### Invoice Models
- Mdl_invoice_sumex
- Mdl_item_amounts
- Mdl_invoices_recurring
- Mdl_items
- Mdl_invoice_amounts
- Mdl_invoice_groups

### Quote Models
- Mdl_quote_custom
- Mdl_quote_amounts
- Mdl_quotes
- Mdl_quote_tax_rates
- Mdl_quote_items
- Mdl_quote_item_amounts

### Client Models
- Mdl_client_notes
- Mdl_client_custom

### Payment Models
- Mdl_payment_logs
- Mdl_payments
- Mdl_payment_methods
- Mdl_payment_custom

### Product Models
- Mdl_families
- Mdl_products
- Mdl_units

### Project Models
- Mdl_projects
- Mdl_tasks

### Other Models
- Mdl_user_clients
- Mdl_import
- Mdl_setup
- Mdl_reports
- Mdl_email_templates
- Mdl_custom_values
- Mdl_sessions
- Mdl_uploads
- Mdl_invoice_custom
- Mdl_user_custom
- Mdl_custom_fields
- Mdl_versions
- Mdl_tax_rates

## Testing Conventions

- All test methods start with `it_` and use snake_case
- All tests are annotated with `#[Test]`
- All tests follow the "Arrange, Act, Assert" pattern where applicable
- Tests extend `CiTestCase` which provides CodeIgniter bootstrap functionality
- Use `#[CoversClass(ClassName::class)]` to indicate which class is being tested

## Running Tests

```bash
vendor/bin/phpunit tests/Unit/Models/
```

## Notes

- InvoicePlane does not use Laravel, so avoid Laravel-specific testing patterns
- InvoicePlane does not have namespaces yet (CodeIgniter 3), but test files use namespaces
- Models are loaded via CodeIgniter's loader: `$this->CI->load->model('module/mdl_name')`
- Model instances are accessed via `$this->CI->mdl_name` after loading
