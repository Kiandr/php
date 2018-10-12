<?php
// Here you can initialize variables that will be available to your tests

function __($text)
{
    if (empty($text)) {

        return '';
    }

    return gettext($text);
}
