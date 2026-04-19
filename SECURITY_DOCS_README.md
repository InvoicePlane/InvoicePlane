# Security Documentation - Arbitrary File Deletion Vulnerability

This directory contains comprehensive documentation for the arbitrary file deletion vulnerability (CVE pending) discovered and fixed in InvoicePlane v1.7.2.

## Quick Reference

| Document | Purpose | Audience |
|----------|---------|----------|
| **SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md** | Complete security advisory with technical details | Security researchers, administrators |
| **CVE_REQUEST_SUMMARY.md** | CVE allocation request guide and templates | CVE requesters, security teams |
| **CHANGELOG.md** | Release notes with vulnerability details | All users |
| **verify_file_deletion_fix.php** | Automated test script | System administrators, developers |

## Vulnerability Summary

**Type:** Arbitrary File Deletion via Path Traversal (CWE-22)  
**Severity:** HIGH (CVSS v3.1 Score: 7.1)  
**Status:** Fixed in v1.7.2  
**CVE ID:** Pending allocation  

### Impact

An authenticated administrator could delete arbitrary files on the server by exploiting path traversal sequences in logo filename settings, potentially causing:
- Application failure (deletion of config files)
- Data loss (deletion of application or user files)
- Denial of service

### Quick Fix

**Users:** Upgrade to InvoicePlane v1.7.2 or later immediately.

```bash
cd /path/to/invoiceplane
git fetch origin
git checkout v1.7.2
```

**Verification:** Run the verification script to confirm the fix:

```bash
php verify_file_deletion_fix.php
```

## Documents Overview

### 1. Security Advisory

**File:** `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md`

**Contents:**
- Vulnerability description and technical analysis
- Attack scenarios and proof of concept
- Fix implementation details with code examples
- Defense-in-depth layers explained
- Remediation instructions for users and developers
- Verification testing procedures
- Timeline and disclosure information

**Use this document for:**
- Understanding the vulnerability in detail
- Learning about the fix implementation
- Getting remediation instructions
- Security auditing and verification

### 2. CVE Request Summary

**File:** `CVE_REQUEST_SUMMARY.md`

**Contents:**
- CVE request submission guide
- Pre-filled request templates
- CVSS v3.1 scoring breakdown
- Suggested CVE descriptions
- Contact information
- References and links

**Use this document for:**
- Requesting a CVE ID
- Submitting to CNA (CVE Numbering Authority)
- Understanding CVSS scoring
- Getting ready-to-use request templates

### 3. Verification Script

**File:** `verify_file_deletion_fix.php`

**Purpose:**
Automated test suite that verifies the arbitrary file deletion fix is properly implemented.

**Tests:**
- ✓ Path traversal detection (../, ..\\, /.., \\..)
- ✓ Null byte injection prevention
- ✓ Absolute path blocking
- ✓ Windows drive letter blocking
- ✓ Valid filename acceptance
- ✓ Settings controller validation
- ✓ File security helper functions
- ✓ File access validation

**Usage:**
```bash
cd /path/to/invoiceplane
php verify_file_deletion_fix.php
```

**Expected output (if fix is present):**
```
=================================================================
✓ ALL TESTS PASSED

The arbitrary file deletion vulnerability fix is properly implemented.
InvoicePlane is protected against path traversal attacks in logo deletion.
=================================================================
```

## For Different Audiences

### For Users

1. **Check if you're affected:**
   - Running InvoicePlane 1.7.0 or 1.7.1? → You're affected
   - Running v1.7.2 or later? → You're protected

2. **Immediate action:**
   ```bash
   # Backup your installation
   tar -czf invoiceplane-backup-$(date +%Y%m%d).tar.gz /var/www/invoiceplane
   
   # Upgrade to v1.7.2
   cd /var/www/invoiceplane
   git fetch origin
   git checkout v1.7.2
   ```

3. **Verify the fix:**
   ```bash
   php verify_file_deletion_fix.php
   ```

4. **Audit your system:**
   - Check for missing files in your installation
   - Review administrator access logs
   - Check database for suspicious logo filename values

### For Developers

1. **Understand the vulnerability:**
   - Read `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md` sections:
     - Technical Details
     - Attack Scenario
     - Fix Implementation

2. **Learn from the fix:**
   - Study the defense-in-depth approach (7 security layers)
   - Examine the `validate_safe_filename()` implementation
   - Review the `validate_file_access()` multi-layer validation

3. **Apply best practices:**
   ```php
   // Always validate file paths
   $this->load->helper('file_security');
   $validation = validate_file_access($filename, $base_directory);
   if (!$validation['valid']) {
       // Handle error
   }
   ```

### For Security Researchers

1. **Full technical details:**
   - See `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md`
   - Attack scenarios, PoC, and exploitation details
   - CVSS scoring rationale

2. **Request CVE:**
   - Use `CVE_REQUEST_SUMMARY.md` as your guide
   - Pre-filled templates ready to submit
   - All required information provided

3. **Verify the fix:**
   ```bash
   php verify_file_deletion_fix.php
   ```

4. **Additional research:**
   - Review the fix implementation in code
   - Test against your own attack vectors
   - Contribute additional test cases

### For CVE Numbering Authorities

All required information for CVE allocation is available in `CVE_REQUEST_SUMMARY.md`:

- **Product information:** InvoicePlane v1.7.0, v1.7.1
- **Vulnerability type:** CWE-22 (Path Traversal)
- **CVSS score:** 7.1 (HIGH)
- **Description templates:** Ready to use (concise and detailed versions)
- **References:** Security advisory, changelog, repository links
- **Timeline:** Discovery, fix, disclosure dates
- **Proof of concept:** Available in security advisory

## Fix Details

### Files Changed

1. **application/modules/settings/controllers/Settings.php**
   - Lines 78-87: Input validation on settings save
   - Lines 272-282: Type parameter validation
   - Lines 293-323: File access validation

2. **application/helpers/file_security_helper.php**
   - `validate_safe_filename()`: Path traversal and malicious input detection
   - `validate_file_in_directory()`: Directory confinement check
   - `validate_file_access()`: Comprehensive multi-layer validation

### Defense-in-Depth Layers

1. **Input validation** - Filenames validated when saved to settings
2. **Type validation** - Logo type restricted to ['invoice', 'login']
3. **Path traversal detection** - Checks for ../, ..\\, /.., \\.., etc.
4. **Null byte detection** - Prevents path truncation attacks
5. **Absolute path rejection** - Blocks /etc/passwd style attacks
6. **Directory confinement** - Files must be in ./uploads/ directory
7. **Secure logging** - Attack attempts logged with hash (prevents log injection)

## Disclosure Timeline

- **2026-04-06:** Vulnerability fixed in v1.7.2
- **2026-04-19:** Security advisory created and published
- **2026-04-19:** CVE request prepared
- **TBD:** CVE ID assigned
- **TBD:** Public disclosure coordinated

## Contact

**Security Issues:**
- Email: security@invoiceplane.com

**General Questions:**
- Email: mail@invoiceplane.com

**Responsible Disclosure:**
Please report security vulnerabilities privately before public disclosure.

## Additional Resources

- **InvoicePlane Security Policy:** SECURITY.md
- **Full Changelog:** CHANGELOG.md
- **Additional Security Fixes:** ADDITIONAL_SECURITY_FIXES_v1.7.2.md
- **RCE Fix Advisory:** SECURITY_ADVISORY_RCE_FIX.md

---

**Document Version:** 1.0  
**Last Updated:** 2026-04-19  
**Maintained by:** InvoicePlane Security Team
