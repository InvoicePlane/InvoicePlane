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

class Versions extends Admin_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('mdl_versions');
	}
	
	public function index($page = 0)
	{
        $this->mdl_versions->paginate(site_url('versions/index'), $page);
        $versions = $this->mdl_versions->result();
        
		$this->layout->set('versions', $versions);
		$this->layout->buffer('content', 'settings/versions');
		$this->layout->render();
	}
	
}

?>