<?php

defined('BASEPATH') || exit('No direct script access allowed');

enum MerchantResponseDriver: string
{
    /* e-invoice / Peppol providers */
    case SuperPdp   = 'superpdp';
    case Qonto      = 'qonto';
    case LetsPeppol = 'letspeppol';

    /* payment gateways (legacy ip_merchant_responses rows) */
    case PayPal = 'paypal';
    case Stripe = 'Stripe';
}
