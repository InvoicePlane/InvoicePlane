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

namespace FI\Modules\MailQueue\Models;

use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    use Sortable;

    protected $table = 'mail_queue';

    protected $sortable = ['created_at', 'from', 'to', 'cc', 'bcc', 'subject', 'sent'];

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function mailable()
    {
        return $this->morphTo();
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at'], true);
    }

    public function getFormattedFromAttribute()
    {
        $from = json_decode($this->attributes['from']);

        return $from->email;
    }

    public function getFormattedToAttribute()
    {
        return implode(', ', json_decode($this->attributes['to']));
    }

    public function getFormattedCcAttribute()
    {
        return implode(', ', json_decode($this->attributes['cc']));
    }

    public function getFormattedBccAttribute()
    {
        return implode(', ', json_decode($this->attributes['bcc']));
    }

    public function getFormattedSentAttribute()
    {
        return ($this->attributes['sent']) ? trans('ip.yes') : trans('ip.no');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where('created_at', 'like', '%' . $keywords . '%')
                ->orWhere('from', 'like', '%' . $keywords . '%')
                ->orWhere('to', 'like', '%' . $keywords . '%')
                ->orWhere('cc', 'like', '%' . $keywords . '%')
                ->orWhere('bcc', 'like', '%' . $keywords . '%')
                ->orWhere('subject', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

}