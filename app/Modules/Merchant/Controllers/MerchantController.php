<?php

namespace FI\Modules\Merchant\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Support\MerchantFactory;

class MerchantController extends Controller
{
    public function pay()
    {
        $merchant = MerchantFactory::create(request('driver'));

        $invoice = Invoice::where('url_key', request('urlKey'))->first();

        try {
            if ($merchant->isRedirect()) {
                return [
                    'redirect' => 1,
                    'url' => $merchant->pay($invoice),
                ];
            } else {
                return [
                    'redirect' => 0,
                    'modalContent' => view('merchant.' . strtolower(request('driver')))
                        ->with('driver', MerchantFactory::create(request('driver')))
                        ->with('invoice', $invoice)
                        ->with('urlKey', request('urlKey'))
                        ->render(),
                ];
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return redirect()->route('clientCenter.public.invoice.show', [request('urlKey')])
                ->with('alert', $e->getMessage());
        }
    }

    public function cancelUrl($driver, $urlKey)
    {
        return redirect()->route('clientCenter.public.invoice.show', [$urlKey]);
    }

    public function returnUrl($driver, $urlKey)
    {
        $invoice = Invoice::where('url_key', $urlKey)->first();

        $merchant = MerchantFactory::create($driver);

        if ($merchant->verify($invoice)) {
            $messageStatus = 'alertSuccess';
            $messageContent = trans('fi.payment_applied');
        } else {
            $messageStatus = 'error';
            $messageContent = trans('fi.error_applying_payment');
        }

        return redirect()->route('clientCenter.public.invoice.show', [$urlKey])
            ->with($messageStatus, $messageContent);
    }

    public function webhookUrl($driver, $urlKey)
    {
        $invoice = Invoice::where('url_key', $urlKey)->first();

        $merchant = MerchantFactory::create($driver);

        $merchant->verify($invoice);
    }
}