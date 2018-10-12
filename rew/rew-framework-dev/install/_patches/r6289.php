<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

$skins = array('lec-2010', 'lec-2011', 'lec-2013', 'pt-1r', 'pt-2r', 'pt-3r');

// Only perform if above skin
if(!in_array(Skin::getDirectory(), $skins)) {
	echo 'This patch is only needed for ' . implode(', ', $skins) . '.';
    return;
}

// Database Connection
$db = DB::get('cms');

// social-icons.txt
if(Skin::getDirectory() == 'lec-2010') {

	echo 'Processing #social-share# update for lec-2010' . PHP_EOL;
	
	// Check if #social-icons# Snippet Exists
	$snippet = $db->fetch("SELECT `id`, `code` FROM `snippets` WHERE `name` = 'social-icons' AND `agent` = 1;");
	if (!empty($snippet)) {
		
		// Make sure there isn't already a #social-share# snippet
		$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
		if(empty($social_share)){
			
			// Update snippet content remove "fc-" from webicon
			$snippet['code'] = str_replace('fc-webicon', 'webicon', $snippet['code']);
				    	
		    // Update snippet name and content
		    $statement = $db->prepare('UPDATE `snippets` SET `name` = :name, `code` = :code WHERE `id` = :id');
		    $statement->execute( array(':name' => 'social-share', ':code' => $snippet['code'], ':id' => $snippet['id']));
		    
		    // Success
		    echo 'Renamed and updated #social-icons# to #social-share#' . PHP_EOL;
		    
		} else {
		
		    // Error
		    throw new Exception ('#social-share# snippet already exists. Could not rename #social-icons# to #social-share#');
		}
	
	} else {
	
	    // Error
	    throw new Exception ('The #social-icons# snippet does not exist on this website. Renaming #social-icons# to #social-share# failed!');
	}
	
}

// lec-header.txt
if(Skin::getDirectory() == 'lec-2011') {

	echo 'Processing #social-share# update for lec-2011' . PHP_EOL;
	
	// Check if #lec-header# Snippet Exists
	$snippet = $db->fetch("SELECT `id`, `code` FROM `snippets` WHERE `name` = 'lec-header' AND `agent` = 1;");
	if (!empty($snippet)) {
		
		// Make sure there isn't already a #social-share# snippet
		$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
		if(empty($social_share)){
			
			// Get #social-share# content
			$file = 'install/lec-2011/_snippets/social-share.txt';
			if(file_exists($file)) $code = file_get_contents($file);
			
			if(!empty($code)){
		
			    // Update snippet name and content
			    $statement = $db->prepare('UPDATE `snippets` SET `name` = :name, `code` = :code WHERE `id` = :id');
			    $statement->execute( array(':name' => 'social-share', ':code' => $code, ':id' => $snippet['id']));
			
			    // Success
			    echo 'Renamed and updated content of #social-icons# to #social-share#' . PHP_EOL;
		    
		    } else {
			    
			    // Error
			    throw new Exception ('New snippet not found: ' . $file);
			    
		    }
		    
		} else {
		
		    // Error
		    throw new Exception ('#social-share# snippet already exists. Could not rename #lec-header# to #social-share#');
		}
	
	} else {
	
	    // Error
	    throw new Exception ('The #lec-header# snippet does not exist on this website. Renaming #lec-header# to #social-share# failed!');
	}
	
}

// lec-sidebar.txt
if(Skin::getDirectory() == 'lec-2013') {

	echo 'Processing #social-share# update for lec-2013' . PHP_EOL;

	// Check if #lec-sidebar# Snippet Exists
	$snippet = $db->fetch("SELECT `id`, `code` FROM `snippets` WHERE `name` = 'lec-sidebar' AND `agent` = 1;");
	if (!empty($snippet)) {
	
		// Make sure there isn't already a #social-share# snippet
		$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
		if(empty($social_share)){
			
			// Update snippet content remove "fc-" from webicon
			$snippet['code'] = str_replace('fc-webicon', 'webicon', $snippet['code']);
		    
			// Update snippet name and content
		    $statement = $db->prepare('UPDATE `snippets` SET `name` = :name, `code` = :code WHERE `id` = :id');
		    $statement->execute( array(':name' => 'social-share', ':code' => $snippet['code'], ':id' => $snippet['id']));
		    
		    // #lec-sidebar# still used in template - add blank one back
		    $db->query("INSERT INTO `snippets` SET `name` = 'lec-sidebar', `agent` = 1;");
		
		    // Success
		    echo 'Renamed and updated #lec-sidebar# to #social-share# - added blank #lec-sidebar#' . PHP_EOL;
		    
		} else {
		
		    // Error
		    throw new Exception ('#social-share# snippet already exists. Could not rename to #social-icons# to #social-share#');
		}
	
	} else {
	
	    // Error - Throw it!
	    throw new Exception ('The #lec-sidebar# snippet does not exist on this website. Renaming #lec-sidebar# to #social-share# failed!');
	}
	
}

