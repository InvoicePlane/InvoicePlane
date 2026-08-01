# EN 16931 and Peppol Schematron validation

This directory contains the runtime used to execute the official UBL business
rules before an e-invoice is sent.

## Rules

`rules/EN16931-UBL-validation-1.3.16.xslt` is the unmodified precompiled XSLT
from the official EN 16931 validation release:

- release: `validation-1.3.16` (10 April 2026);
- source: <https://github.com/ConnectingEurope/eInvoicing-EN16931/releases/tag/validation-1.3.16>;
- archive: `en16931-ubl-1.3.16.zip`;
- archive SHA-256:
  `bafada015efbc5248bf5e05ad2191e1d9833ef96e9dd5f4bce420a747342da85`;
- license: European Union Public Licence 1.2.

`rules/PEPPOL-EN16931-UBL-3.0.21.xslt` was compiled without modification of
the rules from OpenPeppol's `PEPPOL-EN16931-UBL.sch` using SchXslt 1.11.1:

- rules: Peppol BIS Billing May 2026 release 3.0.21;
- source branch: `2026-Q2-DEV-v3.0.21`;
- source: <https://github.com/OpenPEPPOL/peppol-bis-invoice-3>;
- source archive SHA-256:
  `26f2568d392005cf2a01f89c33f15d407f01b1eb707c36c3e7445a6df9369964`;
- compiler artifact SHA-256:
  `11a4b097660387d4ba48960287bf4f57de81540a2a251b35e7c5c466cf9d69d9`.

## Engine

The XSLT 2.0/3.0 stylesheets are executed by Saxon-HE 12.10. The runtime
contains:

- `saxon-he-12.10.jar`, from the official `SaxonHE12-10J.zip` distribution;
- `xmlresolver-5.3.3.jar`, shipped as its required resolver dependency;
- the applicable Mozilla Public License 2.0 and Apache License 2.0 notices.

The Saxon archive SHA-256 is:
`1c7db9f726df835349c64edd631de0310eca31291100230064eba153f607b0be`.

Java 8 or newer must be available as `java`. Validation is fail-closed when
Java, an engine file, a ruleset, or a valid SVRL result is unavailable.

Only `fatal`/error assertions block transmission. Schematron assertions marked
`warning` or `info` are non-blocking.
