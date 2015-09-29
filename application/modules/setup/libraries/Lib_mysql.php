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
    function connect($server, $username, $password)
    {
        if (!$server or !$username) {
            return false;
        }

        if (@mysql_connect($server, $username, $password)) {
            return true;
        }

        return false;
    }

    function select_db($database)
    {
        if (@mysql_select_db($database)) {
            return true;
        }

        return false;
    }

    function query($sql)
    {
        $result = mysql_query($sql);

        return mysql_fetch_object($result);
    }

}
