# eInvoice Module for InvoicePlane

## Overview

The **eInvoice** module enables InvoicePlane to exchange electronic invoices with certified e-invoicing platforms (PDPs) and compatible service providers.

The module provides:

* Electronic invoice submission to a PDP.
* Invoice status tracking.
* Event synchronization.
* Incoming invoice retrieval.
* An extensible architecture allowing additional PDP providers to be integrated easily.

---

# Module Architecture

## File Structure

```text
integrations/
├── controllers/
│   ├── Integrations.php
│   ├── Settings.php
│   ├── Sync.php
│   ├── Incoming.php
│   └── Events.php
│
├── libraries/
│   ├── IntegrationClient.php
│   ├── IntegrationClientInterface.php
│   ├── IntegrationClientRegistry.php
│   └── providers/
│       ├── SuperPdpClient.php
│       └── QontoClient.php
│
├── models/
│   ├── Merchant_clients_model.php
│   └── Merchant_responses_model.php
│
├── views/
│   ├── settings.php
│   ├── history.php
│   ├── events.php
│   ├── incoming.php
│   └── provider_form.php
│
└── (migrations live in application/modules/setup/sql/, e.g. 045_1.8.0.sql)
```

---

# Features

## Invoice Submission

The module allows invoices generated in InvoicePlane to be transmitted electronically to a supported PDP.

Workflow:

```text
InvoicePlane
     │
     ▼
eInvoice Module
     │
     ▼
PDP / Service Provider
     │
     ▼
Tax Administration
```

Each submission generates:

* An external invoice identifier.
* A transmission status.
* A transmission history.
* A complete API request/response log.

---

## Status Synchronization

After submission, the module can query the provider to retrieve invoice processing statuses such as:

* Received
* Accepted
* Rejected
* Delivered
* Paid
* Any additional status exposed by the provider

Status information is stored in the unified provider response history:

```sql
ip_merchant_responses
```

---

## Event Synchronization

The controller:

```text
Sync.php
```

retrieves:

* Invoice events
* Status changes
* PDP notifications

All events are stored locally to ensure complete traceability.

---

## Incoming Invoice Retrieval

The controller:

```text
Incoming.php
```

allows the system to:

* Connect to a PDP
* Retrieve supplier invoices
* Store incoming invoice data locally

Validated incoming documents can be added to the supplier accounting register
from the Incoming Invoices page. The register keeps the supplier identity,
invoice reference, document link, and workflow status (`received`, `approved`,
`paid`, or `rejected`). It is intentionally separate from `ip_invoices`, which
contains sales invoices issued by InvoicePlane.

When a validated Factur-X/CII or UBL document is added, the supplier invoice
module also extracts the supplier identity, invoice number, issue and due
dates, currency, VAT totals, invoice totals, and structured invoice lines. The
original archived document remains linked to the imported record. Documents
whose structured content cannot be read are rejected instead of being imported
with misleading totals.

For manually entered invoices, the supplier invoice form recalculates each line
subtotal, VAT amount, and total on the server from quantity, unit price, and
VAT rate. The browser preview is only a convenience and is not trusted for
persisting monetary values.

The `Add to accounting` action on the PDP incoming-invoices page now calls the
supplier invoice module directly. The import remains idempotent through the
unique `incoming_response_id` link, so retrying the action does not create a
second supplier invoice.

The supplier invoice module is now the canonical register. The historical
`/integrations/incoming/accounting` URL remains as a compatibility redirect to
`/supplier_invoices`, and the legacy status endpoint redirects to the supplier
invoice detail page after processing existing bookmarked forms. The duplicate
integration register view is no longer part of the active navigation and can
be removed after downstream integrations have migrated.

## Archive document registry

Migration `054_archive_document_registry.sql` adds `ip_archive_documents`, a
metadata registry for documents that have been validated and stored by the
archive layer. Incoming PDP invoices are registered automatically with their
source reference, storage path, MIME type, size, profile, validation status,
receipt date, and SHA-256 digest.

The registry is idempotent for a source reference and rejects a different hash
for an existing source. It is the foundation for retention, legal holds,
append-only audit trails, and future WORM or external SAE storage. It does not
by itself make the underlying filesystem immutable.

Migration `055_archive_audit_trail.sql` adds `ip_archive_audit_events`, an
append-only audit trail for registry activity. Each event contains a
sanitized payload and a SHA-256 hash linked to the previous event for the
same document. The `Mdl_Archive_audit_events::verify_chain()` method can be
used to detect altered or removed events. The first `registered` event is
created automatically when a document enters the archive registry. Database
and storage-level WORM guarantees remain a separate concern.

The archive model supports retention dates and legal holds. Retention dates
are normalized and can be cleared or replaced; due documents are returned
only when they are sealed and not under legal hold. A legal hold requires a
reason to place or release it, and both operations are recorded in the
append-only audit chain. The model does not delete expired documents:
expiration is an explicit review signal for a future retention worker or
external SAE policy.

