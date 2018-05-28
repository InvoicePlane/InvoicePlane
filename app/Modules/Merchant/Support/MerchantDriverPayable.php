<?php

namespace FI\Modules\Merchant\Support;

use FI\Modules\Invoices\Models\Invoice;

abstract class MerchantDriverPayable extends MerchantDriver
{
    abstract public function pay(Invoice $invoice);
}