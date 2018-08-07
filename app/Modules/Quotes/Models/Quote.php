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

namespace IP\Modules\Quotes\Models;

use Carbon\Carbon;
use IP\Events\QuoteCreated;
use IP\Events\QuoteCreating;
use IP\Events\QuoteDeleted;
use IP\Support\CurrencyFormatter;
use IP\Support\DateFormatter;
use IP\Support\FileNames;
use IP\Support\HTML;
use IP\Support\NumberFormatter;
use IP\Support\Statuses\QuoteStatuses;
use IP\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quote extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = [
        'number' => ['LENGTH(number)', 'number'],
        'quote_date',
        'expires_at',
        'clients.name',
        'summary',
        'quote_amounts.total',
        'quote_amounts.tax',
        'quote_amounts.subtotal',
    ];

    protected $dates = ['expires_at', 'quote_date'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            event(new QuoteCreating($quote));
        });

        static::created(function ($quote) {
            event(new QuoteCreated($quote));
        });

        static::deleted(function ($quote) {
            event(new QuoteDeleted($quote));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function activities()
    {
        return $this->morphMany('IP\Modules\Activity\Models\Activity', 'audit');
    }

    public function amount()
    {
        return $this->hasOne('IP\Modules\Quotes\Models\QuoteAmount');
    }

    public function attachments()
    {
        return $this->morphMany('IP\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function client()
    {
        return $this->belongsTo('IP\Modules\Clients\Models\Client');
    }

    public function clientAttachments()
    {
        $relationship = $this->morphMany('IP\Modules\Attachments\Models\Attachment', 'attachable');

        $relationship->where('client_visibility', 1);

        return $relationship;
    }

    public function companyProfile()
    {
        return $this->belongsTo('IP\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function currency()
    {
        return $this->belongsTo('IP\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('IP\Modules\CustomFields\Models\QuoteCustom');
    }

    public function group()
    {
        return $this->hasOne('IP\Modules\Groups\Models\Group');
    }

    public function invoice()
    {
        return $this->belongsTo('IP\Modules\Invoices\Models\Invoice');
    }

    public function mailQueue()
    {
        return $this->morphMany('IP\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function items()
    {
        return $this->hasMany('IP\Modules\Quotes\Models\QuoteItem')
            ->orderBy('display_order');
    }

    public function notes()
    {
        return $this->morphMany('IP\Modules\Notes\Models\Note', 'notable');
    }

    // This and items() are the exact same. This is added to appease the IDE gods
    // and the fact that Laravel has a protected items property.
    public function quoteItems()
    {
        return $this->hasMany('IP\Modules\Quotes\Models\QuoteItem')
            ->orderBy('display_order');
    }

    public function user()
    {
        return $this->belongsTo('IP\Modules\Users\Models\User');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('quotes/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return ['0' => trans('ip.not_visible'), '1' => trans('ip.visible')];
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->formatted_quote_date;
    }

    public function getFormattedQuoteDateAttribute()
    {
        return DateFormatter::format($this->attributes['quote_date']);
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['updated_at']);
    }

    public function getFormattedExpiresAtAttribute()
    {
        return DateFormatter::format($this->attributes['expires_at']);
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
        $statuses = QuoteStatuses::statuses();

        return $statuses[$this->attributes['quote_status_id']];
    }

    public function getPdfFilenameAttribute()
    {
        return FileNames::quote($this);
    }

    public function getPublicUrlAttribute()
    {
        return route('clientCenter.public.quote.show', [$this->url_key]);
    }

    public function getIsForeignCurrencyAttribute()
    {
        if ($this->attributes['currency_code'] == config('ip.baseCurrency')) {
            return false;
        }

        return true;
    }

    public function getHtmlAttribute()
    {
        return HTML::quote($this);
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount']);
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

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId) {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('draft'));
    }

    public function scopeSent($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('sent'));
    }

    public function scopeApproved($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('approved'));
    }

    public function scopeRejected($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('rejected'));
    }

    public function scopeCanceled($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('canceled'));
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
            case 'approved':
                $query->approved();
                break;
            case 'rejected':
                $query->rejected();
                break;
            case 'canceled':
                $query->canceled();
                break;
        }

        return $query;
    }

    public function scopeYearToDate($query)
    {
        return $query->where('quote_date', '>=', date('Y') . '-01-01')
            ->where('quote_date', '<=', date('Y') . '-12-31');
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('quote_date', '>=', Carbon::now()->firstOfQuarter())
            ->where('quote_date', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->where('quote_date', '>=', $fromDate)
            ->where('quote_date', '<=', $toDate);
    }

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where(DB::raw('lower(number)'), 'like', '%' . $keywords . '%')
                ->orWhere('quotes.quote_date', 'like', '%' . $keywords . '%')
                ->orWhere('expires_at', 'like', '%' . $keywords . '%')
                ->orWhere('summary', 'like', '%' . $keywords . '%')
                ->orWhereIn('client_id', function ($query) use ($keywords) {
                    $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name))"), 'like', '%' . $keywords . '%');
                });
        }

        return $query;
    }
}