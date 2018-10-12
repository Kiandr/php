<?php

// Include Header
include $page->locateTemplate('directory', 'misc', 'header');

?>
<?php if (isset($_GET['search']) && empty($_GET['search'])) { ?>

    <h2>Directory Error</h2>
    <div class="msg negative">
		<p>You must specify a keyword to search listings in the directory.</p>
    </div>

<?php } else { ?>

    <?php if (empty($categories) && empty($listings) && $cms_pages_count <= 0) { ?>

    	<h2>No Results Found</h2>
	    <div class="msg caution">
	        <p>No results found to match your search query. Please try again.</p>
	    </div>

    <?php } ?>

    <?php if (!empty($categories)) { ?>

        <div class="nav">
			<h2><strong><?=Format::number($category_count); ?> <?=Format::plural($category_count, 'Categories', 'Category'); ?></strong> in the <strong><?=Format::htmlspecialchars($directory_settings['directory_name']); ?></strong> matched your search.</h2>
	        <ul>
		        <?php foreach ($categories as $category) { ?>
		            <li><a href="<?=sprintf(URL_DIRECTORY_CATEGORY, $category['link']); ?>"><?=Format::htmlspecialchars($category['title']); ?></a></li>
		        <?php } ?>
	        </ul>
        </div>

    <?php } ?>

    <?php if (!empty($listings)) { ?>

        <h2><strong><?=Format::number($listing_count); ?> <?=Format::plural($listing_count, 'Listings', 'Listing'); ?></strong> in the <strong><?=Format::htmlspecialchars($directory_settings['directory_name']); ?></strong> matched your search.</h2>

        <?php

        	// Include Pagination TPL (Top)
	        if (!empty($pagination_tpl)) {
	        	$pagination['extra'] = 'top';
	        	include $pagination_tpl;
	        }

	        // Display Listings
	        if (!empty($listings)) {
				echo '<div class="articleset">';
	        	foreach ($listings as $entry) {
	        		include $page->locateTemplate('directory', 'misc' ,'result');
	        	}
	        	echo '</div>';
	        }

        	// Include Pagination TPL (Bottom)
	        if (!empty($pagination_tpl)) {
	        	$pagination['extra'] = 'bottom';
	        	include $pagination_tpl;
	        }

        ?>

    <?php } ?>

    <?php if (!empty($cms_pages)) { ?>
        <div class="nav">
			<h2><strong><?=Format::number($cms_pages_count); ?> <?=Format::plural($cms_pages_count, 'Pages', 'Page'); ?></strong> on <strong>Our Website</strong> matched your search.</h2>
	        <ul>
		        <?php foreach ($cms_pages as $cms_page) { ?>
		            <li><a href="<?=$cms_page['value']; ?>"><?=Format::htmlspecialchars($cms_page['title']); ?></a></li>
		        <?php } ?>
	        </ul>
        </div>
    <?php } ?>

<?php } ?>