<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Models;

use FI\Events\RecurringInvoiceCreated;
use FI\Events\RecurringInvoiceCreating;
use FI\Events\RecurringInvoiceDeleted;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecurringInvoice extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = ['id', 'clients.name', 'summary', 'next_date', 'stop_date', 'recurring_invoice_amounts.total'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($recurringInvoice)
        {
            event(new RecurringInvoiceCreating($recurringInvoice));
        });

        static::created(function ($recurringInvoice)
        {
            event(new RecurringInvoiceCreated($recurringInvoice));
        });

        static::deleted(function ($recurringInvoice)
        {
            event(new RecurringInvoiceDeleted($recurringInvoice));
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
        return $this->hasOne('FI\Modules\RecurringInvoices\Models\RecurringInvoiceAmount');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
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
        return $this->hasOne('FI\Modules\CustomFields\Models\RecurringInvoiceCustom');
    }

    public function group()
    {
        return $this->belongsTo('FI\Modules\Groups\Models\Group');
    }

    public function items()
    {
        return $this->hasMany('FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem')
            ->orderBy('display_order');
    }

    // This and items() are the exact same. This is added to appease the IDE gods
    // and the fact that Laravel has a protected items property.
    public function recurringInvoiceItems()
    {
        return $this->hasMany('FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem')
            ->orderBy('display_order');
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

    public function getFormattedFooterAttribute()
    {
        return nl2br($this->attributes['footer']);
    }

    public function getFormattedNextDateAttribute()
    {
        if ($this->attributes['next_date'] <> '0000-00-00')
        {
            return DateFormatter::format($this->attributes['next_date']);
        }

        return '';
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount']);
    }

    public function getFormattedStopDateAttribute()
    {
        if ($this->attributes['stop_date'] <> '0000-00-00')
        {
            return DateFormatter::format($this->attributes['stop_date']);
        }

        return '';
    }

    public function getFormattedTermsAttribute()
    {
        return nl2br($this->attributes['terms']);
    }

    public function getIsForeignCurrencyAttribute()
    {
        if ($this->attributes['currency_code'] == config('fi.baseCurrency'))
        {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('stop_date', '0000-00-00')
            ->orWhere('stop_date', '>', date('Y-m-d'));
    }

    public function scopeClientId($query, $clientId = null)
    {
        if ($clientId)
        {
            $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId = null)
    {
        if ($companyProfileId)
        {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeInactive($query)
    {
        return $query->where('stop_date', '<>', '0000-00-00')
            ->where('stop_date', '<=', date('Y-m-d'));
    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('summary', 'like', '%' . $keywords . '%')
                ->orWhereIn('client_id', function ($query) use ($keywords)
                {
                    $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name))"), 'like', '%' . $keywords . '%');
                });
        }

        return $query;
    }

    public function scopeRecurNow($query)
    {
        $query->where('next_date', '<>', '0000-00-00');
        $query->where('next_date', '<=', date('Y-m-d'));
        $query->where(function ($q)
        {
            $q->where('stop_date', '0000-00-00');
            $q->orWhere('next_date', '<=', DB::raw('stop_date'));
        });

        return $query;
    }

    public function scopeStatus($query, $status)
    {
        switch ($status)
        {
            case 'active':
                return $query->active();
            case 'inactive':
                return $query->inactive();
        }

        return $query;
    }
}