# Container Deployment

> [!WARNING]
> The container always uses the new (per-item) tax calculation mode.

A pre-built container image is available. Configuration is provided entirely through environment
variables — no `ipconfig.php` file is needed. The entrypoint generates the configuration and
runs any pending database migrations automatically on startup.

## Required environment variables

| Variable | Description |
|---|---|
| `IP_URL` | Public base URL without trailing slash, e.g. `https://invoices.example.com` |
| `DB_HOSTNAME` | Database host |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `DB_DATABASE` | Database name |
| `ENCRYPTION_KEY` | Secret key for encrypted data — generate with `openssl rand -base64 32` |

## Optional environment variables

| Variable | Default | Description |
|---|---|---|
| `DB_PORT` | `3306` | Database port |
| `CI_ENV` | `production` | Set to `development` to show all PHP errors |
| `ENABLE_DEBUG` | `false` | Enable advanced debug logging |
| `SESS_SAVE_PATH` | PHP's `sys_get_temp_dir()` | Directory for session files. Set to an absolute path outside the document root for extra security. Do **not** pass an empty string — it overrides PHP's `session.save_path` and breaks login / the setup wizard; unset it instead to get the default. |
| `SESS_COOKIE_NAME` | `ip_session` | Session cookie name |
| `SESS_TABLE_NAME` | `ip_sessions` | Session database table name (only used when `SESS_DRIVER=database`) |
| `SESS_EXPIRATION` | `864000` | Session lifetime in seconds (0 = expire on browser close) |
| `SESS_MATCH_IP` | `true` | Tie sessions to the client IP address |
| `SESS_REGENERATE_DESTROY` | `true` | Destroy the old session file on ID regeneration (prevents session fixation) |
| `COOKIE_SECURE` | `false` | Send cookies only over HTTPS — set to `true` on HTTPS-only deployments |
| `X_FRAME_OPTIONS` | `SAMEORIGIN` | Value for the `X-Frame-Options` response header |
| `ENABLE_X_CONTENT_TYPE_OPTIONS` | `true` | Send the `X-Content-Type-Options: nosniff` header |
| `LEGACY_CALCULATION` | `false` | Use the classic (pre-1.6.3) tax/discount calculation mode. Required `false` for valid e-invoice XML. |
| `ENABLE_INVOICE_DELETION` | `false` | Allow invoices to be permanently deleted |
| `DISABLE_READ_ONLY` | `false` | Disable the read-only mode for sent invoices |
| `PASSWORD_RESET_IP_MAX_ATTEMPTS` | `5` | Max password reset requests per IP within the time window |
| `PASSWORD_RESET_IP_WINDOW_MINUTES` | `60` | Time window in minutes for IP-based reset rate limiting |
| `PASSWORD_RESET_EMAIL_MAX_ATTEMPTS` | `3` | Max password reset requests per email within the time window |
| `PASSWORD_RESET_EMAIL_WINDOW_HOURS` | `1` | Time window in hours for email-based reset rate limiting |
| `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` | `15` | Minutes before a password reset link expires |
| `CUSTOM_TEMPLATES_FOLDER` | — | Absolute path to a directory of custom invoice/quote templates. The directory must mirror the built-in structure (`invoice_templates/pdf/`, etc.). See [CUSTOM_TEMPLATES.md](CUSTOM_TEMPLATES.md). |
| `CUSTOM_INVOICE_TEMPLATES_PDF` | — | Comma-separated allowlist of custom PDF invoice template names (without `.php`). Quote the value if names contain spaces: `"My Template,Another"` |
| `CUSTOM_INVOICE_TEMPLATES_PUBLIC` | — | Same, for public/web invoice templates |
| `CUSTOM_QUOTE_TEMPLATES_PDF` | — | Same, for PDF quote templates |
| `CUSTOM_QUOTE_TEMPLATES_PUBLIC` | — | Same, for public/web quote templates |
| `SEC_STRIP_EXIF_FROM_IMAGES` | `false` | Strip EXIF metadata (GPS, timestamps, camera info) from uploaded images |
| `SUMEX_SETTINGS` | `false` | Enable Swiss medical invoice (Sumex) customizations |
| `SUMEX_URL` | — | URL to post Sumex XML to in order to receive a generated PDF |
| `ENCRYPTION_CIPHER` | `AES-256` | Cipher used for encrypted settings |

## Default admin user

On first startup the entrypoint creates an admin account if the database is empty.

| Variable | Default | Description |
|---|---|---|
| `DEFAULT_LANGUAGE` | `english` | Application language (`english`, `german`, `french`, …). Only applied on fresh installs. |
| `DEFAULT_ADMIN_EMAIL` | `admin@localhost` | Email for the default admin account |
| `DEFAULT_ADMIN_PASSWORD` | *(random)* | Password for the default admin account. If unset, a random 24-character password is printed to the container log on first startup. |
| `DEFAULT_ADMIN_NAME` | `admin` | Display name for the default admin account |

> User creation is skipped on every subsequent startup once at least one user exists.

## Persistent volumes

| Path | Contents |
|---|---|
| `/var/www/html/uploads` | Client files, logos, and imported documents |
| `/var/www/html/storage` | Session files, framework cache, and application logs |
