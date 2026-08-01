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
    private const UBL_21_SCHEMA_DIRECTORY = __DIR__ . '/../resources/validation/ubl-2.1/xsd/maindoc/';

    private const FACTUR_X_109_SCHEMA = __DIR__ . '/../resources/validation/factur-x-1.09/en16931/Factur-X_EN16931.xsd';

    public function __construct(
        private ?EInvoiceSchematronValidator $schematronValidator = null,
        private ?FrenchEInvoiceValidator $frenchValidator = null
    ) {
        $this->schematronValidator ??= new EInvoiceSchematronValidator();
        $this->frenchValidator ??= new FrenchEInvoiceValidator();
    }

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
     * Validate the structured payload before it is embedded in a hybrid PDF.
     *
     * @return string[]
     */
    public function validateStructuredData(string $path, EInvoiceProfile $profile): array
    {
        return $this->validateXml($path, $profile);
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
            $rootErrors = $this->validateRoot($document, $profile);
            $errors     = array_merge($errors, $rootErrors);

            if ($rootErrors === [] && in_array($profile->syntax(), ['ubl', 'cii'], true)) {
                $schemaErrors = match ($profile->syntax()) {
                    'ubl'   => $this->validateUblSchema($document),
                    'cii'   => $this->validateCiiSchema($document, $profile),
                    default => [],
                };
                $errors = array_merge($errors, $schemaErrors);

                if ($schemaErrors === []) {
                    $errors = array_merge($errors, $this->schematronValidator->validate($path, $profile));
                }
            }

            $errors = array_merge($errors, $this->validateIdentifiers($document, $profile));
            $errors = array_merge($errors, $this->frenchValidator->validate($document, $profile));
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
     * Validate UBL invoices against the normative OASIS UBL 2.1 schemas.
     *
     * Profile-specific EN 16931 and national rules require Schematron and are
     * intentionally kept separate from this syntax validation layer.
     *
     * @return string[]
     */
    private function validateUblSchema(DOMDocument $document): array
    {
        $schemaFiles = [
            'Invoice'    => 'UBL-Invoice-2.1.xsd',
            'CreditNote' => 'UBL-CreditNote-2.1.xsd',
        ];
        $root       = $document->documentElement?->localName ?? '';
        $schemaFile = $schemaFiles[$root] ?? null;

        if ($schemaFile === null) {
            return ['UBL 2.1 XSD validation does not support this document type.'];
        }

        $schemaPath = realpath(self::UBL_21_SCHEMA_DIRECTORY . $schemaFile);
        if ($schemaPath === false || ! is_readable($schemaPath)) {
            return ['The required UBL 2.1 XSD validation artifact is unavailable.'];
        }

        libxml_clear_errors();
        $valid        = $document->schemaValidate($schemaPath, LIBXML_NONET);
        $libxmlErrors = libxml_get_errors();
        libxml_clear_errors();

        if ($valid) {
            return [];
        }

        if ($libxmlErrors === []) {
            return ['Document does not conform to the OASIS UBL 2.1 XSD.'];
        }

        return array_values(array_unique(array_map(
            static function (LibXMLError $error): string {
                $message = preg_replace('/\s+/', ' ', trim($error->message));
                $line    = $error->line > 0 ? ' at line ' . $error->line : '';

                return 'UBL 2.1 XSD' . $line . ': ' . $message;
            },
            $libxmlErrors
        )));
    }

    /**
     * Validate the current French Factur-X EN 16931 profile.
     *
     * Other CII profiles keep their identifier checks until their own
     * versioned validation artifacts are bundled.
     *
     * @return string[]
     */
    private function validateCiiSchema(DOMDocument $document, EInvoiceProfile $profile): array
    {
        if ($profile->format() !== 'factur-x') {
            return [];
        }

        $schemaPath = realpath(self::FACTUR_X_109_SCHEMA);
        if ($schemaPath === false || ! is_readable($schemaPath)) {
            return ['The required Factur-X 1.09 EN 16931 XSD is unavailable.'];
        }

        libxml_clear_errors();
        $valid        = $document->schemaValidate($schemaPath, LIBXML_NONET);
        $libxmlErrors = libxml_get_errors();
        libxml_clear_errors();

        if ($valid) {
            return [];
        }

        if ($libxmlErrors === []) {
            return ['Document does not conform to the Factur-X 1.09 EN 16931 XSD.'];
        }

        return array_values(array_unique(array_map(
            static function (LibXMLError $error): string {
                $message = preg_replace('/\s+/', ' ', trim($error->message));
                $line    = $error->line > 0 ? ' at line ' . $error->line : '';

                return 'Factur-X 1.09 EN 16931 XSD' . $line . ': ' . $message;
            },
            $libxmlErrors
        )));
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
