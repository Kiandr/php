<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');
$_COMPLIANCE['disclaimer'][] = '<p>Listing information is provided by Participants'
. ' of the Great Plains Regional Multiple Listing Service Inc. IDX program'
. ' and is for consumers\' personal, non-commercial use and may not be used'
. ' for any purpose other than to identify prospective properties consumers'
. ' may be interested in purchasing; The information is deemed reliable but'
. ' is not guaranteed accurate.  Copyright, ' . date('Y') . ', Great Plains'
. ' Regional Multiple Listing Service, Inc. </p>';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));
