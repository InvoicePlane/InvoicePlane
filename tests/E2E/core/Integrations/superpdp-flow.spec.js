/**
 * Browser coverage for the SuperPDP e-invoice provider flow.
 * Mirrors tests/Feature/Core/SuperPdpFlowTest.php via the shared einvoiceFlowSuite.
 */

import { einvoiceFlowSuite } from '../support/einvoice-flow.js';

einvoiceFlowSuite({
  provider: 'superpdp',
  driver: 'superpdp',
  nonHttpsField: 'token_url',
  endpointField: 'invoice_endpoint',
  editFormFields: ['client_id', 'client_secret', 'token_url'],
  credMarker: 'prod-client-id',
  payload: {
    label: 'SuperPDP',
    enabled: '0',
    auth_type: 'oauth2',
    client_id: 'prod-client-id',
    client_secret: 'prod-secret',
    token_url: 'https://api.superpdp.tech/oauth2/token',
    api_base_url: 'https://api.superpdp.tech',
    invoice_endpoint: '/v1.beta/invoices',
    invoice_status_endpoint: '/v1.beta/invoices/{id}',
    incoming_invoices_endpoint: '/v1.beta/invoices',
    incoming_document_endpoint: '/v1.beta/invoices/{id}/document',
    invoice_events_endpoint: '/v1.beta/invoice_events',
  },
});
