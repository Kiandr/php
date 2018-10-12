<?php

/**
 *
 * @param unknown_type $message
 * @param unknown_type $title
 */
function errorMsg($message, $title = 'Oops! There was an Error.')
{
    return '<div class="rewui message negative"><strong class="title">' . $title . '</strong><p>' . $message . '</p></div>';
}

/**
 * A function for making time periods readable
 *
 * @param       int     number of seconds elapsed
 * @param       bool    whether to show short version
 * @param       string  which time periods to display
 * @param       bool    whether to show zero time periods
 */
function tpl_date($seconds, $short = false, $use = null, $zeros = false)
{

    /* Short Label */
    if ($short) {
        // Define time periods
        $periods = array (
            'years'   => 31556926,
            'mths'    => 2629743,
            'wks'     => 604800,
            'days'    => 86400,
            'hours'   => 3600,
            'mins'    => 60,
            'secs'    => 1
        );

    /* Full Label */
    } else {
        // Define time periods
        $periods = array (
            'years'     => 31556926,
            'Months'    => 2629743,
            'weeks'     => 604800,
            'days'      => 86400,
            'hours'     => 3600,
            'minutes'   => 60,
            'seconds'   => 1
        );
    }

    // Break into periods
    $seconds = (float) $seconds;
    $segments = array();
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[strtolower($period)] = $count;
        $seconds = $seconds % $value;
    }

    // Build the string
    $string = array();
    foreach ($segments as $key => $value) {
        $segment_name = substr($key, 0, -1);
        $segment = $value . ' ' . $segment_name;
        if ($value != 1) {
            $segment .= 's';
        }
        $string[] = $segment;
    }

    if ($short) {
        $string = array_slice($string, 0, 1);
    }

    return implode(', ', $string);
}
