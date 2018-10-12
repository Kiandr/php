<?php

/**
 * @deprecated
 */
function tpl_format($string, $format = null)
{
    if ($format === 'us_currency') {
        return '$' . Format::number($string);
    } elseif ($format === 'number_format') {
        return Format::number($string);
    } elseif ($format === 'number_format2') {
        return Format::number($string, 2);
    } elseif ($format === 'date_format') {
        if (empty($string) || substr($string, 0, strlen('0000-00-00')) == '0000-00-00') {
            return '';
        }
        return date('F jS, Y', strtotime($string));
    } elseif ($format === 'enum_YN') {
        return ($string == 'Y') ? 'Yes' : '';
    }
    return $string;
}
