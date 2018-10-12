<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('details','brochure'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy;2004-' . date("Y") . ' Charlottesville Area Association of Realtors&reg;. All rights reserved. Information deemed to be reliable but not guaranteed. The data relating to real estate for sale on this website comes in part from the IDX Program of Charlottesville Area Association of Realtors&reg;. Listing broker has attempted to offer accurate data, but buyers are advised to confirm all items. Any use of search facilities of data on this site other than by a consumer interested in the purchase of real estate, is prohibited. Information last updated on <?=date(\'n/j/y g:i A T\', strtotime($last_updated)); ?>.</p>';

}

$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details','brochure'));
