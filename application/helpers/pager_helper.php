<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Returns a printable pagination
 *
 * @param $base_url
 * @param $model
 * @return string
 */
function pager($base_url, $model)
{
    $CI = &get_instance();

    $pager = '<div class="model-pager btn-group btn-group-sm">';

    if (($previous_page = $CI->$model->previous_offset) >= 0) {
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/0" title="' . trans('first') . '"><i class="fa fa-fast-backward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->previous_offset . '" title="' . trans('prev') . '"><i class="fa fa-backward no-margin"></i></a>';
    } else {
        $pager .= '<a class="btn btn-default disabled" href="#" title="' . trans('first') . '"><i class="fa fa-fast-backward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default disabled" href="#" title="' . trans('prev') . '"><i class="fa fa-backward no-margin"></i></a>';
    }

    if (($next_page = $CI->$model->next_offset) <= $CI->$model->last_offset) {
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->next_offset . '" title="' . trans('next') . '"><i class="fa fa-forward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->last_offset . '" title="' . trans('last') . '"><i class="fa fa-fast-forward no-margin"></i></a>';
    } else {
        $pager .= '<a class="btn btn-default disabled" href="#" title="' . trans('next') . '"><i class="fa fa-forward no-margin"></i></a>';
        $pager .= '<a class="btn btn-default disabled" href="#" title="' . trans('last') . '"><i class="fa fa-fast-forward no-margin"></i></a>';
    }

    $pager .= '</div>';

    return $pager;
}

/**
 * Returns a printable pagination detailed
 *
 * @param $model
 * @return string
 */
function pager_detailed($model)
{
    $CI = &get_instance();
    
    $current_item_last = $CI->$model->default_limit * $CI->$model->current_page;
    $current_item_first = ($current_item_last - $CI->$model->default_limit) + 1;
    $total_pages = ($CI->$model->total_pages > 0)?$CI->$model->total_pages:1;
    $total_rows = $CI->$model->total_rows;
    
    if ($total_rows == 0) {
        $current_item_last = 0;
        $current_item_first = 0;
    }
    
    if ($current_item_last > $total_rows) {
        $current_item_last = $total_rows;
    }
    
    $pager = '<div class="model-pager-detailed">';
    $pager .= '<div>';
    $pager .= trans('showing').' '.$current_item_first;
    $pager .= ' '.trans('to').' '.$current_item_last.' ';
    $pager .= ' '.trans('from').' '.$total_rows;
    $pager .= '</div>';
    $pager .= '<div>';
    $pager .= trans('page').' '.$CI->$model->current_page;
    $pager .= ' '.trans('from').' '.$total_pages;
    $pager .= '</div>';
    
    return $pager;
}
