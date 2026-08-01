<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Generates the selected profile and returns a locally validated artifact.
 */
final class EInvoiceDocumentService
{
    public function __construct(private ?EInvoiceDocumentValidator $validator = null)
    {
        $this->validator ??= new EInvoiceDocumentValidator();
    }

    public function generate(
        int $invoiceId,
        object $invoice,
        array $items,
        EInvoiceProfile $profile,
        string $documentDirectory
    ): EInvoiceArtifact {
        if ($invoiceId < 1) {
            throw new InvalidArgumentException('Invalid invoice ID.');
        }

        $generatorPath = APPPATH . 'libraries/XMLtemplates/' . $profile->generator() . 'Xml.php';
        if ( ! is_file($generatorPath)) {
            throw new RuntimeException('The selected e-invoice generator is not installed.');
        }

        $documentDirectory = rtrim($documentDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if ( ! is_dir($documentDirectory)
            && ! mkdir($documentDirectory, 0775, true)
            && ! is_dir($documentDirectory)) {
            throw new RuntimeException('Unable to create e-invoice output directory.');
        }

        $documentPath = $documentDirectory . 'invoice_' . $invoiceId . '.' . $profile->extension();
        if (is_file($documentPath) && ! unlink($documentPath)) {
            throw new RuntimeException('Unable to replace the generated e-invoice.');
        }

        if ($profile->embedded()) {
            $this->validateEmbeddedData($invoice, $items, $profile, $invoiceId);
            $this->writePdf($invoiceId, $documentPath);
        } else {
            $this->writeXml($invoice, $items, $profile, $invoiceId, $documentPath);
        }

        $artifact = $this->validator->validate($documentPath, $profile);
        $artifact->assertValid();

        return $artifact;
    }

    private function writePdf(int $invoiceId, string $documentPath): void
    {
        $pdf = generate_invoice_pdf($invoiceId, false, null, null);
        if (empty($pdf)) {
            throw new RuntimeException('InvoicePlane did not return PDF content.');
        }

        if (is_string($pdf) && is_file($pdf)) {
            if ( ! copy($pdf, $documentPath)) {
                throw new RuntimeException('Unable to copy the generated invoice PDF.');
            }

            return;
        }

        if (file_put_contents($documentPath, $pdf) === false) {
            throw new RuntimeException('Unable to write the generated invoice PDF.');
        }
    }

    private function validateEmbeddedData(
        object $invoice,
        array $items,
        EInvoiceProfile $profile,
        int $invoiceId
    ): void {
        $temporaryName = 'integration_embedded_' . $invoiceId . '_' . bin2hex(random_bytes(8));
        $temporaryPath = generate_xml_invoice_file(
            $invoice,
            $items,
            $profile->generator(),
            $temporaryName,
            $profile->options()
        );

        try {
            $errors = $this->validator->validateStructuredData($temporaryPath, $profile);
            if ($errors !== []) {
                throw new RuntimeException('Embedded e-invoice XML validation failed: ' . implode(' | ', $errors));
            }
        } finally {
            if (is_file($temporaryPath) && ! unlink($temporaryPath)) {
                log_message('warning', 'Unable to remove validated e-invoice temporary file.');
            }
        }
    }

    private function writeXml(
        object $invoice,
        array $items,
        EInvoiceProfile $profile,
        int $invoiceId,
        string $documentPath
    ): void {
        $temporaryName = 'integration_invoice_' . $invoiceId;
        $temporaryPath = generate_xml_invoice_file(
            $invoice,
            $items,
            $profile->generator(),
            $temporaryName,
            $profile->options()
        );

        if ( ! is_file($temporaryPath) || ! copy($temporaryPath, $documentPath)) {
            throw new RuntimeException('Unable to persist the generated e-invoice XML.');
        }

        if ( ! unlink($temporaryPath)) {
            log_message('warning', 'Unable to remove generated e-invoice temporary file.');
        }
    }
}
