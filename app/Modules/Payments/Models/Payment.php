<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Payments\Models;

use Carbon\Carbon;
use FI\Events\InvoiceModified;
use FI\Events\PaymentCreated;
use FI\Events\PaymentCreating;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\FileNames;
use FI\Support\HTML;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use Sortable;

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    protected $sortable = ['paid_at', 'invoices.invoice_date', 'invoices.number', 'invoices.summary', 'clients.name', 'amount', 'payment_methods.name', 'note'];

    protected $dates = ['paid_at'];

    public static function boot()
    {
        static::created(function ($payment)
        {
            event(new InvoiceModified($payment->invoice));
            event(new PaymentCreated($payment));
        });

        static::creating(function ($payment)
        {
            event(new PaymentCreating($payment));
        });

        static::updated(function($payment)
        {
            event(new InvoiceModified($payment->invoice));
        });

        static::deleting(function ($payment)
        {
            foreach ($payment->mailQueue as $mailQueue)
            {
                $mailQueue->delete();
            }

            $payment->custom()->delete();
        });

        static::deleted(function ($payment)
        {
            if ($payment->invoice)
            {
                event(new InvoiceModified($payment->invoice));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\PaymentCustom');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    public function mailQueue()
    {
        return $this->morphMany('FI\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('FI\Modules\PaymentMethods\Models\PaymentMethod');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPaidAtAttribute()
    {
        return DateFormatter::format($this->attributes['paid_at']);
    }

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->attributes['amount'], $this->invoice->currency);
    }

    public function getFormattedNumericAmountAttribute()
    {
        return NumberFormatter::format($this->attributes['amount']);
    }

    public function getFormattedNoteAttribute()
    {
        return nl2br($this->attributes['note']);
    }

    public function getUserAttribute()
    {
        return $this->invoice->user;
    }

    public function getHtmlAttribute()
    {
        return HTML::invoice($this->invoice);
    }

    public function getPdfFilenameAttribute()
    {
        return FileNames::invoice($this->invoice);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeYearToDate($query)
    {
        return $query->where('paid_at', '>=', date('Y') . '-01-01')
            ->where('paid_at', '<=', date('Y') . '-12-31');
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->firstOfQuarter())
            ->where('paid_at', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->where('paid_at', '>=', $from)->where('paid_at', '<=', $to);
    }

    public function scopeYear($query, $year)
    {
        return $query->where('paid_at', '>=', $year . '-01-01')
            ->where('paid_at', '<=', $year . '-12-31');
    }

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('payments.created_at', 'like', '%' . $keywords . '%')
                ->orWhereIn('invoice_id', function ($query) use ($keywords)
                {
                    $query->select('id')->from('invoices')->where(DB::raw('lower(number)'), 'like', '%' . $keywords . '%')
                        ->orWhere('summary', 'like', '%' . $keywords . '%')
                        ->orWhereIn('client_id', function ($query) use ($keywords)
                        {
                            $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name))"), 'like', '%' . $keywords . '%');
                        });
                })
                ->orWhereIn('payment_method_id', function ($query) use ($keywords)
                {
                    $query->select('id')->from('payment_methods')->where(DB::raw('lower(name)'), 'like', '%' . $keywords . '%');
                });
        }

        return $query;
    }

    public function scopeClientId($query, $clientId)
    {
        if ($clientId)
        {
            $query->whereHas('invoice', function ($query) use ($clientId)
            {
                $query->where('client_id', $clientId);
            });
        }

        return $query;
    }

    public function scopeInvoiceId($query, $invoiceId)
    {
        if ($invoiceId)
        {
            $query->whereHas('invoice', function ($query) use ($invoiceId)
            {
                $query->where('id', $invoiceId);
            });
        }

        return $query;
    }

    public function scopeInvoiceNumber($query, $invoiceNumber)
    {
        if ($invoiceNumber)
        {
            $query->whereHas('invoice', function ($query) use ($invoiceNumber)
            {
                $query->where('number', $invoiceNumber);
            });
        }

        return $query;
    }
}