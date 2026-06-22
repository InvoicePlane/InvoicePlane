<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Abstracts the two distinct HTTP operations performed by LetsPeppolApiClient:
 *
 *  - fetchToken()  — unauthenticated form-POST to the OAuth2 token endpoint
 *  - send()        — authenticated API request (GET / POST, optionally multipart)
 *
 * A real implementation wraps curl; test doubles record calls without hitting
 * the network.
 */
interface LetsPeppolHttpClientInterface
{
    /**
     * POST client_credentials grant to the token endpoint.
     *
     * @return array Decoded JSON body, e.g. ['access_token' => '...', 'expires_in' => 3600]
     *
     * @throws RuntimeException on curl error or non-2xx HTTP status
     */
    public function fetchToken(string $tokenUrl, string $clientId, string $clientSecret): array;

    /**
     * Execute an authenticated API call.
     *
     * @param RequestMethod $method
     * @param string        $url       Full URL (base + path + optional query string)
     * @param array         $payload   Request body; empty for GET
     * @param bool          $multipart When true, send payload as multipart/form-data
     * @param string|null   $bearerToken  Access token for the Authorization header
     *
     * @return array Normalised response envelope:
     *   [
     *     'success'     => bool,
     *     'external_id' => string|null,
     *     'status'      => string,
     *     'message'     => string,
     *     'http_code'   => int,
     *     'request'     => ['url' => string, 'method' => string],
     *     'response'    => array,
     *   ]
     */
    public function send(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        ?string $bearerToken = null
    ): array;
}
