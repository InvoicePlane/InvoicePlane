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

| Severity | Vulnerability | Details |
|----------|--------------|---------|
| **Critical** | Remote Code Execution via dynamic template whitelist (bypass of v1.7.1 LFI fix) | [SECURITY_ADVISORY_RCE_FIX.md](SECURITY_ADVISORY_RCE_FIX.md) |
| **Critical** | Broken authentication – password reset tokens never expired | CHANGELOG.md |
| **High** | Open redirect via unvalidated `HTTP_REFERER` | [ADDITIONAL_SECURITY_FIXES_v1.7.2.md](ADDITIONAL_SECURITY_FIXES_v1.7.2.md) |
| **Medium** | SQL query hardened in guest payments | ADDITIONAL_SECURITY_FIXES_v1.7.2.md |
| **Medium** | `HTTP_REFERER` injection in AJAX filter controllers | ADDITIONAL_SECURITY_FIXES_v1.7.2.md |
| **Medium** | PHPMailer SMTP debug output logged unsanitized (log injection) | CHANGELOG.md |
| **Medium** | `phpmail_send()` masked send failures by always returning `true` | CHANGELOG.md |
| **Low** | Binary data corruption in `Cryptor::decryptString()` (`mb_strlen`/`mb_substr`) | CHANGELOG.md |
| **Low** | GitHub Actions `GITHUB_TOKEN` over-broad permissions | CHANGELOG.md |

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

#### 3. Custom templates now appear in the template selector

**Who is affected:** Installations that use `CUSTOM_TEMPLATES_FOLDER`.

**Before:** Templates placed in `CUSTOM_TEMPLATES_FOLDER` were loaded correctly when
selected, but they did **not** appear in the admin template dropdown selectors. There was no
way to select them via the UI.

**After:** When `CUSTOM_TEMPLATES_FOLDER` is set in `ipconfig.php`, templates in that folder
are discovered, validated, and listed alongside the built-in templates in the selector.

**Requirements for custom templates to be listed:**
- The file must have a `.php` extension.
- The file name (without extension) must consist of alphanumeric characters, spaces, hyphens
  (`-`), and underscores (`_`) only. Files with other characters in the name are skipped and
  a warning is written to the CI log.
- Sub-directories inside `CUSTOM_TEMPLATES_FOLDER` are ignored.

#### 4. Template whitelist now covers only built-in templates

**Who is affected:** Installations with custom invoice or quote templates stored **inside the
application's `application/views/` directory**.

**Background:** The RCE fix replaced the dynamic filesystem scan with a hardcoded constant
whitelist that lists only the built-in templates. Templates stored outside the application
(via `CUSTOM_TEMPLATES_FOLDER`) are now the supported way to add custom templates.

**Action required:** If you previously added custom templates directly into
`application/views/invoice_templates/` or `application/views/quote_templates/`, move them
to a directory outside the web root and configure `CUSTOM_TEMPLATES_FOLDER` in
`ipconfig.php` to point to that directory:

```ini
# ipconfig.php
CUSTOM_TEMPLATES_FOLDER=/srv/invoiceplane-templates/
```

Then place your templates in e.g. `/srv/invoiceplane-templates/invoice_templates/pdf/MyTemplate.php`.

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

```bash
php verify_rce_fix.php
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
