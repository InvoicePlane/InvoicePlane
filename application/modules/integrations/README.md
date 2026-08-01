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
einvoice/
├── controllers/
│   ├── Einvoice.php
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
└── migrations/
    └── einvoice.sql
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

Status information is stored in:

```sql
ip_einvoice_responses
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
einvoice/sync/run/{merchant_client_id}
```

Actions performed:

* Retrieve incoming invoices.
* Retrieve events.
* Update status history.

---

## Incoming Invoice Synchronization

Endpoint:

```text
einvoice/incoming/sync/{merchant_client_id}
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
