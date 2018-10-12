<div class="head_wrap">
	<div class="tabset">
	    <ul class="clearfix">

	        <li<?=($_GET['load_page'] == 'details') ? ' class="current"' : ''; ?>><a href="<?=$listing['url_details']; ?>">Property Details</a></li>

	        <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>

	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
	                <li<?=($_GET['load_page'] == 'map') ? ' class="current"' : ''; ?>><a class="map" href="<?=$listing['url_map']; ?>" rel="nofollow">Map <?php if(isset(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS']) && !empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])):?>&amp; Directions<?php endif; ?></a></li>
	            <?php } ?>

	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'])) { ?>
	                <li<?=($_GET['load_page'] == 'birdseye') ? ' class="current"' : ''; ?>><a class="birdseye" href="<?=$listing['url_birdseye']; ?>" rel="nofollow">Bird's Eye View</a></li>
	            <?php } ?>

	        <?php } ?>

                <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
	            <li<?=($_GET['load_page'] == 'local') ? ' class="current"' : ''; ?>><a class="local" href="<?=$listing['url_onboard']; ?>" rel="nofollow">Get Local</a></li>
	        <?php } ?>

	        <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>
	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'])) { ?>
	                <li<?=($_GET['load_page'] == 'streetview') ? ' class="current"' : ''; ?> id="streetview-tab" class="hidden"><a class="streetview" href="<?=$listing['url_streetview']; ?>" rel="nofollow">Streetview</a></li>
	            <?php } ?>
	        <?php } ?>

		</ul>
	</div>
	<?php

	// Streetview available
	if ($page->info('name') !== 'streetview' && !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']) && !empty($listing['Latitude']) && !empty($listing['Longitude'])) {

		// Require map javascript
		$page->getSkin()->loadMapApi();

		// Streetview detection script
		$page->addJavascript('new REWMap.Streetview({
			lat: ' . floatval($listing['Latitude']) . ',
			lng: ' . floatval($listing['Longitude']) . ',
			onSuccess : function (data) {
				$(\'#streetview-tab\').removeClass(\'hidden\');
			}
		});', 'dynamic', false);

	}

	// Listing pagination javascript
	$page->addJavascript('IDX.Paginate({
		mls: ' . json_encode($listing['ListingMLS']) . ',
		feed: ' . json_encode($listing['idx']) . ',
		done: function (data) {
			var $wrap = $(\'#listing-pagination\');
			if (data.prev){
				$(\'<li><a href="\' + data.prev + \'"><i class="icon-chevron-left"></i> Prev</a></li>\').appendTo($wrap);
			}
			if (data.next) {
				$(\'<li><a href="\' + data.next + \'">Next <i class="icon-chevron-right"></i></a></li>\').appendTo($wrap);
			}
		}
	});', 'dynamic', false);

	?>
	<div class="nav horizontal" id="nav_results">
		<ul id="listing-pagination">
			<li><a href="<?=$user->url_back() ?: Settings::getInstance()->SETTINGS['URL_IDX']; ?>"><i class="icon-arrow-left"></i> Back to Results</a></li>
		</ul>
	</div>
</div>

<?php if (isset($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
    echo '<div class="provider">';
	\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing);
	echo '</div>';
} ?>