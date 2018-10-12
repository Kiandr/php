<?php

// Prepend ?popup query string
$popup = isset($_GET['popup']) ? '?popup' : '';

?>
<div class="head_wrap">
	<div class="tabset">
	    <ul class="clearfix">

	        <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>

	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
	                <li<?=($_GET['load_page'] == 'map') ? ' class="current"' : ''; ?>><a class="map" href="<?=$listing['url_map'] . $popup; ?>" rel="nofollow">Map <?php if(isset(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS']) && !empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])):?>&amp; Directions<?php endif; ?></a></li>
	            <?php } ?>

	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'])) { ?>
	                <li<?=($_GET['load_page'] == 'birdseye') ? ' class="current"' : ''; ?>><a class="birdseye" href="<?=$listing['url_birdseye'] . $popup; ?>" rel="nofollow">Bird's Eye View</a></li>
	            <?php } ?>

	        <?php } ?>

	        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
	            <li<?=($_GET['load_page'] == 'local') ? ' class="current"' : ''; ?>><a class="local" href="<?=$listing['url_onboard'] . $popup; ?>" rel="nofollow">Get Local</a></li>
	        <?php } ?>

	        <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>
	            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'])) { ?>
	                <li<?=($_GET['load_page'] == 'streetview') ? ' class="current"' : ''; ?> id="streetview-tab" class="hidden"><a class="streetview" href="<?=$listing['url_streetview'] . $popup; ?>" rel="nofollow">Streetview</a></li>
	            <?php } ?>
	        <?php } ?>

	    </ul>
	</div>
</div>
<?php

// Streetview detection
if ($page->info('name') !== 'streetview' && !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']) && !empty($listing['Latitude']) && !empty($listing['Longitude'])) {

	// Require map javascript
	$page->getSkin()->loadMapApi();

	// Add dynamic javascript (let's skip minify)
	$page->addJavascript('(function () {
		new REWMap.Streetview({
			lat: ' . floatval($listing['Latitude']) . ',
			lng: ' . floatval($listing['Longitude']) . ',
			onSuccess : function (data) {
				$(\'#streetview-tab\').removeClass(\'hidden\');
			}
		});
	})();', 'dynamic', false);

}

if (!empty($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
    echo '<div class="provider">';
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing);
	echo '</div>';
}
