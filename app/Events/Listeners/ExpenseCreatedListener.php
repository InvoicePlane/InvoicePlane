<?php

namespace IP\Events\Listeners;

use IP\Events\ExpenseCreated;
use IP\Modules\CustomFields\Models\ExpenseCustom;

class ExpenseCreatedListener
{
    public function __construct()
    {
        //
    }

    public function handle(ExpenseCreated $event)
    {
        $event->expense->custom()->save(new ExpenseCustom());
    }
}