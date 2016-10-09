<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends Base_Controller{

    public function login(){
        $this->load->helper('url');

        $data = $this->input->post();
        if(!empty($data['User']) && !empty($data['Password'])){
        	$this->load->model('sessions/mdl_sessions', 'Mdl_Sessions');
        	$res = $this->Mdl_Sessions->apiAuth($data['User'], $data['Password']);

        	if (!empty($res)) {
	            $this->output->set_content_type('application/json')->set_status_header(200, 'OK')->set_output(json_encode($res));
	            return;
	        }

        }

        $this->output->set_content_type('application/json')->set_status_header(500, 'Wrong Parameters')->set_output(json_encode(array("Error" => 409, "Message" => 'Wrong Parameters')));
    }

    public function logout(){
        $this->load->helper('url');

        $this->session->sess_destroy();
        $this->output->set_content_type('application/json')->set_status_header(200, 'OK')->set_output(json_encode(true));
        return;
    }
}
