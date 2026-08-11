# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Full write-ups for individual vulnerabilities live in the published
[GitHub Security Advisories](https://github.com/InvoicePlane/InvoicePlane/security/advisories)
and in [`.github/security/`](security/). This changelog records *what* changed; the advisories
record *why* and *how*.

---

## [1.7.2] - 2026-07-17

InvoicePlane 1.7.2 is a **security-focused release**. It resolves every vulnerability
responsibly disclosed against v1.7.0 / v1.7.1 and hardens the application, session handling,
and container tooling throughout. **If you run v1.7.0 or v1.7.1, upgrade immediately** — this
release fixes a critical (CVSSv3 9.9) Remote Code Execution vulnerability.

### Thank you

Huge thanks to the security researchers who responsibly disclosed these issues through private
[GitHub Security Advisories](https://github.com/InvoicePlane/InvoicePlane/security/advisories).
Without your reports this release would not have been possible:

[@0raN9ewww](https://github.com/0raN9ewww),
[@5ud0er](https://github.com/5ud0er),
[@akgul7990](https://github.com/akgul7990),
[@alanturing881](https://github.com/alanturing881),
[@alham-rizvi](https://github.com/alham-rizvi),
[@ali-iltizar](https://github.com/ali-iltizar),
[@ashrexon](https://github.com/ashrexon),
[@baozongwi](https://github.com/baozongwi),
[@capt-bl4ck0ut](https://github.com/capt-bl4ck0ut),
[@chakrapani150](https://github.com/chakrapani150),
[@Char0n1507](https://github.com/Char0n1507),
[@Chittu13](https://github.com/Chittu13),
[@cyabell](https://github.com/cyabell),
[@de3erve-hunter](https://github.com/de3erve-hunter),
[@EvidentObscurity](https://github.com/EvidentObscurity),
[@FelipeSilvany](https://github.com/FelipeSilvany),
[@FORIMOC](https://github.com/FORIMOC),
[@geo-chen](https://github.com/geo-chen),
[@HuajiHD](https://github.com/HuajiHD),
[@iiihaiii](https://github.com/iiihaiii),
[@kitu232](https://github.com/kitu232),
[@lighthousekeeper1212](https://github.com/lighthousekeeper1212),
[@mattmumford-git](https://github.com/mattmumford-git),
[@PLpaPLpa](https://github.com/PLpaPLpa),
[@polybjorn](https://github.com/polybjorn),
[@QiaoNPC](https://github.com/QiaoNPC),
[@radoi-teodor](https://github.com/radoi-teodor),
[@Santoshkumarpuppala](https://github.com/Santoshkumarpuppala),
[@tikket1](https://github.com/tikket1),
[@tonghuaroot](https://github.com/tonghuaroot),
[@udaypali](https://github.com/udaypali),
[@venkatesh2003631](https://github.com/venkatesh2003631),
[@Vijay-raghav7](https://github.com/Vijay-raghav7).

And thank you to the contributors whose code shipped in this release:
[@drewangell](https://github.com/drewangell) (Stripe/PayPal, Advanced Credit Cards & Venmo),
[@mpldr](https://github.com/mpldr) (Docker application container, configurable QR-code size),
and [@PatrickGTR](https://github.com/PatrickGTR) (quote public-template fix).

### Security Vulnerability Summary

> Each **GHSA** link is the canonical record of the report and its reporter, including its full
> severity and CVSS breakdown — use them to trace and credit disclosures. PR cells left as `—`
> are filled from the advisory once published. Every fix here is complete in RC1, but the
> advisories stay in **draft** until 1.7.2 is finalized, so the GHSA links 404 for external
> readers until then. Please do not disclose details of any issue publicly before its advisory
> is published.

| Vulnerability | Severity | Security Advisory (GHSA) | Reported By | Fixed In |
|---|---|---|---|---|
| RCE via template filesystem scan | Critical | [SECURITY_ADVISORY_RCE_FIX.md](security/SECURITY_ADVISORY_RCE_FIX.md) · [Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) |
| Broken auth: password-reset tokens never expired | Critical | [Improper Password Reset Token Expiration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-5r28-6rw3-25c2) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514) |
| Arbitrary file deletion via path traversal | High | [SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md](security/SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md) · [[CVE-2026-40298]: Authenticated Arbitrary File Deletion via Path Traversal in "Invoice Logo" Setting](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-65v2-4g37-rxjw), [[CVE-2026-39978]: Authenticated path traversal in logo removal allows arbitrary file deletion outside uploads](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-45vj-9p52-f8mq) | [@ali-iltizar](https://github.com/ali-iltizar), [@iiihaiii](https://github.com/iiihaiii) | [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) |
| Weak PRNG in password-reset tokens | High | [Predictable Password Reset Token via md5(time()) Enables Account Takeover](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jfgr-778p-m943) | [@tikket1](https://github.com/tikket1) | [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) |
| Password-reset email flood via session-only rate limits | Medium | [Password Reset Rate Limiting Fully Bypassed by Sending Requests Without a Session Cookie](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-p7w2-hmm5-qw7m) | [@Char0n1507](https://github.com/Char0n1507) | [#1658](https://github.com/InvoicePlane/InvoicePlane/pull/1658) |
| SQL/DDL injection in tax rate decimal places | High | [SQL Injection via Unsanitized Tax Rate Decimal Places Field in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x6rh-cr7q-5w7j), [Improper Neutralization of Special Elements used in an SQL Command ('SQL Injection') in invoiceplane/invoiceplane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-34g5-4hfc-g983), [Authenticated SQL Injection via Unsanitized `tax_rate_decimal_places` in InvoicePlane General Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6qfv-cg7h-956g), [SQL Injection (DDL Injection) in Settings Module Leading to Schema Manipulation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-mhvf-2mxp-57g4) | [@tikket1](https://github.com/tikket1), [@udaypali](https://github.com/udaypali), [@FelipeSilvany](https://github.com/FelipeSilvany), [@akgul7990](https://github.com/akgul7990) | [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) |
| Configuration injection in DB setup wizard | High | [Configuration Injection in Setup Module Leading to Environment Manipulation (db_hostname Injection)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-ffq5-mw9f-mv6j) | [@akgul7990](https://github.com/akgul7990) | [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) |
| Unauthenticated database upgrade via setup wizard | High | [Unauthenticated Setup Upgrade Allows Database Migration Execution After Installation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-pp5w-98m2-gvc8), [Unauthenticated Invocation of Database Upgrade Operations Through an Incompletely Locked Setup Wizard](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-f8g5-6r57-xpw7) | [@FelipeSilvany](https://github.com/FelipeSilvany) | [#1659](https://github.com/InvoicePlane/InvoicePlane/pull/1659) |
| IDOR + CSRF on guest quote approve/reject | High | [Guest Quote Approval/Reject Horizontal Privilege Escalation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-pjf5-c2m5-7m4x), [Guest user IDOR: Quote approve/reject missing client_id scoping](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6xj3-274m-4mvg), [Security advisory — InvoicePlane: guest-portal IDOR allows any client to approve/reject another client's quote](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-2c9x-9qvc-r45r), [IDOR in Guest Quote Approval Allows Cross-Client Status Manipulation](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v7xm-q6rp-w6c5) | [@HuajiHD](https://github.com/HuajiHD), [@lighthousekeeper1212](https://github.com/lighthousekeeper1212), [@Santoshkumarpuppala](https://github.com/Santoshkumarpuppala), [@cyabell](https://github.com/cyabell) | [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) |
| Auth bypass in guest invoice/payment endpoints | Medium | [Improper Access Control in Guest Payment Flow Allows Access to Non-Public Invoices](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-f95x-25mh-wcxv) | [@FelipeSilvany](https://github.com/FelipeSilvany) | [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) |
| Setup wizard accessible post-installation | Medium | [Unauthenticated Setup Reconfiguration](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-37pr-q48j-46gg), [Unauthenticated Setup Wizard Re-entry Allows Configuration Overwrite in InvoicePlane 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-2j6j-6f6q-57vq), [Unauthenticated Re-execution of Installation Wizard After Setup Allows Overwrite of Database Configuration, Denial of Service, and Potential Data Compromise](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jx5h-6r8f-m2h3) | [@HuajiHD](https://github.com/HuajiHD), [@iiihaiii](https://github.com/iiihaiii), [@kitu232](https://github.com/kitu232) | [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) |
| SSRF via PDF footer content | Medium | [SSRF via admin-stored PDF footer HTML](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vgq9-469p-q7j3) | [@radoi-teodor](https://github.com/radoi-teodor) | [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) |
| Open redirect via `HTTP_REFERER` | Medium | [Remote Code Execution via Writable Templates Directory in InvoicePlane v1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-v735-2x3r-gwpp) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505) |
| Payment gateway API credentials in plaintext | Medium | [Sensitive Data Exposure via HTML Source Code (Stripe & PayPal API Keys)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-8543-x4j8-jj4q) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) |
| Duplicate payment processing (Stripe callback replay) | Low–Medium | [Stripe Callback Replay](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-6cpc-hr8h-xgr2) | [@HuajiHD](https://github.com/HuajiHD) | [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) |
| Email template preview XSS | Low–Medium | [Stored XSS via Email Templates in InvoicePlane <= 1.7.1](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-4wqv-84px-8jc6) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) |
| Stored XSS via client email in invoice/quote mailer | Medium | [Stored Cross-Site Scripting (XSS) via Client Email in Invoice and Quote Mailer Forms](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-477r-xmgc-vcvj) | [@capt-bl4ck0ut](https://github.com/capt-bl4ck0ut) | [#1635](https://github.com/InvoicePlane/InvoicePlane/pull/1635) |
| EXIF metadata in uploaded images | Low | [Sensitive Information Disclosure via Unstripped EXIF Metadata in Attachments](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-7j67-2v6p-275v) | [@Vijay-raghav7](https://github.com/Vijay-raghav7) | [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) |
| SQL injection via string concatenation in the Ajax `having()` clause | High | [SQL Injection Vulnerability in User Ajax name_query via String Concatenation in having() Clause](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-g7f2-mj3r-xx88) | [@PLpaPLpa](https://github.com/PLpaPLpa) | [#1630](https://github.com/InvoicePlane/InvoicePlane/pull/1630) |
| Arbitrary local file read via mPDF `file://` in the Sales-by-Year report | High | [Arbitrary local file read via mPDF file:// in the Sales-by-Year report (unescaped client VAT-ID)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-j89p-m59x-86r9) | [@tonghuaroot](https://github.com/tonghuaroot) | [#1615](https://github.com/InvoicePlane/InvoicePlane/pull/1615) |
| Unauthenticated PayPal capture payment forgery (no currency/amount validation) | High | [Unauthenticated payment forgery: PayPal capture endpoint does not validate currency or amount against the invoice](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-35qh-94hc-hm2w) | [@tonghuaroot](https://github.com/tonghuaroot) | [#1616](https://github.com/InvoicePlane/InvoicePlane/pull/1616) |
| Path traversal risk | High | [Path Traversal Risk](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-9248-qccw-7x7j) | [@venkatesh2003631](https://github.com/venkatesh2003631) | [#1560](https://github.com/InvoicePlane/InvoicePlane/pull/1560) |
| Sensitive financial data exposure via unauthenticated public invoice | High | [Sensitive Financial Data Exposure via Unauthenticated Public Invoice](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-9hx6-6h6f-2wq3) | [@chakrapani150](https://github.com/chakrapani150) | [#1556](https://github.com/InvoicePlane/InvoicePlane/pull/1556) |
| Authentication bypass via MD5 magic-hash type juggling | High | [Authentication Bypass via MD5 Magic Hash Type Juggling](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x66r-7wf6-wgv7) | [@geo-chen](https://github.com/geo-chen) | [#1555](https://github.com/InvoicePlane/InvoicePlane/pull/1555) |
| Guest attachment access bypass — missing authorization on Get controller | High | [Guest Attachment Access Bypass — Missing Authorization on Get Controller](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-phh5-3jc3-pm6w) | [@QiaoNPC](https://github.com/QiaoNPC) | [#1545](https://github.com/InvoicePlane/InvoicePlane/pull/1545) |
| Disabled users can still authenticate | High | [Disabled Users Can Still Authenticate](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jw57-6692-jh2q) | [@mattmumford-git](https://github.com/mattmumford-git) | [#1621](https://github.com/InvoicePlane/InvoicePlane/pull/1621) |
| Sensitive SUMEX invoice XML disclosure via web-accessible temp directory | High | [Sensitive SUMEX Invoice XML Disclosure via Web-Accessible Temporary Directory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-583r-4pw9-6rc9) | [@mattmumford-git](https://github.com/mattmumford-git) | [#1620](https://github.com/InvoicePlane/InvoicePlane/pull/1620) |
| Sensitive import CSV disclosure via web-accessible import directory | High | [Sensitive Import CSV Disclosure via Web-Accessible Import Directory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-wv2c-c285-9hrq) | [@mattmumford-git](https://github.com/mattmumford-git) | [#1623](https://github.com/InvoicePlane/InvoicePlane/pull/1623) |
| Authorization bypass through user-controlled key (IDOR) | High | [Authorization Bypass Through User-Controlled Key in InvoicePlane/InvoicePlane](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-h4xh-4jwc-485r) | [@0raN9ewww](https://github.com/0raN9ewww) | [#1626](https://github.com/InvoicePlane/InvoicePlane/pull/1626) |
| Missing CSRF protection on state-changing delete / logo actions | High | [CSRF via GET-Based Logo Removal in Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-qx8h-35wf-cgqf), [Missing CSRF Protection on State-Changing delete Actions](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-mhvh-4j3w-7pvj) | [@mattmumford-git](https://github.com/mattmumford-git), [@venkatesh2003631](https://github.com/venkatesh2003631) | [#1559](https://github.com/InvoicePlane/InvoicePlane/pull/1559), [#1561](https://github.com/InvoicePlane/InvoicePlane/pull/1561), [#1622](https://github.com/InvoicePlane/InvoicePlane/pull/1622), [#1624](https://github.com/InvoicePlane/InvoicePlane/pull/1624) |
| Second-order SQL injection via the unvalidated `custom_field_table` field | Medium | [Second-order SQL injection through the unvalidated custom_field_table field in the Custom Fields module](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vv4r-cgmw-w6x2), [Authenticated Stored Blind SQL Injection via Custom Field Table Identifier](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-98vm-2r9v-mpgj) | [@5ud0er](https://github.com/5ud0er), [@capt-bl4ck0ut](https://github.com/capt-bl4ck0ut) | [#1573](https://github.com/InvoicePlane/InvoicePlane/pull/1573), [#1632](https://github.com/InvoicePlane/InvoicePlane/pull/1632) |
| Unauthenticated GET-based quote approval/rejection (CSRF via link prefetching) | Medium | [Unauthenticated GET-based quote approval/rejection enables CSRF-equivalent attack via email link prefetching](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-7r3g-fppx-2p79) | [@de3erve-hunter](https://github.com/de3erve-hunter) | [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) |
| Stored XSS via CSV import (payment method name) | Medium | [Stored XSS in InvoicePlane via CSV import: unescaped payment method name bypasses input filter and executes in administrator sessions](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vm87-2qmm-rhg8) | [@EvidentObscurity](https://github.com/EvidentObscurity) | [#1574](https://github.com/InvoicePlane/InvoicePlane/pull/1574) |
| Invoice PDF passwords stored in plaintext | Medium | [Invoice PDF Passwords Stored Plaintext](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-vphv-wmr3-68fp) | [@chakrapani150](https://github.com/chakrapani150) | [#1557](https://github.com/InvoicePlane/InvoicePlane/pull/1557) |
| Stored XSS in online payment settings | Medium | [Stored Cross-Site Scripting (XSS) in Online Payment Settings](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-xvrc-hv8j-8v8w) | [@ali-iltizar](https://github.com/ali-iltizar) | [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) |
| Vulnerability report (InvoicePlane_001) | Medium | [InvoicePlane_001 Vulnerability Report](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-wj6q-j965-2w2v) | [@FORIMOC](https://github.com/FORIMOC) | [#1547](https://github.com/InvoicePlane/InvoicePlane/pull/1547) |
| Non-constant-time `cron_key` comparison in the recurring-invoice cron endpoint | Low | [Non-constant-time cron_key comparison in recurring invoice cron endpoint (CWE-208)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-jg68-6mqc-hxcr) | [@alanturing881](https://github.com/alanturing881) | [#1583](https://github.com/InvoicePlane/InvoicePlane/pull/1583) |
| PHPMailer debug output in AJAX responses | Low–Medium | — | Internal audit | [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) |
| XSS session hijack via `cookie_httponly=false` | High | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Clickjacking — `X-Frame-Options` not always sent | Medium | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Session fixation — `SESS_REGENERATE_DESTROY` defaulted `false` | Medium | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Log injection via password-reset token/email | Low | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Missing `Referrer-Policy` header | Low | — | Internal audit | [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) |
| Session auth check used loose comparison (`user_type`/`required_key`) | Medium | [Loose Type Comparison in Core Authentication Check (Defense-in-Depth)](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-346c-gqqq-mrm2) | [@Char0n1507](https://github.com/Char0n1507) | [#1640](https://github.com/InvoicePlane/InvoicePlane/pull/1640) |
| Log injection via unsanitized `cron_key` in `Cron::recur()` | Medium | [Log Injection via Unsanitized User Input in Cron Key Error Logging](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-g53q-v2pv-xr83) | [@Char0n1507](https://github.com/Char0n1507) | [#1639](https://github.com/InvoicePlane/InvoicePlane/pull/1639) |
| IDOR: admins could change other users' passwords | Medium | [IDOR: Horizontal Privilege Escalation via Password Change Without Authorization Check](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-x38q-xhjj-jr8w), [Horizontal Privilege Escalation — Any Authenticated Admin Can Change Any Other User's Password](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-p875-mj5j-x2fc) | [@Char0n1507](https://github.com/Char0n1507) | [#1638](https://github.com/InvoicePlane/InvoicePlane/pull/1638), [#1658](https://github.com/InvoicePlane/InvoicePlane/pull/1658) |
| CSRF on seven `delete()`-style endpoints missing token validation | Medium | [Missing CSRF Token Validation on Multiple Delete Endpoints](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-9372-vj68-hmc3) | [@Char0n1507](https://github.com/Char0n1507) | [#1637](https://github.com/InvoicePlane/InvoicePlane/pull/1637) |
| CSRF bypass in `Recurring::stop()` via GET request | Medium | [CSRF: Recurring Invoice State Change via GET Request Without CSRF Protection](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-qf9q-2hxm-4wh9) | [@Char0n1507](https://github.com/Char0n1507) | [#1636](https://github.com/InvoicePlane/InvoicePlane/pull/1636) |
| Guest invoice/quote access keys and CRON authentication key generated with non-cryptographic PRNG | Medium | [Guest invoice/quote access keys and the CRON authentication key are generated with a non-cryptographic PRNG](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-chqc-v432-8pj8) | [@alham-rizvi](https://github.com/alham-rizvi) | [#1651](https://github.com/InvoicePlane/InvoicePlane/pull/1651) |
| Password reset token compared with non-constant-time operator | Medium | [Password reset token is compared with a non-constant-time operator instead of hash_equals()](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-wcqc-qqv5-65ph) | [@alham-rizvi](https://github.com/alham-rizvi) | [#1651](https://github.com/InvoicePlane/InvoicePlane/pull/1651) |
| Sensitive user fields exposed through email-template placeholders | Medium | — | [@Char0n1507](https://github.com/Char0n1507) | [#1660](https://github.com/InvoicePlane/InvoicePlane/pull/1660) |
| SSRF via unescaped report PDF date parameters | Medium | — | [@Char0n1507](https://github.com/Char0n1507) | [#1661](https://github.com/InvoicePlane/InvoicePlane/pull/1661) |
| SSRF via unrestricted `SUMEX_URL` scheme, plus residual XML control-char/attribute injection and PII exposure in `Sumex.php` | Medium | — | [@Char0n1507](https://github.com/Char0n1507) | [#1663](https://github.com/InvoicePlane/InvoicePlane/pull/1663) |
| Missing CSRF protection on bulk `recalculate_all_invoices()` / `recalculate_all_quotes()` endpoints | Medium | — | Internal audit | [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) |
| Unescaped logo filename in PDF invoice header (`invoice_logo_pdf()`) | Low | — | Internal audit | [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) |
| Log injection via unsanitized `checkout_session_id` in the Stripe guest payment callback | Low | — | Internal audit | [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) |
| Permanent account lockout: failed-login counter can never pass 10, so the 12-hour auto-unlock is unreachable dead code | High | [Account lockout is permanent: the 12-hour auto-unlock is unreachable dead code](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-cjwm-qx8w-hrq2) | [@polybjorn](https://github.com/polybjorn) | [#1668](https://github.com/InvoicePlane/InvoicePlane/pull/1668) |
| Password-reset token expiry not enforced on the password-change POST (incomplete fix of GHSA-5r28-6rw3-25c2) | High | [Incomplete fix for password reset token expiration: expiry not enforced on password change](https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-fwj7-c84x-jvjq) | [@baozongwi](https://github.com/baozongwi) | [#1670](https://github.com/InvoicePlane/InvoicePlane/pull/1670), [#1671](https://github.com/InvoicePlane/InvoicePlane/pull/1671) |
| CSRF: `generate_pdf` marks a draft invoice/quote sent and assigns its number via GET | Medium | — | [@ashrexon](https://github.com/ashrexon) | [#1674](https://github.com/InvoicePlane/InvoicePlane/pull/1674) |

### Security fixes

*Ordered by severity, then CVSS, then PR number.*

- [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505), [#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506) — **RCE:** replace `directory_map()` template whitelist with static constants + `ipconfig.php` allowlist; fix five open-redirect instances; add `security_helper.php`. See [CUSTOM_TEMPLATES.md](docs/CUSTOM_TEMPLATES.md) for the resulting custom-template usage guide.
- [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500), [#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516) — **Stored XSS:** comprehensive, application-wide output escaping (32 findings across 17 view files in 4 modules).
- [#1635](https://github.com/InvoicePlane/InvoicePlane/pull/1635) — **Stored XSS (mailer):** escape `client_email` with `htmlsc()` in the invoice/quote mailer views (`to_email` value attribute) — a follow-up sink missed by the earlier sweep — plus `valid_email` validation on `client_email` in `Mdl_Clients` as defense-in-depth.
- [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471), [#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482), [#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487) — **IDOR + CSRF:** guest quote approve/reject and payment gateways now enforce client scoping and require POST.
- [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494) — **Weak PRNG:** replace password-reset token generation with `random_bytes(32)` (256-bit entropy).
- [#1658](https://github.com/InvoicePlane/InvoicePlane/pull/1658) — **Password-reset DoS:** move password-reset IP and email rate-limit counters from PHP session storage into persistent database-backed counters, preventing bypass by omitting or rotating the session cookie.
- [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481), [#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488) — **SQL/DDL injection:** harden tax-rate decimal-places setting (`TaxRateDecimalPlacesProcessor`, transaction wrap, unit tests) ([#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479)).
- [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513) — **Config injection:** block newline injection in the database setup wizard.
- [#1659](https://github.com/InvoicePlane/InvoicePlane/pull/1659) — **Setup upgrade lock:** the HTTP setup controller now rejects access when `SETUP_COMPLETED=true`, preventing unauthenticated post-installation access to database upgrade operations when `DISABLE_SETUP=false`.
- [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512), [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510) — **Arbitrary file deletion:** path-traversal guards for logo deletion; `validate_db_filename()`, symlink protection, attachment-name XSS prevention.
- [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515) — **Credential exposure:** Stripe/PayPal API keys no longer rendered into HTML source.
- [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517), [#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537) — **Auth bypass:** guest invoice/payment endpoints gain a `guest_visible()` filter (no draft-invoice access).
- [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491), [#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511), [#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518) — **Setup wizard:** locked after install (`SETUP_COMPLETED`), with an admin security-warning system.
- [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492) — **SSRF:** sanitize PDF footer content before mPDF rendering.
- [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486), [#1499](https://github.com/InvoicePlane/InvoicePlane/pull/1499) — **Email preview XSS:** render previews as sanitized/plain text instead of raw HTML.
- [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496) — **Payment replay:** unique `payment_external_id` index prevents duplicate Stripe/PayPal processing.
- [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495) — **Info leak:** route PHPMailer SMTP debug to the CI log via `sanitize_for_logging()` (no longer breaks AJAX).
- [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507) — **EXIF:** optional metadata stripping from uploads (`SEC_STRIP_EXIF_FROM_IMAGES`, off by default).
- [#1567](https://github.com/InvoicePlane/InvoicePlane/pull/1567) — **Session & infrastructure hardening:** `cookie_httponly=true`, default `X-Frame-Options: SAMEORIGIN`, session-fixation fix (`SESS_REGENERATE_DESTROY=true`), password-reset log-injection sanitization, and a `Referrer-Policy` header.
- [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536) — Review follow-ups: `entrypoint.sh` config-injection guard, XSS trait fix, logo URL escaping, custom-template allowlisting, `Mdl_reports` cast fix.
- **Auth bypass (deactivated accounts):** `Mdl_Sessions::auth()` now rejects a login for a deactivated user (`user_active != 1`) even with the correct password.
- **SUMEX XML disclosure:** SUMEX invoice XML is written to a non-web-accessible `storage/temp/` directory instead of the public `uploads/temp/` folder.
- **Import CSV disclosure:** added a `Deny from all` `.htaccess` to `uploads/import/`, blocking web access to in-progress CSV imports.
- **Object-level authorization (IDOR):** added `can_user_access()` / `can_user_manage()` model checks for clients, projects, user-clients, invoices, and quotes; wired into `/clients/view/{id}` and `/projects/view/{id}`.
- **CSRF hardening:** explicit POST + CSRF-token validation on the delete endpoints across 12 modules (defense-in-depth against forged state-changing requests).
- [#1640](https://github.com/InvoicePlane/InvoicePlane/pull/1640) — **Auth checkpoint hardening:** use strict (`!==`) comparison for the `user_type`/`required_key` session check in `Admin_Controller`/`Guest_Controller`, removing a PHP type-juggling risk in the app's primary authorization checkpoint.
- [#1639](https://github.com/InvoicePlane/InvoicePlane/pull/1639) — **Log injection:** sanitize `cron_key` before logging in `Cron::recur()`.
- [#1638](https://github.com/InvoicePlane/InvoicePlane/pull/1638), [#1658](https://github.com/InvoicePlane/InvoicePlane/pull/1658) — **IDOR:** restrict `Users::change_password()` so only the account owner or the primary administrator (`user_id=1`) can change a user's password.
- [#1637](https://github.com/InvoicePlane/InvoicePlane/pull/1637) — **CSRF:** add POST + CSRF-token validation to seven delete-style endpoints that relied only on `Base_Controller`'s URL-substring check.
- [#1636](https://github.com/InvoicePlane/InvoicePlane/pull/1636) — **CSRF:** add POST + CSRF-token validation to `Recurring::stop()`, previously reachable via a bare GET request.
- [#1651](https://github.com/InvoicePlane/InvoicePlane/pull/1651) — **Token hardening:** generate guest invoice/quote access keys and the CRON authentication key with a CSPRNG, and compare password-reset tokens with `hash_equals()`.
- [#1660](https://github.com/InvoicePlane/InvoicePlane/pull/1660) — **Email template data exposure:** `parse_template()` now substitutes only documented invoice, quote, client, user, SUMEX, and custom-field tags; unknown placeholders render empty instead of reflecting arbitrary properties from the selected database row. Invoice and quote queries also select explicit public `ip_users` columns, excluding password hashes, salts, and password-reset tokens. Thanks to [@Char0n1507](https://github.com/Char0n1507) for the responsible disclosure.
- [#1661](https://github.com/InvoicePlane/InvoicePlane/pull/1661) — **SSRF:** report PDFs now pass normalized date labels to their templates and escape those labels before mPDF rendering, preventing injected HTML resources in date fields from triggering server-side HTTP requests. Thanks to [@Char0n1507](https://github.com/Char0n1507) for the responsible disclosure.
- [#1663](https://github.com/InvoicePlane/InvoicePlane/pull/1663) — **SSRF + XML injection + PII exposure:** `Sumex::pdf()` now rejects any `SUMEX_URL` that isn't `https://` and restricts the cURL request/redirect protocols to HTTPS with certificate verification on, closing an admin-configuration SSRF into `file://`, internal hosts, or cloud metadata endpoints. XML control characters are stripped from free-text fields, and `invoice_number` / `user_subscribernumber` are restricted to a strict character allowlist before reaching `setAttribute()`, `nodeValue`, or the ESR coding-line builder. `storage/` (which holds the temporary SUMEX XML and its patient PII) now has explicit web-access-deny rules for both Apache and nginx. Thanks to [@Char0n1507](https://github.com/Char0n1507) for the responsible disclosure.
- [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) — **CSRF:** `recalculate_all_invoices()` and `recalculate_all_quotes()` were GET-reachable with no CSRF guard, unlike every other state-changing bulk action; both now require `ensure_valid_post_request()`, matching the pattern already used by `delete()` in the same controllers.
- [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) — **Stored XSS (PDF):** `invoice_logo_pdf()` interpolated the logo filename into `<img src="...">` unescaped, unlike its sibling `invoice_logo()`; now wrapped in `html_escape()`.
- [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) — **Log injection:** the Stripe guest callback logged the raw, unauthenticated `checkout_session_id` without `sanitize_for_logging()`, and a second error-log call only stripped the literal `<br>` string, not CRLF; both call sites now sanitize consistently.
- [#1664](https://github.com/InvoicePlane/InvoicePlane/pull/1664) — **Hardening (defense-in-depth):** `Mailer.php`'s `json_encode()` calls for the email-template compose modal now use `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT`, matching the existing pattern in `email_templates/views/form.php`; `Upload::delete_file()` now uses the shared `validate_file_in_directory()` helper instead of an ad-hoc `realpath()` check; `country_helper.php` validates the `$cldr` locale segment against an allowlist pattern before using it in an `include` path; and the password-reset request no longer issues a token to a deactivated account (mirroring the login-time `user_active` check) while preserving the identical response used to prevent email enumeration.
- [#1668](https://github.com/InvoicePlane/InvoicePlane/pull/1668) — **Permanent account lockout:** `Sessions::authenticate()`'s per-account lockout guard (`log_count < 10`) also gated the failure-recording call, so `log_count` could never pass 10 - the unlock branch in `_login_log_check()`, gated on `log_count > 10`, was therefore unreachable dead code and locked-out accounts stayed locked forever, recoverable only via a manual database `DELETE`. Separately, that unlock branch measured elapsed time with `DateInterval::$h`, which is only the 0-23 hour *component* of the difference, not the total elapsed hours, so even a reachable check would misfire past the first day. `_login_log_check()` now unlocks at `log_count >= 10` (matching the actual, unchanged 10-attempt threshold) using the same Unix-timestamp-based window check already used for password-reset rate limiting (`_login_log_is_within_window()`), so the 12-hour lockout now actually expires. Thanks to [@polybjorn](https://github.com/polybjorn) for the responsible disclosure.
- [#1670](https://github.com/InvoicePlane/InvoicePlane/pull/1670), [#1671](https://github.com/InvoicePlane/InvoicePlane/pull/1671) — **Password-reset token expiry bypass (incomplete-fix follow-up):** the token-expiry check added for GHSA-5r28-6rw3-25c2 ran only on the reset-link (GET) flow, so the password-change POST (`btn_new_password`) could still set a new password with an expired-but-stored token; the released schema also shipped without the `user_passwordreset_token_expiry` column the controller reads and writes. `_reject_expired_password_reset_token()` now enforces the expiry on both the GET and POST flows, and the column is added in the `043_1.7.2.sql` migration (#1670). The expiry parse is further hardened to strict `DateTime::createFromFormat('!Y-m-d H:i:s', …)` with an anchored canonical-format guard (rejecting values such as `25:99:99` or `2026-8-10 9:05:07`), the password-change POST now validates its CSRF token and uses the shared `get_safe_referer()` helper instead of raw `$_SERVER['HTTP_REFERER']`, and unknown / expired / malformed tokens all return one generic message so the response never reveals which check failed (#1671). Thanks to [@baozongwi](https://github.com/baozongwi) for the responsible disclosure.
- [#1674](https://github.com/InvoicePlane/InvoicePlane/pull/1674) — **CSRF (state-changing GET):** `invoices/generate_pdf/<id>` (and its `quotes/generate_pdf/<id>` sibling) assigned an official invoice/quote number and marked a draft as sent (via the `mark_invoices_sent_pdf` / `mark_quotes_sent_pdf` settings) on any authenticated GET, so a forged cross-site request — e.g. `<img src=".../invoices/generate_pdf/ID">` — could silently transition drafts to sent and burn document numbers in an administrator's session. The mark-sent side effect now runs only when the request carries a valid same-origin CSRF token (`verify_get_csrf_token()`, a query-string variant of `verify_csrf_token()`, checked against the CSRF cookie with `hash_equals()`); the in-app "generate PDF" links pass the token via the new `_csrf_query()` helper. The PDF itself is a safe read and still streams regardless of the token, so download links are unaffected. Thanks to [@ashrexon](https://github.com/ashrexon) (Yash Shendge) for the responsible disclosure.

### Added

- **`CLAUDE.md`** — quick-start guide for AI coding agents and new contributors: CI3 mental model, non-negotiable security rules, key helper functions, code style, testing commands, and common pitfalls.
- Security helpers, each a single source of truth for one concern: `sanitize_for_logging()` (log-injection prevention, CWE-117), `validate_safe_filename()` / `validate_file_in_directory()` (path-traversal prevention, CWE-22), `get_safe_referer()` (open-redirect-safe referer resolution, CWE-601), `verify_csrf_token()` (timing-safe CSRF verification), and `generate_secure_token()` / `generate_password_reset_token()` (CSPRNG-based tokens).
- `validate_template_name()` — 7-layer template validation (empty check, path traversal, type, scope, static whitelist, character set, logging).
- Database-backed rate limiting for password-reset requests — per IP (`PASSWORD_RESET_IP_MAX_ATTEMPTS`, `PASSWORD_RESET_IP_WINDOW_MINUTES`) and per email (`PASSWORD_RESET_EMAIL_MAX_ATTEMPTS`, `PASSWORD_RESET_EMAIL_WINDOW_HOURS`) — so limits survive missing or rotated session cookies.
- `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` env var (default `15`) and `SEC_STRIP_EXIF_FROM_IMAGES` env var (default `false`).
- Session env vars `SESS_SAVE_PATH`, `SESS_TABLE_NAME`, `SESS_COOKIE_NAME`, and `SESS_REGENERATE_DESTROY`, all documented in `ipconfig.php.example`. `SESS_SAVE_PATH` may point outside the document root for additional security; it still defaults to PHP's system temp directory.
- Allowlist-based custom templates: the template selector is built from the built-in whitelist plus the names explicitly listed in the `CUSTOM_INVOICE_TEMPLATES_PDF`, `CUSTOM_INVOICE_TEMPLATES_PUBLIC`, `CUSTOM_QUOTE_TEMPLATES_PDF`, and `CUSTOM_QUOTE_TEMPLATES_PUBLIC` env vars. Built-in templates always appear in the selector; custom templates appear only if explicitly allowlisted. `CUSTOM_TEMPLATES_FOLDER` locates the corresponding `.php` file on disk at render time and is never scanned to populate the selector. Names containing spaces or hyphens must be quoted, e.g. `CUSTOM_INVOICE_TEMPLATES_PDF="Corporate - Modern,My Template"`.
- Settings page warning for saved custom invoice and quote template names that are not yet present in the matching `ipconfig.php` allowlist. This helps administrators copy selected legacy template names into `CUSTOM_INVOICE_TEMPLATES_PDF`, `CUSTOM_INVOICE_TEMPLATES_PUBLIC`, `CUSTOM_QUOTE_TEMPLATES_PDF`, or `CUSTOM_QUOTE_TEMPLATES_PUBLIC` after upgrading.
- `Referrer-Policy: strict-origin-when-cross-origin` header sent on every admin response.

### Changed

- Re-introduced Stripe & PayPal, adding PayPal Advanced Credit Cards and Venmo support ([#1289](https://github.com/InvoicePlane/InvoicePlane/pull/1289), [#1288](https://github.com/InvoicePlane/InvoicePlane/issues/1288)).
- The QR-code size on invoices is now configurable ([#1489](https://github.com/InvoicePlane/InvoicePlane/pull/1489), [#1376](https://github.com/InvoicePlane/InvoicePlane/issues/1376)).
- The email-template live preview now displays the raw template source as plain text instead of rendering it as HTML, eliminating a DOM-based XSS vector.
- XSS input sanitisation now covers deeply nested POST arrays, with full field-path logging.
- **Breaking:** `phpmail_send()` now returns the actual `bool` result of the send instead of always returning `true`. Any integration that treated the return value as always-truthy must handle `false` as a delivery failure.

### Fixed

- [#1426](https://github.com/InvoicePlane/InvoicePlane/pull/1426) — Remittance slip / QR-code remittance text ([#1140](https://github.com/InvoicePlane/InvoicePlane/issues/1140)).
- [#1449](https://github.com/InvoicePlane/InvoicePlane/pull/1449) — `quote_templates/public` showing the client on both header and footer.
- [#1451](https://github.com/InvoicePlane/InvoicePlane/pull/1451) — QR-code generation for batch invoice processing.
- [#1465](https://github.com/InvoicePlane/InvoicePlane/pull/1465) — Simplify redundant conditionals in email template tags.
- [#1473](https://github.com/InvoicePlane/InvoicePlane/pull/1473) — Undefined-array-key error for mPDF footer names on PHP 8.3+ ([#1180](https://github.com/InvoicePlane/InvoicePlane/issues/1180)).
- [#1478](https://github.com/InvoicePlane/InvoicePlane/pull/1478) — Add the missing `attachment()` method to the guest `Get` controller.
- [#1665](https://github.com/InvoicePlane/InvoicePlane/pull/1665) — `Upload::delete_file()` no longer reports a spurious "delete failed" error for a file that was already removed. `validate_file_in_directory()` resolves paths with `realpath()`, which returns `false` for a nonexistent path, so the already-deleted case is now handled before that check runs, and the file's metadata is still cleared correctly.
- Session config bug: `sess_table_name` and `sess_cookie_name` incorrectly read from the `SESS_DRIVER` env var instead of their own `SESS_TABLE_NAME` / `SESS_COOKIE_NAME` variables, making it impossible to rename the session cookie or table without also changing the driver name.
- `not_configured.php`: added the missing `<?php _csrf_field(); ?>` to a `<form method="post">` that violated the project's CSRF rule.
- `Cryptor::decryptString()`: use byte-safe `strlen()` / `substr()` instead of `mb_strlen()` / `mb_substr()` when splitting binary ciphertext, fixing intermittent decryption failures under multibyte internal encodings.

### Removed

- SVG logo upload support — SVGs can carry embedded JavaScript (an XSS vector). Convert existing logos to PNG, JPG, or GIF (see [SVG_FILES.md](security/SVG_FILES.md)).
- Dynamic filesystem scanning for template discovery — replaced with static allowlist constants (RCE prevention).

### Performance

- [#1483](https://github.com/InvoicePlane/InvoicePlane/pull/1483) — Batch settings save: reduce DB queries from ~70 to 3 (transaction-wrapped).
- [#1503](https://github.com/InvoicePlane/InvoicePlane/pull/1503) — Add database indexes on frequently queried columns.

### Infrastructure & CI

- [#1466](https://github.com/InvoicePlane/InvoicePlane/pull/1466) — Move the setup-php-composer composite action into `.github/actions/`.
- [#1490](https://github.com/InvoicePlane/InvoicePlane/pull/1490) — Add a Laravel Pint code-style check.
- [#1509](https://github.com/InvoicePlane/InvoicePlane/pull/1509) — Add the Docker application container (combined web server + PHP).
- [#1529](https://github.com/InvoicePlane/InvoicePlane/pull/1529) — Document the arbitrary file-deletion vulnerability for CVE allocation.
- Declared explicit `permissions: contents: read` in the `phpunit.yml`, `quickstart.yml`, and `setup.yml` workflows (CWE-272).

---

## [1.7.0] - 2024

### Added

- PHP 8.2+ compatibility (minimum PHP 8.1 required; PHP 7.x no longer supported).
- Updated all Composer and Yarn dependencies.

### Security

- **Critical — Local File Inclusion (LFI) in PDF template handling ([#1433](https://github.com/InvoicePlane/InvoicePlane/issues/1433)):** invoice and quote template parameters are now validated before use, blocking directory traversal through template selection, with security logging added for template operations.
- **Critical — Cross-Site Scripting (XSS):** quote/invoice numbers, tax-rate names, payment-method names, custom-field labels, client addresses, SUMEX observations, and quote notes are now escaped in all templates and views; email templates use proper HTML escaping throughout.
- **High — Log poisoning in the file-upload controller:** file names are sanitized before logging to prevent control-character injection.
- **High — SVG logo files blocked:** SVGs can contain embedded JavaScript; convert to PNG, JPG, or GIF instead.

### Fixed

- [#1388](https://github.com/InvoicePlane/InvoicePlane/issues/1388), [#1387](https://github.com/InvoicePlane/InvoicePlane/issues/1387) — Unsafe jQuery plugin vulnerabilities.
- [#1389](https://github.com/InvoicePlane/InvoicePlane/issues/1389) — Missing workflow permissions in GitHub Actions.
- [#1383](https://github.com/InvoicePlane/InvoicePlane/issues/1383) — File access vulnerabilities across controllers.
- [#1381](https://github.com/InvoicePlane/InvoicePlane/issues/1381) — Version checking and logging for `client_einvoicing` fields.
- [#1380](https://github.com/InvoicePlane/InvoicePlane/issues/1380) — Dependency: `qs` package bump.
- [#1377](https://github.com/InvoicePlane/InvoicePlane/issues/1377) — QR code image width reduced to 100 px.
- [#1375](https://github.com/InvoicePlane/InvoicePlane/issues/1375) — Email address verification now accepts both comma and semicolon separators.
- [#1373](https://github.com/InvoicePlane/InvoicePlane/issues/1373) — Removed deprecated library dependencies.
- [#1367](https://github.com/InvoicePlane/InvoicePlane/issues/1367), [#1368](https://github.com/InvoicePlane/InvoicePlane/issues/1368) — Various bug fixes.

### Removed

- PHP 7.x compatibility.
- Deprecated library dependencies.

---

## [1.6.4] and earlier

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please report it privately by opening a
[GitHub Security Advisory](https://github.com/InvoicePlane/InvoicePlane/security/advisories/new)
before disclosing it publicly. See [SECURITY.md](../SECURITY.md) for the full policy.
