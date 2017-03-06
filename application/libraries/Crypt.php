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
 * Class Crypt
 */
class Crypt
{
    public function salt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    public function generate_password($password, $salt)
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    public function check_password($hash, $password)
    {
        $new_hash = crypt($password, $hash);

        return ($hash == $new_hash);
    }

    public function encode($data){
      $CI = &get_instance();
      $CI->load->library('cryptor');

      $key = getenv('ENCRYPTION_KEY');
      if(preg_match("/^base64:(.*)$/", $key, $matches)){
        $key = base64_decode($matches[1]);
      }

      $encrypted = Cryptor::Encrypt($data, $key);
      return $encrypted;

    }

    public function decode($data){
      $CI = &get_instance();
      $CI->load->library('cryptor');

      $key = getenv('ENCRYPTION_KEY');
      if(preg_match("/^base64:(.*)$/", $key, $matches)){
        $key = base64_decode($matches[1]);
      }

      $decrypted = Cryptor::Decrypt($data, $key);
      return $decrypted;

    }
}
