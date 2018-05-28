<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Settings\Models;

use FI\Events\SettingSaving;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($setting)
        {
            event(new SettingSaving($setting));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function deleteByKey($key)
    {
        self::where('setting_key', $key)->delete();
    }

    public static function getByKey($key)
    {
        $setting = self::where('setting_key', $key)->first();

        if ($setting)
        {
            return $setting->setting_value;
        }

        return null;
    }

    public static function saveByKey($key, $value)
    {
        $setting = self::firstOrNew(['setting_key' => $key]);

        $setting->setting_value = $value;

        $setting->save();

        config(['fi.' . $key => $value]);
    }

    public static function setAll()
    {
        try
        {
            $settings = self::all();

            foreach ($settings as $setting)
            {
                config(['fi.' . $setting->setting_key => $setting->setting_value]);
            }

            return true;
        }
        catch (QueryException $e)
        {
            return false;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    public static function writeEmailTemplates()
    {
        $emailTemplates = [
            'invoiceEmailBody',
            'quoteEmailBody',
            'overdueInvoiceEmailBody',
            'upcomingPaymentNoticeEmailBody',
            'quoteApprovedEmailBody',
            'quoteRejectedEmailBody',
            'paymentReceiptBody',
            'quoteEmailSubject',
            'invoiceEmailSubject',
            'overdueInvoiceEmailSubject',
            'upcomingPaymentNoticeEmailSubject',
            'paymentReceiptEmailSubject',
        ];

        foreach ($emailTemplates as $template)
        {
            $templateContents = self::getByKey($template);
            $templateContents = str_replace('{{', '{!!', $templateContents);
            $templateContents = str_replace('}}', '!!}', $templateContents);

            Storage::put('email_templates/' . $template . '.blade.php', $templateContents);
        }
    }
}