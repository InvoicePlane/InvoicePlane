<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Explicit allow-list of e-invoice profiles supported by provider integrations.
 *
 * Profiles are intentionally registered in code. Merely placing a PHP generator
 * on disk must never make it eligible for automatic provider transmission.
 */
final class EInvoiceProfileRegistry
{
    /**
     * @var array<string, EInvoiceProfile>
     */
    private array $profiles = [];

    /**
     * @param EInvoiceProfile[] $profiles
     */
    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $profile) {
            $this->register($profile);
        }
    }

    public static function builtIn(): self
    {
        return new self([
            new EInvoiceProfile(
                'UblPeppolV21',
                'Peppol BIS Billing 3.0 (UBL 2.1)',
                'BE',
                'Ublv24',
                'peppol-bis-billing-3',
                'ubl',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'CustomizationID' => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
                    'ProfileID'       => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                    'EndpointID'      => [
                        'user'   => 'einvoice_identifier',
                        'client' => 'peppol_id',
                    ],
                    'user_eas_code'       => '0088',
                    'client_eas_code'     => '0088',
                    'PartyIdentification' => true,
                    'PartyLegalEntity'    => [
                        'CompanyID' => 'vat_id',
                        'SchemeID'  => false,
                    ],
                ],
                'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
                'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                PeppolDocumentType::BillingInvoice->value,
                ['letspeppol', 'acube', 'dokapi', 'arratech', 'storecove']
            ),
            new EInvoiceProfile(
                'Facturxv10',
                'Factur-X v1.09 - EN 16931',
                'FR',
                'Facturxv10',
                'factur-x',
                'cii',
                'application/pdf',
                'pdf',
                true,
                'factur-x.xml',
                [
                    'GuidelineSpecifiedDocumentContextParameterID' => 'urn:cen.eu:en16931:2017',
                    'FrenchSiren'                                  => true,
                ],
                'urn:cen.eu:en16931:2017',
                null,
                null,
                ['superpdp', 'qonto']
            ),
            new EInvoiceProfile(
                'Ublxrechnungv30de',
                'XRechnung UBL 3.0',
                'DE',
                'Ublv24',
                'xrechnung-3',
                'ubl',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
                    'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                    'BuyerReference'      => true,
                    'client_eas_code'     => '0204',
                    'user_eas_code'       => '0204',
                    'EndpointID'          => 'tax_code',
                    'PartyIdentification' => false,
                    'PartyLegalEntity'    => [
                        'CompanyID' => 'tax_code',
                        'SchemeID'  => false,
                    ],
                ],
                'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
                'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                PeppolDocumentType::BillingInvoice->value,
                ['letspeppol']
            ),
            new EInvoiceProfile(
                'Xrechnungciiv30de',
                'XRechnung CII 3.0',
                'DE',
                'Facturxv10',
                'xrechnung-3',
                'cii',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'BusinessProcessSpecifiedDocumentContextParameterID' => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                    'GuidelineSpecifiedDocumentContextParameterID'       => 'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
                    'CII'                                                => true,
                    'client_eas_code'                                    => '0204',
                    'user_eas_code'                                      => '0204',
                    'URIUniversalCommunication'                          => [
                        'client' => [
                            'URIID'    => 'client_vat_id',
                            'schemeID' => '9930',
                        ],
                        'user' => [
                            'URIID'    => 'user_vat_id',
                            'schemeID' => '9930',
                        ],
                    ],
                ],
                'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
                'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0'
            ),
            new EInvoiceProfile(
                'Zugferdv23',
                'ZUGFeRD v2.3 - EN 16931',
                'DE',
                'Facturxv10',
                'zugferd-2.3',
                'cii',
                'application/pdf',
                'pdf',
                true,
                'factur-x.xml',
                [
                    'GuidelineSpecifiedDocumentContextParameterID' => 'urn:cen.eu:en16931:2017',
                ],
                'urn:cen.eu:en16931:2017'
            ),
            new EInvoiceProfile(
                'Ublciusv10ro',
                'RO_CIUS UBL Invoice 1.0',
                'RO',
                'Ublv24',
                'ro-cius',
                'ubl',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1',
                    'BuyerReference'      => true,
                    'client_eas_code'     => '9947',
                    'user_eas_code'       => '9947',
                    'EndpointID'          => 'vat_id',
                    'PartyIdentification' => false,
                    'PartyLegalEntity'    => [
                        'CompanyID' => 'tax_code',
                        'SchemeID'  => false,
                    ],
                ],
                'urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1'
            ),
            new EInvoiceProfile(
                'Ksefv20',
                'KSeF 2.0 - FA(3)',
                'PL',
                'Ksefv20',
                'ksef-fa3',
                'ksef',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'custom_fields' => [],
                ]
            ),
            new EInvoiceProfile(
                'Facturaev32',
                'Facturae 3.2.1',
                'ES',
                'Facturaev32',
                'facturae-3.2.1',
                'facturae',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'series' => 'IP',
                ],
                null,
                null,
                null,
                ['b2brouter']
            ),
            new EInvoiceProfile(
                'Fatturapav12',
                'FatturaPA v1.2.2',
                'IT',
                'Fatturapav12',
                'fatturapa-1.2.2',
                'fatturapa',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'regimefisc' => 'RF01',
                ],
                null,
                null,
                null,
                ['acube']
            ),
        ]);
    }

    public function register(EInvoiceProfile $profile): void
    {
        if (isset($this->profiles[$profile->code()])) {
            throw new LogicException('Duplicate e-invoice profile code.');
        }

        $this->profiles[$profile->code()] = $profile;
    }

    public function get(string $code): EInvoiceProfile
    {
        if ( ! isset($this->profiles[$code])) {
            throw new OutOfBoundsException('Unsupported e-invoice profile.');
        }

        return $this->profiles[$code];
    }

    public function has(string $code): bool
    {
        return isset($this->profiles[$code]);
    }

    /**
     * @return array<string, EInvoiceProfile>
     */
    public function all(): array
    {
        return $this->profiles;
    }

    public static function builtIn(): self
    {
        return new self([
            new EInvoiceProfile(
                'UblPeppolV21',
                'Peppol BIS Billing 3.0 (UBL 2.1)',
                'BE',
                'Ublv24',
                'peppol-bis-billing-3',
                'ubl',
                'application/xml',
                'xml',
                false,
                '',
                [
                    'CustomizationID' => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
                    'ProfileID'       => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                    'EndpointID'      => [
                        'user'   => 'einvoice_identifier',
                        'client' => 'peppol_id',
                    ],
                    'user_eas_code'       => '0088',
                    'client_eas_code'     => '0088',
                    'PartyIdentification' => true,
                    'PartyLegalEntity'    => [
                        'CompanyID' => 'vat_id',
                        'SchemeID'  => false,
                    ],
                ],
                'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
                'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                PeppolDocumentType::BillingInvoice->value,
                ['letspeppol']
            ),
        ]);
    }
}
