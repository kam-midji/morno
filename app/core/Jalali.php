<?php

/**
 * Jalali (Shamsi) DateTime Class. Supports years higher than 2038.
 *
 * Copyright (c) 2012 Sallar Kaboli <sallar.kaboli@gmail.com>
 * http://sallar.me
 *
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * 1- The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * 2- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Original Jalali to Gregorian (and vice versa) converter:
 * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
 *
 * List of supported timezones can be found here:
 * http://www.php.net/manual/en/timezones.php
 *
 * @package    jDateTime
 * @author     Sallar Kaboli <sallar.kaboli@gmail.com>
 * @author     Omid Pilevar <omid.pixel@gmail.com>
 * @copyright  2003-2025 Sallar Kaboli
 * @license    http://opensource.org/licenses/mit-license.php The MIT License
 * @link       https://github.com/sallar/jDateTime
 * @see        DateTime
 * @version    2.3.0
 */
class jDateTime
{
    /**
     * Defaults
     */
    private static bool $jalali = true; // Use Jalali Date, If set to false, falls back to gregorian
    private static bool $convert = true; // Convert numbers to Farsi characters in utf-8
    private static ?string $timezone = null; // Timezone String e.g Asia/Tehran, Defaults to Server Timezone Settings
    private static array $temp = [];

    /**
     * jDateTime::Constructor
     *
     * Pass these parameters when creating a new instance
     * of this Class, and they will be used as defaults.
     * e.g $obj = new jDateTime(false, true, 'Asia/Tehran');
     * To use system defaults pass null for each one or just
     * create the object without any parameters.
     *
     * @author Sallar Kaboli
     * @param bool|null $convert Converts numbers to Farsi
     * @param bool|null $jalali Converts date to Jalali
     * @param string|null $timezone Timezone string
     */
    public function __construct(bool $convert = null, bool $jalali = null, string $timezone = null)
    {
        if ($jalali !== null) {
            self::$jalali = (bool)$jalali;
        }
        if ($convert !== null) {
            self::$convert = (bool)$convert;
        }
        if ($timezone !== null) {
            self::$timezone = $timezone;
        }
    }

    /**
     * Convert a formatted string from Gregorian Calendar to Jalali Calendar.
     * This will be useful to directly convert time strings coming from databases.
     * Example:
     *
     * // Suppose this comes from database
     * $a = '2016-02-14 14:20:38';
     * $date = \jDateTime::convertFormatToFormat('Y-m-d H:i:s', 'Y-m-d H:i:s', $a);
     * // $date will now be '۱۳۹۴-۱۱-۲۵ ۱۴:۲۰:۳۸'
     *
     * @author Vahid Fazlollahzade
     * @param string $jalaliFormat Return format. Same as static::date(...)
     * @param string $georgianFormat The format of $timeString. See php.net/date
     * @param string $timeString The time itself, formatted as $georgianFormat
     * @param null|DateTimeZone|string $timezone The timezone. Same as static::date(...)
     * @return string
     * @throws \Exception
     */
    public static function convertFormatToFormat(string $jalaliFormat, string $georgianFormat, string $timeString, $timezone = null): string
    {
        // Normalize $timezone, take from static::date(...)
        $timezone = ($timezone != null) ? $timezone : (self::$timezone ?? date_default_timezone_get());
        if (is_string($timezone)) {
            $timezone = new \DateTimeZone($timezone);
        } elseif (!$timezone instanceof \DateTimeZone) {
            throw new \RuntimeException('Provided timezone is not correct.');
        }

        // Convert to timestamp, then to Jalali
        $datetime = \DateTime::createFromFormat($georgianFormat, $timeString, $timezone);
        return static::date($jalaliFormat, $datetime->getTimestamp(), null, null, $timezone);
    }

