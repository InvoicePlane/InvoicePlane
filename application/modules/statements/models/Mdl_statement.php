<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Mdl_Statement
 *
 * (Currently) a non-persistent store of a current client statement request
 *
 */
class Mdl_Statement extends CI_Model
{
    private $statement_transactions;

    private $statement_start_date;
    private $statement_end_date;

    private $statement_date;

    private $opening_balance;

    private $statement_balance;

    private $statement_number;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getStatement_number()
    {
        return $this->statement_number;
    }

    /**
     * @return mixed
     */
    public function getStatement_transactions()
    {
        return $this->statement_transactions;
    }

    /**
     * @return mixed
     */
    public function getStatement_start_date()
    {
        return $this->statement_start_date;
    }

    /**
     * @return mixed
     */
    public function getStatement_end_date()
    {
        return $this->statement_end_date;
    }

    /**
     * @return mixed
     */
    public function getStatement_date()
    {
        return $this->statement_date;
    }

    /**
     * @return mixed
     */
    public function getOpening_balance()
    {
        return $this->opening_balance;
    }

    /**
     * @return mixed
     */
    public function getStatement_balance()
    {
        return $this->statement_balance;
    }

    /**
     * @param mixed $statement_number
     */
    public function setStatement_number($statement_number)
    {
        $this->statement_number = $statement_number;
    }

    /**
     * @param mixed $statement_transactions
     */
    public function setStatement_transactions($statement_transactions)
    {
        $this->statement_transactions = $statement_transactions;
    }

    /**
     * @param mixed $statement_start_date
     */
    public function setStatement_start_date($statement_start_date)
    {
        $this->statement_start_date = $statement_start_date;
    }

    /**
     * @param mixed $statement_end_date
     */
    public function setStatement_end_date($statement_end_date)
    {
        $this->statement_end_date = $statement_end_date;
    }

    /**
     * @param mixed $statement_date
     */
    public function setStatement_date($statement_date)
    {
        $this->statement_date = $statement_date;
    }

    /**
     * @param mixed $opening_balance
     */
    public function setOpening_balance($opening_balance)
    {
        $this->opening_balance = $opening_balance;
    }

    /**
     * @param mixed $statement_balance
     */
    public function setStatement_balance($statement_balance)
    {
        $this->statement_balance = $statement_balance;
    }
}
