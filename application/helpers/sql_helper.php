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
 |   sql_<func_name>(<params>)
 |
 | Currently added support for db-drivers (ci3-names):
 |   - mysqli
 |   - postgre          for postgres 9.1+
 |
 */

/**
 * Retrieve general database driver. The dbdriver
 * is cached within a global variable for quicker loading
 * in case .
 *
 * @return Default CI database driver
 */
function sql_db_driver()
{
    if (!array_key_exists('dbdriver', $GLOBALS)) {
        $CI = &get_instance();
        if (!$CI->db->dbdriver) {
            $CI->load->database();
        }
        $GLOBALS['dbdriver'] = $CI->db->dbdriver;
    }
    return $GLOBALS['dbdriver'];
}

/*
 |-----------------------------------------------------------------
 | SQL control & generic functions and other wrappers
 |-----------------------------------------------------------------
 */

/**
 * Add SQL_CALC_FOUND_ROWS prefix depending on db driver.
 *
 * @return Either 'SQL_CALC_FOUND_ROWS ' as string with trailing space for mysql or 
 *         an empty string when using any other database.
 */
function sql_calc_found_rows()
{
    // Use SQL_CALC_FOUND_ROWS only for mysqli
    return (sql_db_driver() != 'mysqli') ? '' : 'SQL_CALC_FOUND_ROWS ';
}


/**
 * Emulate the IfNull functionality, returning either the first value in case
 * it is not null or the second value if the first is null. The second value
 * might also be null. Equivalent to MySQLs IFNULL method.
 *
 * @param val1  First value that will be tested against null
 * @param val2  Second value
 * @return First value if it is not null, otherwise second value. If second value
 *         if also null, null is returned.
 */
function sql_if_null($val1, $val2)
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "coalesce($val1, $val2)";
        default:
            /* default mysqli */
            break;
    }
    return "IFNULL($val1, $val2)";
}


/**
 * Rename table
 *
 * @param old   Old table name
 * @param new   New table name
 * @return Alter table query as string without trailing semicolon.
 */
function sql_rename_table($old, $new)
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "ALTER TABLE $old RENAME TO $new";
        default:
            /* default mysqli */
            break;
    }
    return "RENAME TABLE `$old` TO `$new`";
}


/**
 * Get SQL quote char
 *
 * @return Single quote char used within driver to quote e.g. table names.
 *         Might also be an empty string.
 */
function sql_quote_char()
{
    switch(sql_db_driver()) {
        case 'postgre':
            return '';
        default:
            /* default mysqli */
            break;
    }
    return '`';
}


/*
 |-----------------------------------------------------------------
 | SQL data types
 |-----------------------------------------------------------------
 */

/**
 * Get SQL integer data type
 *
 * @return Integer datatype as string.
 */
function sql_integer()
{
    switch(sql_db_driver()) {
        case 'postgre':
            return 'INTEGER';
        default:
            /* default mysqli */
            break;
    }
    return 'INT';
}

/**
 * Get general boolean value from database.
 * Takes any sql value and tests against general known boolean types
 * to obtain a valid bool representation in php.
 *
 * @param val   The value that will be tested.
 * @return Boolean value representation
 */
function sql_to_bool($val)
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
 * Cast a date value to string type.
 *
 * @param val   The value that will be cast.
 * @return String calling the CAST method to get a string representation
 */
function sql_date_to_string($val)
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "CAST($val AS TEXT)";
        default:
            /* default mysqli */
            break;
    }
    return "CAST($val AS CHAR)";
}

/**
 * Get a special marked null or empty string as an empty date input
 * for form data. Null marked strings will be handled specially 
 * when inserting data into the database.
 *
 * @return 'NULL::NULL' strings for databases where NULL type should be 
 *         stored, empty string for e.g. MySQL.
 */
function sql_empty_date_input()
{
    // Use non-standard zero-dates for MySQL
    if (sql_db_driver() == 'mysqli')
        return '';
    // Default is null
    return 'NULL::NULL';
}

/**
 * Get a null or zero date as string
 *
 * @return In case of mysqli a zero date, otherwise NULL as string
 */
function sql_null_date()
{
    // Use non-standard zero-dates for MySQL
    if (sql_db_driver() == 'mysqli')
        return '0000-00-00';
    // Default is null
    return 'NULL';
}

/**
 * Get a null or zero time as string
 *
 * @return In case of mysqli a zero time, otherwise NULL as string
 */
function sql_null_time()
{
    // Use non-standard zero-times for MySQL
    if (sql_db_driver() == 'mysqli')
        return '00:00:00';
    // Default is null
    return 'NULL';
}

/**
 * Get a null or zero datetime timestamp as string
 *
 * @return In case of mysqli a zero timestamp, otherwise NULL as string
 */
function sql_null_date_time()
{
    // Use non-standard zero-datetimes for MySQL
    if (sql_db_driver() == 'mysqli')
        return '0000-00-00 00:00:00';
    // Default is null
    return 'NULL';
}


/**
 * Get primary key column constraint
 *
 * @return Primary key column constraint for create table statement
 */
function sql_primary_key()
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "SERIAL";
        default:
            /* default mysqli */
            break;
    }
    return 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT';
}


/*
 |-----------------------------------------------------------------
 | SQL string methods
 |-----------------------------------------------------------------
 */

/**
 * Concatenate two or more (non-)strings
 * 
 * @param ... vals   Variadic list of values
 * @return String concatenating all input values
 */
function sql_concat(...$vals)
{
    $start  = 'CONCAT(';
    $end    = ')';
    $glue   = ',';
    
    switch(sql_db_driver()) {
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
 * @return Sql string calling the year function with the passed
 *         parameter, e.g. "YEAR(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sql_year($val)
{
    switch(sql_db_driver()) {
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
 * @return Sql string calling the month function with the passed
 *         parameter, e.g. "MONTH(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sql_month($val)
{
    switch(sql_db_driver()) {
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
 * @return Sql string calling the quarter function with the passed
 *         parameter, e.g. "QUARTER(<val>)". Result string might 
 *         contain single quotes for inner strings.
 */
function sql_quarter($val)
{
    switch(sql_db_driver()) {
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
 *
 * @return Sql string for datetime interval as string. Result string 
 *         might contain single quotes for inner strings.
 */
function sql_dt_interval($val)
{
    switch(sql_db_driver()) {
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
 * @return Sql string for datetime interval as integer result
 */
function sql_date_diff($dts, $dte)
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "(".$dts."::timestamp::date - ".$dte."::timestamp::date)";
        default:
            /* default mysqli */
            break;
    }
    return "DATEDIFF($dts, $dte)";
}


/**
 * Subtract date / interval
 *
 * @param val1  Date, time, timestamp or interval value 1
 * @param val2  Date, time, timestamp or interval value 2
 * @return Sql string for subtracting value 1 from value 2
 */
function sql_date_subtract($val1, $val2)
{
    switch(sql_db_driver()) {
        case 'postgre':
            return "$val1 - $val2";
        default:
            /* default mysqli */
            break;
    }
    return "DATE_SUB($val1, $val2)";
}
