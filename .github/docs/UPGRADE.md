# Upgrade Guide

This guide provides instructions for upgrading InvoicePlane to newer versions.

## Table of Contents

- [Instructions to upgrade to 1.7.2 from 1.7.0 / 1.7.1](#instructions-to-upgrade-to-172-from-170--171)
- [Instructions to upgrade to 1.6.3 from 1.6.2](#instructions-to-upgrade-to-163-from-162)
- [Instructions to upgrade to 1.6.0 from 1.5.11](#instructions-to-upgrade-to-160-from-1511)

---

## Instructions to upgrade to 1.7.2 from 1.7.0 / 1.7.1

> **This is a critical security release.** v1.7.0 and v1.7.1 contain a Remote Code
> Execution vulnerability (CVSSv3 9.9). Upgrade immediately. Consult
> [MIGRATION_GUIDE_v1.7.2.md](MIGRATION_GUIDE_v1.7.2.md) for a full pre-upgrade security
> audit checklist.

### Security fixes in this release

> The full, GHSA-linked list of every vulnerability fixed in v1.7.2 lives in the
> [CHANGELOG](../CHANGELOG.md#172---2026-04-06). The table below is a quick reference to
> the most impactful fixes.

| Severity | Vulnerability | Details |
|----------|--------------|---------|
| **Critical** | Remote Code Execution via dynamic template whitelist (bypass of v1.7.1 LFI fix) | [SECURITY_ADVISORY_RCE_FIX.md](../security/SECURITY_ADVISORY_RCE_FIX.md) |
| **Critical** | Broken authentication – password reset tokens never expired | [CHANGELOG](../CHANGELOG.md) |
| **High** | Open redirect via unvalidated `HTTP_REFERER` | [CHANGELOG](../CHANGELOG.md) |
| **Medium** | SQL query hardened in guest payments | [CHANGELOG](../CHANGELOG.md) |
| **Medium** | `HTTP_REFERER` injection in AJAX filter controllers | [CHANGELOG](../CHANGELOG.md) |
| **Medium** | PHPMailer SMTP debug output logged unsanitized (log injection) | [CHANGELOG](../CHANGELOG.md) |
| **Medium** | `phpmail_send()` masked send failures by always returning `true` | [CHANGELOG](../CHANGELOG.md) |
| **Low** | Binary data corruption in `Cryptor::decryptString()` (`mb_strlen`/`mb_substr`) | [CHANGELOG](../CHANGELOG.md) |
| **Low** | GitHub Actions `GITHUB_TOKEN` over-broad permissions | [CHANGELOG](../CHANGELOG.md) |

### Behavioral changes that may require action

The following changes alter observable behavior or API contracts. Review each one before
upgrading.

#### 1. `phpmail_send()` now returns `false` on delivery failure

**Who is affected:** Installations with custom code that calls `phpmail_send()` directly and
relies on its return value.

**Before (v1.7.0–1.7.1):** `phpmail_send()` always returned `true`, even when the underlying
SMTP/sendmail call failed. Delivery failures were silently dropped.

**After (v1.7.2):** `phpmail_send()` returns the actual boolean result — `true` on success,
`false` on failure. Failed sends now set a flash error message for the user.

**Action required:** If your custom code calls `phpmail_send()`, update it to check the return
value:

```php
// Before — always truthy, failures silently ignored
phpmail_send($from, $to, $subject, $body);

// After — check for failure
if ( ! phpmail_send($from, $to, $subject, $body)) {
    // handle delivery failure
}
```

#### 2. Email template preview now shows plain text

**Who is affected:** All installations using the email template editor.

**Before:** The live preview panel in the email template editor rendered the template source as
HTML, using `innerHTML` to update a preview iframe.

**After:** The preview panel now displays the raw template source as plain text
(`textContent`). This eliminates a DOM-based XSS risk. The templates themselves are not
affected — this change only affects the in-browser preview.

**Action required:** None. Existing templates continue to send as HTML. If you need to
visually check HTML formatting, send a test email instead of relying on the preview panel.

#### 3. Custom templates can be listed via an explicit allowlist (no filesystem scan)

**Who is affected:** Installations that add their own invoice or quote templates.

**Before:** The template selector was built by scanning the templates directory on the
filesystem. Any `.php` file present in the directory appeared in the selector automatically —
the same dynamic scan that caused the RCE vulnerability (see #4 below).

**After:** The filesystem is **never** scanned. The selector is built from the built-in
templates plus **only** the custom template names you explicitly allowlist. Built-in templates
always appear in the selector. Adding a custom template is a **two-step** operation — both steps
are required, and setting `CUSTOM_TEMPLATES_FOLDER` alone lists nothing:

1. **Place the `.php` file** under `CUSTOM_TEMPLATES_FOLDER`, in the sub-path that matches the
   template's type and scope:
   ```
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/public/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/public/MyTemplate.php
   ```
   `CUSTOM_TEMPLATES_FOLDER` only tells InvoicePlane **where the file lives** at render time.
   It does not add the template to the selector.

2. **Add the template name** (without `.php`) to the matching allowlist variable in
   `ipconfig.php`. Only names listed in these variables appear in the selector:
   ```ini
   CUSTOM_INVOICE_TEMPLATES_PDF="MyTemplate,Corporate - Modern"
   CUSTOM_INVOICE_TEMPLATES_PUBLIC="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PDF="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PUBLIC="MyTemplate"
   ```
   Quote the value when a name contains spaces or hyphens.

**Requirements for a custom template name:**
- The corresponding `.php` file must exist under `CUSTOM_TEMPLATES_FOLDER` at the matching
  sub-path (step 1).
- The name may contain only alphanumeric characters, spaces, hyphens (`-`), and underscores
  (`_`). Names with any other character are skipped and a warning is written to the CI log.

See [CUSTOM_TEMPLATES.md](CUSTOM_TEMPLATES.md) for the full walkthrough, and section #4 below
if you previously kept custom templates inside `application/views/`.

**Upgrade aid:** After upgrading and running `/setup`, the Settings page checks the saved PDF
and public template settings in the database. If one of those settings names a template that is
not built in and not present in the matching `CUSTOM_*_TEMPLATES` allowlist, InvoicePlane shows
an administrator warning with the exact template name and `ipconfig.php` variable to update.
This warning intentionally uses only saved database settings; InvoicePlane still does not scan
template folders to discover unused template files.

#### 4. Template whitelist now covers only built-in templates

**Who is affected:** Installations with custom invoice or quote templates stored **inside the
application's `application/views/` directory**.

**Background:** The RCE fix replaced the dynamic filesystem scan with an allowlist mechanism.
Built-in templates are listed in hardcoded constants; custom templates can be added via explicit
allowlist config variables. Templates stored outside the application (via `CUSTOM_TEMPLATES_FOLDER`)
with explicit allowlisting are now the supported way to add custom templates.

**Action required:** If you previously added custom templates directly into
`application/views/invoice_templates/` or `application/views/quote_templates/`, move them
to a directory outside the web root and configure `CUSTOM_TEMPLATES_FOLDER` in
`ipconfig.php` to point to that directory, then add each template name to the matching
allowlist variable:

```ini
# ipconfig.php
CUSTOM_TEMPLATES_FOLDER=/srv/invoiceplane-templates/
CUSTOM_INVOICE_TEMPLATES_PDF="MyCustomTemplate"
```

Then place your templates in e.g. `/srv/invoiceplane-templates/invoice_templates/pdf/MyTemplate.php`.
See section #3 above for the full two-step process.

Alternatively, if you prefer to keep templates inside the application, add each template name
to the appropriate constant in `application/modules/invoices/models/Mdl_templates.php`:

```php
private const ALLOWED_INVOICE_TEMPLATES = [
    'pdf' => [
        'InvoicePlane',
        'InvoicePlane - paid',
        'InvoicePlane - overdue',
        'MyCustomTemplate',  // ← add your template name here
    ],
    // ...
];
```

### 1. Pre-upgrade security audit

Run the full pre-upgrade checklist in [MIGRATION_GUIDE_v1.7.2.md](MIGRATION_GUIDE_v1.7.2.md)
before applying the upgrade, especially if you are upgrading from v1.7.0 or v1.7.1.

### 2. Replace files

1. Back up your database and all files.
2. Copy all new files to your InvoicePlane installation root, but **do not overwrite**:
   - `ipconfig.php`
   - Custom templates in `application/views/` (better: move them to `CUSTOM_TEMPLATES_FOLDER`)
   - Custom styles: `assets/core/css/custom.css`, `assets/core/css/custom-pdf.css`
   - Uploaded images in `uploads/`
   - Custom language keys: `application/language/COUNTRY/custom_lang.php`

### 3. Database migration

Open `http://yourdomain.com/index.php/setup` and follow the on-screen instructions. The
setup wizard runs the required database migrations automatically, including adding the
`user_passwordreset_token_expiry` column for the password reset token expiry fix.

After `/setup` completes, log in as an administrator and open **Settings**. If InvoicePlane
finds saved custom template names that are not listed in `ipconfig.php`, it shows a warning
with the names to copy into `CUSTOM_INVOICE_TEMPLATES_PDF`,
`CUSTOM_INVOICE_TEMPLATES_PUBLIC`, `CUSTOM_QUOTE_TEMPLATES_PDF`, or
`CUSTOM_QUOTE_TEMPLATES_PUBLIC`.

### 4. Set secure template directory permissions

```bash
chmod 555 application/views/invoice_templates/public/
chmod 555 application/views/invoice_templates/pdf/
chmod 555 application/views/quote_templates/public/
chmod 555 application/views/quote_templates/pdf/
find application/views/invoice_templates/ application/views/quote_templates/ \
  -name "*.php" -exec chmod 444 {} \;
```

### 5. Verify the upgrade

Confirm the template system no longer scans the filesystem and only serves allow-listed
templates by following the manual verification steps in the
[RCE advisory](../security/SECURITY_ADVISORY_RCE_FIX.md#verification). For the arbitrary
file-deletion fix, run the bundled checker:

```bash
php .github/security/verify_file_deletion_fix.php
```

Expected output: all checks pass with no warnings.

---

## Instructions to upgrade to 1.6.3 from 1.6.2

### 1. Preliminary operations

Follow the procedure outlined in [Upgrade 1.6.0 from 1.5.11](#1-preliminary-operations).

### 2. Replace files & test

1. Copy all files to the root directory of your InvoicePlane installation but **do not** overwrite the following files:
   - The `ipconfig.php` file
   - Customized templates in the `application/views/` folder
   - The files for custom styles: `assets/core/css/custom.css` and `assets/core/css/custom-pdf.css`
   - Uploaded images in the `uploads/` folder (e.g. your company logo)
   - Custom language keys at `application/language/COUNTRY/custom_lang.php`

   > **Hint:** An *easy* way of performing this operation is to upload the whole new InvoicePlane version in a different folder, outside of your current installation root folder, and copy the above mentioned files in the new folder you just uploaded. Afterwards just rename your current folder to something like `my_current_folder_old` and rename your new-version-folder with the name of `my_current_folder`.

2. Open `http://yourdomain.com/index.php/setup` and follow the instructions. The app will run all updates on its own.
   - If you encounter any errors when upgrading the table, press "Try Again" to resolve those errors and continue with the setup.

3. Now that the update is installed, moved and protected, it's time to log in and see if everything is working: login again and check if everything is working.

---

## Instructions to upgrade to 1.6.0 from 1.5.11

### 1. Preliminary operations

1. Make a backup of your database and all files. (This is **very important** to prevent any data loss)

2. Download the latest version from [InvoicePlane.com](https://invoiceplane.com/downloads).

### 2. Replace files & test

1. Copy all files to the root directory of your InvoicePlane installation but **do not** overwrite the following files:
   - The `ipconfig.php` file
   - Customized templates in the `application/views/` folder
   - The files for custom styles: `assets/core/css/custom.css` and `assets/core/css/custom-pdf.css`
   - Uploaded images in the `uploads/` folder (e.g. your company logo)
   - Custom language keys at `application/language/COUNTRY/custom_lang.php`

   > **Hint:** An *easy* way of performing this operation is to upload the whole new InvoicePlane version in a different folder, outside of your current installation root folder, and copy the above mentioned files in the new folder you just uploaded. Afterwards just rename your current folder to something like `my_current_folder_old` and rename your new-version-folder with the name of `my_current_folder`.

2. Now that the files are placed, it's time to fix the `ipconfig.php` file.
   - Open `ipconfig.php` and comment out the top line in the file by adding a `#` at the beginning of the first line. The result should be like this:
     ```
     # <?php exit('No direct script access allowed'); ?>
     ```
   - Close the `ipconfig.php` file

3. Open `http://yourdomain.com/index.php/setup` and follow the instructions. The app will run all updates on its own.
   - If you encounter any errors when upgrading the table, press "Try Again" to resolve those errors and continue with the setup.

4. Now that the `ipconfig.php` file is fixed, moved and protected, it's time to log in and see if everything is working
   - Login again and check if everything is working.
   - If you were using the online payments module please navigate to `//your-domain.com/settings` and to the tab `online payment` and disable all payment methods that are not *stripe*. InvoicePlane 1.6 at the moment supports only Stripe as a payment gateway.

---

> **Note:** For additional help and support, visit the [official wiki](https://wiki.invoiceplane.com) or the [community forum](https://community.invoiceplane.com/).
