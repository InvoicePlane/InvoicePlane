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

namespace IP\Modules\Users\Models;

use IP\Events\UserCreated;
use IP\Events\UserDeleted;
use IP\Traits\Sortable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Sortable;

    protected $table = 'users';

    protected $guarded = ['id', 'password', 'password_confirmation'];

    protected $hidden = ['password', 'remember_token', 'api_public_key', 'api_secret_key'];

    protected $sortable = ['name', 'email'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            event(new UserCreated($user));
        });

        static::deleted(function ($user) {
            event(new UserDeleted($user));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo('IP\Modules\Clients\Models\Client');
    }

    public function custom()
    {
        return $this->hasOne('IP\Modules\CustomFields\Models\UserCustom');
    }

    public function expenses()
    {
        return $this->hasMany('IP\Modules\Expenses\Models\Expense');
    }

    public function invoices()
    {
        return $this->hasMany('IP\Modules\Invoices\Models\Invoice');
    }

    public function quotes()
    {
        return $this->hasMany('IP\Modules\Quotes\Models\Quote');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getUserTypeAttribute()
    {
        return ($this->client_id) ? 'client' : 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUserType($query, $userType)
    {
        if ($userType == 'client') {
            $query->where('client_id', '<>', 0);
        } elseif ($userType == 'admin') {
            $query->where('client_id', 0);
        }

        return $query;
    }
}