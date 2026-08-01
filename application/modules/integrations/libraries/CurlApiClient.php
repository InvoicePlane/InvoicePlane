<?php

defined('BASEPATH') || exit('No direct script access allowed');

class CurlApiClient implements ApiClientInterface
{
    public function request(RequestMethod $method, string $url, array $options = []): array
    {
        $headers = ['Accept: application/json'];
        $binary  = ! empty($options['binary']);

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

        if (isset($options['resolve']) && is_array($options['resolve']) && count($options['resolve']) === 3) {
            [$host, $port, $ip]           = $options['resolve'];
            $resolvedAddress              = str_contains($ip, ':') ? '[' . $ip . ']' : $ip;
            $curlOptions[CURLOPT_RESOLVE] = [sprintf('%s:%d:%s', $host, $port, $resolvedAddress)];
        }

        $binaryBody     = '';
        $binaryTooLarge = false;
        if ($binary) {
            $maximumBytes                        = max(1, (int) ($options['max_response_bytes'] ?? 15 * 1024 * 1024));
            $curlOptions[CURLOPT_RETURNTRANSFER] = false;
            $curlOptions[CURLOPT_WRITEFUNCTION]  = static function ($curl, string $chunk) use (&$binaryBody, &$binaryTooLarge, $maximumBytes): int {
                if (mb_strlen($binaryBody, '8bit') + mb_strlen($chunk, '8bit') > $maximumBytes) {
                    $binaryTooLarge = true;

                    return 0;
                }

                $binaryBody .= $chunk;

                return mb_strlen($chunk, '8bit');
            };
        }

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
        $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $curlError   = curl_error($ch);

        curl_close($ch);

        $decoded = $binary ? [] : (json_decode(is_string($rawResponse) ? $rawResponse : '', true) ?? []);

        if ($curlError) {
            return [
                'success'      => false,
                'external_id'  => null,
                'status'       => 'error',
                'message'      => $binaryTooLarge ? 'Provider document exceeds the download size limit.' : $curlError,
                'http_code'    => 0,
                'request'      => ['url' => $url, 'method' => $method->value],
                'response'     => [],
                'body'         => '',
                'content_type' => '',
            ];
        }

        $result = [
            'success'      => $httpCode >= 200 && $httpCode < 300,
            'external_id'  => $decoded['id'] ?? $decoded['external_id'] ?? null,
            'status'       => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message'      => $decoded['message'] ?? 'API response received',
            'http_code'    => $httpCode,
            'request'      => ['url' => $url, 'method' => $method->value],
            'response'     => $decoded,
            'content_type' => mb_strtolower(trim(explode(';', $contentType, 2)[0])),
        ];

        if ($binary) {
            $result['body'] = $binaryBody;
        }

        return $result;
    }
}
