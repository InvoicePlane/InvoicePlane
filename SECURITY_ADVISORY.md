# Security Advisory: Encryption Key Loading Bug (Issue #1702)

## Summary

After system upgrades, SMTP credential authentication could fail (535 5.7.8) due to a bug in encryption key loading. This advisory describes the root cause, impact, recovery, and validation.

## Root Cause

The application uses `Dotenv::createImmutable()` to load `ipconfig.php` configuration into `$_ENV` via `bootstrap/kernel.php`. However, two encryption classes (`Crypt` and `IntegrationSettingsCipher`) attempted to load the `ENCRYPTION_KEY` using `getenv()`:

```php
// WRONG: Dotenv populates $_ENV, not the process environment
$key = getenv('ENCRYPTION_KEY');  // Always returns NULL
```

Because `Dotenv::createImmutable()` does not call `putenv()`, the `getenv()` function cannot find keys that are only in `$_ENV`. This caused:

1. **Pre-upgrade scenario**: Credentials encrypted with the correct key from ipconfig.php
2. **Post-upgrade scenario**: New encryption attempts use an empty key (getenv() returns NULL)
3. **Result**: Encrypted credentials from before and after upgrade are incompatible
4. **Symptom**: SMTP authentication fails after upgrade; re-entering password fixes it

## Affected Code

1. **`application/libraries/Crypt.php`** (Already fixed)
   - `getEncryptionKey()` method used `getenv('ENCRYPTION_KEY')`
   - Used for SMTP credentials, invoice/quote passwords, gateway credentials

2. **`application/modules/integrations/libraries/IntegrationSettingsCipher.php`** (Fixed in this commit)
   - `key()` method had `$_ENV['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY')`
   - Used for integration provider settings (Peppol, Stripe, etc.)

## Fix

Both classes now consistently use `env()` helper instead of `getenv()`:

```php
// CORRECT: env() reads from $_ENV (populated by Dotenv)
$key = env('ENCRYPTION_KEY', '');

// Or with fallback for edge cases:
$configured = env('ENCRYPTION_KEY') ?: ($_ENV['ENCRYPTION_KEY'] ?? null);
```

The `env()` helper is defined in `bootstrap/kernel.php`:

```php
if ( ! function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}
```

## Impact

- **Credentials saved BEFORE upgrade** with the correct key: ✅ Still work (encrypted with real key)
- **Credentials saved AFTER upgrade** with empty key: ❌ Fail to decrypt (encrypted with empty key)
- **Credentials re-entered AFTER upgrade**: ✅ Work correctly (encrypted with real key via fixed code)

## Recovery Steps

If you upgraded and experienced SMTP authentication failures:

### Option 1: Re-enter Credentials (Recommended)

1. Navigate to Admin → Settings → Email / SMTP
2. Re-enter your SMTP password
3. Test the connection — it will now use the correct encryption key
4. Any other credentials (gateway credentials, integration settings) that were failing can be re-entered similarly

**Why this works:**
- The fix ensures new encryptions use the correct key from `ipconfig.php`
- Old encrypted values (from the broken period) are garbage and unrecoverable
- Re-entering passwords takes seconds and requires no database recovery

### Option 2: Manual Database Fix (Advanced)

If you have a backup from before the upgrade, you can restore credentials from that backup. Otherwise, manual database recovery is not recommended since:

1. Credentials encrypted during the broken period cannot be decrypted (wrong key)
2. The encrypted values in the database are not human-readable
3. Re-entering credentials is simpler and safer

## Validation

Run the security test suite to confirm the fix:

```bash
# Tests validating the bug is fixed
vendor/bin/phpunit tests/Unit/Security/EncryptionKeyLoadingTest.php
vendor/bin/phpunit tests/Unit/Security/IntegrationSettingsCipherTest.php

# Full unit test suite
vendor/bin/phpunit tests/Unit/
```

Tests verify:
- ✅ `env()` correctly reads ENCRYPTION_KEY from $_ENV
- ✅ `getenv()` cannot find keys populated only by Dotenv
- ✅ SMTP credential round-trip encryption/decryption works
- ✅ Integration settings cipher uses correct key loading
- ✅ Missing ENCRYPTION_KEY fails loudly (not silently with empty key)

## Prevention

To prevent regression:

1. **Never use `getenv()` for security-critical configuration** — Dotenv only populates `$_ENV`
2. **Always use `env()` helper** defined in `bootstrap/kernel.php`
3. **Or directly access `$_ENV`** as a fallback
4. **Run test suite** before pushing changes to ensure encryption tests pass

## References

- Related test: `tests/Unit/Security/EncryptionKeyLoadingTest.php`
- Related test: `tests/Unit/Security/IntegrationSettingsCipherTest.php`
- Bootstrap config: `bootstrap/kernel.php` (see `env()` function)
- Crypt implementation: `application/libraries/Crypt.php`
- Integration cipher: `application/modules/integrations/libraries/IntegrationSettingsCipher.php`

## Questions?

If you experience issues after upgrading or applying this fix:
1. Check that `ipconfig.php` has `ENCRYPTION_KEY` set
2. Re-enter SMTP password via Admin → Settings → Email
3. Verify test suite passes: `vendor/bin/phpunit tests/Unit/Security/`
4. Report any remaining issues with `ENCRYPTION_KEY` configuration
