<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/IntegrationPayloadSanitizer.php';

final class IncomingInvoiceSynchronizer
{
    public function __construct(private ?IncomingInvoiceDocumentService $documents = null)
    {
        $this->documents ??= new IncomingInvoiceDocumentService();
    }

    /**
     * @return array{received: int, archived: int, skipped: int, failed: int, supplier_imported: int, supplier_import_failed: int, error_codes: array<string, int>, errors: string[]}
     */
    public function synchronize(
        IntegrationClient $client,
        string $providerCode,
        int $merchantClientId,
        MerchantResponseDriver $driver,
        array $items,
        object $responsesModel,
        string $archiveDirectory,
        ?callable $supplierInvoiceImporter = null
    ): array {
        $result = [
            'received'    => 0,
            'archived'    => 0,
            'skipped'     => 0,
            'failed'      => 0,
            'supplier_imported' => 0,
            'supplier_import_failed' => 0,
            'error_codes' => [],
            'errors'      => [],
        ];

        foreach ($items as $item) {
            if ( ! is_array($item)) {
                continue;
            }

            $result['received']++;
            $externalId = $item['id']
                ?? $item['external_id']
                ?? $item['document_id']
                ?? $item['invoice_id']
                ?? null;
            $externalId = is_scalar($externalId) ? (string) $externalId : null;

            if (is_string($externalId)
                && $externalId !== ''
                && $responsesModel->has_valid_incoming_document($merchantClientId, $externalId)) {
                $result['skipped']++;

                if ($supplierInvoiceImporter !== null) {
                    $responseId = $responsesModel->get_valid_incoming_document_id($merchantClientId, $externalId);
                    $this->importSupplierInvoice(
                        $responseId,
                        $supplierInvoiceImporter,
                        $result
                    );
                }

                continue;
            }

            $participantId = $item['sender'] ?? $item['peppol_participant_id'] ?? null;
            $documentType  = $this->documentType($item);

            try {
                $download = $client->downloadInvoiceDocument($item);
                $document = $this->documents->archive(
                    $providerCode,
                    $item,
                    $download,
                    $archiveDirectory
                );
                $result['archived']++;
            } catch (Throwable $e) {
                $errorCode         = $this->errorCode($e);
                $message           = IntegrationPayloadSanitizer::text($e->getMessage()) ?? 'Incoming document validation failed.';
                $item['status']    = 'error';
                $item['error_code'] = $errorCode;
                $item['error_detail'] = $message;
                $item['message']      = 'Incoming document rejected [' . $errorCode . ']: ' . $message;
                $document             = [
                    'document_validation_status' => 'failed',
                    'document_validation_error'  => $message,
                ];
                $result['failed']++;
                $result['error_codes'][$errorCode] = ($result['error_codes'][$errorCode] ?? 0) + 1;
                if (count($result['errors']) < 10) {
                    $reference = $externalId !== null
                        ? ' (' . (IntegrationPayloadSanitizer::text($externalId, 100) ?? 'unknown') . ')'
                        : '';
                    $result['errors'][] = 'Incoming document [' . $errorCode . ']' . $reference . ': ' . $message;
                }
            }

            $responseId = $responsesModel->create_inbound_item(
                $merchantClientId,
                $item,
                $driver,
                is_string($participantId) ? $participantId : null,
                $documentType,
                $document
            );

            if (($document['document_validation_status'] ?? null) === 'valid'
                && $supplierInvoiceImporter !== null) {
                $this->importSupplierInvoice(
                    $responseId,
                    $supplierInvoiceImporter,
                    $result
                );
            }
        }

        return $result;
    }

    /**
     * @param array{supplier_imported: int, supplier_import_failed: int, error_codes: array<string, int>, errors: string[]} $result
     */
    private function importSupplierInvoice(
        ?int $responseId,
        callable $supplierInvoiceImporter,
        array &$result
    ): void {
        if ($responseId === null || $responseId <= 0) {
            return;
        }

        try {
            $supplierInvoiceImporter($responseId);
            $result['supplier_imported']++;
        } catch (Throwable $e) {
            $errorCode = 'supplier_invoice_import_failed';
            $message = IntegrationPayloadSanitizer::text($e->getMessage(), 500)
                ?? 'Unable to import the supplier invoice.';
            $result['supplier_import_failed']++;
            $result['error_codes'][$errorCode] = ($result['error_codes'][$errorCode] ?? 0) + 1;
            if (count($result['errors']) < 10) {
                $result['errors'][] = 'Supplier invoice import [' . $errorCode . ']: ' . $message;
            }
        }
    }

    private function errorCode(Throwable $error): string
    {
        $message = mb_strtolower($error->getMessage());

        return match (true) {
            str_contains($message, 'download') || str_contains($message, 'provider returned') => 'document_download_failed',
            str_contains($message, 'archive') || str_contains($message, 'stage') => 'document_archive_failed',
            str_contains($message, 'factur-x') || str_contains($message, 'xml') || str_contains($message, 'validation') => 'document_validation_failed',
            default => 'incoming_document_failed',
        };
    }

    private function documentType(array $item): ?PeppolDocumentType
    {
        $value = $item['document_type'] ?? $item['peppol_document_type'] ?? null;

        return is_string($value) ? PeppolDocumentType::tryFrom($value) : null;
    }
}
