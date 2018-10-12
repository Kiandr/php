<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Console output
$_title = 'Running Featured Communities Patch (' . basename(__FILE__) . ')';
echo str_repeat('#', strlen($_title) + 6);
echo PHP_EOL . '## ' . $_title . ' ##' . PHP_EOL;
echo str_repeat('#', strlen($_title) + 6);
echo PHP_EOL . PHP_EOL;

// DB connection
$db = DB::get();

// First.. let's check if this patch should be ran...
echo 'Checking for `featured_communities`.`search_criteria`:' . PHP_EOL . PHP_EOL;
$find_col = $db->query("SHOW COLUMNS FROM `featured_communities` LIKE 'search_criteria';");
if ($col = $find_col->fetchColumn()) {
	echo "\t" . 'Column already exists - DO NOT CONTINUE' . PHP_EOL;
	return;
} else {

	// Let's insert the new column we need
	$db->query("ALTER TABLE `featured_communities` ADD `search_criteria` LONGTEXT NULL DEFAULT NULL AFTER `search_idx`;");
	echo "\t" . 'New column added to database.' . PHP_EOL;

}
// Output
echo PHP_EOL . 'Updating Featured Communities:' . PHP_EOL . PHP_EOL;

// Update featurd communities search criteria
$communities = $db->fetchAll("SELECT `id`, `title`, `search_type`, `search_city`, `search_subdivision` FROM `featured_communities`;");
if (!empty($communities)) {
	$update = $db->prepare("UPDATE `featured_communities` SET `search_criteria` = :search_criteria WHERE `id` = :id;");
	foreach ($communities as $community) {

		// Search criteria
		$criteria = array_filter(array(
			'search_subdivision' => $community['search_subdivision'],
			'search_city' => $community['search_city'],
			'search_type' => $community['search_type']
		));

		// Update community
		$update->execute(array(
			'search_criteria' => serialize($criteria),
			'id' => $community['id']
		));

		// Output
		echo "\t" . '#' . $community['id'] . ': ' . $community['title'];
		if (!empty($criteria)) echo ' (' . implode(', ', $criteria) . ')';
		echo PHP_EOL;

	}
} else {
	echo "\t" . 'No Featured Communities Found' . PHP_EOL;

}

// Check for and remove old columns
echo PHP_EOL . 'Removing old columns: ' . PHP_EOL ;
$find_col = $db->prepare("SHOW COLUMNS FROM `featured_communities` LIKE :column;");
$columns = array('search_type', 'search_city', 'search_subdivision');
foreach ($columns as $column) {
	echo PHP_EOL . "\t" . $column . ': ';
	$find_col->execute(array('column' => $column));
	if ($col = $find_col->fetchColumn()) {
		$db->query("ALTER TABLE `featured_communities` DROP `" . $column . "`;");
		echo 'REMOVED';
	} else {
		echo 'NOT FOUND';
	}
}
echo PHP_EOL;