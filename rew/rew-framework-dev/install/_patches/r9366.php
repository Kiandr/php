<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// DB connection
$db = DB::get();

// Connection settings
$settings = DB::settings();

// Only run for 4.6+
if ((float)Settings::getInstance()->APP_VERSION < 4.6) {
	echo 'This patch is only needed for 4.6+' . PHP_EOL;
	return;
}

// Output
echo "Moving contents of images/ over to uploads/" . PHP_EOL;

// Copy contents of images over to uploads
$command .= 'if [ -e ~/httpdocs/images ]; then ';
$command .= 'rsync -avP --filter="exclude .svn" ~/httpdocs/images/ ~/httpdocs/uploads/; ';
$command .= 'else echo "~/httpdocs/images does not exist"; ';
$command .= "fi; ";
passthru($command, $error);
if (!empty($error)) throw new Exception ('Error copying images/ to uploads/');

// Output
echo "Removing images folder" . PHP_EOL;

// Remove images folder
$command = "rm -rf images/";
exec($command, $output, $error);
if (!empty($error)) throw new Exception ('Error occurred while cleaning up temp files');

// List of tables and columns for find/replace commands
$tables = array(
	'agents' => array(
		'columns' 	=> array('blog_profile','blog_signature','signature'),
	),
	'associates' => array(
		'columns' 	=> array('signature'),
	),
	'auto_responders' => array(
		'columns' 	=> array('document'),
	),
	'blog_entries' => array(
		'columns' 	=> array('body'),
	),
	'default_info' => array(
		'columns' 	=> array('footer', 'category_html'),
	),
	'directory_listings' => array(
		'columns' 	=> array('description'),
	),
	'docs' => array(
		'columns' 	=> array('document'),
	),
	'docs_templates' => array(
		'columns' 	=> array('template'),
	),
	'featured_communities' => array(
		'columns' 	=> array('description'),
	),
	'flyers' => array(
		'columns' 	=> array('html'),
	),
	'pages' => array(
		'columns' 	=> array('footer','category_html'),
	),
	'snippets' => array(
		'columns' 	=> array('code'),
	),
	'testimonials' => array(
		'columns' 	=> array('testimonial'),
	),
);

// Find/replace values
$searches = array(
	array(
		'find' 		=> '="/images/',
		'replace' 	=> '="/uploads/'
	),
	array(
		'find' 		=> '=\'/images/',
		'replace' 	=> '=\'/uploads/'
	),
);

// Loop through each search
foreach ($searches as $search) {
	
	// Output
	echo "Changing " . $search['find'] . " to " . $search['replace'] . PHP_EOL;
	
	// Loop through each table
	foreach ($tables as $key => $value) {
		
		// Loop through each column
		foreach ($value['columns'] as $column) {
			
			try {
				
				// Output
				echo "\tUpdating " . $key . "." . $column . PHP_EOL;
				
				// Update database values
				$sql = "UPDATE "  . $key . " SET "  . $column . " = REPLACE("  . $column . ", :find, :replace)";
				$query = $db->prepare($sql);
				$query->execute(array(':find' => $search['find'], ':replace' => $search['replace']));
				
			// Report database error
			} catch (PDOException $e) {
				echo "\t\tQuery Error: " . $e->getMessage() . PHP_EOL;
			}
			
		}
	}

}
