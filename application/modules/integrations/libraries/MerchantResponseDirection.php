<?php

defined('BASEPATH') or exit('No direct script access allowed');

enum MerchantResponseDirection: string
{
    case In  = 'in';
    case Out = 'out';
}
