# Migration Guide: Upgrading to v1.7.2 (RCE Fix)

This guide helps administrators safely upgrade from vulnerable versions (v1.7.0, v1.7.1) to v1.7.2 which fixes a critical Remote Code Execution vulnerability.

## Pre-Upgrade Security Audit

**IMPORTANT:** Before upgrading, check if your installation has already been compromised.

### 1. Check for Malicious Template Files

List all files in template directories:

```bash
cd /path/to/invoiceplane

# List invoice templates (public)
ls -la application/views/invoice_templates/public/

# List invoice templates (PDF)
ls -la application/views/invoice_templates/pdf/

# List quote templates (public)
ls -la application/views/quote_templates/public/

# List quote templates (PDF)
ls -la application/views/quote_templates/pdf/
```

**Expected Files Only:**

Invoice Templates (Public):
- `InvoicePlane_Web.php`
- `.gitignore`

Invoice Templates (PDF):
- `InvoicePlane.php`
- `InvoicePlane - paid.php`
- `InvoicePlane - overdue.php`
- `.gitignore`

Quote Templates (Public):
- `InvoicePlane_Web.php`
- `.gitignore`

Quote Templates (PDF):
- `InvoicePlane.php`
- `.gitignore`

**⚠️ If you find ANY other PHP files, they are potentially malicious and should be investigated immediately.**

### 2. Check Database for Suspicious Template Settings

```sql
-- Connect to your InvoicePlane database
mysql -u your_username -p your_database_name

-- Check all template settings
SELECT setting_key, setting_value 
FROM ip_settings 
WHERE setting_key LIKE '%template%';
```

**Expected Values:**
- `public_invoice_template`: `InvoicePlane_Web`
- `public_quote_template`: `InvoicePlane_Web`
- `pdf_invoice_template`: `InvoicePlane`
- `pdf_invoice_template_paid`: `InvoicePlane - paid`
- `pdf_invoice_template_overdue`: `InvoicePlane - overdue`
- `pdf_quote_template`: `InvoicePlane`
- `email_invoice_template`: (various, should be from email templates)
- `email_quote_template`: (various, should be from email templates)

**⚠️ If any PDF or public template setting contains an unexpected value, investigate immediately.**

### 3. Review Web Server Logs

Check for suspicious access patterns:

```bash
# Check Apache access logs
grep -i "evil\|shell\|cmd\|exec\|system" /var/log/apache2/access.log | tail -50

# Check for unusual query parameters
grep "guest/view/invoice" /var/log/apache2/access.log | grep -i "[\?&][a-z0-9]=.*[;&|<>]" | tail -20

# Check error logs for template-related errors
grep "template" /var/log/apache2/error.log | tail -20
```

### 4. Check Recent File Modifications

Find recently modified PHP files:

```bash
cd /path/to/invoiceplane

# Find PHP files modified in the last 30 days in template directories
find application/views/invoice_templates/ application/views/quote_templates/ \
  -name "*.php" -type f -mtime -30 -ls

# Check for suspicious file names
find application/views/ -name "*.php" | grep -iE "(shell|cmd|evil|hack|backdoor|c99|r57)"
```

## Upgrade Process

### Step 1: Backup Everything

```bash
# Backup database
mysqldump -u your_username -p your_database_name > invoiceplane_backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
cd /path/to/invoiceplane/..
tar -czf invoiceplane_files_backup_$(date +%Y%m%d_%H%M%S).tar.gz invoiceplane/

# Verify backups were created
ls -lh *.tar.gz *.sql
```

### Step 2: Remove Suspicious Files (If Found)

If you found unexpected PHP files in template directories:

```bash
# DO NOT just delete - investigate first!
# Move suspicious files to quarantine for analysis
mkdir -p /tmp/invoiceplane_quarantine
mv application/views/invoice_templates/public/suspicious_file.php /tmp/invoiceplane_quarantine/

# Document what you found
ls -la /tmp/invoiceplane_quarantine/ > quarantine_inventory.txt
```

### Step 3: Clean Suspicious Database Settings (If Found)

