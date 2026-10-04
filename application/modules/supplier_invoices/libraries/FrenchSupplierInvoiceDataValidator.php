<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Validates French supplier invoice data after structured document parsing.
 *
 * XML profile validation remains the responsibility of the integrations
 * module. This validator protects the accounting register from incomplete or
 * inconsistent normalized data.
 */
final class FrenchSupplierInvoiceDataValidator
{
    /**
     * @return string[]
     */
    public function validate(array $parsed): array
    {
        $supplier = is_array($parsed['supplier'] ?? null) ? $parsed['supplier'] : [];
        $invoice  = is_array($parsed['invoice'] ?? null) ? $parsed['invoice'] : [];
        $country  = strtoupper(trim((string) ($supplier['supplier_country'] ?? '')));
        $vatId    = $this->identifier($supplier['supplier_vat_id'] ?? null);

        if ($country !== 'FR' && ! str_starts_with($vatId, 'FR')) {
            return [];
        }

        $errors = [];
        if (trim((string) ($supplier['supplier_name'] ?? '')) === '') {
            $errors[] = 'France: supplier name is required.';
        }
        if (trim((string) ($supplier['supplier_address_1'] ?? '')) === '') {
            $errors[] = 'France: supplier address is required.';
        }
        $taxCode = preg_replace('/\D+/', '', (string) ($supplier['supplier_tax_code'] ?? ''));
        if ( ! is_string($taxCode) || preg_match('/^\d{9}$/', $taxCode) !== 1) {
            $errors[] = 'France: supplier SIREN must contain exactly 9 digits.';
        }

        if ($vatId !== '' && preg_match('/^FR[0-9A-Z]{2}\d{9}$/', $vatId) !== 1) {
            $errors[] = 'France: supplier VAT ID must use the FR plus two-character key and nine-digit SIREN format.';
        } elseif ($vatId !== '' && ctype_digit(substr($vatId, 2, 2)) && $taxCode !== '') {
            $expectedKey = (12 + 3 * ((int) $taxCode % 97)) % 97;
            if ((int) substr($vatId, 2, 2) !== $expectedKey) {
                $errors[] = 'France: supplier VAT ID key does not match the SIREN.';
            }
        }

        $invoiceNumber = trim((string) ($invoice['supplier_invoice_number'] ?? ''));
        if ($invoiceNumber === '' || preg_match('/^[A-Za-z0-9+_\/-]+$/', $invoiceNumber) !== 1) {
            $errors[] = 'France: invoice number must use only letters, digits, +, -, _, or /.';
        }

        $invoiceDate = (string) ($invoice['supplier_invoice_date'] ?? '');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $invoiceDate);
        if ($date === false || $date->format('Y-m-d') !== $invoiceDate) {
            $errors[] = 'France: invoice date must be a valid ISO date.';
        }

        $currency = strtoupper(trim((string) ($invoice['currency_code'] ?? '')));
        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            $errors[] = 'France: invoice currency must be a three-letter ISO 4217 code.';
        }

        $items = is_array($parsed['items'] ?? null) ? $parsed['items'] : [];
        if ($items === []) {
            $errors[] = 'France: at least one invoice line is required for structured supplier invoice import.';
        }

        $subtotal = $this->number($invoice['subtotal'] ?? null);
        $taxTotal = $this->number($invoice['tax_total'] ?? null);
        $total    = $this->number($invoice['total'] ?? null);
        if ($subtotal === null || $taxTotal === null || $total === null) {
            $errors[] = 'France: invoice subtotal, VAT total, and total are required.';
        } elseif (abs(($subtotal + $taxTotal) - $total) > 0.02) {
            $errors[] = 'France: invoice total must equal subtotal plus VAT within two cents.';
        }

        return $errors;
    }

    private function identifier(mixed $value): string
    {
        if ( ! is_scalar($value)) {
            return '';
        }

        return strtoupper((string) preg_replace('/\s+/', '', (string) $value));
    }

    private function number(mixed $value): ?float
    {
        if ( ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
