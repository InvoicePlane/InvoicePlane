<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

function pager($base_url, $model)
{
	$CI = & get_instance();

	$pager = '<div class="btn-group">';

	if (($previous_page = $CI->$model->previous_offset) >= 0)
	{
		$pager .= '<a class="btn" href="' . $base_url . '/0" title="' . lang('first') . '"><i class="icon-fast-backward"></i></a>';
		$pager .= '<a class="btn" href="' . $base_url . '/' . $CI->$model->previous_offset . '" title="' . lang('prev') . '"><i class="icon-backward"></i></a>';
	}
	else
	{
		$pager .= '<a class="btn disabled" href="#" title="' . lang('first') . '"><i class="icon-fast-backward"></i></a>';
		$pager .= '<a class="btn disabled" href="#" title="' . lang('prev') . '"><i class="icon-backward"></i></a>';
	}

	if (($next_page = $CI->$model->next_offset) <= $CI->$model->last_offset)
	{
		$pager .= '<a class="btn" href="' . $base_url . '/' . $CI->$model->next_offset . '" title="' . lang('next') . '"><i class="icon-forward"></i></a>';
		$pager .= '<a class="btn" href="' . $base_url . '/' . $CI->$model->last_offset . '" title="' . lang('last') . '"><i class="icon-fast-forward"></i></a>';
	}
	else
	{
		$pager .= '<a class="btn disabled" href="#" title="' . lang('next') . '"><i class="icon-forward"></i></a>';
		$pager .= '<a class="btn disabled" href="#" title="' . lang('last') . '"><i class="icon-fast-forward"></i></a>';
	}
	
	$pager .= '</div>';
	
	return $pager;

}

?>