/**
 * Browser coverage for the Qonto e-invoice provider flow.
 * Mirrors tests/Feature/Core/QontoFlowTest.php via the shared einvoiceFlowSuite.
 */

import { einvoiceFlowSuite } from '../support/einvoice-flow.js';

einvoiceFlowSuite({
  provider: 'qonto',
  driver: 'qonto',
  nonHttpsField: 'api_base_url',
  endpointField: 'client_invoices_endpoint',
  editFormFields: ['access_token', 'api_base_url'],
  credMarker: null,
  payload: {
    label: 'Qonto',
    enabled: '0',
    auth_type: 'bearer',
    access_token: 'prod-access-token',
    api_base_url: 'https://thirdparty.qonto.com',
    import_endpoint: '/v2/client_invoices/bulk',
    client_invoices_endpoint: '/v2/client_invoices',
    send_invoice_endpoint: '/v2/client_invoices/{id}/send_by_einvoice',
    invoice_status_endpoint: '/v2/client_invoices/{id}',
    incoming_invoices_endpoint: '/v2/supplier_invoices',
    attachment_endpoint: '/v2/attachments/{id}',
  },
});
