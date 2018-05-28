<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        return ($this->attributes['sent']) ? trans('fi.yes') : trans('fi.no');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
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