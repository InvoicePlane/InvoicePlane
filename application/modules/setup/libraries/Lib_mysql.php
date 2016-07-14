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
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Lib_mysql
{
    function connect($server, $username, $password)
    {
        if (!$server or !$username) {
            return false;
        }

        if (@mysqli_connect($server, $username, $password)) {
            return mysqli_connect($server, $username, $password);
        }

        return false;
    }

    function select_db($link, $database)
    {
        if (@mysqli_select_db($link, $database)) {
            return true;
        }

        return false;
    }

    function query($link, $sql)
    {
        $result = mysqli_query($link, $sql);

        return mysqli_fetch_object($result);
    }

}
