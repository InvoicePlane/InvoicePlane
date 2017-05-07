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
 * Class Upload
 */
class Upload extends Admin_Controller
{
    public $targetPath;

    public $ctype_default = "application/octet-stream";
    public $content_types = array(
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'txt' => 'text/plain',
        'xml' => 'application/xml',
    );

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->targetPath = UPLOADS_FOLDER . '/customer_files';
    }

    /**
     * @param $customerId
     * @param $url_key
     * @return boolean|null
     */
    public function upload_file($customerId, $url_key)
    {
        Upload::create_dir($this->targetPath . '/');

        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $targetFile = $this->targetPath . '/' . $url_key . '_' . $fileName;
            $file_exists = file_exists($targetFile);

            if (!$file_exists) {
                // If file does not exists then upload
                $data = array(
                    'client_id' => $customerId,
                    'url_key' => $url_key,
                    'file_name_original' => $fileName,
                    'file_name_new' => $url_key . '_' . $fileName
                );

                $this->mdl_uploads->create($data);

                move_uploaded_file($tempFile, $targetFile);

                echo json_encode([
                    'success' => true
                ]);

            } else {
                // If file exists then echo the error and set a http error response
                echo json_encode([
                    'success' => false,
                    'message' => trans('error_duplicate_file')
                ]);
                http_response_code(404);
            }

        } else {
            Upload::show_files($url_key, $customerId);
        }
    }

    /**
     * @param string $path
     * @param string $chmod
     * @return bool
     */
    public function create_dir($path, $chmod = '0777')
    {
        if (!(is_dir($path) || is_link($path))) {
            return mkdir($path, $chmod);
        } else {
            return false;
        }
    }

    /**
     * @param $url_key
     * @param null $customerId
     * @return void
     */
    public function show_files($url_key, $customerId = null)
    {
        $result = array();
        $path = $this->targetPath;

        $files = scandir($path);

        if ($files !== false) {

            foreach ($files as $file) {
                if (in_array($file, array(".", ".."))) {
                    continue;
                }
                if (strpos($file, $url_key) !== 0) {
                    continue;
                }
                if (substr(realpath($path), realpath($file) == 0)) {
                    $obj['name'] = substr($file, strpos($file, '_', 1) + 1);
                    $obj['fullname'] = $file;
                    $obj['size'] = filesize($path . '/' . $file);
                    $obj['fullpath'] = $path . '/' . $file;
                    $result[] = $obj;
                }
            }

        } else {
            return;
        }

        echo json_encode($result);
    }

    /**
     * @param $url_key
     */
    public function delete_file($url_key)
    {
        $path = $this->targetPath;
        $fileName = $_POST['name'];

        $this->mdl_uploads->delete_file($url_key, $fileName);

        // AVOID TREE TRAVERSAL!
        $finalPath = $path . '/' . $url_key . '_' . $fileName;

        if (strpos(realpath($path), realpath($finalPath)) == 0) {
            unlink($path . '/' . $url_key . '_' . $fileName);
        }
    }

    /**
     * Returns the corresponding file as a download and prevents execution of files
     *
     * @param string $filename
     * @return resource
     */
    public function get_file($filename)
    {
        $base_path = UPLOADS_FOLDER . 'customer_files/';
        $file_path = $base_path . $filename;

        if (strpos(realpath($base_path), realpath($file_path)) != 0) {
            show_404();
            exit;
        }

        $path_parts = pathinfo($file_path);
        $file_ext = $path_parts['extension'];

        if (file_exists($file_path)) {
            $file_size = filesize($file_path);

            $save_ctype = isset($this->content_types[$file_ext]);
            $ctype = $save_ctype ? $this->content_types[$file_ext] : $this->ctype_default;

            header("Expires: -1");
            header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: " . $ctype);
            header("Content-Length: " . $file_size);

            echo file_get_contents($file_path);
            exit;
        }

        show_404();
        exit;
    }

}
