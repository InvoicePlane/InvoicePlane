# OASIS UBL 2.1 validation schemas

This directory contains the unmodified normative XML schemas required to
validate UBL 2.1 Invoice and CreditNote documents locally.

Source:

- OASIS UBL 2.1 Standard: <https://docs.oasis-open.org/ubl/os-UBL-2.1/>
- Distribution archive: <https://docs.oasis-open.org/ubl/os-UBL-2.1/UBL-2.1.zip>
- Archive SHA-256:
  `60b80d76394a8a2add90723ecb8e0e2e9d826775de9749df37a72d60703f86ed`

Included from the distribution:

- `xsd/maindoc/UBL-Invoice-2.1.xsd`;
- `xsd/maindoc/UBL-CreditNote-2.1.xsd`;
- all schema dependencies under `xsd/common/`.

The files retain their original OASIS copyright headers and have not been
modified. OASIS permits copies to be furnished provided its copyright notice
and notices are retained. See the notices in the UBL 2.1 specification and the
OASIS Intellectual Property Rights Policy:
<https://www.oasis-open.org/policies-guidelines/ipr/>.

UBL XSD validation verifies the document syntax. EN 16931, Peppol, and national
business rules expressed as Schematron are a separate validation layer and are
not covered by these schemas.
