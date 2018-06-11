<?php

namespace FI\Modules\Merchant\Support\Drivers;

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Models\MerchantClient;
use FI\Modules\Merchant\Models\MerchantPayment;
use FI\Modules\Merchant\Support\MerchantDriver;
use FI\Modules\Payments\Models\Payment;
use Stripe\Charge;
use Stripe\Customer;

class Stripe extends MerchantDriver
{
    protected $isRedirect = false;

    public function getSettings()
    {
        return ['publishableKey', 'secretKey'];
    }

    public function verify(Invoice $invoice)
    {
        \Stripe\Stripe::setApiKey($this->getSetting('secretKey'));

        $clientMerchantId = MerchantClient::getByKey($this->getName(), $invoice->client_id, 'id');

        if ($clientMerchantId) {
            try {
                $customer = Customer::retrieve($clientMerchantId);
            } catch (\Exception $e) {
                // Don't need to do anything here.
            }
        }

        if (!isset($customer) or $customer->deleted) {
            $customer = $this->createCustomer($invoice, request('token'));
        } else {
            $customer->source = request('token');
            $customer->save();
        }

        try {
            $charge = Charge::create([
                'customer' => $customer->id,
                'amount' => $invoice->amount->balance * 100,
                'currency' => $invoice->currency_code,
                'description' => trans('ip.invoice') . ' #' . $invoice->number,
            ]);

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $charge->amount / 100,
                'payment_method_id' => config('fi.onlinePaymentMethod'),
            ]);

            MerchantPayment::saveByKey($this->getName(), $payment->id, 'id', $charge->id);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createCustomer($invoice, $source)
    {
        $customer = Customer::create([
            'description' => $invoice->client->name,
            'email' => $invoice->client->email,
            'source' => $source,
        ]);

        MerchantClient::saveByKey($this->getName(), $invoice->client_id, 'id', $customer->id);

        return $customer;
    }
}