<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

class Lib_mysql
{
    private $link = null;
    function connect($server, $username, $password)
    {
        if (!$server or !$username) {
            return false;
        }

        if ($this->link = @mysqli_connect($server, $username, $password)) {
            return true;
        }

        return false;
    }

    function select_db($database)
    {
        if ($this->link != null && @mysqli_select_db($this->link,$database)) {
            return true;
        }

        return false;
    }

    function query($sql)
    {
        if($this->link != null){
          $result = mysqli_query($this->link,$sql);
          return mysqli_fetch_object($result);
        }
        return null;
    }

}
