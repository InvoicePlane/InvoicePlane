<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Performs local structural checks before a document reaches a provider.
 *
 * National XSD and Schematron validation can be added behind this class. These
 * checks deliberately fail closed for malformed files and profile mismatches.
 */
final class EInvoiceDocumentValidator
{
    public function validate(string $path, EInvoiceProfile $profile): EInvoiceArtifact
    {
        $errors = [];

        if ( ! is_file($path) || filesize($path) === 0) {
            return new EInvoiceArtifact($path, $profile, ['Document is missing or empty.']);
        }

        if ($profile->extension() === 'pdf') {
            $errors = $this->validatePdf($path, $profile);
        } else {
            $errors = $this->validateXml($path, $profile);
        }

        return new EInvoiceArtifact($path, $profile, $errors);
    }

    /**
     * @return string[]
     */
    private function validatePdf(string $path, EInvoiceProfile $profile): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ['Document cannot be read.'];
        }

        $signature = fread($handle, 5);
        fclose($handle);

        if ($signature !== '%PDF-') {
            return ['Document is not a PDF file.'];
        }

        if ( ! $profile->embedded()) {
            return [];
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return ['Document cannot be read.'];
        }

        if ( ! str_contains($contents, '/EmbeddedFiles') && ! str_contains($contents, '/AF')) {
            return ['Hybrid PDF does not declare an embedded file.'];
        }

        if ($profile->xmlName() !== '' && ! str_contains($contents, $profile->xmlName())) {
            return ['Hybrid PDF does not contain the expected XML attachment name.'];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function validateXml(string $path, EInvoiceProfile $profile): array
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $document                     = new DOMDocument();
        $document->resolveExternals   = false;
        $document->substituteEntities = false;
        $loaded                       = $document->load($path, LIBXML_NONET);
        $errors                       = [];

        if ( ! $loaded || $document->documentElement === null) {
            $errors[] = 'Document is not well-formed XML.';
        } else {
            $errors = array_merge($errors, $this->validateRoot($document, $profile));
            $errors = array_merge($errors, $this->validateIdentifiers($document, $profile));
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return array_values(array_unique($errors));
    }

    /**
     * @return string[]
     */
    private function validateRoot(DOMDocument $document, EInvoiceProfile $profile): array
    {
        $expectedRoots = [
            'ubl'       => ['Invoice', 'CreditNote'],
            'cii'       => ['CrossIndustryInvoice'],
            'fatturapa' => ['FatturaElettronica'],
            'facturae'  => ['Facturae'],
            'ksef'      => ['Faktura'],
        ];
        $expected = $expectedRoots[$profile->syntax()] ?? [];

        if ($expected !== [] && ! in_array($document->documentElement->localName, $expected, true)) {
            return ['XML root element does not match the selected profile.'];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function validateIdentifiers(DOMDocument $document, EInvoiceProfile $profile): array
    {
        $errors = [];
        $xpath  = new DOMXPath($document);

        if ($profile->customizationId() !== null) {
            $query = $profile->syntax() === 'cii'
                ? '//*[local-name()="GuidelineSpecifiedDocumentContextParameter"]/*[local-name()="ID"]'
                : '//*[local-name()="CustomizationID"]';
            $nodes = $xpath->query($query);
            if ($nodes === false || $nodes->length === 0 || trim($nodes->item(0)->textContent) !== $profile->customizationId()) {
                $errors[] = 'CustomizationID does not match the selected profile.';
            }
        }

        if ($profile->profileId() !== null) {
            $query = $profile->syntax() === 'cii'
                ? '//*[local-name()="BusinessProcessSpecifiedDocumentContextParameter"]/*[local-name()="ID"]'
                : '//*[local-name()="ProfileID"]';
            $nodes = $xpath->query($query);
            if ($nodes === false || $nodes->length === 0 || trim($nodes->item(0)->textContent) !== $profile->profileId()) {
                $errors[] = 'ProfileID does not match the selected profile.';
            }
        }

        return $errors;
    }
}
