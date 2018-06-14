<?php

namespace IP\Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantClient extends Model
{
    protected $table = 'merchant_clients';

    protected $guarded = ['id'];

    public static function getByKey($driver, $clientId, $key)
    {
        $setting = self::where('driver', $driver)
            ->where('client_id', $clientId)
            ->where('merchant_key', $key)
            ->first();

        if ($setting) {
            return $setting->merchant_value;
        }

        return null;
    }

    public static function saveByKey($driver, $clientId, $key, $value)
    {
        $setting = self::firstOrNew([
            'driver' => $driver,
            'client_id' => $clientId,
            'merchant_key' => $key,

        ]);

        $setting->merchant_value = $value;

        $setting->save();
    }
}