    /**
     * jDateTime::Date
     *
     * Formats and returns given timestamp just like php's
     * built in date() function.
     * e.g:
     * $obj->date("Y-m-d H:i", time());
     * $obj->date("Y-m-d", time(), false, false, 'America/New_York');
     *
     * @author Sallar Kaboli
     * @param string $format Accepts format string based on: php.net/date
     * @param int|false $stamp Unix Timestamp (Epoch Time)
     * @param bool|null $convert (Optional) forces convert action. pass null to use system default
     * @param bool|null $jalali (Optional) forces jalali conversion. pass null to use system default
     * @param string|null $timezone (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     * @throws \Exception
     */
    public static function date(string $format, $stamp = false, bool $convert = null, bool $jalali = null, string $timezone = null): string
    {
        // Timestamp + Timezone
        $stamp = ($stamp !== false) ? $stamp : time();
        $timezone = ($timezone != null) ? $timezone : (self::$timezone ?? date_default_timezone_get());
        $obj = new DateTime('@' . $stamp);
        $obj->setTimezone(new DateTimeZone($timezone));

        if ((self::$jalali === false && $jalali === null) || $jalali === false) {
            return $obj->format($format);
        }

        // Find what to replace
        $chars = (preg_match_all('/([a-zA-Z]{1})/', $format, $matches)) ? $matches[0] : [];

        // Intact Keys
        $intact = ['B', 'h', 'H', 'g', 'G', 'i', 's', 'I', 'U', 'u', 'Z', 'O', 'P'];
        $intact = self::filterArray($chars, $intact);
        $intactValues = [];
        foreach ($intact as $k => $v) {
            $intactValues[$k] = $obj->format($v);
        }

        // Changed Keys
        [$year, $month, $day] = [$obj->format('Y'), $obj->format('n'), $obj->format('j')];
        [$jyear, $jmonth, $jday] = self::toJalali($year, $month, $day);

        $keys = ['d', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 't', 'L', 'o', 'Y', 'y', 'a', 'A', 'c', 'r', 'e', 'T'];
        $keys = self::filterArray($chars, $keys, ['z']);
        $values = [];

        foreach ($keys as $k => $key) {
            $v = '';
            switch ($key) {
                // Day
                case 'd':
                    $v = sprintf('%02d', $jday);
                    break;
                case 'D':
                    $v = self::getDayNames($obj->format('D'), true);
                    break;
                case 'j':
                    $v = $jday;
                    break;
                case 'l':
                    $v = self::getDayNames($obj->format('l'));
                    break;
                case 'N':
                    $v = self::getDayNames($obj->format('l'), false, 1, true);
                    break;
                case 'S':
                    $v = 'ام';
                    break;
                case 'w':
                    $v = self::getDayNames($obj->format('l'), false, 1, true) - 1;
                    break;
                case 'z':
                    if ($jmonth > 6) {
                        $v = 186 + (($jmonth - 7) * 30) + $jday;
                    } else {
                        $v = (($jmonth - 1) * 31) + $jday;
                    }
                    self::$temp['z'] = $v;
                    break;
                // Week
                case 'W':
                    $v = is_int(self::$temp['z'] / 7) ? (self::$temp['z'] / 7) : intval(self::$temp['z'] / 7 + 1);
                    break;
                // Month
                case 'F':
                    $v = self::getMonthNames($jmonth);
                    break;
                case 'm':
                    $v = sprintf('%02d', $jmonth);
                    break;
                case 'M':
                    $v = self::getMonthNames($jmonth, true);
                    break;
                case 'n':
                    $v = $jmonth;
                    break;
                case 't':
                    if ($jmonth >= 1 && $jmonth <= 6) {
                        $v = 31;
                    } elseif ($jmonth >= 7 && $jmonth <= 11) {
                        $v = 30;
                    } elseif ($jmonth == 12) {
                        $v = self::isLeapJalaliYear($jyear) ? 30 : 29;
                    }
                    break;
                // Year
                case 'L':
                    $v = self::isLeapJalaliYear($jyear) ? '1' : '0';
                    break;
                case 'o':
                case 'Y':
                    $v = $jyear;
                    break;
                case 'y':
                    $v = $jyear % 100;
                    break;
                // Time
                case 'a':
                    $v = ($obj->format('a') == 'am') ? 'ق.ظ' : 'ب.ظ';
                    break;
                case 'A':
                    $v = ($obj->format('A') == 'AM') ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;
                // Full Dates
                case 'c':
                    $v = $jyear . '-' . sprintf('%02d', $jmonth) . '-' . sprintf('%02d', $jday) . 'T';
                    $v .= $obj->format('H') . ':' . $obj->format('i') . ':' . $obj->format('s') . $obj->format('P');
                    break;
                case 'r':
                    $v = self::getDayNames($obj->format('D'), true) . ', ' . sprintf('%02d', $jday) . ' ' . self::getMonthNames($jmonth, true);
                    $v .= ' ' . $jyear . ' ' . $obj->format('H') . ':' . $obj->format('i') . ':' . $obj->format('s') . ' ' . $obj->format('P');
                    break;
                // Timezone
                case 'e':
                    $v = $obj->format('e');
                    break;
                case 'T':
                    $v = $obj->format('T');
                    break;
            }
            $values[$k] = $v;
        }

        // Merge
        $keys = array_merge($intact, $keys);
        $values = array_merge($intactValues, $values);

        // Return
        $ret = strtr($format, array_combine($keys, $values));

        $shouldConvert = $convert === true || ($convert === null && self::$convert === true);
        $isJalali = $jalali === true || ($jalali === null && self::$jalali === true);

        return ($shouldConvert && $isJalali) ? self::convertNumbers($ret) : $ret;
    }

