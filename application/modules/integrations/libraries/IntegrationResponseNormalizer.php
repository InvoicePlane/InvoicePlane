<?php

defined('BASEPATH') || exit('No direct script access allowed');

final class IntegrationResponseNormalizer
{
    public static function extractItems(array $providerResponse, array $collectionKeys): array
    {
        $payload = $providerResponse['response'] ?? [];

        if ( ! is_array($payload)) {
            return [];
        }

        foreach ($collectionKeys as $key) {
            if (array_key_exists($key, $payload)) {
                if ( ! is_array($payload[$key])) {
                    return [];
                }

                $payload = $payload[$key];
                break;
            }
        }

        if (isset($payload['id']) || isset($payload['external_id'])) {
            return [$payload];
        }

        if ( ! array_is_list($payload)) {
            return [];
        }

        return array_values(array_filter($payload, 'is_array'));
    }
}
