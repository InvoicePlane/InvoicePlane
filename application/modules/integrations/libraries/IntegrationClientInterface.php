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

    public function getInvoiceEvents(array $filters = []): array;

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array;

    public function fetchToken(array $settings): string;
}
