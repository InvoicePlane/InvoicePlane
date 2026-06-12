<?php

defined('BASEPATH') or exit('No direct script access allowed');

interface MerchantProviderInterface
{
    public static function providerCode(): string;

    public static function providerName(): string;

    public function authenticate(array $settings): bool;

    public function sendInvoice(string $documentPath, array $metadata): array;

    public function getInvoiceStatus(string $externalId): array;

    public function receiveInvoices(array $filters = []): array;

    public function getInvoiceEvents(array $filters = []): array;
}