Migration `056_archive_integrity_status.sql` adds verification state to the
registry. `verify_integrity()` resolves the archived path within the configured
archive directory, recalculates its SHA-256 digest, and records the result.
`seal()` performs that verification before setting `sealed_at`; a document
with a missing or altered file cannot be sealed. Sealing remains an
application-level control until a WORM or external SAE storage backend is
connected.

`export_compliant()` produces a versioned ZIP package containing the sealed
document, `manifest.json`, and `audit-events.json`. The export is refused for
unsealed or unverifiable documents, is written below the archive directory,
and never overwrites an existing package. The package is an interoperable
evidence export; legal qualification and long-term preservation still depend
on the configured SAE/WORM infrastructure and applicable policy.

Migration `057_encrypted_archive_storage.sql` adds encryption metadata for
the `EncryptedArchiveStorageAdapter`. The adapter uses AES-256-GCM with a
versioned envelope and InvoicePlane's `ENCRYPTION_KEY`; it keeps separate
plaintext and ciphertext SHA-256 digests. Encrypted files are authenticated
before decryption, and archive integrity verification validates the plaintext
digest as well as the encrypted storage digest. The encryption key must be
kept outside the repository and backed up separately.

Archive key rotation is configuration-driven. Set
`ARCHIVE_ENCRYPTION_KEYS` to a JSON object containing the retained key
versions and set `ARCHIVE_ENCRYPTION_ACTIVE_KEY_VERSION` to the new version.
Existing documents continue to decrypt with their recorded version. Calling
`rotate_encryption_key()` verifies the existing content, writes a new
authenticated ciphertext under the active key, updates the registry, and
records the old and new versions in the audit chain. The previous ciphertext
is retained for recovery; old keys must remain available until every document
using them has been rotated and verified.

The batch command `php index.php integrations/cli/rotate_archive_keys [limit]`
processes documents that do not use the configured active key version. It is
safe to run repeatedly; failures are reported by document ID for a later
retry, while each successful rotation remains auditable.

Migration `058_external_sae_storage.sql` records the remote SAE object key,
version ID, provider, and retention date. `S3ObjectLockArchiveConnector`
requires an S3 bucket with versioning and Object Lock already enabled. It
uses per-object `COMPLIANCE` retention, optionally enables an S3 legal hold,
and sends a SHA-256 checksum with the package. `store_on_s3_worm()` refuses
documents without a retention date and keeps the remote version ID in the
local registry. The bucket must be configured and credentials supplied
outside the repository; Object Lock cannot be retrofitted safely to an
ordinary bucket after documents have been stored.

Manual supplier invoice creation also checks the supplier/number combination.
The same rule is enforced by the `uq_supplier_invoice_supplier_number`
database constraint, while allowing identical invoice numbers for different
suppliers.

Supplier invoices are archived reversibly rather than physically deleted. The
archive keeps the original document, attachments, payments, and status history;
archived records are hidden from the active list and can be restored through
the supplier invoice detail page.

The supplier invoice module is restricted to administrator accounts (`user_type
1`). This applies to reading invoices and documents, editing data, recording
payments, uploading attachments, and archiving/restoring records. All state-
changing actions require POST and a valid CSRF token.

---

# Provider Management

## Provider Architecture

Every provider must implement:

```php
IntegrationClientInterface
```

The:

```php
IntegrationClientRegistry
```

automatically discovers and registers available providers.

Provider discovery is performed dynamically using:

```php
glob('*Client.php')
```

This makes the module easily extensible.

---

# Currently Supported Providers

## SuperPDP

### Identification

```text
Code : superpdp
Name : SuperPDP
```

### Authentication

OAuth2 Client Credentials

Configuration parameters:

| Parameter     | Description         |
| ------------- | ------------------- |
| client_id     | OAuth Client ID     |
| client_secret | OAuth Client Secret |
| token_url     | Token endpoint      |
| api_base_url  | Base API URL        |

### Default Endpoints

```text
POST /v1.beta/invoices
GET  /v1.beta/invoices/{id}
GET  /v1.beta/invoices
GET  /v1.beta/invoice_events
```

### Options

```json
{
  "disable_pre_check": false
}
```

Allows provider-side pre-validation checks to be disabled.

---

## Qonto

### Identification

```text
Code : qonto
Name : Qonto PA
```

### Authentication

Bearer Token

Configuration parameters:

| Parameter    | Description      |
| ------------ | ---------------- |
| access_token | API Access Token |
| api_base_url | Base API URL     |

### Default Endpoints

```text
POST /v2/client_invoices/bulk
POST /v2/client_invoices/{id}/send_by_einvoice
GET  /v2/client_invoices/{id}
GET  /v2/client_invoices
GET  /v2/supplier_invoices
```

### Specific Workflow

For invoices already issued by InvoicePlane, Qonto requires a two-step process:

1. Import the original Factur-X PDF without regenerating it.
2. Submit the returned client-invoice ID through the e-invoicing workflow.

The module automatically executes these steps, treats the `204` submission
response as asynchronous, and reads French lifecycle events from the client
invoice resource.

