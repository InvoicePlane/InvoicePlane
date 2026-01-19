# GitHub Copilot Instructions for InvoicePlane

This document provides context and guidelines for GitHub Copilot when working on the InvoicePlane codebase.

## Project Context

InvoicePlane is a simple self-hosted invoicing application built with CodeIgniter 3.x framework. This application is fully compatible with PHP 8.2+ and follows modern PHP standards within the CodeIgniter 3.x architecture.

## Technology Stack

- **Backend**: PHP 8.1, 8.2, 8.3+ (CodeIgniter 3.x framework)
- **Frontend**: Bootstrap 3.4, jQuery 3.7+, jQuery UI 1.14
- **Database**: MariaDB
- **Build Tools**: Grunt (frontend assets), Composer (PHP dependencies), Yarn (JavaScript dependencies)
- **Linting**: Laravel Pint (PSR-12), PHP_CodeSniffer, Rector

## Coding Standards

### PHP Code

- **Follow PSR-12** coding standard
- Use **short array syntax** (`[]` instead of `array()`)
- Use **single quotes** for strings (unless interpolation needed)
- **No closing PHP tags** (`?>`) in PHP-only files
- Use **type hints** where possible (PHP 8.1+ features)
- Follow **CodeIgniter 3.x conventions** for controllers, models, and views
- Use **CodeIgniter's built-in security features** (XSS filtering, CSRF protection)
- **PHP 8.2+ Compatible**: Ensure all code works with PHP 8.2 and 8.3+

### File Structure

- Controllers: `application/controllers/`
- Models: `application/models/`
- Views: `application/views/`
- Helpers: `application/helpers/`
- Libraries: `application/libraries/`
- Config: `application/config/`

### JavaScript Code

- Use **modern ES6+ syntax** where appropriate
- Follow **Standard JS** formatting
- Prefer **jQuery** for DOM manipulation (project standard)
- Keep **functions focused and small**
- **Comment complex logic**

### CSS/SASS

- Use **SASS** for stylesheets
- Follow **Bootstrap 3 conventions**
- Keep styles **modular and reusable**
- Use **meaningful class names**

## Common Patterns

### Controllers (CodeIgniter 3.x)

```php
<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    public function index()
    {
        $data['invoices'] = $this->invoice_model->get_all();
        $this->load->view('invoices/index', $data);
    }
}
```

### Models (CodeIgniter 3.x)

```php
<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoice_Model extends CI_Model
{
    public function get_all()
    {
        return $this->db->get('ip_invoices')->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where('invoice_id', $id)
                        ->get('ip_invoices')
                        ->row();
    }
}
```

### Input Sanitization

Always use CodeIgniter's input class for user data:

```php
// Good
$email = $this->input->post('email');
$user_id = $this->input->get('user_id');

// Avoid direct access
// Bad: $_POST['email'], $_GET['user_id']
```

### Database Queries

Use Query Builder or prepared statements:

```php
// Good - Query Builder
$this->db->where('id', $id)
         ->update('table_name', $data);

// Good - Prepared statements
$sql = "SELECT * FROM ip_invoices WHERE client_id = ? AND status = ?";
$result = $this->db->query($sql, [$client_id, $status]);
```

### Security Considerations

- **Always validate and sanitize user input**
- **Escape output** to prevent XSS
- **Use parameterized queries** to prevent SQL injection
- **Check user permissions** before sensitive operations
- **Never expose sensitive data** in responses or logs
- **Use CodeIgniter's form validation library**

```php
// Input validation
$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');

if ($this->form_validation->run() === false) {
    // Handle validation errors
}
```

## Development Workflow

### Making Changes

1. **Understand the context**: Review related code before making changes
2. **Follow existing patterns**: Match the style of surrounding code
3. **Test changes**: Manually test all affected functionality
4. **Run linters**: Execute `composer check` before committing
5. **Document complex logic**: Add comments for non-obvious code

### Linting and Formatting

Always run linters before committing:

```bash
# Run all checks
composer check

# Or run individually
composer rector    # Automated refactoring
composer phpcs     # CodeSniffer
composer pint      # Laravel Pint (PSR-12)
```

### Building Assets

When changing JavaScript or CSS:

```bash
# One-time build
yarn build

# Watch mode for development
grunt watch
```

## Common Pitfalls to Avoid

1. **Don't use `array()` syntax** - Use short array syntax `[]`
2. **Don't use double quotes unnecessarily** - Use single quotes unless interpolation needed
3. **Don't access superglobals directly** - Use `$this->input->post()` / `->get()`
4. **Don't forget CSRF tokens** - CodeIgniter forms need CSRF protection
5. **Don't hardcode values** - Use configuration files or constants
6. **Don't skip input validation** - Always validate user input
7. **Don't forget to escape output** - Prevent XSS vulnerabilities
8. **Don't use deprecated functions** - Check PHP version compatibility

## Testing

### Linting and Code Quality

Before committing, always run:
```bash
composer check  # Runs rector, phpcs, and pint
```

### Building Assets

Test that assets build successfully:
```bash
yarn build
```

### Manual Testing

- Test all affected features thoroughly
- Check for regressions in related functionality
- Test in different browsers when making UI changes
- Verify responsive behavior on mobile devices

### Note on Testing Tools

- **PHPStan**: Not required for this CodeIgniter 3 application
- **PHPUnit**: Not required for this CodeIgniter 3 application
- Focus on **linting** and **manual testing**

### Reporting Issues

- Search existing issues first
- Provide clear reproduction steps
- Include environment details (PHP version, browser, etc.)
- Add screenshots for UI-related issues

## Resources

- **Documentation**: https://wiki.invoiceplane.com/
- **Community**: https://community.invoiceplane.com/
- **Discord**: https://discord.gg/PPzD2hTrXt
- **Issue Tracker**: https://github.com/InvoicePlane/InvoicePlane/issues
- **CodeIgniter 3 Docs**: https://codeigniter.com/userguide3/

## Key Reminders

- This is a **simple CodeIgniter 3.x** application
- **PHP 8.2+ compatible** - ensure all code works with PHP 8.2 and 8.3+
- Follow **PSR-12** coding standards for PHP
- Use **CodeIgniter's built-in security features**
- Always **sanitize input and escape output**
- Run **linters before committing** (`composer check`)
- Build **assets** before committing (`yarn build`)
- Test **thoroughly** - manual testing is critical
- Keep changes **focused and minimal**
- Follow **existing code patterns** in the project

---

**Note**: When in doubt, check existing code patterns in the codebase and follow the same conventions.
