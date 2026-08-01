<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Validates French F10 transaction and payment reports against DGFiP v3.2.
 */
final class FrenchEReportingValidator
{
    private const SCHEMA_PATH = __DIR__ . '/../resources/validation/france-v3.2/e-reporting/ereporting.xsd';

    /**
     * @return string[]
     */
    public function validate(string $path): array
    {
        if ( ! is_file($path) || filesize($path) === 0) {
            return ['French e-reporting document is missing or empty.'];
        }

        $schemaPath = realpath(self::SCHEMA_PATH);
        if ($schemaPath === false || ! is_readable($schemaPath)) {
            return ['The required DGFiP e-reporting v3.2 XSD is unavailable.'];
        }

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $document                     = new DOMDocument();
        $document->resolveExternals   = false;
        $document->substituteEntities = false;
        $loaded                       = $document->load($path, LIBXML_NONET);

        if ( ! $loaded || $document->documentElement?->localName !== 'Report') {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            return ['Document is not a French F10 e-reporting XML report.'];
        }

        libxml_clear_errors();
        $valid        = $document->schemaValidate($schemaPath, LIBXML_NONET);
        $libxmlErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($valid) {
            return [];
        }

        if ($libxmlErrors === []) {
            return ['Document does not conform to the DGFiP e-reporting v3.2 XSD.'];
        }

        return array_values(array_unique(array_map(
            static function (LibXMLError $error): string {
                $message = preg_replace('/\s+/', ' ', trim($error->message));
                $line    = $error->line > 0 ? ' at line ' . $error->line : '';

                return 'DGFiP e-reporting v3.2 XSD' . $line . ': ' . $message;
            },
            $libxmlErrors
        )));
    }
}
