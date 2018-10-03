<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Number formats
| -------------------------------------------------------------------
| This is a list of available number formats that are used by
| the settings:
|
| US/UK format...................... 1,000,000.00
| European format................... 1.000.000,00
| ISO 80000-1 with decimal point.... 1 000 000.00
| ISO 80000-1 with decimal comma.... 1 000 000,00
| Compact with decimal point........   1000000.00
| Compact with decimal comma........   1000000,00
|
*/

$config['number_formats'] = [
    'number_format_us_uk' =>
        [
            'label' => 'number_format_us_uk',
            'decimal_point' => '.',
            'thousands_separator' => ',',
        ],
    'number_format_european' =>
        [
            'label' => 'number_format_european',
            'decimal_point' => ',',
            'thousands_separator' => '.',
        ],
    'number_format_iso80k1_point' =>
        [
            'label' => 'number_format_iso80k1_point',
            'decimal_point' => '.',
            'thousands_separator' => ' ',
        ],
    'number_format_iso80k1_comma' =>
        [
            'label' => 'number_format_iso80k1_comma',
            'decimal_point' => ',',
            'thousands_separator' => ' ',
        ],
    'number_format_compact_point' =>
        [
            'label' => 'number_format_compact_point',
            'decimal_point' => '.',
            'thousands_separator' => '',
        ],
    'number_format_compact_comma' =>
        [
            'label' => 'number_format_compact_comma',
            'decimal_point' => ',',
            'thousands_separator' => '',
        ],
];

