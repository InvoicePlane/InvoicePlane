<?php

namespace FI\Events\Listeners;

use FI\Events\ExpenseSaving;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Models\ExpenseVendor;

class ExpenseSavingListener
{
    public function __construct()
    {
        //
    }

    public function handle(ExpenseSaving $event)
    {
        $expense = $event->expense;

        if (!$expense->id)
        {
            $expense->user_id = auth()->user()->id;
        }

        if ($expense->category_name)
        {
            $expense->category_id = ExpenseCategory::firstOrCreate(['name' => $expense->category_name])->id;
        }

        if (isset($expense->vendor_name) and $expense->vendor_name)
        {
            $expense->vendor_id = ExpenseVendor::firstOrCreate(['name' => $expense->vendor_name])->id;
        }
        elseif (isset($expense->vendor_name))
        {
            $expense->vendor_id = 0;
        }

        if ($expense->company_profile)
        {
            if (!CompanyProfile::where('company', $expense->company_profile)->count())
            {
                $expense->company_profile_id = config('fi.defaultCompanyProfile');
            }
        }

        if (isset($expense->client_name) and $expense->client_name)
        {
            $expense->client_id = Client::firstOrCreateByUniqueName($expense->client_name)->id;
        }
        elseif (isset($expense->client_name))
        {
            $expense->client_id = 0;
        }

        unset($expense->company_profile, $expense->client_name, $expense->vendor_name, $expense->category_name);
    }
}