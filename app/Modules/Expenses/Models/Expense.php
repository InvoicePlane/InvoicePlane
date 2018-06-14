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

namespace IP\Modules\Expenses\Models;

use IP\Events\CheckAttachment;
use IP\Events\ExpenseCreated;
use IP\Events\ExpenseDeleting;
use IP\Events\ExpenseSaving;
use IP\Support\CurrencyFormatter;
use IP\Support\DateFormatter;
use IP\Support\NumberFormatter;
use IP\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use Sortable;

    protected $table = 'expenses';

    protected $guarded = ['id'];

    protected $sortable = ['expense_date', 'expense_categories.name', 'description', 'amount'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($expense) {
            event(new ExpenseCreated($expense));
        });

        static::saved(function ($expense) {
            event(new CheckAttachment($expense));
        });

        static::saving(function ($expense) {
            event(new ExpenseSaving($expense));
        });

        static::deleting(function ($expense) {
            event(new ExpenseDeleting($expense));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachments()
    {
        return $this->morphMany('IP\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function category()
    {
        return $this->belongsTo('IP\Modules\Expenses\Models\ExpenseCategory');
    }

    public function client()
    {
        return $this->belongsTo('IP\Modules\Clients\Models\Client');
    }

    public function companyProfile()
    {
        return $this->belongsTo('IP\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function custom()
    {
        return $this->hasOne('IP\Modules\CustomFields\Models\ExpenseCustom');
    }

    public function invoice()
    {
        return $this->belongsTo('IP\Modules\Invoices\Models\Invoice');
    }

    public function vendor()
    {
        return $this->belongsTo('IP\Modules\Expenses\Models\ExpenseVendor');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('expenses/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return [
            '0' => trans('ip.not_visible'),
            '1' => trans('ip.visible'),
        ];
    }

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->amount);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->tax);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->description);
    }

    public function getFormattedExpenseDateAttribute()
    {
        return DateFormatter::format($this->expense_date);
    }

    public function getFormattedNumericAmountAttribute()
    {
        return NumberFormatter::format($this->amount);
    }

    public function getFormattedNumericTaxAttribute()
    {
        return NumberFormatter::format($this->tax);
    }

    public function getHasBeenBilledAttribute()
    {
        if ($this->invoice_id) {
            return true;
        }

        return false;
    }

    public function getIsBillableAttribute()
    {
        if ($this->client_id) {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCategoryId($query, $categoryId = null)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId = null)
    {
        if ($companyProfileId) {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeDefaultQuery($query)
    {
        return $query->select('expenses.*', 'expense_categories.name AS category_name',
            'expense_vendors.name AS vendor_name', 'clients.unique_name AS client_name')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('expense_vendors', 'expense_vendors.id', '=', 'expenses.vendor_id')
            ->leftJoin('clients', 'clients.id', '=', 'expenses.client_id');
    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where('expenses.expense_date', 'like', '%' . $keywords . '%')
                ->orWhere('expenses.description', 'like', '%' . $keywords . '%')
                ->orWhere('expense_vendors.name', 'like', '%' . $keywords . '%')
                ->orWhere('clients.name', 'like', '%' . $keywords . '%')
                ->orWhere('expense_categories.name', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    public function scopeStatus($query, $status = null)
    {
        if ($status) {
            switch ($status) {
                case 'billed':
                    $query->where('invoice_id', '<>', 0);
                    break;
                case 'not_billed':
                    $query->where('client_id', '<>', 0)->where('invoice_id', '=', 0);
                    break;
                case 'not_billable':
                    $query->where('client_id', 0);
                    break;
            }
        }

        return $query;
    }

    public function scopeVendorId($query, $vendorId = null)
    {
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return $query;
    }
}