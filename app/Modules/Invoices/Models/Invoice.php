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

namespace FI\Modules\Invoices\Models;

use Carbon\Carbon;
use FI\Events\InvoiceCreated;
use FI\Events\InvoiceCreating;
use FI\Events\InvoiceDeleted;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\FileNames;
use FI\Support\HTML;
use FI\Support\NumberFormatter;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = [
        'number' => ['LENGTH(number)', 'number'],
        'invoice_date',
        'due_at',
        'clients.name',
        'summary',
        'invoice_amounts.total',
        'invoice_amounts.balance',
        'invoice_amounts.tax',
        'invoice_amounts.subtotal',
    ];

    protected $dates = ['due_at', 'invoice_date'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            event(new InvoiceCreating($invoice));
        });

        static::created(function ($invoice) {
            event(new InvoiceCreated($invoice));
        });

        static::deleted(function ($invoice) {
            event(new InvoiceDeleted($invoice));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function activities()
    {
        return $this->morphMany('FI\Modules\Activity\Models\Activity', 'audit');
    }

    public function amount()
    {
        return $this->hasOne('FI\Modules\Invoices\Models\InvoiceAmount');
    }

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function clientAttachments()
    {
        $relationship = $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');

        if ($this->status_text == 'paid') {
            $relationship->whereIn('client_visibility', [1, 2]);
        } else {
            $relationship->where('client_visibility', 1);
        }

        return $relationship;
    }

    public function companyProfile()
    {
        return $this->belongsTo('FI\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function currency()
    {
        return $this->belongsTo('FI\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\InvoiceCustom');
    }

    public function group()
    {
        return $this->belongsTo('FI\Modules\Groups\Models\Group');
    }

    public function items()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceItem')
            ->orderBy('display_order');
    }

    // This and items() are the exact same. This is added to appease the IDE gods
    // and the fact that Laravel has a protected items property.
    public function invoiceItems()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceItem')
            ->orderBy('display_order');
    }

    public function mailQueue()
    {
        return $this->morphMany('FI\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function payments()
    {
        return $this->hasMany('FI\Modules\Payments\Models\Payment');
    }

    public function quote()
    {
        return $this->hasOne('FI\Modules\Quotes\Models\Quote');
    }

    public function transactions()
    {
        return $this->hasMany('FI\Modules\Merchant\Models\InvoiceTransaction');
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('invoices/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return [
            '0' => trans('fi.not_visible'),
            '1' => trans('fi.visible'),
            '2' => trans('fi.visible_after_payment'),
        ];
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->formatted_invoice_date;
    }

    public function getFormattedInvoiceDateAttribute()
    {
        return DateFormatter::format($this->attributes['invoice_date']);
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['updated_at']);
    }

    public function getFormattedDueAtAttribute()
    {
        return DateFormatter::format($this->attributes['due_at']);
    }

    public function getFormattedTermsAttribute()
    {
        return nl2br($this->attributes['terms']);
    }

    public function getFormattedFooterAttribute()
    {
        return nl2br($this->attributes['footer']);
    }

    public function getStatusTextAttribute()
    {
        $statuses = InvoiceStatuses::statuses();

        return $statuses[$this->attributes['invoice_status_id']];
    }

    public function getIsOverdueAttribute()
    {
        // Only invoices in Sent status qualify to be overdue
        if ($this->attributes['due_at'] < date('Y-m-d') and $this->attributes['invoice_status_id'] == InvoiceStatuses::getStatusId('sent'))
            return 1;

        return 0;
    }

    public function getPublicUrlAttribute()
    {
        return route('clientCenter.public.invoice.show', [$this->url_key]);
    }

    public function getIsForeignCurrencyAttribute()
    {
        if ($this->attributes['currency_code'] == config('fi.baseCurrency')) {
            return false;
        }

        return true;
    }

    public function getHtmlAttribute()
    {
        return HTML::invoice($this);
    }

    public function getPdfFilenameAttribute()
    {
        return FileNames::invoice($this);
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount']);
    }

    public function getIsPayableAttribute()
    {
        return $this->status_text <> 'canceled' and $this->amount->balance > 0;
    }

    /**
     * Gathers a summary of both invoice and item taxes to be displayed on invoice.
     *
     * @return array
     */
    public function getSummarizedTaxesAttribute()
    {
        $taxes = [];

        foreach ($this->items as $item) {
            if ($item->taxRate) {
                $key = $item->taxRate->name;

                if (!isset($taxes[$key])) {
                    $taxes[$key] = new \stdClass();
                    $taxes[$key]->name = $item->taxRate->name;
                    $taxes[$key]->percent = $item->taxRate->formatted_percent;
                    $taxes[$key]->total = $item->amount->tax_1;
                    $taxes[$key]->raw_percent = $item->taxRate->percent;
                } else {
                    $taxes[$key]->total += $item->amount->tax_1;
                }
            }

            if ($item->taxRate2) {
                $key = $item->taxRate2->name;

                if (!isset($taxes[$key])) {
                    $taxes[$key] = new \stdClass();
                    $taxes[$key]->name = $item->taxRate2->name;
                    $taxes[$key]->percent = $item->taxRate2->formatted_percent;
                    $taxes[$key]->total = $item->amount->tax_2;
                    $taxes[$key]->raw_percent = $item->taxRate2->percent;
                } else {
                    $taxes[$key]->total += $item->amount->tax_2;
                }
            }
        }

        foreach ($taxes as $key => $tax) {
            $taxes[$key]->total = CurrencyFormatter::format($tax->total, $this->currency);
        }

        return $taxes;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeClientId($query, $clientId = null)
    {
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        return $query->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('draft'));
    }

    public function scopeSent($query)
    {
        return $query->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('sent'));
    }

    public function scopePaid($query)
    {
        return $query->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('paid'));
    }

    public function scopeCanceled($query)
    {
        return $query->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('canceled'));
    }

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId) {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeNotCanceled($query)
    {
        return $query->where('invoice_status_id', '<>', InvoiceStatuses::getStatusId('canceled'));
    }

    public function scopeStatusIn($query, $statuses)
    {
        $statusCodes = [];

        foreach ($statuses as $status) {
            $statusCodes[] = InvoiceStatuses::getStatusId($status);
        }

        return $query->whereIn('invoice_status_id', $statusCodes);
    }

    public function scopeStatus($query, $status = null)
    {
        switch ($status) {
            case 'draft':
                $query->draft();
                break;
            case 'sent':
                $query->sent();
                break;
            case 'viewed':
                $query->viewed();
                break;
            case 'paid':
                $query->paid();
                break;
            case 'canceled':
                $query->canceled();
                break;
            case 'overdue':
                $query->overdue();
                break;
        }

        return $query;
    }

    public function scopeOverdue($query)
    {
        // Only invoices in Sent status qualify to be overdue
        return $query
            ->where('invoice_status_id', '=', InvoiceStatuses::getStatusId('sent'))
            ->where('due_at', '<', date('Y-m-d'));
    }

    public function scopeYearToDate($query)
    {
        return $query->where('invoice_date', '>=', date('Y') . '-01-01')
            ->where('invoice_date', '<=', date('Y') . '-12-31');
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('invoice_date', '>=', Carbon::now()->firstOfQuarter())
            ->where('invoice_date', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->where('invoice_date', '>=', $fromDate)
            ->where('invoice_date', '<=', $toDate);
    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where(DB::raw('lower(number)'), 'like', '%' . $keywords . '%')
                ->orWhere('invoices.invoice_date', 'like', '%' . $keywords . '%')
                ->orWhere('due_at', 'like', '%' . $keywords . '%')
                ->orWhere('summary', 'like', '%' . $keywords . '%')
                ->orWhereIn('client_id', function ($query) use ($keywords) {
                    $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name))"), 'like', '%' . $keywords . '%');
                });
        }

        return $query;
    }
}