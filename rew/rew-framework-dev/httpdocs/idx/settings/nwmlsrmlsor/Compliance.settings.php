<?php
$_COMPLIANCE['commingled_feed_name'] = 'nwmlsrmlsor';
$_COMPLIANCE['feeds'] = array('nwmls','rmlsor');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Display Update Time
	$_COMPLIANCE['update_time'] = true;

}
