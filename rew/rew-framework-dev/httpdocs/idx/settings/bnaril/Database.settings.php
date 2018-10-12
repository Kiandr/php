<?php

	// Database Colletion
	$DATABASE = array();

	// IDX Database Connection
	$CONNECTION = array();
	$CONNECTION['settings']['type'] = 'MySQLImproved';
	$CONNECTION['settings']['host'] = 'idxdb-bnaril.gce.rewhosting.com';
	$CONNECTION['settings']['user'] = 'idx_bnaril';
	$CONNECTION['settings']['pass'] = '4ebrukuqapanujaD';
	$CONNECTION['settings']['db']   = 'rewidx_bnaril';

	// Add to Database Collection
	array_push($DATABASE, array('name' => 'bnaril', 'settings' => $CONNECTION['settings']));

	// Clear Memory
	unset($CONNECTION);

?>
