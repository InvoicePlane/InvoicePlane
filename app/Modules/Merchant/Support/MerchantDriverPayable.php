<?php

namespace IP\Modules\Merchant\Support;

use IP\Modules\Invoices\Models\Invoice;

abstract class MerchantDriverPayable extends MerchantDriver
{
    abstract public function pay(Invoice $invoice);
}