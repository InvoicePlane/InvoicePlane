<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * InvoicePlane - KSeF 2.0 XML Generator
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 * 
 * Sources:
 *  - https://ksef.podatki.gov.pl/media/4u1bmhx4/information-sheet-on-the-fa-3-logical-structure.pdf
 *  - https://github.com/odoo/odoo/blob/19.0/addons/l10n_pl_edi/data/fa3_template.xml
 * 
 * Known limitations:
 *  - Invoice types:
 *      - Only basic VAT invoices (corrective, advance and final invoices are not supported)
 *      - Only for services (not goods)
 *      - Annotations only compute the reverse charge mechanism (everything else is hardcoded to "no")
 *      - Units are hardcoded to "szt." (pieces)
 *      - Delivery date and exchange rate are the same for all items
 *  - Domestic provision of services:
 *      - Only supports basic (23% or 22%), first reduced (8% or 7%) and second reduced (5%) VAT rates
 *      - Local government authorities (JST) and VAT group members (GV) are not supported
 */

class Ksefv20Xml
{
    public $doc;
    public $filename;
    public $invoice;
    public $items;
    public $quantity_decimals;
    public $amount_decimals;
    public $currency_code;
    public $pln_exchange_rate;
    public $seller_krs;
    public $seller_regon;
    public $seller_bdo;

    public function __construct(array $params)
    {
        $CI = &get_instance();
        $CI->load->model(['custom_fields/mdl_custom_fields']);

        $this->filename = $params['filename'];
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->quantity_decimals = $CI->mdl_settings->setting('default_item_decimals');
        $this->amount_decimals = $CI->mdl_settings->setting('tax_rate_decimal_places');

        $custom_field_paths = $params['options']['custom_fields'] ?? [];
        $custom_field_values = [
            'invoice' => $CI->mdl_custom_fields->get_values_for_fields('mdl_invoice_custom', $this->invoice->invoice_id),
            'user' => $CI->mdl_custom_fields->get_values_for_fields('mdl_user_custom', $this->invoice->user_id),
            'client' => $CI->mdl_custom_fields->get_values_for_fields('mdl_client_custom', $this->invoice->client_id),
        ];

        $this->currency_code = $this->get_array_value_by_path($custom_field_values, $custom_field_paths['currency_code'] ?? '', $CI->mdl_settings->setting('currency_code'));
        $this->pln_exchange_rate = $this->get_array_value_by_path($custom_field_values, $custom_field_paths['pln_exchange_rate'] ?? '');
        $this->seller_krs = $this->get_array_value_by_path($custom_field_values, $custom_field_paths['seller_krs'] ?? '');
        $this->seller_regon = $this->get_array_value_by_path($custom_field_values, $custom_field_paths['seller_regon'] ?? '');
        $this->seller_bdo = $this->get_array_value_by_path($custom_field_values, $custom_field_paths['seller_bdo'] ?? '');
    }

    public function xml(): void
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = IP_DEBUG;

        $xml = $this->array_to_xml([
            'Faktura' => array_merge(
                [
                    '@attributes' => [
                        'xmlns:etd' => 'http://crd.gov.pl/xml/schematy/dziedzinowe/mf/2022/01/05/eD/DefinicjeTypy/',
                        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                        'xmlns' => 'http://crd.gov.pl/wzor/2025/06/25/13775/',
                    ],
                ],
                $this->get_header(),
                $this->get_seller(),
                $this->get_buyer(),
                $this->get_invoice(),
                $this->get_footer(),
            ),
        ]);

