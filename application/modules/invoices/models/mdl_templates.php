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

class Mdl_Templates extends CI_Model {

    public function get_invoice_templates($type = 'pdf')
    {
        $this->load->helper('directory');

        if ($type == 'pdf')
        {
            $templates = directory_map(APPPATH . '/views/invoice_templates/pdf', TRUE);
        }
        elseif ($type == 'public')
        {
            $templates = directory_map(APPPATH . '/views/invoice_templates/public', TRUE);
        }

        $templates = $this->remove_extension($templates);

        return $templates;
    }

    public function get_quote_templates($type = 'pdf')
    {
        $this->load->helper('directory');

        if ($type == 'pdf')
        {
            $templates = directory_map(APPPATH . '/views/quote_templates/pdf', TRUE);
        }
        elseif ($type == 'public')
        {
            $templates = directory_map(APPPATH . '/views/quote_templates/public', TRUE);
        }

        $templates = $this->remove_extension($templates);

        return $templates;
    }

    private function remove_extension($files)
    {
        foreach ($files as $key => $file)
        {
            $files[$key] = str_replace('.php', '', $file);
        }

        return $files;
    }

}

?>