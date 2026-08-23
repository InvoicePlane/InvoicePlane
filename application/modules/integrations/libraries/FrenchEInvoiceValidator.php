<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Applies deterministic French startup-scope checks from DGFiP specification 3.2.
 *
 * Directory existence and routing remain the responsibility of the accredited
 * platform because they require access to the national directory.
 */
final class FrenchEInvoiceValidator
{
    /**
     * @return string[]
     */
    public function validate(DOMDocument $document, EInvoiceProfile $profile): array
    {
        if ($profile->countryCode() !== 'FR' || $profile->format() !== 'factur-x') {
            return [];
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('ram', 'urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100');
        $xpath->registerNamespace('rsm', 'urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100');

        $errors = [];
        $this->validateSiren(
            $xpath,
            '//ram:SellerTradeParty/ram:SpecifiedLegalOrganization/ram:ID',
            'BT-30',
            'seller',
            $errors
        );
        $this->validateSiren(
            $xpath,
            '//ram:BuyerTradeParty/ram:SpecifiedLegalOrganization/ram:ID',
            'BT-47',
            'buyer',
            $errors
        );
        $this->validateElectronicAddress(
            $xpath,
            '//ram:SellerTradeParty/ram:URIUniversalCommunication/ram:URIID',
            'BT-34',
            'seller electronic address',
            $errors
        );
        $this->validateElectronicAddress(
            $xpath,
            '//ram:BuyerTradeParty/ram:URIUniversalCommunication/ram:URIID',
            'BT-49',
            'buyer electronic address',
            $errors
        );

        $invoiceNumber = $this->firstValue($xpath, '/rsm:CrossIndustryInvoice/rsm:ExchangedDocument/ram:ID');
        if ($invoiceNumber === null || preg_match('/^[A-Za-z0-9+_\/-]+$/', $invoiceNumber) !== 1) {
            $errors[] = 'France BR-FR-02: invoice number must use only letters, digits, +, -, _, or /.';
        }

        foreach (['PMT', 'PMD', 'AAB'] as $subjectCode) {
            $query = '/rsm:CrossIndustryInvoice/rsm:ExchangedDocument/ram:IncludedNote[ram:SubjectCode="'
                . $subjectCode . '"]/ram:Content[normalize-space()]';
            if ($this->firstValue($xpath, $query) === null) {
                $errors[] = 'France: mandatory legal note ' . $subjectCode . ' is missing.';
            }
        }

        return $errors;
    }

    /**
     * @param string[] $errors
     */
    private function validateSiren(
        DOMXPath $xpath,
        string $query,
        string $businessTerm,
        string $label,
        array &$errors
    ): void {
        $nodes = $xpath->query($query);
        $node  = $nodes === false ? null : $nodes->item(0);

        if ( ! $node instanceof DOMElement) {
            $errors[] = 'France G1.63: ' . $label . ' SIREN (' . $businessTerm . ') is required.';

            return;
        }

        if (preg_match('/^[0-9]{9}$/', trim($node->textContent)) !== 1) {
            $errors[] = 'France G1.89: ' . $label . ' SIREN (' . $businessTerm . ') must contain exactly 9 digits.';
        }

        if ($node->getAttribute('schemeID') !== '0002') {
            $errors[] = 'France G1.63: ' . $businessTerm . ' must use ISO 6523 scheme 0002.';
        }
    }

    /**
     * @param string[] $errors
     */
    private function validateElectronicAddress(
        DOMXPath $xpath,
        string $query,
        string $businessTerm,
        string $label,
        array &$errors
    ): void {
        $nodes = $xpath->query($query);
        $node  = $nodes === false ? null : $nodes->item(0);

        if ( ! $node instanceof DOMElement || trim($node->textContent) === '') {
            $errors[] = 'France G1.63: ' . $label . ' (' . $businessTerm . ') is required.';

            return;
        }

        if ($node->getAttribute('schemeID') === '') {
            // Electronic addresses (Peppol EAS) are not SIREN-only — SIRET (0009) and other
            // ISO 6523 schemes are valid here, unlike the legal-registration SIREN checks above.
            $errors[] = 'France G1.63: ' . $label . ' (' . $businessTerm . ') must declare its ISO 6523 EAS scheme.';
        }
    }

    private function firstValue(DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        $value = trim($nodes->item(0)?->textContent ?? '');

        return $value === '' ? null : $value;
    }
}
