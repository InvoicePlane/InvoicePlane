<?php

defined('BASEPATH') || exit('No direct script access allowed');

enum MerchantResponseType: string
{
    case Payment         = 'payment';
    case OutboundStatus  = 'outbound_status';
    case IncomingInvoice = 'incoming_invoice';
    case InvoiceEvent    = 'invoice_event';
}
