<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package     InvoicePlane
 * @author      Kovah (www.kovah.de)
 * @copyright   Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 */

class Upload extends Admin_Controller
{
    public $targetPath;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->targetPath = getcwd() . '/uploads/customer_files';

    }

    public function upload_file($customerId, $url_key)
    {
        Upload::create_dir($this->targetPath . '/');

        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $targetFile = $this->targetPath . '/' . $url_key . '_' . $fileName;
            $file_exists = file_exists($targetFile);

            if (!$file_exists) //If file does not exists then upload
            {
                $data = array(
                    'client_id' => $customerId,
                    'url_key' => $url_key,
                    'file_name_original' => $fileName,
                    'file_name_new' => $url_key . '_' . $fileName
                );
                $this->mdl_uploads->create($data);

                move_uploaded_file($tempFile, $targetFile);
            } else //If file exists then echo the error and set a http error response
            {
                echo lang('error_dublicate_file');;
                http_response_code(404);
            }

        } else {
            return Upload::show_files($url_key, $customerId);
        }
    }

    // public function file_delete($customerId,$id,$fileName)
    public function delete_file($url_key)
    {
        $path = $this->targetPath;
        $fileName = $_POST['name'];

        $this->mdl_uploads->delete($url_key, $fileName);
        unlink($path . '/' . $url_key . '_' . $fileName);

    }

    public function create_dir($path, $chmod = '0777')
    {
        if (!(is_dir($path) OR is_link($path)))
            return mkdir($path, $chmod);
        else
            return false;
    }

    public function show_files($url_key, $customerId = NULL)
    {

        $result = array();
        $path = $this->targetPath;

        $files = scandir($path);
        if ($files !== false) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && strpos($file, $url_key) !== false) {
                    $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                    $obj['fullname'] = $file;
                    $obj['size'] = filesize($path . '/' . $file);
                    $obj['fullpath'] = $path . '/' . $file;
                    $result[] = $obj;
                }
            }
        } else {

            return false;
        }
        echo json_encode($result);
    }

}