```sql
-- Reset template settings to defaults (if they were tampered with)
UPDATE ip_settings SET setting_value = 'InvoicePlane_Web' WHERE setting_key = 'public_invoice_template';
UPDATE ip_settings SET setting_value = 'InvoicePlane_Web' WHERE setting_key = 'public_quote_template';
UPDATE ip_settings SET setting_value = 'InvoicePlane' WHERE setting_key = 'pdf_invoice_template';
UPDATE ip_settings SET setting_value = 'InvoicePlane - paid' WHERE setting_key = 'pdf_invoice_template_paid';
UPDATE ip_settings SET setting_value = 'InvoicePlane - overdue' WHERE setting_key = 'pdf_invoice_template_overdue';
UPDATE ip_settings SET setting_value = 'InvoicePlane' WHERE setting_key = 'pdf_quote_template';
```

### Step 4: Download and Install v1.7.2

```bash
cd /path/to/invoiceplane

# Pull the latest code (if using git)
git fetch origin
git checkout v1.7.2  # Or the appropriate branch/tag

# Or download the release package
# wget https://github.com/InvoicePlane/InvoicePlane/releases/download/v1.7.2/InvoicePlane-v1.7.2.zip
# unzip InvoicePlane-v1.7.2.zip -d /path/to/invoiceplane

# Update Composer dependencies
composer install --no-dev --optimize-autoloader

# Clear CodeIgniter cache
rm -rf application/cache/*
```

### Step 5: Set Secure File Permissions

```bash
cd /path/to/invoiceplane

# Make template directories read-only for web server
chmod 555 application/views/invoice_templates/public/
chmod 555 application/views/invoice_templates/pdf/
chmod 555 application/views/quote_templates/public/
chmod 555 application/views/quote_templates/pdf/

# Make template files read-only
find application/views/invoice_templates/ application/views/quote_templates/ \
  -name "*.php" -type f -exec chmod 444 {} \;

# Set ownership (adjust www-data to your web server user)
chown -R www-data:www-data application/
chown -R www-data:www-data uploads/

# Make critical files read-only even for web server
chmod 444 application/views/invoice_templates/public/*.php
chmod 444 application/views/invoice_templates/pdf/*.php
chmod 444 application/views/quote_templates/public/*.php
chmod 444 application/views/quote_templates/pdf/*.php
```

### Step 6: Verify the Fix

Test that malicious templates are rejected:

```bash
# Create a test file (this should be blocked by the new code)
echo '<?php echo "TEST - This should never execute"; ?>' > application/views/invoice_templates/public/test_evil.php

# Try to use it via database
mysql -u your_username -p your_database_name -e \
  "UPDATE ip_settings SET setting_value = 'test_evil' WHERE setting_key = 'public_invoice_template';"

# Access a public invoice URL
# Expected: Default template (InvoicePlane_Web) is used, NOT test_evil.php
# Check logs for error: "Template validation failed: Template not in static whitelist: test_evil"

# Clean up test file
rm application/views/invoice_templates/public/test_evil.php

# Reset template setting to default
mysql -u your_username -p your_database_name -e \
  "UPDATE ip_settings SET setting_value = 'InvoicePlane_Web' WHERE setting_key = 'public_invoice_template';"
```

### Step 7: Test Normal Functionality

1. Log in as administrator
2. Create a test invoice
3. View the invoice as a guest (public URL)
4. Generate PDF
5. Verify everything works as expected

## Post-Upgrade Security Hardening

### 1. Implement File Integrity Monitoring

Install and configure AIDE (Advanced Intrusion Detection Environment):

```bash
# Install AIDE
sudo apt-get install aide  # Debian/Ubuntu
sudo yum install aide      # CentOS/RHEL

# Initialize database
sudo aideinit

# Run checks daily via cron
echo "0 2 * * * /usr/bin/aide --check" | sudo crontab -
```

### 2. Enable ModSecurity (Web Application Firewall)

```bash
# Install ModSecurity
sudo apt-get install libapache2-mod-security2

# Enable recommended rules
sudo cp /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf
sudo sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.conf

# Restart Apache
sudo systemctl restart apache2
```

### 3. Limit Admin Access

- Use separate admin accounts (no shared credentials)
- Implement IP restrictions for admin panel
- Enable two-factor authentication (if available in future versions)
- Review and revoke unused admin accounts

### 4. Regular Security Audits

Create a monthly security checklist:

