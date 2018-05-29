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

namespace FI\Modules\Clients\Models;

use FI\Events\ClientCreated;
use FI\Events\ClientCreating;
use FI\Events\ClientDeleted;
use FI\Events\ClientSaving;
use FI\Support\CurrencyFormatter;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use Sortable;

    protected $guarded = ['id', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $sortable = ['unique_name', 'email', 'phone', 'balance', 'active', 'custom'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            event(new ClientCreating($client));
        });

        static::created(function ($client) {
            event(new ClientCreated($client));
        });

        static::saving(function ($client) {
            event(new ClientSaving($client));
        });

        static::deleted(function ($client) {
            event(new ClientDeleted($client));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function firstOrCreateByUniqueName($uniqueName)
    {
        $client = self::firstOrNew([
            'unique_name' => $uniqueName,
        ]);

        if (!$client->id) {
            $client->name = $uniqueName;
            $client->save();
            return self::find($client->id);
        }

        return $client;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function contacts()
    {
        return $this->hasMany('FI\Modules\Clients\Models\Contact');
    }

    public function currency()
    {
        return $this->belongsTo('FI\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\ClientCustom');
    }

    public function expenses()
    {
        return $this->hasMany('FI\Modules\Expenses\Models\Expense');
    }

    public function invoices()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\Invoice');
    }

    public function merchant()
    {
        return $this->hasOne('FI\Modules\Merchant\Models\MerchantClient');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function quotes()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\Quote');
    }

    public function recurringInvoices()
    {
        return $this->hasMany('FI\Modules\RecurringInvoices\Models\RecurringInvoice');
    }

    public function user()
    {
        return $this->hasOne('FI\Modules\Users\Models\User');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('clients/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return ['0' => trans('fi.not_visible')];
    }

    public function getFormattedBalanceAttribute()
    {
        return CurrencyFormatter::format($this->balance, $this->currency);
    }

    public function getFormattedPaidAttribute()
    {
        return CurrencyFormatter::format($this->paid, $this->currency);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyFormatter::format($this->total, $this->currency);
    }

    public function getFormattedAddressAttribute()
    {
        return nl2br(formatAddress($this));
    }

    public function getClientEmailAttribute()
    {
        return $this->email;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeGetSelect()
    {
        return self::select('clients.*',
            DB::raw('(' . $this->getBalanceSql() . ') as balance'),
            DB::raw('(' . $this->getPaidSql() . ') AS paid'),
            DB::raw('(' . $this->getTotalSql() . ') AS total')
        );
    }

    private function getBalanceSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(balance)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')
                ->from('invoices')
                ->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'))
                ->where('invoices.invoice_status_id', '<>', DB::raw(InvoiceStatuses::getStatusId('canceled')));
        })->toSql();
    }

    private function getPaidSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(paid)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')->from('invoices')->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'));
        })->toSql();
    }

    /*
    |--------------------------------------------------------------------------
    | Subqueries
    |--------------------------------------------------------------------------
    */

    private function getTotalSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(total)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')->from('invoices')->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'));
        })->toSql();
    }

    public function scopeStatus($query, $status)
    {
        if ($status == 'active') {
            $query->where('active', 1);
        } elseif ($status == 'inactive') {
            $query->where('active', 0);
        }

        return $query;
    }

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords) {
            $keywords = explode(' ', $keywords);

            foreach ($keywords as $keyword) {
                if ($keyword) {
                    $keyword = strtolower($keyword);

                    $query->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name),LOWER(email),phone,fax,mobile)"), 'LIKE', "%$keyword%");
                }
            }
        }

        return $query;
    }
}