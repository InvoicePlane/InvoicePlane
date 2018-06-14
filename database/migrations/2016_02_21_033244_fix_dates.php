<?php

use Carbon\Carbon;
use IP\Modules\Invoices\Models\Invoice;
use IP\Modules\Payments\Models\Payment;
use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class FixDates extends Migration
{
    public function up()
    {
        $invoicesDueAfter = Setting::getByKey('invoicesDueAfter');

        if (is_numeric($invoicesDueAfter)) {
            $invoices = Invoice::where('due_at', '0000-00-00')->get();

            foreach ($invoices as $invoice) {
                $invoice->due_at = Carbon::createFromFormat('Y-m-d H:i:s', $invoice->created_at)->addDays($invoicesDueAfter);

                $invoice->save();
            }
        }

        Payment::where('paid_at', '0000-00-00')->update(['paid_at' => DB::raw('created_at')]);
    }

    public function down()
    {
        //
    }
}
