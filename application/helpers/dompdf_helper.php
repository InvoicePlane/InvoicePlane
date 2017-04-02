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
 * @copyright	Copyright (c) 2012 - 2013, Jesse Terry
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.
 * 
 */

function pdf_create($html, $filename, $stream = TRUE) {
	
	require_once(APPPATH . 'helpers/dompdf/dompdf_config.inc.php');
    
    $dompdf = new DOMPDF();
    
    $dompdf->load_html($html);
    
    $dompdf->set_paper('a4');	//---it---
    $dompdf->render();
    
    if ($stream) {
    	
        $dompdf->stream($filename . '.pdf');
        
    }
    
    else {

		$CI =& get_instance();

		$CI->load->helper('file');

        write_file('./uploads/temp/' . $filename . '.pdf', $dompdf->output());
		
		return './uploads/temp/' . $filename . '.pdf';        
    }
    
}

?>