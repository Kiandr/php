<?php

// Category Breadcrumbs
$_GET['category'] = isset($_POST['listing_category'][0]) ? $_POST['listing_category'][0] : $_GET['category'];
if (!empty($_GET['category'])) {

	// DB Connection
	$db = DB::get('directory');

	// Build Breadcrumbs
	$breadcrumbs = array();
	$result = $db->prepare("SELECT `c`.`link` AS `c`, `s`.`link` AS `s`, `t`.`link` AS `t` FROM `directory_categories` `c` LEFT JOIN `directory_categories` `s` ON `c`.`parent` = `s`.`link` LEFT JOIN `directory_categories` `t` ON `s`.`parent` = `t`.`link` WHERE `c`.`link` = :category;");
	$result->execute(array('category' => $_GET['category']));
	$categories = $result->fetch();
	if (!empty($categories)) {
		$categories = array_reverse($categories);
		$breadcrumb = $db->prepare("SELECT `link`, `title` FROM `directory_categories` WHERE `link` = :category LIMIT 1;");
		foreach ($categories as $crumb) {
			if (empty($crumb)) continue;
			$breadcrumb->execute(array('category' => $crumb));
			$crumb = $breadcrumb->fetch();
			if (!empty($crumb)) {
				$breadcrumbs[] = array(
					'title'		=> $crumb['title'],
					'link'		=> URL_DIRECTORY . $crumb['link'] . '/',
					'notlink'	=> ($_GET['page'] != 'details' && $_GET['category'] == $crumb['link'])
				);
			}
		}
	}

	// Listing Details
	if ($_GET['page'] == 'details' && !empty($entry)) {
		$breadcrumbs[] = array('link' => $entry['url_details'], 'title' => $entry['business_name'], 'notlink' => true);
	}

	// Directory Home
	if (!empty($breadcrumbs)) array_unshift($breadcrumbs, array('link' => URL_DIRECTORY, 'title' => 'Directory Home'));

}

?>
<div id="directory-header">
    <?php

        // Show Heading
        if ((empty($_GET['page']) || $_GET['page'] == 'directory') && !empty($directory_settings['directory_name'])) echo '<h1>' . $directory_settings['directory_name'] . '</h1>';

	?>
	<form action="<?=URL_DIRECTORY_SEARCH; ?>" class="search">
		<h4>
			Find a Business
			<span class="tween">&bull;</span>
			<a href="<?=URL_DIRECTORY; ?>add/?listing_category[]=<?=Format::htmlspecialchars($_GET['category']); ?>">Add a Business</a>
		</h4>
		<div class="field x10 o0">
	    	<input type="search" placeholder='E.G. "Coffee", "Home and Garden".' name="search" value="<?=Format::htmlspecialchars($_GET['search']); ?>" required>
		</div>
		<div class="field x2 o10">
			<button type="submit" class="search-btn">Search</button>
		</div>
	</form>
	<?php rew_snippet('business-directory-intro'); ?>
</div>

<?php if (!empty($breadcrumbs)) { ?>
    <div class="breadcrumbs">
        <ul>
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php if ($breadcrumb['notlink']) { ?>
                    <li><?=Format::htmlspecialchars($breadcrumb['title']); ?></li>
                <?php } else { ?>
                    <li><a href="<?=$breadcrumb['link']; ?>"><?=Format::htmlspecialchars($breadcrumb['title']); ?></a></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