---

## LetsPeppol

LetsPeppol uses OAuth2 client credentials and accepts the Peppol BIS Billing
and XRechnung UBL profiles enabled in the profile registry. Invoice, status,
incoming-invoice, event, participant, credit-note, document, and transmission
endpoints are configurable because their paths depend on the provider contract.

---

# Configuration

## Access

Navigation:

```text
Settings
→ eInvoice
```

---

## Enabling a Provider

1. Open eInvoice settings.
2. Select a provider.
3. Enter authentication credentials.
4. Enable the provider.
5. Save the configuration.

Only one provider can be active at a time.

When a provider is enabled:

```php
enabled = 1
```

all other providers are automatically disabled.

---

# Manual Synchronization

## Full Synchronization

Endpoint:

```text
integrations/sync/run/{merchant_client_id}
```

Actions performed:

* Retrieve incoming invoices.
* Retrieve events.
* Update status history.

---

## Incoming Invoice Synchronization

Endpoint:

```text
integrations/incoming/sync/{merchant_client_id}
```

Actions performed:

* Query the provider.
* Download incoming invoices.
* Store data locally.

---

# Database Structure

## Table: ip_merchant_clients

Stores PDP configurations.

### Fields

| Field         | Description         |
| ------------- | ------------------- |
| id            | Internal identifier |
| merchant_type | Provider type       |
| label         | Display name        |
| enabled       | Active status       |
| auth_type     | Authentication type |
| settings_json | Provider settings   |
| created_at    | Creation date       |
| updated_at    | Last update         |

---

## Table: ip_einvoice_responses

Stores invoice exchange history.

### Fields

| Field              | Description          |
| ------------------ | -------------------- |
| id                 | Internal identifier  |
| merchant_client_id | Provider reference   |
| direction          | out / in             |
| record_type        | Record type          |
| invoice_id         | Local invoice ID     |
| external_id        | Provider invoice ID  |
| status             | Business status      |
| message            | Provider message     |
| http_code          | HTTP response code   |
| request_json       | API request payload  |
| response_json      | API response payload |
| created_at         | Creation date        |
| updated_at         | Last update          |

---

# Adding a New Provider

Create a file whose name ends with `Client.php`:

```text
libraries/providers/MyClient.php
```

The class implements its API logic and declares the fields rendered by the
generic settings form. No controller or view changes are required.

```php
class MyClient implements IntegrationClientInterface
{
    public static function clientCode(): string
    {
        return 'myprovider';
    }

    public static function clientName(): string
    {
        return 'My Provider';
    }

    public static function authType(): string
    {
        return 'bearer';
    }

    public static function defaultSettings(): array
    {
        return [
            'api_token'    => '',
            'api_base_url' => 'https://api.example.com',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'api_token' => [
                'type'      => 'password',
                'label'     => 'API Token',
                'required'  => true,
                'sensitive' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        return true;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        return [];
    }

    public function getInvoiceStatus(string $externalId): array
    {
        return [];
    }

    public function receiveInvoices(array $filters = []): array
    {
        return [];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        return [];
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    public function fetchToken(array $settings): string
    {
        return $settings['api_token'] ?? '';
    }
}
```

Supported field types are `text`, `password`, `url`, `path`, `checkbox`, and
`select`. The registry discovers the file, creates its database entry, and the
form is generated from `settingsSchema()`. Sensitive values are never rendered,
and submitting an empty sensitive field preserves the stored secret.

---

# Security

The module currently supports:

* OAuth2 Client Credentials
* Bearer Token authentication
* API Key authentication (architecture-ready)

Sensitive credentials are stored in:

```text
settings_json
```

within the:

```text
ip_merchant_clients
```

table.

---

# Logging and Audit Trail

Every API exchange stores:

* Full request payload
* Full response payload
* HTTP status code
* Business status
* External provider identifier

This provides:

* Full auditability
* Easier troubleshooting
* Improved support capabilities
* Regulatory traceability

---

# Use Cases

## Outgoing Invoice

```text
Invoice PDF
      │
      ▼
eInvoice Module
      │
      ▼
SuperPDP / Qonto
      │
      ▼
Status Returned
      │
      ▼
Invoice History
```

---

## Incoming Supplier Invoice

```text
PDP
 │
 ▼
Incoming Sync
 │
 ▼
Local Database
 │
 ▼
User Consultation
```

---

# Current Compatibility Matrix

| Provider | Send Invoices | Status Tracking | Incoming Invoices | Events |
| -------- | ------------- | --------------- | ----------------- | ------ |
| SuperPDP | Yes           | Yes             | Yes               | Yes    |
| Qonto    | Yes           | Yes             | Yes               | Yes    |
| LetsPeppol | Yes         | Yes             | Yes               | Yes    |

---

# Conclusion

The **eInvoice** module provides a generic integration layer between InvoicePlane and electronic invoicing providers. Its provider-based architecture allows new PDPs and service providers to be added with minimal development effort while maintaining a unified user experience within InvoicePlane.
