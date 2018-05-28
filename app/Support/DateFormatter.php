<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

use DateInterval;
use DateTime;

class DateFormatter
{
    /**
     * Returns an array of date format options.
     *
     * @return array
     */
    static function formats()
    {
        return [
            'm/d/Y' => [
                'setting'    => 'm/d/Y',
                'datepicker' => 'mm/dd/yyyy',
            ],
            'm-d-Y' => [
                'setting'    => 'm-d-Y',
                'datepicker' => 'mm-dd-yyyy',
            ],
            'm.d.Y' => [
                'setting'    => 'm.d.Y',
                'datepicker' => 'mm.dd.yyyy',
            ],
            'Y/m/d' => [
                'setting'    => 'Y/m/d',
                'datepicker' => 'yyyy/mm/dd',
            ],
            'Y-m-d' => [
                'setting'    => 'Y-m-d',
                'datepicker' => 'yyyy-mm-dd',
            ],
            'Y.m.d' => [
                'setting'    => 'Y.m.d',
                'datepicker' => 'yyyy.mm.dd',
            ],
            'd/m/Y' => [
                'setting'    => 'd/m/Y',
                'datepicker' => 'dd/mm/yyyy',
            ],
            'd-m-Y' => [
                'setting'    => 'd-m-Y',
                'datepicker' => 'dd-mm-yyyy',
            ],
            'd.m.Y' => [
                'setting'    => 'd.m.Y',
                'datepicker' => 'dd.mm.yyyy',
            ],
        ];
    }

    /**
     * Returns a flattened version of the format() method array to display as dropdown options.
     *
     * @return array
     */
    public static function dropdownArray()
    {
        $formats = self::formats();

        $return = [];

        foreach ($formats as $format)
        {
            $return[$format['setting']] = $format['setting'];
        }

        return $return;
    }

    /**
     * Converts a stored date to the user formatted date.
     *
     * @param string $date The yyyy-mm-dd standardized date
     * @param bool $includeTime Whether or not to include the time
     * @return string             The user formatted date
     */
    public static function format($date = null, $includeTime = false)
    {
        $date = new DateTime($date);

        if (!$includeTime)
        {
            return $date->format(config('fi.dateFormat'));
        }

        return $date->format(config('fi.dateFormat') . (!config('fi.use24HourTimeFormat') ? ' g:i A' : ' H:i'));
    }

    /**
     * Converts a user submitted date back to standard yyyy-mm-dd format.
     *
     * @param  string $userDate The user submitted date
     * @return string             The yyyy-mm-dd standardized date
     */
    public static function unformat($userDate = null)
    {
        if ($userDate)
        {
            $date = DateTime::createFromFormat(config('fi.dateFormat'), $userDate);

            return $date->format('Y-m-d');
        }

        return null;
    }

    /**
     * Adds a specified number of days to a yyyy-mm-dd formatted date.
     *
     * @param  string $date The date
     * @param  int $numDays The number of days to increment
     * @return string The yyyy-mm-dd standardized incremented date
     */
    public static function incrementDateByDays($date, $numDays)
    {
        $date = DateTime::createFromFormat('Y-m-d', $date);

        $date->add(new DateInterval('P' . $numDays . 'D'));

        return $date->format('Y-m-d');
    }

    /**
     * Adds a specified number of periods to a yyyy-mm-dd formatted date.
     *
     * @param  date $date The date
     * @param  int $period 1 = Days, 2 = Weeks, 3 = Months, 4 = Years
     * @param  int $numPeriods The number of periods to increment
     * @return string The yyyy-mm-dd standardized incremented date
     */
    public static function incrementDate($date, $period, $numPeriods)
    {
        $date = DateTime::createFromFormat('Y-m-d', $date);

        switch ($period)
        {
            case 1:
                $date->add(new DateInterval('P' . $numPeriods . 'D'));
                break;
            case 2:
                $date->add(new DateInterval('P' . $numPeriods . 'W'));
                break;
            case 3:
                $date->add(new DateInterval('P' . $numPeriods . 'M'));
                break;
            case 4:
                $date->add(new DateInterval('P' . $numPeriods . 'Y'));
                break;
        }

        return $date->format('Y-m-d');
    }

    /**
     * Returns the short name of the month from a numeric representation.
     *
     * @param  int $monthNumber
     * @return string
     */
    public static function getMonthShortName($monthNumber)
    {
        return date('M', mktime(0, 0, 0, $monthNumber, 1, date('Y')));
    }

    /**
     * Returns the format required to initialize the datepicker.
     *
     * @return string
     */
    public static function getDatepickerFormat()
    {
        $formats = self::formats();

        return $formats[config('fi.dateFormat')]['datepicker'];
    }
}