<?php

//-- print out the associative array of the Environment Predefined Variable

print "<pre>";
print_r($_ENV);
print "</pre>";

//-- grab just the one environment variable (works even when environment predefined variables are turned off)
print getenv('APACHE_LOG_DIR');