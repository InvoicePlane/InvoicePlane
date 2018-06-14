<?php

namespace IP\Modules\Merchant\Support;

use IP\Modules\Invoices\Models\Invoice;

abstract class MerchantDriver
{
    protected $isRedirect;

    public function isRedirect()
    {
        return $this->isRedirect;
    }

    public function getName()
    {
        return class_basename($this);
    }

    public function getSetting($setting)
    {
        return config('fi.' . $this->getSettingKey($setting));
    }

    public function getSettingKey($setting)
    {
        return 'merchant_' . class_basename($this) . '_' . $setting;
    }

    public abstract function verify(Invoice $invoice);

    public abstract function getSettings();
}