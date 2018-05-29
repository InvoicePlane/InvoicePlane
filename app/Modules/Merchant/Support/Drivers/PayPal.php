<?php

namespace FI\Modules\Merchant\Support\Drivers;

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Models\MerchantPayment;
use FI\Modules\Merchant\Support\MerchantDriverPayable;
use FI\Modules\Payments\Models\Payment as FIPayment;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PayPal extends MerchantDriverPayable
{
    protected $isRedirect = true;

    public function getSettings()
    {
        return ['clientId', 'clientSecret', 'mode' => ['sandbox' => trans('fi.sandbox'), 'live' => trans('fi.live')]];
    }

    public function pay(Invoice $invoice)
    {
        $apiContext = $this->getApiContext();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName(trans('fi.invoice') . ' #' . $invoice->number)
            ->setCurrency($invoice->currency_code)
            ->setQuantity(1)
            ->setPrice($invoice->amount->balance + 0);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amount = new Amount();
        $amount->setCurrency($invoice->currency_code)
            ->setTotal($invoice->amount->balance + 0);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(trans('fi.invoice') . ' #' . $invoice->number)
            ->setInvoiceNumber(uniqid())
            ->setItemList($itemList);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('merchant.returnUrl', [$this->getName(), $invoice->url_key]))
            ->setCancelUrl(route('merchant.cancelUrl', [$this->getName(), $invoice->url_key]));

        $payment = new Payment();

        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try {
            $payment->create($apiContext);

            return $payment->getApprovalLink();
        } catch (PayPalConnectionException $ex) {
            \Log::info($ex->getData());
        }
    }

    private function getApiContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->getSetting('clientId'),
                $this->getSetting('clientSecret')
            )
        );

        $apiContext->setConfig(['mode' => $this->getSetting('mode')]);

        return $apiContext;
    }

    public function verify(Invoice $invoice)
    {
        $paymentId = request('paymentId');
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        $transaction = new Transaction();
        $amount = new Amount();

        $amount->setCurrency($invoice->currency_code)
            ->setTotal($invoice->amount->balance + 0);

        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $payment->execute($execution, $apiContext);

        $payment = Payment::get($paymentId, $apiContext);

        if ($payment->getState() == 'approved') {
            foreach ($payment->getTransactions() as $transaction) {
                $fiPayment = FIPayment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $transaction->getAmount()->getTotal(),
                    'payment_method_id' => config('fi.onlinePaymentMethod'),
                ]);

                MerchantPayment::saveByKey($this->getName(), $fiPayment->id, 'id', $payment->getId());
            }

            return true;
        }

        return false;
    }
}