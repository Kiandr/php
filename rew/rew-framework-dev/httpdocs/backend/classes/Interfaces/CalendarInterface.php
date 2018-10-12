<?php

namespace REW\Backend\Interfaces;

interface CalendarInterface
{

    /**
     * Number of seconds in an hour
     * @var integer
     */
    const SECONDS_IN_AN_HOUR = 3600;

    /**
     * Number of hours in a day
     * @var integer
     */
    const HOURS_IN_A_DAY = 24;

    /**
     * Number of days in a week
     * @var integer
     */
    const DAYS_IN_A_WEEK = 7;

    /**
     * Days of the week titles
     * @var array
     */
    const DAYS_OF_THE_WEEK = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    /**
     * Days of the week titles in abbreviated form
     * @var array
     */
    const ABBRV_DAYS_OF_THE_WEEK = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    /**
     * Set the calendar date according to the given date Y-m-d
     * @param string $date
     */
    public function setDate($date);

    /**
     * Returns the currently set date in the following format Y-m-d
     * @return string
     */
    public function getDate();

    /**
     * Returns the currently set year
     * @return int
     */
    public function getYear();

    /**
     * Returns the currently set month
     * @return int
     */
    public function getMonth();

    /**
     * Returns the currently set day
     * @return int
     */
    public function getDay();


    /**
     * Get Last Month
     *
     * @return string Y-m
     */
    public function lastMonth();

    /**
     * Returns the current month
     * @return string Y-m
     */
    public function currentMonth();

    /**
     * Get Next Month
     *
     * @return string Y-m
     */
    public function nextMonth();

    /**
     * Returns the last calendar day
     * @return string
     */
    public function lastDay();

    /**
     * Returns the next calendar day
     * @return string
     */
    public function nextDay();

    /**
     * Returns the number of days in this month
     * @return int
     */
    public function getDays();

    /**
     * Returns an associative array of the first day of the current month's date and time.
     * @return array
     */
    public function getDateOfFirst();
}
