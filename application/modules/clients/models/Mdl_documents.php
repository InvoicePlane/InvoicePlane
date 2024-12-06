
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author              InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license             https://invoiceplane.com/license.txt
 * @link                https://invoiceplane.com
 *
 * this documents extension by chrissie
 */

/**
 * Class Mdl_Documents
 *
 *   create table ip_documents (document_id int auto_increment primary key,
 *     client_id int, document_filename varchar(255), document_description varchar(255),
 *     document_deleted int default 0, document_created datetime );
 */
/*
| document_filename    | varchar(255) | YES  |     | NULL    |                |
| document_description | varchar(255) | YES  |     | NULL    |                |
| document_created     | datetime     | YES  |     | NULL    |                |
| document_deleted     | int(11)      | YES  |     | 0       |
*/
class Mdl_Documents extends Response_Model
{
    public $table = 'ip_documents';
    public $primary_key = 'ip_documents.document';

    function get_documents($client_id )
    {
        $docs =  $this->mdl_documents
            ->where('client_id' , $client_id)
            ->where('document_deleted' , 0)
            ->order_by('ip_documents.document_created', 'DESC')
            ->get()
            ->result();
        return $docs;
    }

        public function insert_document( $client_id, $document_filename, $document_description )
        {
		$data = array (
			'client_id' => $client_id,
			'document_filename' => $document_filename,
			'document_description' => $document_description,
			'document_created' => date('Y-m-d H:i:s'),
			'document_deleted' => 0
		);
		$this->db->insert('ip_documents', $data);
        }

        public function delete_document( $document_id)
        {
		$this->db->update('ip_documents', ['document_deleted' => 1],  ['document_id' => $document_id]);
	}

}

