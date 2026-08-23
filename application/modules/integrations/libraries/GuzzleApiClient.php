<?php

defined('BASEPATH') || exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class GuzzleApiClient implements ApiClientInterface
{
    private Client $guzzle;

    public function __construct(?Client $guzzle = null)
    {
        $this->guzzle = $guzzle ?? new Client([
            'connect_timeout' => 10,
            'allow_redirects' => false,
            'http_errors'     => false,
            'verify'          => true,
            'timeout'         => 30,
        ]);
    }

    public function request(RequestMethod $method, string $url, array $options = []): array
    {
        if (mb_strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
            throw new \RuntimeException('Provider requests must use HTTPS.');
        }

        $headers = ['Accept' => 'application/json'];

        if ( ! empty($options['bearer'])) {
            $headers['Authorization'] = 'Bearer ' . $options['bearer'];
        }

        foreach ($options['headers'] ?? [] as $line) {
            [$name, $value] = explode(': ', $line, 2);
            $headers[$name] = $value;
        }

        if ( ! empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        $binary        = ! empty($options['binary']);
        $guzzleOptions = ['headers' => $headers, 'stream' => true];

        if (isset($options['resolve']) && is_array($options['resolve']) && count($options['resolve']) === 3) {
            [$host, $port, $ip]                     = $options['resolve'];
            $resolvedAddress                        = str_contains($ip, ':') ? '[' . $ip . ']' : $ip;
            $guzzleOptions['curl'][CURLOPT_RESOLVE] = [sprintf('%s:%d:%s', $host, $port, $resolvedAddress)];
        }

        if (isset($options['body'])) {
            $guzzleOptions['body'] = $options['body'];
        } elseif (isset($options['json'])) {
            $guzzleOptions['json'] = $options['json'];
        } elseif (isset($options['form_params'])) {
            $guzzleOptions['form_params'] = $options['form_params'];
        } elseif (isset($options['multipart'])) {
            $guzzleOptions['multipart'] = $this->buildMultipart($options['multipart']);
        }

        try {
            $response = $this->guzzle->request($method->value, $url, $guzzleOptions);
            $httpCode = $response->getStatusCode();

            $defaultMaxBytes = $binary ? 15 * 1024 * 1024 : 20 * 1024 * 1024;

            try {
                $rawBody = $this->readBinaryBody($response->getBody(), (int) ($options['max_response_bytes'] ?? $defaultMaxBytes));
            } catch (\RuntimeException $e) {
                return [
                    'success'     => false,
                    'external_id' => null,
                    'status'      => 'error',
                    'message'     => $binary
                        ? 'Provider document exceeds the download size limit.'
                        : 'Provider response exceeds the maximum allowed size.',
                    'http_code'    => $httpCode,
                    'request'      => ['url' => $url, 'method' => $method->value],
                    'response'     => [],
                    'body'         => '',
                    'content_type' => '',
                ];
            }

            $decoded = $binary ? [] : (json_decode($rawBody, true) ?? []);
        } catch (ConnectException $e) {
            throw new \RuntimeException('API connection error: ' . $e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            $httpCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $decoded  = $e->getResponse()
                ? (json_decode((string) $e->getResponse()->getBody(), true) ?? [])
                : [];

            return [
                'success'      => false,
                'external_id'  => null,
                'status'       => 'error',
                'message'      => $e->getMessage(),
                'http_code'    => $httpCode,
                'request'      => ['url' => $url, 'method' => $method->value],
                'response'     => $decoded,
                'body'         => '',
                'content_type' => '',
            ];
        }

        $result = [
            'success'      => $httpCode >= 200 && $httpCode < 300,
            'external_id'  => $decoded['id'] ?? null,
            'status'       => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300 ? 'sent' : 'error'),
            'message'      => $decoded['message'] ?? 'API response received',
            'http_code'    => $httpCode,
            'request'      => ['url' => $url, 'method' => $method->value],
            'response'     => $decoded,
            'content_type' => mb_strtolower(trim(explode(';', $response->getHeaderLine('Content-Type'), 2)[0])),
        ];

        if ($binary) {
            $result['body'] = $rawBody;
        }

        return $result;
    }

    private function buildMultipart(array $payload): array
    {
        $parts = [];

        foreach ($payload as $name => $value) {
            if ($value instanceof \CURLFile) {
                $parts[] = [
                    'name'     => $name,
                    'contents' => fopen($value->getFilename(), 'r'),
                    'filename' => $value->getPostFilename(),
                    'headers'  => ['Content-Type' => $value->getMimeType()],
                ];
            } else {
                $parts[] = ['name' => $name, 'contents' => (string) $value];
            }
        }

        return $parts;
    }

    private function readBinaryBody(\Psr\Http\Message\StreamInterface $stream, int $maximumBytes): string
    {
        $body         = '';
        $maximumBytes = max(1, $maximumBytes);

        while ( ! $stream->eof()) {
            $chunk = $stream->read(8192);
            if (mb_strlen($body, '8bit') + mb_strlen($chunk, '8bit') > $maximumBytes) {
                throw new RuntimeException('Provider document exceeds the download size limit.');
            }

            $body .= $chunk;
        }

        return $body;
    }
}
