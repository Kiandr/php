<?php

// Require Vendor autoloader
require_once __DIR__ . '/../../boot/app.php';

// Only perform for pt-2r
if (Skin::getDirectory() !== 'pt-2r') {
	echo 'This patch is only needed for pt-2r';
    return;
}

// DB connection
$db = DB::get('cms');

// Snippet name
$snippet_name = 'footer-links';

// Output
echo 'Processing #' . $snippet_name . '# update for pt-2r' . PHP_EOL;

// Check if snippet exists
$select = $db->prepare("SELECT `id` FROM `snippets` WHERE `name` = :name AND `agent` = 1;");
$select->execute(array('name' => $snippet_name));
$snippet = $select->fetch();
if (empty($snippet)) {

	// Get snippet content
	$snippet_file = 'install/pt-2r/_snippets/' . $snippet_name . '.txt';
	$snippet_code = file_exists($snippet_file) ? file_get_contents($snippet_file) : false;
	if (!empty($snippet_code)) {

		// Add snippet to database
		$insert = $db->prepare("INSERT INTO `snippets` SET `name` = :name, `code` = :code, `agent` = 1;");
		$insert->execute(array('name' => $snippet_name, 'code' => $snippet_code));

		// Success
	    echo 'Added #' . $snippet_name . '#' . PHP_EOL;

	 } else {

		// Error
		throw new Exception ('Snippet code not found: ' . $snippet_file);

	 }

} else {

    // Error
    throw new Exception ('#' . $snippet_name . '# snippet already exists');

}