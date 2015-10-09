<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function get_cron_key()
    {
        echo random_string('alnum', 16);
    }

    /**
     * Returns settings required for gateway to operate
     *
     * @param $gateway
     */
    public function get_gateway_params($gateway)
    {
        $array = \Omnipay\Omnipay::create($gateway)->getDefaultParameters();
        foreach ($array as $element => $type) {
            echo '<div class="form-group"><label for="' . $element . '" class="control-label">' . lang($element) . '</label>';
            echo $this->renderElement($element, $type);
            echo '</div>';
        }
    }

    /**
     * Renders element for payment merchant form
     *
     * @todo Cleanup, allow values to be passed through
     * @param $element
     * @param $type
     */
    protected function renderElement($element, $type)
    {
        if (is_array($type)) {
            echo '<select name="settings[merchant_settings][' . $element . ']" class="input-sm form-control">';
            foreach ($type as $option) {
                echo '<option value="' . $option . '">' . $option . '</option>';
            }
            echo '</select>';
        } elseif (is_string($type)) {
            echo '<input type="text" name="settings[merchant_settings][' . $element . ']" class="input-sm form-control">';
        } elseif (is_bool($type)) {
            echo '<select name="settings[merchant_settings][' . $element . ']" class="input-sm form-control">
                    <option value="0">' . lang('no') . '</option>
                    <option value="1">' . lang('yes') . '</option></select>';
        }
    }

}