        $this->doc->appendChild($xml);
        $this->doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
    }

    protected function get_header()
    {
        $current_utc_date_time = gmdate('Y-m-d\TH:i:s\Z');

        return [
            'Naglowek' => [
                'KodFormularza' => [
                    '@attributes' => [
                        'kodSystemowy' => 'FA (3)',
                        'wersjaSchemy' => '1-0E',
                    ],
                    '@value' => 'FA',
                ],
                'WariantFormularza' => '3',
                'DataWytworzeniaFa' => $current_utc_date_time,
                'SystemInfo' => 'InvoicePlane',
            ],
        ];
    }

    protected function get_seller()
    {
        $name = $this->invoice->user_company;
        $nip = $this->get_seller_nip();
        $address_lines = $this->get_seller_address_lines();
        $country = $this->invoice->user_country;

        return [
            'Podmiot1' => array_merge(
                [
                    'DaneIdentyfikacyjne' => [
                        'NIP' => $nip,
                        'Nazwa' => $name,
                    ],
                    'Adres' => [
                        'KodKraju' => $country,
                        'AdresL1' => $address_lines[0],
                        'AdresL2' => $address_lines[1],
                    ],
                ],
                $this->get_seller_contact_info(),
            ),
        ];
    }

    protected function get_buyer()
    {
        $name = $this->invoice->client_company ?: $this->invoice->client_name;
        $nip = $this->get_buyer_nip();
        $vat = trim($this->invoice->client_vat_id, " \t\n\r\0\x0B-");
        $address_lines = $this->get_buyer_address_lines();
        $country = $this->invoice->client_country;

        if ($this->is_pl_buyer()) {
            $id_data = [
                'NIP' => $nip,
                'Nazwa' => $name
            ];
        } else if ($this->is_eu_buyer()) {
            // Normalize country code and VAT ID, then remove only a leading country prefix (case-insensitive).
            $country_code = strtoupper(trim($country));
            $normalized_vat = strtoupper(preg_replace('/\s+/', '', $vat));

            if ($country_code !== '' && stripos($normalized_vat, $country_code) === 0) {
                $vat_without_country = substr($normalized_vat, strlen($country_code));
            } else {
                $vat_without_country = $normalized_vat;
            }

            $id_data = [
                'KodUE' => $country_code,
                'NrVatUE' => $vat_without_country,
                'Nazwa' => $name
            ];
        } else if (!empty($vat)) {
            $id_data = [
                'KodKraju' => $country,
                'NrID' => $vat,
                'Nazwa' => $name
            ];
        } else {
            $id_data = [
                'BrakID' => 1, // No tax ID (1 = yes, 2 = no)
                'Nazwa' => $name
            ];
        }

        return [
            'Podmiot2' => array_merge(
                [
                    'DaneIdentyfikacyjne' => $id_data,
                    'Adres' => [
                        'KodKraju' => $country,
                        'AdresL1' => $address_lines[0],
                        'AdresL2' => $address_lines[1],
                    ],
                ],
                $this->get_buyer_contact_info(),
                [
                    'JST' => '2', // Local government authority (1 = yes, 2 = no)
                    'GV' => '2', // VAT group member (1 = yes, 2 = no)
                ]
            ),
        ];
    }

    protected function get_invoice()
    {
        $invoice_number = $this->invoice->invoice_number;
        $location = $this->invoice->user_city;
        $current_utc_date = gmdate('Y-m-d');
        $issue_date = $this->invoice->invoice_date_created;

        return [
            'Fa' => array_merge(
                [
                    'KodWaluty' => $this->currency_code,
                    'P_1' => $current_utc_date,
                    'P_1M' => $location,
                    'P_2' => $invoice_number,
                    'P_6' => $this->format_date($issue_date),
                ],
                $this->get_invoice_totals(),
                $this->get_invoice_annotations(),
                [
                    'RodzajFaktury' => 'VAT', // Basic VAT invoice
                ],
                $this->get_invoice_items(),
                $this->get_invoice_payment(),
            ),
        ];
    }

    protected function get_footer()
    {
        $name = $this->invoice->user_company;

        return [
            'Stopka' => [
                'Rejestry' => array_merge(
                    [
                        'PelnaNazwa' => $name,
                    ],
                    !empty($this->seller_krs) ? ['KRS' => $this->seller_krs] : [],
                    !empty($this->seller_regon) ? ['REGON' => $this->seller_regon] : [],
                    !empty($this->seller_bdo) ? ['BDO' => $this->seller_bdo] : [],
                ),
            ]
        ];
    }

    protected function get_invoice_totals()
    {
        $gross_total = $this->invoice->invoice_total;
        $net_total = $this->invoice->invoice_item_subtotal;
        $net_totals = [];

        if ($this->is_pl_buyer()) {
            $basic_net_total = 0;
            $basic_tax_total = 0;
            $first_reduced_net_total = 0;
            $first_reduced_tax_total = 0;
            $second_reduced_net_total = 0;
            $second_reduced_tax_total = 0;
            $unsupported_items = [];
            $unsupported_net_total = 0;
            $unsupported_tax_total = 0;

            foreach ($this->items as $item) {
                if ($item->item_tax_rate_percent == 23 || $item->item_tax_rate_percent == 22) {
                    $basic_net_total += $item->item_subtotal;
                    $basic_tax_total += $item->item_tax_total;
                } else if ($item->item_tax_rate_percent == 8 || $item->item_tax_rate_percent == 7) {
                    $first_reduced_net_total += $item->item_subtotal;
                    $first_reduced_tax_total += $item->item_tax_total;
                } else if ($item->item_tax_rate_percent == 5) {
                    $second_reduced_net_total += $item->item_subtotal;
                    $second_reduced_tax_total += $item->item_tax_total;
                } else {
                    // Accumulate items with unsupported VAT rates
                    $unsupported_net_total += $item->item_subtotal;
                    $unsupported_tax_total += $item->item_tax_total;
                    $unsupported_items[] = [
                        'rate' => $item->item_tax_rate_percent,
                        'name' => $item->item_name ?? '',
                        'code' => $item->item_code ?? '',
                        'net' => $item->item_subtotal,
                        'tax' => $item->item_tax_total,
                    ];
                }
            }

            // Log warning if there are items with unsupported VAT rates
            if (!empty($unsupported_items)) {
                $rates = array_unique(array_column($unsupported_items, 'rate'));
                $item_identifiers = array_map(function ($item) {
                    return !empty($item['name']) ? $item['name'] : $item['code'];
                }, $unsupported_items);

                log_message('warning', sprintf(
                    'KSeF XML: Items with unsupported VAT rates detected for PL buyer. ' .
                    'Rates: [%s], Total net: %s, Total tax: %s, Items: [%s]',
                    implode(', ', $rates),
                    $this->format_amount($unsupported_net_total),
                    $this->format_amount($unsupported_tax_total),
                    implode(', ', array_filter($item_identifiers))
                ));
            }

            $net_totals = array_merge(
                $basic_net_total > 0 ? [
                    'P_13_1' => $this->format_amount($basic_net_total), // K_19 in JPK_V7
                    'P_14_1' => $this->format_amount($basic_tax_total), // K_20 in JPK_V7
                ] : [],
                $first_reduced_net_total > 0 ? [
                    'P_13_2' => $this->format_amount($first_reduced_net_total), // K_17 in JPK_V7
                    'P_14_2' => $this->format_amount($first_reduced_tax_total), // K_18 in JPK_V7
                ] : [],
                $second_reduced_net_total > 0 ? [
                    'P_13_3' => $this->format_amount($second_reduced_net_total), // K_15 in JPK_V7
                    'P_14_3' => $this->format_amount($second_reduced_tax_total), // K_16 in JPK_V7
                ] : [],
            );
        } else if ($this->is_eu_buyer()) {
            $net_totals = [
                'P_13_9' => $this->format_amount($net_total), // Provision of services within the EU (K_12 in JPK_V7)
            ];
        } else {
            $net_totals = [
                'P_13_8' => $this->format_amount($net_total), // Provision of services outside the EU (K_11 in JPK_V7)
            ];
        }

        return array_merge(
            $net_totals,
            [
                'P_15' => $this->format_amount($gross_total)
            ]
        );
    }

    protected function get_invoice_annotations()
    {
        return [
            'Adnotacje' => [
                'P_16' => 2, // Cash accounting (1 = yes, 2 = no)
                'P_17' => 2, // Self-billing (1 = yes, 2 = no)
                'P_18' => $this->is_eu_buyer() ? 1 : 2, // Reverse charge mechanism (1 = yes, 2 = no)
                'P_18A' => 2, // Split payment mechanism (1 = yes, 2 = no)
                'Zwolnienie' => [
                    'P_19N' => 1, // Tax-exempt items (1 = no, 2 = yes)
                ],
                'NoweSrodkiTransportu' => [
                    'P_22N' => 1, // New means of transport (1 = no, 2 = yes)
                ],
                'P_23' => 2, // Triangular transaction (1 = yes, 2 = no)
                'PMarzy' => [
                    'P_PMarzyN' => 1, // Margin scheme (1 = no, 2 = yes)
                ],
            ],
        ];
    }

    protected function get_invoice_items()
    {
        return [
            'FaWiersz' => array_map(function ($index, $item) {
                $line_number = $index + 1;
                $name = trim($item->item_name, " \t\n\r\0\x0B-");
                $description = trim($item->item_description, " \t\n\r\0\x0B-");

                // Three-step fallback for P_7 (item name) to ensure it's never empty
                $p7_value = '';
                if (!empty($name)) {
                    $p7_value = $name;
                } elseif (!empty($description)) {
                    $p7_value = $description;
                } elseif (!empty($item->item_code)) {
                    $p7_value = $item->item_code;
                } else {
                    $p7_value = 'Brak nazwy';
                    // Log warning when both name and description are missing
                    log_message('warning', sprintf(
                        'KSeF XML: Item #%d missing both name and description, using placeholder "%s"',
                        $line_number,
                        $p7_value
                    ));
                }

                if ($this->is_pl_buyer()) {
                    $tax_rate = $this->format_amount($item->item_tax_rate_percent, true);
                } else if ($this->is_eu_buyer()) {
                    $tax_rate = 'np II'; // Provision of services within the EU
                } else {
                    $tax_rate = 'np I'; // Provision of services outside the EU
                }

                return array_merge(
                    [
                        'NrWierszaFa' => $line_number,
                        'P_7' => $p7_value,
                        'P_8A' => 'szt.',
                        'P_8B' => $this->format_quantity($item->item_quantity),
                        'P_9A' => $this->format_amount($item->item_price),
                        'P_11' => $this->format_amount($item->item_subtotal),
                        'P_12' => $tax_rate,
                    ],
                    !empty($this->pln_exchange_rate) ? ['KursWaluty' => $this->pln_exchange_rate] : []
                );
            }, array_keys($this->items), $this->items)
        ];
    }

    protected function get_invoice_payment()
    {
        $due_date = $this->invoice->invoice_date_due;
        $payment_method = strtolower((string) ($this->invoice->payment_method_name ?? ''));
        $iban = str_replace(' ', '', $this->invoice->user_iban);
        $bic = $this->invoice->user_bic;
        $bank_name = $this->invoice->user_bank;

        if (strpos($payment_method, 'cash') !== false) {
            $payment_method_code = 1;
        } else if (strpos($payment_method, 'card') !== false) {
            $payment_method_code = 2;
        } else {
            $payment_method_code = 6; // Bank transfer
        }

        return [
            'Platnosc' => array_merge(
                [
                    'TerminPlatnosci' => [
                        'Termin' => $this->format_date($due_date),
                    ],
                    'FormaPlatnosci' => $payment_method_code,
                ],
                !empty($iban) ? [
                    'RachunekBankowy' => array_merge(
                        [
                            'NrRB' => $iban,
                        ],
                        !empty($bic) ? ['SWIFT' => $bic] : [],
                        !empty($bank_name) ? ['NazwaBanku' => $bank_name] : [],
                        [
                            'OpisRachunku' => $this->currency_code,
                        ]
                    )
                ] : [],
            ),
        ];
    }

    protected function get_nip($whose = 'seller')
    {
        $nip = '';
        $vat = '';

        if ($whose === 'seller') {
            $nip = $this->invoice->user_tax_code;
            $vat = $this->invoice->user_vat_id;
        } else {
            $nip = $this->invoice->client_tax_code;
            $vat = $this->invoice->client_vat_id;
        }

        // Extract NIP from VAT if the former is empty and the latter starts with "PL"
        if (empty($nip) && strtoupper(substr($vat, 0, 2)) === 'PL') {
            $nip = substr($vat, 2);
        }

        return $nip;
    }

    protected function get_seller_nip()
    {
        return $this->get_nip('seller');
    }

    protected function get_buyer_nip()
    {
        return $this->get_nip('buyer');
    }

    protected function get_address_lines($whose = 'seller')
    {
        $street = '';
        $city = '';
        $state = '';
        $zip = '';

        if ($whose === 'seller') {
            $street = join(', ', array_filter([$this->invoice->user_address_1, $this->invoice->user_address_2]));
            $city = $this->invoice->user_city;
            $state = $this->invoice->user_state;
            $zip = $this->invoice->user_zip;
        } else {
            $street = join(', ', array_filter([$this->invoice->client_address_1, $this->invoice->client_address_2]));
            $city = $this->invoice->client_city;
            $state = $this->invoice->client_state;
            $zip = $this->invoice->client_zip;
        }

        $line1 = $street;

        if (!empty($state)) {
            // "City, State ZIP"
            $line2 = join(' ', array_filter([
                join(', ', array_filter([$city, $state])),
                $zip
            ]));
        } else {
            // "ZIP City"
            $line2 = join(' ', array_filter([$zip, $city]));
        }

        return [
            $line1,
            $line2
        ];
    }

    protected function get_seller_address_lines()
    {
        return $this->get_address_lines('seller');
    }

    protected function get_buyer_address_lines()
    {
        return $this->get_address_lines('buyer');
    }

    protected function get_contact_info($whose = 'seller')
    {
        $email = '';
        $phone = '';

        if ($whose === 'seller') {
            $email = $this->invoice->user_email;
            $phone = str_replace(' ', '', $this->invoice->user_phone ?: $this->invoice->user_mobile);
        } else {
            $email = $this->invoice->client_email;
            $phone = str_replace(' ', '', $this->invoice->client_phone ?: $this->invoice->client_mobile);
        }

        if (empty($email) && empty($phone)) {
            return [];
        }

        return [
            'DaneKontaktowe' => array_merge(
                !empty($email) ? ['Email' => $email] : [],
                !empty($phone) ? ['Telefon' => $phone] : [],
            ),
        ];
    }

    protected function get_seller_contact_info()
    {
        return $this->get_contact_info('seller');
    }

    protected function get_buyer_contact_info()
    {
        return $this->get_contact_info('buyer');
    }

    protected function is_eu_country($country_code)
    {
        $eu_country_codes = [
            'AT', // Austria
            'BE', // Belgium
            'BG', // Bulgaria
            'CY', // Cyprus
            'CZ', // Czech Republic
            'DE', // Germany
            'DK', // Denmark
            'EE', // Estonia
            'ES', // Spain
            'FI', // Finland
            'FR', // France
            'GR', // Greece
            'HR', // Croatia
            'HU', // Hungary
            'IE', // Ireland
            'IT', // Italy
            'LT', // Lithuania
            'LU', // Luxembourg
            'LV', // Latvia
            'MT', // Malta
            'NL', // Netherlands
            'PL', // Poland
            'PT', // Portugal
            'RO', // Romania
            'SE', // Sweden
            'SI', // Slovenia
            'SK', // Slovakia
        ];

        if (in_array($country_code, $eu_country_codes)) {
            return true;
        }

        return false;
    }

    protected function is_pl_buyer()
    {
        $country = $this->invoice->client_country;
        return $country === 'PL';
    }

    protected function is_eu_buyer()
    {
        $vat = $this->invoice->client_vat_id;
        $country = $this->invoice->client_country;
        return !empty($vat) && $this->is_eu_country($country) && $country !== $this->invoice->user_country;
    }

    /**
     * Converts an array structure to XML elements with support for attributes
     * 
     * @param array $data Array structure to convert
     * @param DOMElement|null $parent Parent element to append to
     * @return DOMElement|null The created element
     * 
     * Usage examples:
     * - Simple: ['Name' => 'John'] -> <Name>John</Name>
     * - Nested: ['Person' => ['Name' => 'John']] -> <Person><Name>John</Name></Person>
     * - With attributes: ['Code' => ['@attributes' => ['version' => '1.0'], '@value' => 'FA']] -> <Code version="1.0">FA</Code>
     * - Numeric arrays: ['Item' => [['Name' => 'A'], ['Name' => 'B']]] -> <Item><Name>A</Name></Item><Item><Name>B</Name></Item>
     */
    protected function array_to_xml(array $data, $parent = null)
    {
        $last_element = null;

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            // Handle numeric arrays (multiple elements with same tag)
            if (is_array($value) && array_keys($value) === range(0, count($value) - 1)) {
                foreach ($value as $item) {
                    $last_element = $this->create_xml_element($key, $item, $parent);
                }
                continue;
            }

            $last_element = $this->create_xml_element($key, $value, $parent);
        }

        return $last_element ?? $parent;
    }

    private function create_xml_element($name, $value, $parent)
    {
        $element = $this->doc->createElement($name);

        if (is_array($value)) {
            // Add attributes
            if (isset($value['@attributes'])) {
                foreach ($value['@attributes'] as $attr => $attrValue) {
                    $element->setAttribute($attr, $attrValue);
                }
                unset($value['@attributes']);
            }

            // Add text value
            if (isset($value['@value'])) {
                $textNode = $this->doc->createTextNode($value['@value']);
                $element->appendChild($textNode);
                unset($value['@value']);
            }

            // Add children
            if (!empty($value)) {
                $this->array_to_xml($value, $element);
            }
        } else if ($value !== null && $value !== '') {
            $textNode = $this->doc->createTextNode($value);
            $element->appendChild($textNode);
        }

        if ($parent) {
            $parent->appendChild($element);
        }

        return $element;
    }

    protected function get_array_value_by_path($array, $path, $fallback = null)
    {
        if (empty($path)) {
            return $fallback;
        }

        $keys = explode('.', $path);
        $value = $array;

        foreach ($keys as $key) {
            if (is_array($value) && array_key_exists($key, $value) && !empty($value[$key])) {
                $value = $value[$key];
            } else {
                return $fallback;
            }
        }

        return $value;
    }

    protected function format_date($date_string, $format = 'Y-m-d')
    {
        if ($date_string) {
            $date = DateTime::createFromFormat('Y-m-d', $date_string);
            if ($date === false) {
                // Fallback: try parsing with DateTime constructor
                try {
                    $date = new DateTime($date_string);
                    return $date->format($format);
                } catch (Exception $e) {
                    return '';
                }
            }
            return $date->format($format);
        }
        return '';
    }

    protected function format_number($value, $decimals = null, $trim_trailing_zeros = false)
    {
        $decimals = $decimals ?? $this->amount_decimals;
        $formatted_value = number_format($value, $decimals, '.', '');

        if ($trim_trailing_zeros) {
            return rtrim(rtrim($formatted_value, '0'), '.');
        }

        return $formatted_value;
    }

    protected function format_amount($value, $trim_trailing_zeros = false)
    {
        return $this->format_number($value, $this->amount_decimals, $trim_trailing_zeros);
    }

    protected function format_quantity($value)
    {
        return $this->format_number($value, $this->quantity_decimals, true);
    }
}
