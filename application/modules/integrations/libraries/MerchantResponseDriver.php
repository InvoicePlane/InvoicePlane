<?php

defined('BASEPATH') || exit('No direct script access allowed');

enum MerchantResponseDriver: string
{
    /* e-invoice / Peppol providers */
    case SuperPdp   = 'superpdp';
    case Qonto      = 'qonto';
    case ACube      = 'acube';
    case Dokapi     = 'dokapi';
    case B2BRouter   = 'b2brouter';
    case Arratech    = 'arratech';
    case Storecove   = 'storecove';
    case LetsPeppol = 'letspeppol';

    /* payment gateways (legacy ip_merchant_responses rows) */
    case PayPal = 'paypal';
    case Stripe = 'Stripe';
}
