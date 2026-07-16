# Security Advisory: RCE Vulnerability Fix (v1.7.2)

## Summary

This document describes the critical Remote Code Execution (RCE) vulnerability discovered in InvoicePlane v1.7.1 and the comprehensive fix implemented in v1.7.2.

**Vulnerability Type:** Remote Code Execution (RCE)  
**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Fixed Version:** InvoicePlane v1.7.2  
**CVE:** Pending  
**CVSS Score:** 9.9 (Critical)

## Vulnerability Details

### Attack Chain

The vulnerability consisted of three chained weaknesses:

1. **Writable Templates Directory (CWE-732)**
   - The template directories were writable by the web server process
   - Allowed administrators to write arbitrary PHP files to the templates directory

2. **Dynamic Whitelist Construction (CWE-693)**
   - The `validate_template_name()` function used `directory_map()` to scan the templates directory at runtime
   - Any file written to the directory was automatically added to the "trusted" whitelist
   - This completely bypassed the v1.7.1 patch that was intended to prevent LFI attacks

3. **Database-Controlled PHP File Inclusion (CWE-98)**
   - Template names from the database directly controlled which PHP file was included
   - Combined with the dynamic whitelist, this allowed arbitrary code execution

### Attack Scenario

1. Attacker with admin access writes a PHP webshell (`evil.php`) to the templates directory
2. The webshell is automatically added to the whitelist on the next request
3. Attacker sets `public_invoice_template=evil` in settings
4. Any unauthenticated visitor accessing a public invoice URL triggers RCE
5. The webshell executes with web server privileges

### Impact

- **Confidentiality:** HIGH - Full read access to all server files and database
- **Integrity:** HIGH - Arbitrary file modification and database manipulation
- **Availability:** HIGH - Potential for ransomware and data destruction
- **Scope:** CHANGED - Allows escape from web application to operating system

## Fix Implementation

### 1. Static Whitelist (Primary Defense)

**File:** `application/modules/invoices/models/Mdl_templates.php`

**Before (Vulnerable):**
```php
public function get_invoice_templates($type = 'pdf')
{
    $this->load->helper('directory');
    if ($type == 'pdf') {
        $templates = directory_map(APPPATH . '/views/invoice_templates/pdf', true);
    } elseif ($type == 'public') {
        $templates = directory_map(APPPATH . '/views/invoice_templates/public', true);
    }
    return $this->remove_extension($templates);
}
```

**After (Secure):**
```php
private const ALLOWED_INVOICE_TEMPLATES = [
    'pdf' => [
        'InvoicePlane',
        'InvoicePlane - paid',
        'InvoicePlane - overdue',
    ],
    'public' => [
        'InvoicePlane_Web',
    ],
];

public function get_invoice_templates($type = 'pdf')
{
    // Security: built-in templates come from the static whitelist only — the
    // application's own template directories are NEVER scanned.
    if ($type === 'pdf') {
        $built_in = self::ALLOWED_INVOICE_TEMPLATES['pdf'];
    } elseif ($type === 'public') {
        $built_in = self::ALLOWED_INVOICE_TEMPLATES['public'];
    } else {
        return [];
    }

    // Merge any admin-approved custom templates declared in ipconfig.php.
    return $this->_merge_custom('invoice_templates/' . $type, $built_in);
}
```

**Key Changes:**
- Removed `directory_map()` calls that scanned the filesystem
- Built-in templates are served from the hardcoded `ALLOWED_INVOICE_TEMPLATES` and
  `ALLOWED_QUOTE_TEMPLATES` constants — the application's own template directories are
  **never** scanned
