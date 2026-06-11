<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

interface PdpProviderInterface
{
    public function authenticate(array $settings): bool;
    public function sendInvoice(string $filePath, array $invoiceData): array;
    public function getInvoiceStatus(string $externalId): array;
    public function receiveInvoices(array $filters = array()): array;
}
