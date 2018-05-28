<?php

namespace FI\Events\Listeners;

use FI\Events\ExpenseCreated;
use FI\Modules\CustomFields\Models\ExpenseCustom;

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