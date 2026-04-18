<?php

declare(strict_types=1);

namespace types;

class QrCodeSwiss extends AbstractType
{
    public function generate($generalOptions, $invoiceOptions): void
    {
        // TODO: Implement generate() method.
    }

    public function getGeneralOptions(): array
    {
        return [
            'currency' => [
                'type' => 'text',
                'label' => trans('payment_method_type_qr_code_swiss_currency'),
                'default' => 'CHF',
                'required' => true,
            ],
            'besrid' => [
                'type' => 'text',
                'label' => trans('payment_method_type_qr_code_swiss_besrid'),
                'required' => false,
            ],
        ];
    }

    public function getInvoiceOptions(): array
    {
        return [];
    }

    public function validateGeneralOptions($generalOptions): bool
    {
        // TODO: Implement getInvoiceOptions() method.
        return true;
    }

    public function validateInvoiceOptions($invoiceOptions): bool
    {
        // TODO: Implement validateInvoiceOptions() method.
        return true;
    }
}
