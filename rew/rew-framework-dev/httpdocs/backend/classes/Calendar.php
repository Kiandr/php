<?php

namespace REW\Backend;

use REW\Backend\Interfaces\CalendarInterface;

/**
 * Calendar
 *
 */
class Calendar implements CalendarInterface
{

    /**
     * @var int Calendar Year
     */
    private $year;

    /**
     * @var int Calendar Month
     */
    private $month;

    /**
     * @var int Calendar Day
     */
    private $day;

    /**
     * Setup Calendar
     *
     * @param int $timestamp UNIX Timestamp
     * @return void
     */
    public function __construct($options = [])
    {
        $timestamp = isset($options['timestamp']) ? $options['timestamp'] : time();
        $this->day   = date('d', $timestamp);
        $this->year  = date('Y', $timestamp);
        $this->month = date('m', $timestamp);
    }

    /**
     * Set the calendar date according to the given date Y-m-d
     * @param string $date
     */
    public function setDate($date)
    {
        list($year, $month, $day) = explode('-', $date);
        $this->day   = $day;
        $this->year  = $year;
        $this->month = $month;
    }

    /**
     * Returns the currently set date in the following format Y-m-d
     * @return string
     */
    public function getDate()
    {
        return $this->year . '-' . $this->month . '-' . $this->day;
    }

    /**
     * Returns the currently set year
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Returns the currently set month
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Returns the currently set day
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Get Last Month
     *
     * @return string Y-m
     */
    public function lastMonth()
    {
        $year = $this->year;
        $last_month = $this->month - 1;
        if ($last_month <= 0) {
            $last_month = $last_month + 12;
            $year--;
        }
        $last_month = str_pad(intval($last_month), 2, '0', STR_PAD_LEFT);
        return $year . '-' . $last_month;
    }

    /**
     * Returns the current month
     * @return string Y-m
     */
    public function currentMonth()
    {
        return $this->year . '-' . $this->month;
    }

    /**
     * Get Next Month
     *
     * @return string Y-m
     */
    public function nextMonth()
    {
        $year = $this->year;
        $next_month = $this->month + 1;
        if ($next_month > 12) {
            $next_month = $next_month - 12;
            $year++;
        }
        $next_month = str_pad(intval($next_month), 2, '0', STR_PAD_LEFT);
        return $year . '-' . $next_month;
    }

    /**
     * Returns the last calendar day
     * @return string
     */
    public function lastDay()
    {
        return date('Y-m-d', mktime(0, 0, 0, $this->month, $this->day - 1, $this->year));
    }


    /**
     * Returns the next calendar day
     * @return string
     */
    public function nextDay()
    {
        return date('Y-m-d', mktime(0, 0, 0, $this->month, $this->day + 1, $this->year));
    }

    /**
     * Returns the number of days in this month
     * @return int
     */
    public function getDays()
    {
        return date('t', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    /**
     * Returns an associative array of the first day of the current month's date and time.
     * @return array
     */
    public function getdateOfFirst()
    {
        return getdate(mktime(0, 0, 0, $this->month, 1, $this->year));
    }
}
