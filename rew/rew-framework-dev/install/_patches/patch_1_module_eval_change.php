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
	$addEnumValue = 'old-module';

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

// Output
echo 'Updating modules to old module type: ';

try {

	// Execute the query
	$db->query('UPDATE `snippets` SET `type` = \'old-module\' WHERE `type` = \'module\' AND `code` LIKE \'<?php%\';');

	// All is well!
	echo 'Success' . PHP_EOL;

} catch (PDOException $e)  {

	// Update failed
	echo $e->getMessage() . PHP_EOL;

}
