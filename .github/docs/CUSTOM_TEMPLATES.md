# Custom Invoice & Quote Templates

> **Security note:** The filesystem is never scanned for templates. This prevents Remote Code
> Execution (RCE) attacks where an attacker writes a PHP file to the templates directory and
> then triggers it via the admin panel. This allowlist-based mechanism was introduced as part of
> the [1.7.2 security release](../CHANGELOG.md#172---2026-07-14) — see
> [SECURITY_ADVISORY_RCE_FIX.md](../security/SECURITY_ADVISORY_RCE_FIX.md) for the full advisory.

To add a custom template:

1. **Create the template `.php` file** inside `CUSTOM_TEMPLATES_FOLDER` under the appropriate sub-path:
   ```
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/invoice_templates/public/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/pdf/MyTemplate.php
   <CUSTOM_TEMPLATES_FOLDER>/quote_templates/public/MyTemplate.php
   ```

2. **Add the template name** (without `.php`) to the matching allowlist key in `ipconfig.php`.
   Quote the value when names contain spaces or hyphens:
   ```
   CUSTOM_INVOICE_TEMPLATES_PDF="MyTemplate,Corporate - Modern"
   CUSTOM_INVOICE_TEMPLATES_PUBLIC="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PDF="MyTemplate"
   CUSTOM_QUOTE_TEMPLATES_PUBLIC="MyTemplate"
   ```
   Template names may only contain letters, digits, spaces, hyphens (`-`), and underscores (`_`).

3. The template will appear in **Settings → Invoice / Quote** once it is listed.

> The built-in template directories are never scanned — only the `CUSTOM_TEMPLATES_FOLDER` is
> searched, and only for names you have explicitly listed. This is the RCE prevention mechanism.

For container deployments, `CUSTOM_TEMPLATES_FOLDER` and the allowlist variables above are set
as environment variables instead — see [CONTAINER_DEPLOYMENT.md](CONTAINER_DEPLOYMENT.md).