```bash
#!/bin/bash
# save as: /usr/local/bin/invoiceplane_security_audit.sh

echo "=== InvoicePlane Security Audit ==="
echo "Date: $(date)"
echo ""

echo "1. Checking for unexpected template files..."
find /path/to/invoiceplane/application/views/invoice_templates/ \
     /path/to/invoiceplane/application/views/quote_templates/ \
     -name "*.php" -type f | sort
echo ""

echo "2. Checking template file permissions..."
find /path/to/invoiceplane/application/views/invoice_templates/ \
     /path/to/invoiceplane/application/views/quote_templates/ \
     -name "*.php" -type f -ls
echo ""

echo "3. Checking template directory permissions..."
ls -ld /path/to/invoiceplane/application/views/invoice_templates/*/
echo ""

echo "4. Recent access to guest invoice URLs..."
grep "guest/view/invoice" /var/log/apache2/access.log | tail -10
echo ""

echo "5. Template-related errors..."
grep "template" /var/log/apache2/error.log | tail -5
echo ""
```

Schedule it monthly:
```bash
chmod +x /usr/local/bin/invoiceplane_security_audit.sh
echo "0 0 1 * * /usr/local/bin/invoiceplane_security_audit.sh > /var/log/invoiceplane_audit_$(date +\%Y\%m).log" | sudo crontab -
```

## Troubleshooting

### Issue: Custom Templates Stop Working

**Symptom:** Custom template you created no longer works after upgrade

**Cause:** Custom templates are not in the static whitelist

**Solution:** Add your custom template to the whitelist in code:

1. Edit `application/modules/invoices/models/Mdl_templates.php`
2. Add your template name to the appropriate array:
   ```php
   private const ALLOWED_INVOICE_TEMPLATES = [
       'pdf' => [
           'InvoicePlane',
           'InvoicePlane - paid',
           'InvoicePlane - overdue',
           'YourCustomTemplate',  // Add this line
       ],
       // ...
   ];
   ```
3. Redeploy the application

### Issue: "Template file not found" Error

**Symptom:** Error 500 when viewing invoices/quotes

**Cause:** Template file doesn't exist or has wrong permissions

**Solution:**

```bash
# Check if file exists
ls -la application/views/invoice_templates/public/InvoicePlane_Web.php

# Fix permissions if needed
chmod 444 application/views/invoice_templates/public/InvoicePlane_Web.php
chown www-data:www-data application/views/invoice_templates/public/InvoicePlane_Web.php
```

### Issue: "Permission Denied" When Trying to Add New Templates

**Symptom:** Cannot upload or create new template files

**Cause:** Template directories are now read-only (by design)

**Solution:** This is the correct security posture. To add new templates:

1. Add the file via SSH/SFTP with elevated privileges
2. Add the template to the code whitelist
3. Deploy both changes
4. Set file back to read-only: `chmod 444 new_template.php`

## Support

If you need assistance with the upgrade:

1. **Community Forum:** https://community.invoiceplane.com
2. **GitHub Issues:** https://github.com/InvoicePlane/InvoicePlane/issues
3. **Security Issues:** security@invoiceplane.com (private, do not post publicly)

## Verification Checklist

After completing the upgrade, verify:

- [ ] All template directories are read-only (chmod 555)
- [ ] All template files are read-only (chmod 444)
- [ ] Database template settings are using valid template names only
- [ ] No unexpected PHP files exist in template directories
- [ ] Invoices and quotes display correctly on public URLs
- [ ] PDF generation works correctly
- [ ] Error logs show no template-related errors
- [ ] Web server logs show no suspicious activity
- [ ] File integrity monitoring is configured
- [ ] Regular security audits are scheduled

## Next Steps

1. Subscribe to InvoicePlane security announcements
2. Keep InvoicePlane updated with latest security patches
3. Review the [SECURITY.md](SECURITY.md) file for reporting procedures
4. Consider professional security audit if heavily customized
5. Implement defense-in-depth security measures (WAF, IDS, etc.)

## Important Notes

- **Do not skip the pre-upgrade audit** - you need to know if you were compromised
- **Keep backups** for at least 90 days after upgrade
- **Monitor logs** closely for the first week after upgrade
- **Test thoroughly** in a staging environment first if possible
- **Document everything** you find during the audit process

This vulnerability was serious. Take time to ensure your upgrade is complete and secure.
