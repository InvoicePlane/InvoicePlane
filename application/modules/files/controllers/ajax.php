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
 * @contributor Longka (www.longkyanh.info)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;

    public function modal_create_file()
    {
        $this->load->module('layout');

        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_name' => $this->input->post('client_name'),
            'clients' => $this->mdl_clients->get()->result(),
        );

        $this->layout->load_view('files/modal_create_file', $data);
    }

    public function create()
    {
        // first upload file
        $file = $this->do_upload();

        if (array_key_exists('error', $file)) {
            //@todo: validation error helper
            echo json_encode(
                array (
                    'success' => 0,
                    'validation_errors' => $file['error']
                )
            );

            return;
        }
        // then insert to db
        $client_id = $this->input->post('client_id');

        $this->load->model('files/mdl_files');

        $file_id = $this->mdl_files->create(
            array(
                'client_id' => $client_id,
                'file_name' => $file['file_name'],
                'file_date_created' => date('Y-m-d H:i:s'),
                'file_date_modified' => date('Y-m-d H:i:s'),
            )
        );

        echo json_encode(array('success' => 1, 'file_id' =>$file_id));
    }

    protected function do_upload()
    {
        //@todo move upload_path to config file
        $config['upload_path'] = './uploads/customer_files';
		$config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('file')) {
			$error = array('error' => $this->upload->display_errors());

			return $error;
		}

        return $this->upload->data();
    }
}
