<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Contract for HTTP adapters used by integration provider clients.
 *
 * Recognized $options keys:
 *   bearer      string   Authorization: Bearer token
 *   json        array    JSON-encoded request body (sets Content-Type: application/json)
 *   form_params array    URL-encoded form body (sets Content-Type: application/x-www-form-urlencoded)
 *   multipart   array    Multipart fields; values may be CURLFile objects for file uploads
 *   body        string   Raw request body; set Content-Type through headers when required
 *   query       array    Query-string parameters appended to $url
 *   headers     array    Extra "Name: value" header strings added verbatim
 *   binary      bool     Return the response bytes under body instead of decoding JSON
 *   max_response_bytes int Reject a binary response larger than this limit
 *   resolve     array    Optional cURL host pin: [host, port, resolved public IP]
 *
 * Returned envelope always contains:
 *   success     bool
 *   external_id string|null
 *   status      string
 *   message     string
 *   http_code   int
 *   request     array
 *   response    array   decoded JSON body (empty array when body is absent or non-JSON)
 *   body        string  response bytes when the binary option is enabled
 *   content_type string response Content-Type without parameters
 */
interface ApiClientInterface
{
    public function request(RequestMethod $method, string $url, array $options = []): array;
}
