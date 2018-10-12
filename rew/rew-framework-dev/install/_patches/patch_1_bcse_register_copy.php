<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// DB connection
$db = DB::get();

// Only run for BCSE skin...
if (Skin::getDirectory() !== 'bcse') {
	echo 'This patch is only needed for bcse';
	return;
}

// Remove default registration page copy
$db->query("UPDATE `rewidx_system` SET `copy_register` = '';");