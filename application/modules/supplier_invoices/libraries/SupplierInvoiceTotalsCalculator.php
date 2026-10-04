<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

final class SupplierInvoiceTotalsCalculator
{
    public function calculate(array $items): array
    {
        $normalizedItems = [];
        $subtotal = 0.0;
        $taxTotal = 0.0;

        foreach ($items as $item) {
            $name = trim((string) ($item['item_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $quantity = $this->number($item['quantity'] ?? null, 1.0);
            $unitPrice = $this->nullableNumber($item['unit_price'] ?? null);
            $taxRate = $this->number($item['tax_rate'] ?? null, 0.0);
            $lineSubtotal = $unitPrice === null
                ? $this->nullableNumber($item['subtotal'] ?? null)
                : $this->money($quantity * $unitPrice);
            $lineTax = $lineSubtotal === null
                ? $this->nullableNumber($item['tax_total'] ?? null)
                : $this->money($lineSubtotal * $taxRate / 100);
            $lineTotal = $lineSubtotal === null && $lineTax === null
                ? $this->nullableNumber($item['total'] ?? null)
                : $this->money(($lineSubtotal ?? 0) + ($lineTax ?? 0));

            $normalizedItems[] = [
                'item_name' => $name,
                'item_description' => trim((string) ($item['item_description'] ?? '')) ?: null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate' => $taxRate,
                'subtotal' => $lineSubtotal,
                'tax_total' => $lineTax,
                'total' => $lineTotal,
            ];
            $subtotal += $lineSubtotal ?? 0;
            $taxTotal += $lineTax ?? 0;
        }

        return [
            'items' => $normalizedItems,
            'subtotal' => $this->money($subtotal),
            'tax_total' => $this->money($taxTotal),
            'total' => $this->money($subtotal + $taxTotal),
        ];
    }

    private function nullableNumber(mixed $value): ?float
    {
        if ($value === null || $value === '' || ! is_numeric(str_replace(',', '.', (string) $value))) {
            return null;
        }

        return (float) str_replace(',', '.', (string) $value);
    }

    private function number(mixed $value, float $default): float
    {
        return $this->nullableNumber($value) ?? $default;
    }

    private function money(float $value): float
    {
        return round($value, 2);
    }
}
