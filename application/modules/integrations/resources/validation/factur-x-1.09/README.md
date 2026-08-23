# Factur-X 1.09 EN 16931 validation

This directory contains the Factur-X 1.09 EN 16931 XSD files used to validate
the CII payload before it is embedded in a PDF/A-3 invoice.

Factur-X 1.09 / ZUGFeRD 2.5 was published by FNFE-MPE and FeRD on 10 June
2026 and is the applicable release from 1 July 2026:

- <https://fnfe-mpe.org/factur-x/>;
- <https://www.ferd-net.de/publikationen-produkte/publikationen/detailseite/zugferd-25-english>.

The artifacts were obtained from the BSD-licensed `akretion/factur-x`
distribution, which republishes the FNFE-MPE/FeRD validation files:

- source: <https://github.com/akretion/factur-x>;
- source commit: `5edb9af9186e5f06f446372688e6fdbbd0b790d3`;
- source archive SHA-256:
  `04eac61640e541066e252197b28a4dc1bde7e4691372bbc9dc0771f4d6385ba9`;
- distribution license: BSD 3-Clause (included as
  `LICENSE-AKRETION-BSD.txt`);
- FNFE-MPE/FeRD validation-artifact license: Apache 2.0 (included as
  `LICENSE-FACTURX-APACHE-2.0.txt`).

The matching precompiled Schematron stylesheet and code database are stored at
`../schematron/rules/Factur-X_1.09_EN16931.xsl` and
`../schematron/rules/FACTUR-X_EN16931_codedb.xml`. They are executed with the
bundled Saxon-HE runtime.
