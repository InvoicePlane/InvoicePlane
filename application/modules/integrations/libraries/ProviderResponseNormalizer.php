<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Normalizes common provider response envelopes without discarding the raw body.
 */
final class ProviderResponseNormalizer
{
    public static function entity(array $transportResponse, array $envelopeKeys = []): array
    {
        $entity = $transportResponse['response'] ?? [];

        if ( ! is_array($entity)) {
            return $transportResponse;
        }

        foreach ($envelopeKeys as $key) {
            if (isset($entity[$key]) && is_array($entity[$key])) {
                $entity = $entity[$key];
                break;
            }
        }

        if (isset($entity['attributes']) && is_array($entity['attributes'])) {
            $entity = array_replace($entity, $entity['attributes']);
        }

        $transportResponse['response']['entity'] = $entity;

        $externalId = self::firstScalar($entity, [
            'id',
            'external_id',
            'invoice_id',
            'document_id',
            'transmission_id',
        ]);
        $statusCode = self::firstScalar($entity, ['status_code', 'lifecycle_status_code']);
        $status     = $statusCode ?? self::firstScalar($entity, ['status', 'einvoicing_status', 'state']);
        $message    = self::firstScalar($entity, ['message', 'reason_message', 'reason', 'detail']);

        if ($externalId !== null) {
            $transportResponse['external_id'] = $externalId;
        }

        if ($status !== null) {
            $transportResponse['status'] = $status;
        }

        if ($statusCode !== null) {
            $transportResponse['status_code'] = $statusCode;
        }

        if ($message !== null) {
            $transportResponse['message'] = $message;
        }

        return $transportResponse;
    }

    public static function collection(
        array $transportResponse,
        array $collectionKeys,
        string $normalizedKey
    ): array {
        $payload = $transportResponse['response'] ?? [];

        if ( ! is_array($payload)) {
            return $transportResponse;
        }

        $collection = self::findCollection($payload, $collectionKeys);
        if ($collection !== null) {
            $transportResponse['response'][$normalizedKey] = $collection;
        }

        return $transportResponse;
    }

    private static function findCollection(array $payload, array $keys): ?array
    {
        if (array_is_list($payload)) {
            return $payload;
        }

        foreach ($keys as $key) {
            if ( ! isset($payload[$key]) || ! is_array($payload[$key])) {
                continue;
            }

            $candidate = $payload[$key];
            if (array_is_list($candidate)) {
                return $candidate;
            }

            foreach ($keys as $nestedKey) {
                if (isset($candidate[$nestedKey]) && is_array($candidate[$nestedKey]) && array_is_list($candidate[$nestedKey])) {
                    return $candidate[$nestedKey];
                }
            }
        }

        return null;
    }

    private static function firstScalar(array $entity, array $keys): int|string|null
    {
        foreach ($keys as $key) {
            if (isset($entity[$key]) && (is_int($entity[$key]) || is_string($entity[$key]))) {
                return $entity[$key];
            }
        }

        return null;
    }
}