    /**
     * jDateTime::gDate
     *
     * Same as jDateTime::Date method but this one works as a helper and returns Gregorian Date
     * in case someone doesn't like to pass all those false arguments to Date method.
     *
     * e.g. $obj->gDate("Y-m-d") //Outputs: 2011-05-05
     * $obj->date("Y-m-d", false, false, false); //Outputs: 2011-05-05
     * Both return the exact same result.
     *
     * @author Sallar Kaboli
     * @param string $format Accepts format string based on: php.net/date
     * @param int|false $stamp Unix Timestamp (Epoch Time)
     * @param string|null $timezone (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     * @throws \Exception
     */
    public static function gDate(string $format, $stamp = false, string $timezone = null): string
    {
        return self::date($format, $stamp, false, false, $timezone);
    }

    /**
     * jDateTime::Strftime
     *
     * Format a local time/date according to locale settings
     * built in strftime() function.
     * e.g:
     * $obj->strftime("%x %H", time());
     * $obj->strftime("%H", time(), false, false, 'America/New_York');
     *
     * @author Omid Pilevar
     * @param string $format Accepts format string based on: php.net/strftime
     * @param int|false $stamp Unix Timestamp (Epoch Time)
     * @param bool|null $convert (Optional) forces convert action. pass null to use system default
     * @param bool|null $jalali (Optional) forces jalali conversion. pass null to use system default
     * @param string|null $timezone (Optional) forces a different timezone. pass null to use system default
     * @return string Formatted input
     * @throws \Exception
     */
    public static function strftime(string $format, $stamp = false, bool $convert = null, bool $jalali = null, string $timezone = null): string
    {
        $str_format_code = [
            '%a', '%A', '%d', '%e', '%j', '%u', '%w',
            '%U', '%V', '%W',
            '%b', '%B', '%h', '%m',
            '%C', '%g', '%G', '%y', '%Y',
            '%H', '%I', '%l', '%M', '%p', '%P', '%r', '%R', '%S', '%T', '%X', '%z', '%Z',
            '%c', '%D', '%F', '%s', '%x',
            '%n', '%t', '%%'
        ];

        $date_format_code = [
            'D', 'l', 'd', 'j', 'z', 'N', 'w',
            'W', 'W', 'W',
            'M', 'F', 'M', 'm',
            'Y', 'y', 'Y', 'y', 'Y', // Approximations for C, g, G
            'H', 'h', 'g', 'i', 'A', 'a', 'h:i:s A', 'H:i', 's', 'H:i:s', 'H:i:s', 'O', 'T', // %X, %z, %Z
            'r', 'm/d/y', 'Y-m-d', 'U', 'm/d/y', // %c, %D, %F, %s, %x
            "\n", "\t", '%'
        ];

        // Change Strftime format to Date format
        $format = str_replace($str_format_code, $date_format_code, $format);

        // Convert to date
        return self::date($format, $stamp, $convert, $jalali, $timezone);
    }

