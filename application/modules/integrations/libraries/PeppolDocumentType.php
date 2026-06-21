<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Peppol BIS document type identifiers.
 *
 * Each case value is the full document type ID as registered in the
 * Peppol Service Metadata Publisher (SMP). These identifiers are exchanged
 * during access-point capability lookup and appear in AS4 message headers.
 *
 * @see https://docs.peppol.eu/poacc/billing/3.0/
 */
enum PeppolDocumentType: string
{
    /* BIS Billing 3.0 — the standard for outbound e-invoicing in the EU */
    case BillingInvoice    = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1';
    case BillingCreditNote = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2::CreditNote##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1';

    /* BIS Order Only 3.0 */
    case Order         = 'urn:oasis:names:specification:ubl:schema:xsd:Order-2::Order##urn:fdc:peppol.eu:2017:poacc:ordering:01:1.0::2.1';
    case OrderResponse = 'urn:oasis:names:specification:ubl:schema:xsd:OrderResponse-2::OrderResponse##urn:fdc:peppol.eu:2017:poacc:ordering:01:1.0::2.1';

    /* BIS Catalogue 3.0 */
    case Catalogue         = 'urn:oasis:names:specification:ubl:schema:xsd:Catalogue-2::Catalogue##urn:fdc:peppol.eu:2017:poacc:catalogue:01:1.0::2.1';
    case CatalogueResponse = 'urn:oasis:names:specification:ubl:schema:xsd:ApplicationResponse-2::ApplicationResponse##urn:fdc:peppol.eu:2017:poacc:catalogue:01:1.0::2.1';

    /* Message Level Response (MLR) — transport acknowledgement */
    case MessageLevelResponse = 'urn:oasis:names:specification:ubl:schema:xsd:ApplicationResponse-2::ApplicationResponse##urn:fdc:peppol.eu:2017:poacc:mlr:01:1.0::2.1';
}
