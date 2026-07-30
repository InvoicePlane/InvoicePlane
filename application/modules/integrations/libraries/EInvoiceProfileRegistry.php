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
