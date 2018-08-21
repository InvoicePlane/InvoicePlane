<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 |-----------------------------------------------------------------
 | InvoicePlane
 |
 | @author		InvoicePlane Developers & Contributors
 | @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 | @license		https://invoiceplane.com/license.txt
 | @link		https://invoiceplane.com
 |
 |-----------------------------------------------------------------
 | Sql helper functions
 |-----------------------------------------------------------------
 |
 | Add support functions for different database drivers. Most
 | functions listed are adding SQL function calls as string instead
 | of directly calling any function or running logic.
 | Sql helper functions have the name convention 
 |
 |   sql<FuncName>(<params>)
 |
 | Currently added support for db-drivers (ci3-names):
 |   - mysqli
 |   - postgre          for postgres 9.1+
 |
 */

/*
 |-----------------------------------------------------------------
 | SQL control functions and other wrappers
 |-----------------------------------------------------------------
 */

/**
 * Add SQL_CALC_FOUND_ROWS prefix depending on db driver.
 *
 * @param dbd   Database driver
 * @return Either 'SQL_CALC_FOUND_ROWS ' as string with trailing space for mysql or 
 *         an empty string when using any other database.
 */
function sqlCalcFoundRows($dbd = 'mysqli')
{
    // Use SQL_CALC_FOUND_ROWS only for mysqli
    return ($dbd != 'mysqli') ? '' : 'SQL_CALC_FOUND_ROWS ';
}


/**
 * Emulate the IfNull functionality, returning either the first value in case
 * it is not null or the second value if the first is null. The second value
 * might also be null. Equivalent to MySQLs IFNULL method.
 *
 * @param val1  First value that will be tested against null
 * @param val2  Second value
 * @param dbd   Database driver
 * @return First value if it is not null, otherwise second value. If second value
 *         if also null, null is returned.
 */
function sqlIfNull($val1, $val2, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "coalesce($val1, $val2)";
        default:
            /* default mysqli */
            break;
    }
    return "IFNULL($val1, $val2)";
}


/*
 |-----------------------------------------------------------------
 | SQL data types
 |-----------------------------------------------------------------
 */

/**
 * Get general boolean value from database.
 * Takes any sql value and tests against general known boolean types
 * to obtain a valid bool representation in php.
 *
 * @param val   The value that will be tested.
 * @return Boolean value representation
 */
function sqlToBool($val)
{
    return $val && (
        $val === 1      || 
        $val === '1'    || 
        $val === true   || 
        $val === 'true' || 
        $val === 't'
    );
}

/**
 * Get a null or zero date as string
 *
 * @param dbd   Database driver
 * @return In case of mysqli a zero date, otherwise NULL as string
 */
function sqlNullDate($dbd = 'mysqli')
{
    // Use non-standard zero-dates for MySQL
    if ($dbd == 'mysqli')
        return '0000-00-00';
    // Default is null
    return 'NULL';
}

/**
 * Get a null or zero time as string
 *
 * @param dbd   Database driver
 * @return In case of mysqli a zero time, otherwise NULL as string
 */
function sqlNullTime($dbd = 'mysqli')
{
    // Use non-standard zero-times for MySQL
    if ($dbd == 'mysqli')
        return '00:00:00';
    // Default is null
    return 'NULL';
}

/**
 * Get a null or zero datetime timestamp as string
 *
 * @param dbd   Database driver
 * @return In case of mysqli a zero timestamp, otherwise NULL as string
 */
function sqlNullDateTime($dbd = 'mysqli')
{
    // Use non-standard zero-datetimes for MySQL
    if ($dbd == 'mysqli')
        return '0000-00-00 00:00:00';
    // Default is null
    return 'NULL';
}


/*
 |-----------------------------------------------------------------
 | SQL string methods
 |-----------------------------------------------------------------
 */

/**
 * Concatenate two or more (non-)strings. Note that the first
 * param is the used database driver, the first concat value
 * is the second param.
 * 
 * @param dbd       Database driver
 * @param ... vals   Variadic list of values
 * @return String concatenating all input values
 */
function sqlConcat($dbd, ...$vals)
{
    $start  = 'CONCAT(';
    $end    = ')';
    $glue   = ',';
    
    switch($dbd) {
        case 'postgre':
            $start = '(';
            $glue = '||';
            break;
        default:
            /* default mysqli */
            break;
    }
    
    // Concatenate all and return
    return $start . implode($glue, $vals) . $end;
}


/*
 |-----------------------------------------------------------------
 | SQL date methods
 |-----------------------------------------------------------------
 */

/**
 * Call driver specific year function.
 *
 * @param val   Any datetime string that will be enclosed by the
 *              driver specific year function. 
 * @param dbd   Database driver
 * @return Sql string calling the year function with the passed
 *         parameter, e.g. "YEAR(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sqlYear($val, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "to_char($val, 'YYYY')";
        default:
            /* default mysqli */
            break;
    }
    return 'YEAR('.$val.')';
}


/**
 * Call driver specific month function.
 *
 * @param val   Any datetime string that will be enclosed by the
 *              driver specific month function. 
 * @param dbd   Database driver
 * @return Sql string calling the month function with the passed
 *         parameter, e.g. "MONTH(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sqlMonth($val, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "to_char($val, 'MM')";
        default:
            /* default mysqli */
            break;
    }
    return 'MONTH('.$val.')';
}


/**
 * Call driver specific quarter function.
 *
 * @param val   Any datetime string that will be enclosed by the
 *              driver specific quarter function. 
 * @param dbd   Database driver
 * @return Sql string calling the quarter function with the passed
 *         parameter, e.g. "QUARTER(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sqlQuarter($val, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "to_char($val, 'Q')";
        default:
            /* default mysqli */
            break;
    }
    return 'QUARTER('.$val.')';
}


/**
 * Get sql interval as string
 *
 * @param val   Interval value
 * @param dbd   Database driver
 *
 * @return Sql string for datetime interval as string. Result string 
 *         might contain single quotes for inner strings.
 */
function sqlDtInterval($val, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "INTERVAL '$val'";;
        default:
            /* default mysqli */
            break;
    }
    return "INTERVAL $val";
}


/**
 * Get interval from two dates
 *
 * @param dts   Datetime start value
 * @param dte   Datetime end value
 * @param dbd   Database driver
 *
 * @return Sql string for datetime interval as integer result
 */
function sqlDateDiff($dts, $dte, $dbd = 'mysqli')
{
    switch($dbd) {
        case 'postgre':
            return "(".$dts."::timestamp::date - ".$dte."::timestamp::date)";
        default:
            /* default mysqli */
            break;
    }
    return "DATEDIFF($dts, $dte)";
}
