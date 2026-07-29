<?php

defined('BASEPATH') || exit('No direct script access allowed');

class CurlApiClient implements ApiClientInterface
{
    public function request(RequestMethod $method, string $url, array $options = []): array
    {
        $headers = ['Accept: application/json'];

        if ( ! empty($options['bearer'])) {
            $headers[] = 'Authorization: Bearer ' . $options['bearer'];
        }

        foreach ($options['headers'] ?? [] as $h) {
            $headers[] = $h;
        }

        if ( ! empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        $ch = curl_init();

        $curlOptions = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_TIMEOUT         => 30,
        ];

        if (isset($options['body'])) {
            $curlOptions[CURLOPT_POST]       = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $options['body'];
        } elseif (isset($options['form_params'])) {
            $curlOptions[CURLOPT_POST]       = true;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($options['form_params']);
            $headers[]                       = 'Content-Type: application/x-www-form-urlencoded';
            $curlOptions[CURLOPT_HTTPHEADER] = $headers;
        } elseif (isset($options['multipart'])) {
            $curlOptions[CURLOPT_POST]       = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $options['multipart'];
        } elseif (isset($options['json'])) {
            $json = json_encode($options['json']);
            if ($json === false) {
                throw new RuntimeException('Unable to encode the provider request as JSON.');
            }

            $curlOptions[CURLOPT_POST]       = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $json;
            $headers[]                       = 'Content-Type: application/json';
            $curlOptions[CURLOPT_HTTPHEADER] = $headers;
        } elseif ($method === RequestMethod::POST) {
            $curlOptions[CURLOPT_POST]       = true;
            $curlOptions[CURLOPT_POSTFIELDS] = '';
        }

        curl_setopt_array($ch, $curlOptions);

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($rawResponse ?: '', true) ?? [];

        if ($curlError) {
            return [
                'success'     => false,
                'external_id' => null,
                'status'      => 'error',
                'message'     => $curlError,
                'http_code'   => 0,
                'request'     => ['url' => $url, 'method' => $method->value],
                'response'    => [],
            ];
        }

        return [
            'success'     => $httpCode >= 200 && $httpCode < 300,
            'external_id' => $decoded['id'] ?? $decoded['external_id'] ?? null,
            'status'      => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message'     => $decoded['message'] ?? 'API response received',
            'http_code'   => $httpCode,
            'request'     => ['url' => $url, 'method' => $method->value],
            'response'    => $decoded,
        ];
    }
}
