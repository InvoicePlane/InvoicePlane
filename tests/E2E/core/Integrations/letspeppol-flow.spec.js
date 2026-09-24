/**
 * Browser coverage for the LetsPeppol e-invoice provider flow.
 * Mirrors tests/Feature/Core/LetsPeppolFlowTest.php via the shared einvoiceFlowSuite.
 */

import { einvoiceFlowSuite } from '../support/einvoice-flow.js';

einvoiceFlowSuite({
  provider: 'letspeppol',
  driver: 'letspeppol',
  nonHttpsField: 'token_url',
  endpointField: 'invoice_endpoint',
  editFormFields: ['client_id', 'token_url', 'api_base_url'],
  credMarker: 'prod-client-id',
  payload: {
    label: 'LetsPeppol',
    enabled: '0',
    auth_type: 'oauth2',
    client_id: 'prod-client-id',
    client_secret: 'prod-secret',
    token_url: 'https://api.letspeppol.eu/oauth2/token',
    api_base_url: 'https://api.letspeppol.eu',
    invoice_endpoint: '/v1/invoices',
    invoice_status_endpoint: '/v1/invoices/{id}',
    incoming_invoices_endpoint: '/v1/incoming-invoices',
    invoice_events_endpoint: '/v1/invoice-events',
    credit_note_endpoint: '/v1/credit-notes',
    credit_note_status_endpoint: '/v1/credit-notes/{id}',
    participants_endpoint: '/v1/participants',
    participant_lookup_endpoint: '/v1/participants/{id}',
    transmissions_endpoint: '/v1/transmissions',
    transmission_status_endpoint: '/v1/transmissions/{id}',
    documents_endpoint: '/v1/documents',
    document_endpoint: '/v1/documents/{id}',
  },
});
