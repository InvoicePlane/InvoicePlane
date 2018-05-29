<?php

namespace FI\Events\Listeners;

use FI\Events\InvoiceDeleted;
use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Quotes\Models\Quote;

class InvoiceDeletedListener
{
    public function __construct()
    {
        //
    }

    public function handle(InvoiceDeleted $event)
    {
        foreach ($event->invoice->items as $item) {
            $item->delete();
        }

        foreach ($event->invoice->payments as $payment) {
            $payment->delete();
        }

        foreach ($event->invoice->activities as $activity) {
            $activity->delete();
        }

        foreach ($event->invoice->mailQueue as $mailQueue) {
            $mailQueue->delete();
        }

        foreach ($event->invoice->notes as $note) {
            $note->delete();
        }

        $event->invoice->custom()->delete();
        $event->invoice->amount()->delete();

        Quote::where('invoice_id', $event->invoice->id)->update(['invoice_id' => 0]);

        Expense::where('invoice_id', $event->invoice->id)->update(['invoice_id' => 0]);

        $group = Group::where('id', $event->invoice->group_id)
            ->where('last_number', $event->invoice->number)
            ->first();

        if ($group) {
            $group->next_id = $group->next_id - 1;
            $group->save();
        }
    }
}
