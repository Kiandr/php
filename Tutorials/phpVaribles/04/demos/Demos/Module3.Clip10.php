<?php

//echo $_SERVER['REQUEST_TIME']; // just a bunch of numbers, time stamp

$timeStamp = $_SERVER['REQUEST_TIME']; //Onix Time Format
print date('Y-m-d H:i:s', $timeStamp);