- Custom templates are opt-in through **explicit allowlists in `ipconfig.php`**
  (`CUSTOM_TEMPLATES_FOLDER` + `CUSTOM_INVOICE_TEMPLATES_*` / `CUSTOM_QUOTE_TEMPLATES_*`),
  resolved by the `_merge_custom()` helper — never through a filesystem scan. See
  [Adding New Templates (Post-Fix)](#adding-new-templates-post-fix).
- Even if an attacker writes `evil.php` to a templates directory, it will NOT appear in the
  whitelist unless its exact name is also listed in the operator-controlled `ipconfig.php`

### 2. Enhanced Validation (Defense-in-Depth)

**File:** `application/helpers/template_helper.php`

**Security Layers Added:**
1. Empty/non-string value rejection
2. Path traversal detection using `validate_safe_filename()`
3. Type parameter validation (`invoice` or `quote` only)
4. Scope parameter validation (`pdf` or `public` only)
5. **Static whitelist validation (CRITICAL)**
6. Character validation (alphanumeric, spaces, hyphens, underscores only)
7. Comprehensive logging of all validation failures

### 3. File Existence Verification

**File:** `application/modules/guest/controllers/View.php`

**Additional Protections:**
- Verify template file actually exists before including it
- Fallback to default template if configured template is invalid
- Show error if even the default template is missing
- Prevents arbitrary file inclusion even if whitelist is somehow bypassed

### 4. Permission Checking (Security Audit)

**File:** `application/modules/invoices/models/Mdl_templates.php`

Added `check_template_directory_permissions()` method:
- Checks if template directories are writable by web server
- Returns warnings if insecure permissions are detected
- Can be used by administrators to audit their installation

## Verification

### Test 1: Malicious Template Rejection

**Objective:** Verify that files not in the whitelist are rejected

**Steps:**
1. Create a file `/application/views/invoice_templates/public/evil.php` with content: `<?php echo "PWNED"; ?>`
2. Set database value: `UPDATE ip_settings SET setting_value = 'evil' WHERE setting_key = 'public_invoice_template';`
3. Access a public invoice URL

**Expected Result:**
- Template validation fails
- Error logged: "Template validation failed: Template not in static whitelist: evil"
- Default template (InvoicePlane_Web) is used instead
- The `evil.php` file is NEVER executed

### Test 2: Path Traversal Rejection

**Objective:** Verify that path traversal attempts are blocked

**Steps:**
1. Set database value: `UPDATE ip_settings SET setting_value = '../../../etc/passwd' WHERE setting_key = 'public_invoice_template';`
2. Access a public invoice URL

**Expected Result:**
- Path traversal detected by `validate_safe_filename()`
- Error logged with hash of the attempted path
- Default template is used
- No file inclusion occurs

### Test 3: Valid Template Works

**Objective:** Verify that legitimate templates still work

**Steps:**
1. Set database value: `UPDATE ip_settings SET setting_value = 'InvoicePlane_Web' WHERE setting_key = 'public_invoice_template';`
2. Access a public invoice URL

**Expected Result:**
- Template validation succeeds
- Invoice displays correctly using InvoicePlane_Web template

### Test 4: Permission Audit

**Objective:** Check for writable template directories

**Steps:**
```php
$CI = &get_instance();
$CI->load->model('invoices/mdl_templates');
$warnings = $CI->mdl_templates->check_template_directory_permissions();
var_dump($warnings);
```

**Expected Result:**
- Returns array of warnings if any template directory is writable
- Empty array if all permissions are secure

## Remediation for Affected Installations

### Immediate Actions (Emergency)

If you are running InvoicePlane v1.7.0 or v1.7.1:

1. **Upgrade immediately to v1.7.2 or later**
2. **Audit template directories for malicious files:**
   ```bash
   ls -la application/views/invoice_templates/public/
   ls -la application/views/invoice_templates/pdf/
   ls -la application/views/quote_templates/public/
   ls -la application/views/quote_templates/pdf/
   ```
3. **Remove any unexpected PHP files** (only these should exist):
   - `invoice_templates/public/InvoicePlane_Web.php`
   - `invoice_templates/pdf/InvoicePlane.php`
   - `invoice_templates/pdf/InvoicePlane - paid.php`
   - `invoice_templates/pdf/InvoicePlane - overdue.php`
   - `quote_templates/public/InvoicePlane_Web.php`
   - `quote_templates/pdf/InvoicePlane.php`

4. **Check database for malicious template settings:**
   ```sql
   SELECT * FROM ip_settings WHERE setting_key LIKE '%template%';
   ```
   - Verify all template values are legitimate template names
   - Reset any suspicious values to defaults

5. **Review web server logs** for suspicious activity:
   ```bash
   grep "evil" /var/log/apache2/access.log
   grep "shell" /var/log/apache2/access.log
   grep "cmd" /var/log/apache2/access.log
   ```

### Long-Term Security Hardening

1. **Set template directories to read-only:**
   ```bash
   chmod 555 application/views/invoice_templates/public/
   chmod 555 application/views/invoice_templates/pdf/
   chmod 555 application/views/quote_templates/public/
   chmod 555 application/views/quote_templates/pdf/
   chmod 444 application/views/invoice_templates/public/*.php
   chmod 444 application/views/invoice_templates/pdf/*.php
   chmod 444 application/views/quote_templates/public/*.php
   chmod 444 application/views/quote_templates/pdf/*.php
   ```

2. **Use file integrity monitoring** (e.g., AIDE, Tripwire) to detect unauthorized file changes

3. **Implement principle of least privilege** for admin accounts

4. **Enable web application firewall** (ModSecurity, Cloudflare WAF)

5. **Regular security audits** of file permissions and database settings

## Adding New Templates (Post-Fix)

The `ALLOWED_INVOICE_TEMPLATES` / `ALLOWED_QUOTE_TEMPLATES` constants in `Mdl_templates.php`
hold the **built-in defaults only**. Operators are **not** expected to edit that file — doing so
means patching application source on every upgrade. Instead, custom templates are declared
through an explicit, operator-controlled allowlist in **`ipconfig.php`** (or the equivalent
environment variables in a container deployment). The filesystem is still never scanned, so the
RCE protection is fully preserved.

To add a custom template in v1.7.2+:

1. **Create the template `.php` file** inside the directory referenced by
   `CUSTOM_TEMPLATES_FOLDER`, mirroring the built-in structure:
   ```
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/public/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/public/MyTemplate.php
   ```

2. **Declare the folder and the exact template name(s)** in `ipconfig.php`. Only names listed
   here are ever exposed — quote values that contain spaces or hyphens:
   ```ini
   CUSTOM_TEMPLATES_FOLDER=/var/lib/invoiceplane/templates
   CUSTOM_INVOICE_TEMPLATES_PDF="MyTemplate,Corporate - Modern"
   CUSTOM_INVOICE_TEMPLATES_PUBLIC="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PDF="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PUBLIC="MyTemplate"
   ```
   Template names may only contain letters, digits, spaces, hyphens (`-`) and underscores (`_`).
   Any name that fails this check is skipped and logged (see `_merge_custom()`).

3. The template then appears in **Settings → Invoice / Quote** alongside the built-in templates.

**Important:** A template file on disk does nothing on its own. It becomes selectable **only**
after its exact name is added to the matching `CUSTOM_*` allowlist in `ipconfig.php`. This is the
mechanism that preserves the RCE fix — approval lives in operator-controlled configuration, not
in whatever files happen to exist on disk.

> **Editing `Mdl_templates.php` directly** is reserved for changing the shipped defaults within
> the project itself (e.g. a maintainer adding a new bundled template in a release). Deployments
> should always use the `ipconfig.php` allowlist above.

## Technical Details for Security Researchers

### Defense-in-Depth Architecture

This fix implements multiple independent security layers:

```
User Input (template name from database)
         ↓
Layer 1: Empty/null check
         ↓
Layer 2: Path traversal detection
         ↓
Layer 3: Type validation (invoice/quote)
         ↓
Layer 4: Scope validation (pdf/public)
         ↓
Layer 5: Static whitelist check (PRIMARY DEFENSE)
         ↓
Layer 6: Character validation
         ↓
Layer 7: File existence check
         ↓
Template Inclusion (if all layers pass)
```

### Why Each Layer Matters

- **Layers 1-4:** Prevent obviously malicious input
- **Layer 5 (Static Whitelist):** PRIMARY DEFENSE - blocks any file not explicitly approved in code
- **Layer 6:** Prevents edge cases with special characters
- **Layer 7:** Final verification before inclusion

Even if layers 1-6 are somehow bypassed, layer 7 provides a final safety check.

### Attack Surface Reduction

**Before Fix:**
- Attack surface = ANY file in templates directory
- Dynamic whitelist = filesystem scan results
- Attacker controls filesystem → Attacker controls whitelist

**After Fix:**
- Attack surface = built-in constants **plus** names explicitly listed in the operator-controlled
  `ipconfig.php` allowlist — nothing else
- Whitelist source = immutable code constants + operator configuration, never a filesystem scan
- Attacker controls filesystem → Whitelist unchanged (a dropped file is ignored unless its exact
  name is also present in `ipconfig.php`, which the web server process cannot modify)

## Credits

- **Vulnerability Discovery:** [@Vijay-raghav7](https://github.com/Vijay-raghav7), reported via a
  private [GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp)
- **Fix Implementation:** InvoicePlane Security Team
- **Review:** InvoicePlane Core Team

## Timeline

- **2026-07-17:** Vulnerability reported
- **2026-07-17:** Fix developed and tested
- **2026-07-14:** v1.7.2 released with fix
- **2026-07-14:** Security advisory published

## References

- [CWE-732: Incorrect Permission Assignment for Critical Resource](https://cwe.mitre.org/data/definitions/732.html)
- [CWE-693: Protection Mechanism Failure](https://cwe.mitre.org/data/definitions/693.html)
- [CWE-98: Improper Control of Filename for Include/Require Statement in PHP Program](https://cwe.mitre.org/data/definitions/98.html)
- [OWASP: Server-Side Template Injection](https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/07-Input_Validation_Testing/18-Testing_for_Server-side_Template_Injection)

## Contact

Report security issues privately by opening a
[GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/new)
(see [SECURITY.md](../../SECURITY.md)). Do not disclose security vulnerabilities publicly until a
fix is available.
