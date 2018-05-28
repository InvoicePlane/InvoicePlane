<?php

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CompanyProfilesCreate extends Migration
{
    public function up()
    {
        $maxUserId = Invoice::select('user_id')
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->groupBy('user_id')
            ->whereIn('user_id', function ($query)
            {
                $query->select('id')->from('users')->where('client_id', 0);
            })
            ->first();

        if ($maxUserId)
        {
            Expense::where('user_id', 0)->update(['user_id' => $maxUserId->user_id]);
        }

        foreach (User::where('client_id', 0)->get() as $user)
        {
            if (count($user->invoices) or count($user->quotes) or count($user->expenses))
            {
                CompanyProfile::flushEventListeners();
                $companyProfile = CompanyProfile::firstOrNew(['company' => ($user->company) ?: $user->name]);

                if (!$companyProfile->id)
                {
                    $companyProfile->fill([
                        'address' => $user->address,
                        'city'    => $user->city,
                        'state'   => $user->state,
                        'zip'     => $user->zip,
                        'country' => $user->country,
                        'phone'   => $user->phone,
                        'fax'     => $user->fax,
                        'mobile'  => $user->mobile,
                        'web'     => $user->web,
                    ]);

                    $companyProfile->save();
                }

                Invoice::where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
                Quote::where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
                Expense::where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
            }
        }
    }

    public function down()
    {
        //
    }
}
