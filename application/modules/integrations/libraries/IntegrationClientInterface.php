<?php

defined('BASEPATH') || exit('No direct script access allowed');

interface IntegrationClientInterface
{
    public static function clientCode(): string;

    public static function clientName(): string;

    public static function authType(): string;

    public static function defaultSettings(): array;

    public static function settingsSchema(): array;

    public function authenticate(array $settings): bool;

    public function sendInvoice(string $documentPath, array $metadata): array;

    public function getInvoiceStatus(string $externalId): array;

    public function receiveInvoices(array $filters = []): array;

    public function downloadInvoiceDocument(array $invoice): array;

    public function getInvoiceEvents(array $filters = []): array;

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array;

    public function fetchToken(array $settings): string;

    /**
     * Probe the provider's API for reachability without sending anything.
     *
     * Implementations authenticate with $settings and make one cheap read call.
     * "reachable" means the endpoint answered at all (any HTTP status) — a
     * transport failure (DNS, refused connection, TLS, timeout) or an auth
     * failure is reachable = false. The http_code / message carry the detail so
     * callers can tell "up but unhealthy" (5xx) from "up and well" (2xx).
     *
     * @param array<string, mixed> $settings
     *
     * @return array{reachable: bool, http_code: int, message: string}
     */
    public function ping(array $settings): array;
}