    /**
     * jDateTime::Mktime
     *
     * Creates a Unix Timestamp (Epoch Time) based on given parameters
     * works like php's built in mktime() function.
     * e.g:
     * $time = $obj->mktime(0,0,0,2,10,1368);
     * $obj->date("Y-m-d", $time); //Format and Display
     *
     * @author Sallar Kaboli
     * @param int $hour Hour based on 24 hour system
     * @param int $minute Minutes
     * @param int $second Seconds
     * @param int $month Month Number
     * @param int $day Day Number
     * @param int $year Four-digit Year number eg. 1390
     * @param bool|null $jalali (Optional) pass false if you want to input gregorian time
     * @param string|null $timezone (Optional) accepts an optional timezone if you want one
     * @return int Unix Timestamp (Epoch Time)
     * @throws \Exception
     */
    public static function mktime(int $hour, int $minute, int $second, int $month, int $day, int $year, bool $jalali = null, string $timezone = null): int
    {
        // Defaults
        $month = ($month == 0) ? self::date('n') : $month;
        $day = ($day == 0) ? self::date('j') : $day;
        $year = ($year == 0) ? self::date('Y') : $year;

        // Convert to Gregorian if necessary
        if ($jalali === true || ($jalali === null && self::$jalali === true)) {
            [$year, $month, $day] = self::toGregorian($year, $month, $day);
        }

        // Create a new object and set the timezone if available
        $date = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day) . ' ' . $hour . ':' . $minute . ':' . $second;

        if (self::$timezone != null || $timezone != null) {
            $tz = ($timezone != null) ? $timezone : self::$timezone;
            $obj = new DateTime($date, new DateTimeZone($tz));
        } else {
            $obj = new DateTime($date);
        }

