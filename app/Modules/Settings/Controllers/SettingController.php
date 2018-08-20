<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Modules\Settings\Controllers;

use Illuminate\Support\Facades\Crypt;
use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Currencies\Models\Currency;
use IP\Modules\Groups\Models\Group;
use IP\Modules\Invoices\Support\InvoiceTemplates;
use IP\Modules\MailQueue\Support\MailSettings;
use IP\Modules\Merchant\Support\MerchantFactory;
use IP\Modules\PaymentMethods\Models\PaymentMethod;
use IP\Modules\Quotes\Support\QuoteTemplates;
use IP\Modules\Settings\Models\Setting;
use IP\Modules\Settings\Requests\SettingUpdateRequest;
use IP\Modules\TaxRates\Models\TaxRate;
use IP\Support\DashboardWidgets;
use IP\Support\DateFormatter;
use IP\Support\Languages;
use IP\Support\PDF\PDFFactory;
use IP\Support\Skins;
use IP\Support\Statuses\InvoiceStatuses;
use IP\Support\Statuses\QuoteStatuses;
use IP\Support\UpdateChecker;

class SettingController extends Controller
{
    public function index()
    {
        try {
            Crypt::decrypt(config('ip.mailPassword'));
            session()->forget('error');
        } catch (\Exception $e) {
            // Do nothing, already done in Config Provider
        }

        return view('settings.index')
            ->with([
                'languages' => Languages::listLanguages(),
                'dateFormats' => DateFormatter::dropdownArray(),
                'invoiceTemplates' => InvoiceTemplates::lists(),
                'quoteTemplates' => QuoteTemplates::lists(),
                'groups' => Group::getList(),
                'taxRates' => TaxRate::getList(),
                'paymentMethods' => PaymentMethod::getList(),
                'emailSendMethods' => MailSettings::listSendMethods(),
                'emailEncryptions' => MailSettings::listEncryptions(),
                'yesNoArray' => ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                'timezones' => array_combine(timezone_identifiers_list(), timezone_identifiers_list()),
                'paperSizes' => ['letter' => trans('ip.letter'), 'A4' => trans('ip.a4'), 'legal' => trans('ip.legal')],
                'paperOrientations' => ['portrait' => trans('ip.portrait'), 'landscape' => trans('ip.landscape')],
                'currencies' => Currency::getList(),
                'exchangeRateModes' => ['automatic' => trans('ip.automatic'), 'manual' => trans('ip.manual')],
                'pdfDrivers' => PDFFactory::getDrivers(),
                'convertQuoteOptions' => [
                    'quote' => trans('ip.convert_quote_option1'),
                    'invoice' => trans('ip.convert_quote_option2'),
                ],
                'clientUniqueNameOptions' => [
                    '0' => trans('ip.client_unique_name_option_1'),
                    '1' => trans('ip.client_unique_name_option_2'),
                ],
                'dashboardWidgets' => DashboardWidgets::listsByOrder(),
                'colWidthArray' => array_combine(range(1, 12), range(1, 12)),
                'displayOrderArray' => array_combine(range(1, 24), range(1, 24)),
                'merchant' => config('ip.merchant'),
                'skins' => Skins::lists(),
                'resultsPerPage' => array_combine(range(15, 100, 5), range(15, 100, 5)),
                'amountDecimalOptions' => ['0' => '0', '2' => '2', '3' => '3', '4' => '4'],
                'roundTaxDecimalOptions' => ['2' => '2', '3' => '3', '4' => '4'],
                'companyProfiles' => CompanyProfile::getList(),
                'merchantDrivers' => MerchantFactory::getDrivers(),
                'invoiceStatuses' => InvoiceStatuses::listsAllFlat() + ['overdue' => trans('ip.overdue')],
                'quoteStatuses' => QuoteStatuses::listsAllFlat(),
                'invoiceWhenDraftOptions' => [
                    0 => trans('ip.keep_invoice_date_as_is'),
                    1 => trans('ip.change_invoice_date_to_todays_date'),
                ],
                'quoteWhenDraftOptions' => [
                    0 => trans('ip.keep_quote_date_as_is'),
                    1 => trans('ip.change_quote_date_to_todays_date'),
                ],
            ]);
    }

    public function update(SettingUpdateRequest $request)
    {
        foreach (request('setting') as $key => $value) {
            $skipSave = false;

            if ($key == 'mailPassword' and $value) {
                $value = Crypt::encrypt($value);
            } elseif ($key == 'mailPassword' and !$value) {
                $skipSave = true;
            }

            if ($key == 'merchant') {
                $value = json_encode($value);
            }

            if (!$skipSave) {
                Setting::saveByKey($key, $value);
            }
        }

        Setting::writeEmailTemplates();

        return redirect()->route('settings.index')
            ->with('alertSuccess', trans('ip.settings_successfully_saved'));
    }

    public function updateCheck()
    {
        $updateChecker = new UpdateChecker;

        $updateAvailable = $updateChecker->updateAvailable();
        $currentVersion = $updateChecker->getCurrentVersion();

        if ($updateAvailable) {
            $message = trans('ip.update_available', ['version' => $currentVersion]);
        } else {
            $message = trans('ip.update_not_available');
        }

        return response()->json(
            [
                'success' => true,
                'message' => $message,
            ], 200
        );
    }

    public function saveTab()
    {
        session(['settingTabId' => request('settingTabId')]);
    }
}
