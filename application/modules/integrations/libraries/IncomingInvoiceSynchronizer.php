<?php

defined('BASEPATH') || exit('No direct script access allowed');

final class IncomingInvoiceSynchronizer
{
    public function __construct(private ?IncomingInvoiceDocumentService $documents = null)
    {
        $this->documents ??= new IncomingInvoiceDocumentService();
    }

    /**
     * @return array{received: int, archived: int, skipped: int, failed: int}
     */
    public function synchronize(
        IntegrationClient $client,
        string $providerCode,
        int $merchantClientId,
        MerchantResponseDriver $driver,
        array $items,
        object $responsesModel,
        string $archiveDirectory
    ): array {
        $result = ['received' => 0, 'archived' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($items as $item) {
            if ( ! is_array($item)) {
                continue;
            }

            $result['received']++;
            $externalId = $item['id'] ?? $item['external_id'] ?? null;
            $externalId = is_scalar($externalId) ? (string) $externalId : null;

            if (is_string($externalId)
                && $externalId !== ''
                && $responsesModel->has_valid_incoming_document($merchantClientId, $externalId)) {
                $result['skipped']++;

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
                $message         = mb_substr($e->getMessage(), 0, 1000);
                $item['status']  = 'error';
                $item['message'] = 'Incoming document rejected: ' . $message;
                $document        = [
                    'document_validation_status' => 'failed',
                    'document_validation_error'  => $message,
                ];
                $result['failed']++;
            }

            $responsesModel->create_inbound_item(
                $merchantClientId,
                $item,
                $driver,
                is_string($participantId) ? $participantId : null,
                $documentType,
                $document
            );
        }

        return $result;
    }

    private function documentType(array $item): ?PeppolDocumentType
    {
        $value = $item['document_type'] ?? $item['peppol_document_type'] ?? null;

        return is_string($value) ? PeppolDocumentType::tryFrom($value) : null;
    }
}