// social-links.txt
if(Skin::getDirectory() == 'pt-1r') {

	echo 'Processing #social-share# update for pt-1r' . PHP_EOL;
	
	// Check if #social-links# Snippet Exists
	$snippet = $db->fetch("SELECT `id`, `code` FROM `snippets` WHERE `name` = 'social-links' AND `agent` = 1;");
	if (!empty($snippet)) {
		
		// Make sure there isn't already a #social-share# snippet
		$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
		if(empty($social_share)){
			
			// Update snippet content
			$snippet['code'] = str_replace('social-links', 'social-share', $snippet['code']);
			$snippet['code'] = str_replace('social-link', 'webicon', $snippet['code']);
			$snippet['code'] = str_replace('sl-', '', $snippet['code']);
		
			// Update snippet name and content
		    $statement = $db->prepare('UPDATE `snippets` SET `name` = :name, `code` = :code WHERE `id` = :id');
		    $statement->execute( array(':name' => 'social-share', ':code' => $snippet['code'], ':id' => $snippet['id']));
		
		    // Success
		    echo 'Renamed and updated #social-links# to #social-share#' . PHP_EOL;
		    
		} else {
		
		    // Error
		    throw new Exception ('#social-share# snippet already exists. Could not rename #social-links# to #social-share#');
		}
	
	} else {
	
	    // Error
	    throw new Exception ('The #social-links# snippet does not exist on this website. Renaming #social-links# to #social-share# failed!');
	}
	
}

// social-icons.txt
if(Skin::getDirectory() == 'pt-2r') {

	echo 'Processing #social-share# update for pt-2r' . PHP_EOL;

	// Check if #social-icons# Snippet Exists
	$snippet = $db->fetch("SELECT `id`, `code` FROM `snippets` WHERE `name` = 'social-icons' AND `agent` = 1;");
	if (!empty($snippet)) {
		
		// Make sure there isn't already a #social-share# snippet
		$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
		if(empty($social_share)){
			
			// Update snippet content
			$snippet['code'] = str_replace('id="social-icons"', 'class="social-share"', $snippet['code']);
			$snippet['code'] = str_replace('<strong>Share:</strong>', '', $snippet['code']);
			$snippet['code'] = str_replace(array('<ul>', '</ul>'), '', $snippet['code']);
			
			// Default list
			$snippet['code'] = str_replace('<li id="facebook"><a ',  '<a class="webicon facebook small" ', $snippet['code']);
			$snippet['code'] = str_replace('<li id="twitter"><a ', 	 '<a class="webicon twitter small" ', $snippet['code']);
			$snippet['code'] = str_replace('<li id="google"><a ', 	 '<a class="webicon googleplus small" ', $snippet['code']);
			// Others
			$snippet['code'] = str_replace('<li id="linkedin"><a ',  '<a class="webicon linkedin small" ', $snippet['code']);
			$snippet['code'] = str_replace('<li id="pinterest"><a ', '<a class="webicon pinterest small" ', $snippet['code']);
			$snippet['code'] = str_replace('<li id="youtube"><a ', 	 '<a class="webicon youtube small" ', $snippet['code']);
			$snippet['code'] = str_replace('<li id="rss"><a ', 	     '<a class="webicon rss small" ', $snippet['code']);
		    
			// Update snippet name and content
		    $statement = $db->prepare('UPDATE `snippets` SET `name` = :name, `code` = :code WHERE `id` = :id');
		    $statement->execute( array(':name' => 'social-share', ':code' => $snippet['code'], ':id' => $snippet['id']));
		
		    // Success
		    echo 'Renamed and updated #social-icons# to #social-share#' . PHP_EOL;
		    
		} else {
		
		    // Error
		    throw new Exception ('#social-share# snippet already exists. Could not rename #social-icons# to #social-share#');
		}
	
	} else {
	
	    // Error
	    throw new Exception ('The #social-icons# snippet does not exist on this website. Renaming #social-icons# to #social-share# failed!');
	}
	
}

// social-media.txt
if(Skin::getDirectory() == 'pt-3r') {

	echo 'Processing #social-share# update for pt-3r' . PHP_EOL;

	// Make sure there isn't already a #social-share# snippet
	$social_share = $db->fetch("SELECT `id` FROM `snippets` WHERE `name` = 'social-share' AND `agent` = 1;");
	if(empty($social_share)){
				
		// Get #social-share# content
		$file = 'install/pt-3r/_snippets/social-share.txt';
		if(file_exists($file)) $code = file_get_contents($file);
		
		if(!empty($code)){
		
			// Add #social-share
			$db->query("INSERT INTO `snippets` SET `name` = 'social-share', `code` = '".$code."', `agent` = 1;");
			
			// Success
		    echo 'Added #social-share# - Could not update #social-share# with the links from #social-media#.  ** Please manually update the #social-share# urls. ***' . PHP_EOL;
		    
		 } else {
			 
			// Error
			throw new Exception ('New snippet not found: ' . $file);
			 
		 }
	
	} else {
		
	    // Error
	    throw new Exception ('#social-share# snippet already exists, could not add.');
	}
	
}
