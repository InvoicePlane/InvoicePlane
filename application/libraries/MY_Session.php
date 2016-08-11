<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');




class MY_Session extends CI_Session{

	function my_set_userdata($newdata = array(), $newval = ''){
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		return $this->my_set_cookie();
	}


	function my_set_cookie($cookie_data = NULL){
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		if ($this->sess_encrypt_cookie == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
		}

		$cookie_data .= hash_hmac('sha1', $cookie_data, $this->encryption_key);

		$expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

		// Set the cookie
		setcookie(
			$this->sess_cookie_name,
			$cookie_data,
			$expire,
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure
		);

		return array('name'=>$this->sess_cookie_name, 'data'=>$cookie_data, 'expire'=>$expire, 'path'=>$this->cookie_path, 'domain'=>$this->cookie_domain, 'secure'=>$this->cookie_secure);
	}
}