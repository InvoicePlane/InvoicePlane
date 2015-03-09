<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

function pager($base_url, $model)
{
    $CI = &get_instance();

    $pager = '<div class="btn-group">';

    if (($previous_page = $CI->$model->previous_offset) >= 0) {
        $pager .= '<a class="btn btn-default btn-sm" href="' . $base_url . '/0" title="' . lang('first') . '"><i class="fa fa-fast-backward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default btn-sm" href="' . $base_url . '/' . $CI->$model->previous_offset . '" title="' . lang('prev') . '"><i class="fa fa-backward no-margin"></i></a>';
    } else {
        $pager .= '<a class="btn btn-default btn-sm disabled" href="#" title="' . lang('first') . '"><i class="fa fa-fast-backward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default btn-sm disabled" href="#" title="' . lang('prev') . '"><i class="fa fa-backward no-margin"></i></a>';
    }

    if (($next_page = $CI->$model->next_offset) <= $CI->$model->last_offset) {
        $pager .= '<a class="btn btn-default btn-sm" href="' . $base_url . '/' . $CI->$model->next_offset . '" title="' . lang('next') . '"><i class="fa fa-forward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default btn-sm" href="' . $base_url . '/' . $CI->$model->last_offset . '" title="' . lang('last') . '"><i class="fa fa-fast-forward no-margin"></i></a>';
    } else {
        $pager .= '<a class="btn btn-default disabled btn-sm" href="#" title="' . lang('next') . '"><i class="fa fa-forward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default disabled btn-sm" href="#" title="' . lang('last') . '"><i class="fa fa-fast-forward no-margin"></i></a>';
    }

    $pager .= '</div>';

    return $pager;

}
