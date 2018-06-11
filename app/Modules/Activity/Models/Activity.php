<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Modules\Activity\Models;

use FI\Support\DateFormatter;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $guarded = ['id'];

    public function audit()
    {
        return $this->morphTo();
    }

    public function getFormattedActivityAttribute()
    {
        if ($this->audit) {
            switch ($this->audit_type) {
                case 'FI\Modules\Quotes\Models\Quote':

                    switch ($this->activity) {
                        case 'public.viewed':
                            return trans('ip.activity_quote_viewed', ['number' => $this->audit->number, 'link' => route('quotes.edit', [$this->audit->id])]);
                            break;

                        case 'public.approved':
                            return trans('ip.activity_quote_approved', ['number' => $this->audit->number, 'link' => route('quotes.edit', [$this->audit->id])]);
                            break;

                        case 'public.rejected':
                            return trans('ip.activity_quote_rejected', ['number' => $this->audit->number, 'link' => route('quotes.edit', [$this->audit->id])]);
                            break;
                    }

                    break;

                case 'FI\Modules\Invoices\Models\Invoice':

                    switch ($this->activity) {
                        case 'public.viewed':
                            return trans('ip.activity_invoice_viewed', ['number' => $this->audit->number, 'link' => route('invoices.edit', [$this->audit->id])]);
                            break;
                        case 'public.paid':
                            return trans('ip.activity_invoice_paid', ['number' => $this->audit->number, 'link' => route('invoices.edit', [$this->audit->id])]);
                            break;
                    }

                    break;
            }
        }

        return '';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->created_at, true);
    }
}