        return (int)$obj->format('U');
    }

    /**
     * jDateTime::Checkdate
     *
     * Checks the validity of the date formed by the arguments.
     * A date is considered valid if each parameter is properly defined.
     * works like php's built in checkdate() function.
     * Leap years are taken into consideration.
     * e.g:
     * $obj->checkdate(10, 21, 1390); // Return true
     * $obj->checkdate(9, 31, 1390);  // Return false
     *
     * @author Omid Pilevar
     * @param int $month The month is between 1 and 12 inclusive.
     * @param int $day The day is within the allowed number of days for the given month.
     * @param int $year The year is between 1 and 32767 inclusive.
     * @param bool|null $jalali (Optional) pass false if you want to input gregorian time
     * @return bool
     * @throws \Exception
     */
    public static function checkdate(int $month, int $day, int $year, bool $jalali = null): bool
    {
        // Defaults
        $month = ($month == 0) ? (int)self::date('n') : $month;
        $day = ($day == 0) ? (int)self::date('j') : $day;
        $year = ($year == 0) ? (int)self::date('Y') : $year;

        // Check if its jalali date
        if ($jalali === true || ($jalali === null && self::$jalali === true)) {
            $jMonths = [1 => 31, 2 => 31, 3 => 31, 4 => 31, 5 => 31, 6 => 31, 7 => 30, 8 => 30, 9 => 30, 10 => 30, 11 => 30, 12 => 29];
            if ($month < 1 || $month > 12 || $day < 1) {
                return false;
            }
            if ($month == 12 && self::isLeapJalaliYear($year)) {
                $daysInMonth = 30;
            } else {
                $daysInMonth = $jMonths[$month];
            }
            return $day <= $daysInMonth;
        } else { // Gregorian Date
            return checkdate($month, $day, $year);
        }
    }

    /**
     * jDateTime::getdate
     *
     * Like php built-in function, returns an associative array containing the date information of a timestamp,
     * or the current local time if no timestamp is given.
     *
     * @author Meysam Pour Ganji
     * @param int|null $timestamp The timestamp that would convert to date information array, if NULL passed, current timestamp will be processed.
     * @return array An associative array of information related to the timestamp.
     * @throws \Exception
     */
    public static function getdate($timestamp = null): array
    {
        $timestamp = $timestamp ?? time();

        if (is_string($timestamp)) {
            if (ctype_digit($timestamp) || ($timestamp[0] == '-' && ctype_digit(substr($timestamp, 1)))) {
                $timestamp = (int)$timestamp;
            } else {
                $timestamp = strtotime($timestamp);
            }
        }

        $dateString = self::date("s|i|G|j|w|n|Y|z|l|F", $timestamp);
        $dateArray = explode("|", $dateString);

        return [
            "seconds" => $dateArray[0],
            "minutes" => $dateArray[1],
            "hours" => $dateArray[2],
            "mday" => $dateArray[3],
            "wday" => $dateArray[4],
            "mon" => $dateArray[5],
            "year" => $dateArray[6],
            "yday" => $dateArray[7],
            "weekday" => $dateArray[8],
            "month" => $dateArray[9],
            0 => $timestamp
        ];
    }

    /**
     * System Helpers below
     * ------------------------------------------------------
     */

    /**
     * Determines if a given year is a leap year in the Jalali calendar.
     * Uses the Birashk algorithm.
     *
     * @param int $year
     * @return bool
     */
    private static function isLeapJalaliYear(int $year): bool
    {
        // The Birashk algorithm for determining leap years in the Jalali calendar.
        $remain = $year % 33;
        $leapYears = [1, 5, 9, 13, 17, 22, 26, 30];
        return in_array($remain, $leapYears);
    }

    /**
     * Filters out an array
     */
    private static function filterArray(array $needle, array $heystack, array $always = []): array
    {
        return array_intersect(array_merge($needle, $always), $heystack);
    }

    /**
     * Returns correct names for week days
     */
    private static function getDayNames(string $day, bool $shorten = false, int $len = 1, bool $numeric = false)
    {
        $days = [
            'sat' => [1, 'شنبه'],
            'sun' => [2, 'یکشنبه'],
            'mon' => [3, 'دوشنبه'],
            'tue' => [4, 'سه شنبه'],
            'wed' => [5, 'چهارشنبه'],
            'thu' => [6, 'پنجشنبه'],
            'fri' => [7, 'جمعه']
        ];

        $day = substr(strtolower($day), 0, 3);
        $day = $days[$day];

        return ($numeric) ? $day[0] : (($shorten) ? self::substr($day[1], 0, $len) : $day[1]);
    }

    /**
     * Returns correct names for months
     */
    private static function getMonthNames(int $month, bool $shorten = false, int $len = 3): string
    {
        $months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
        $ret = $months[$month - 1];
        return ($shorten) ? self::substr($ret, 0, $len) : $ret;
    }

    /**
     * Converts latin numbers to farsi script
     */
    private static function convertNumbers(string $matches): string
    {
        $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english_array, $farsi_array, $matches);
    }

    /**
     * Division
     */
    private static function div(int $a, int $b): int
    {
        return (int)($a / $b);
    }

    /**
     * Substring helper
     */
    private static function substr(string $str, int $start, int $len): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $len, 'UTF-8');
        }
        // Fallback for non-mbstring environments, might not be accurate for all characters.
        return substr($str, $start, $len * 2);
    }

    /**
     * Gregorian to Jalali Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     */
    public static function toJalali(int $g_y, int $g_m, int $g_d): array
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            $g_day_no++;
        }
        $g_day_no += $gd;
        $j_day_no = $g_day_no - 79;
        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;
        $jy = 979 + 33 * $j_np + 4 * self::div($j_day_no, 1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
            $j_day_no -= $j_days_in_month[$i];
        }

        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return [$jy, $jm, $jd];
    }

    /**
     * Jalali to Gregorian Conversion
     * Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
     */
    public static function toGregorian(int $j_y, int $j_m, int $j_d): array
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + self::div($jy, 33) * 8 + self::div($jy % 33 + 3, 4);
        for ($i = 0; $i < $jm; ++$i) {
            $j_day_no += $j_days_in_month[$i];
        }

        $j_day_no += $jd;
        $g_day_no = $j_day_no + 79;
        $gy = 1600 + 400 * self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;
        $leap = true;

        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * self::div($g_day_no, 36524);
            $g_day_no = $g_day_no % 36524;
            if ($g_day_no >= 365) {
                $g_day_no++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;
            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++) {
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        }

        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return [$gy, $gm, $gd];
    }
}
