<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Output
echo 'Updating `snippets`.`type`: ';

// DB connection
$db = DB::get('cms');

// Find `history_events`.`type` column info
$field = $db->fetch("SHOW COLUMNS FROM `snippets` WHERE `Field` = 'type';");
if (!empty($field)) {

	// ENUM value to add
	$addEnumValue = 'bdx';

	// ENUM values
	$enumValues = rtrim(ltrim($field['Type'], 'enum('), ')');
	$enumValues = array_map(function ($enumValue) {
		return trim($enumValue, '\'');
	}, explode(',', $enumValues));

	// ENUM value already exists
	if (in_array($addEnumValue, $enumValues)) {
		echo 'Already Done' . PHP_EOL;

	} else {

		// Add new ENUM value
		$enumValues[] = $addEnumValue;

		// Execute ALTER query
		$query = "ALTER TABLE `snippets` CHANGE `type` `type` ENUM('" . implode("', '", $enumValues) . "');";
		$db->query($query);

		// All is well!
		echo 'Success' . PHP_EOL;

	}

} else {

    // Missing database column
    throw new Exception ('Database column could not be found: `snippets`.`type`');

}