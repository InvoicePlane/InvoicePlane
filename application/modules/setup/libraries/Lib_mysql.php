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
 * Class Lib_mysql
 */
class Lib_mysql
{
    /**
     * @param $server
     * @param $username
     * @param $password
     * @return bool|mysqli
     */
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

    /**
     * @param $link
     * @param $database
     * @return bool
     */
    function select_db($link, $database)
    {
        if (@mysqli_select_db($link, $database)) {
            return true;
        }

        return false;
    }

    /**
     * @param $link
     * @param $sql
     * @return null|object
     */
    function query($link, $sql)
    {
        $result = mysqli_query($link, $sql);

        return mysqli_fetch_object($result);
    }

}
