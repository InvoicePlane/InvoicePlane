<?php

defined('BASEPATH') or exit('No direct script access allowed');

interface IntegrationClientInterface
{
    public static function clientCode(): string;

    public static function clientName(): string;

    public function authenticate(array $settings): bool;

    public function sendInvoice(string $documentPath, array $metadata): array;

    public function getInvoiceStatus(string $externalId): array;

    public function receiveInvoices(array $filters = []): array;

    public function getInvoiceEvents(array $filters = []): array;

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array;

    public function fetchToken(array $settings): string;
}
