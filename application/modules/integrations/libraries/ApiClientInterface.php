<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Contract for HTTP adapters used by integration provider clients.
 *
 * Recognized $options keys:
 *   bearer      string   Authorization: Bearer token
 *   json        array    JSON-encoded request body (sets Content-Type: application/json)
 *   form_params array    URL-encoded form body (sets Content-Type: application/x-www-form-urlencoded)
 *   multipart   array    Multipart fields; values may be CURLFile objects for file uploads
 *   query       array    Query-string parameters appended to $url
 *   headers     array    Extra "Name: value" header strings added verbatim
 *
 * Returned envelope always contains:
 *   success     bool
 *   external_id string|null
 *   status      string
 *   message     string
 *   http_code   int
 *   request     array
 *   response    array   decoded JSON body (empty array when body is absent or non-JSON)
 */
interface ApiClientInterface
{
    public function request(RequestMethod $method, string $url, array $options = []): array;
}
