<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class UpgradeEmailTemplates extends Migration
{
    public function up()
    {
        $emailTemplates = [
            'invoiceEmailBody',
            'quoteEmailBody',
            'overdueInvoiceEmailBody',
            'upcomingPaymentNoticeEmailBody',
            'quoteApprovedEmailBody',
            'quoteRejectedEmailBody',
            'paymentReceiptBody',
            'quoteEmailSubject',
            'invoiceEmailSubject',
            'overdueInvoiceEmailSubject',
            'upcomingPaymentNoticeEmailSubject',
            'paymentReceiptEmailSubject',
        ];

        $findReplace = [
            'user->company'           => 'companyProfile->company',
            'user->formatted_address' => 'companyProfile->formatted_address',
            'user->phone'             => 'companyProfile->phone',
            'user->fax'               => 'companyProfile->fax',
            'user->mobile'            => 'companyProfile->mobile',
            'user->web'               => 'companyProfile->web',
            'user->address'           => 'companyProfile->address',
            'user->city'              => 'companyProfile->city',
            'user->state'             => 'companyProfile->state',
            'user->zip'               => 'companyProfile->zip',
            'user->country'           => 'companyProfile->country',
        ];

        foreach ($emailTemplates as $emailTemplate)
        {
            $template = Setting::getByKey($emailTemplate);

            foreach ($findReplace as $find => $replace)
            {
                $template = str_replace($find, $replace, $template);
            }

            Setting::saveByKey($emailTemplate, $template);
        }

        Setting::writeEmailTemplates();
    }

    public function down()
    {
        //
    }
}
