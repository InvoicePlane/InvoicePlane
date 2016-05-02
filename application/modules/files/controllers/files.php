<?php

if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Files extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_files');
    }

    public function index()
    {
        redirect('files/status/all');
    }

    public function download($file_id)
    {
        //get file name by id
        $file = $this->mdl_files->where('file_id', $file_id)->get()->result_array()[0];
        $file_path = './uploads/customer_files' . $file['file_name'];

        $this->load->helper('download');
        force_download($file['file_name'], $file_path);
    }

    /**
     * Get All Status of File
     *
     * @param  string  $status status can be:
     *     - all
     *     - active
     *     - inactive (soft deleted)
     * @param  integer $page
     * @return mixed
     */
    public function status($status = all, $page = 0)
    {
        //@todo filter by Status and pagination

        $files = $this->mdl_files->get()->result();

        $this->layout->set(
            array(
                'records' => $files,
                'filter_display'     => TRUE,
                'filter_placeholder' => lang('filter_files'),
                'filter_method'      => 'filter_files'
            )
        );

        $this->layout->buffer('content', 'files/index');
        $this->layout->render();
    }

    public function delete($file_id)
    {
        $this->mdl_files->delete($file_id);
        redirect('files/');
    }
}
