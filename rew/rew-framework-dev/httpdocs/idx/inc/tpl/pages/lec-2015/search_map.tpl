<?php

// Map search variables
$page->addJavascript('
	var idxFeed = ' . json_encode(Settings::getInstance()->IDX_FEED) . ';
', 'dynamic', false);

// Map search javascript
$page->addJavascript('js/idx/search_map.js', 'page')
->addJavascript('var mapOptions = $.extend(true, mapOptions || {}, ' . json_encode(array(
	'manager' => array(
		'icon' => $this->getSkin()->getUrl() . '/img/map-ico.png',
		'iconWidth' => 21,
		'iconHeight' => 26,
	)
)) . ');', 'dynamic', false);

// Search form
$this->container('idx-feature')->module('idx-search', array(
	'className'	=> 'snippet-search',
	'button'	=> 'Refine',
	'isSaved'	=> $saved_search,
	'canSave'	=> true,
	'advanced'	=> true
))->display();

// Find "Result" TPL
include $page->locateTemplate('idx', 'misc', 'search-controls');

?>

<div class="toolbar">
	<span id="idx-map-message" class="summary"></span>
	<div class="sort">
		<select name="sortorder">
			<?php foreach (IDX_Builder::getSortOptions() as $sortorder) { ?>
				<option value="<?=$sortorder['value']; ?>"<?=($_REQUEST['sortorder'] == $sortorder['value'] ? ' selected' : ''); ?>><?= Format::htmlspecialchars($sortorder['title']); ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div id="idx-map-search-wrap">

	<div id="idx-map-search"></div>

	<div id="idx-map-legend" class="closed">
		<div class="legend-tabs">
			<div class="legend_tab legendTrigger"></div>
			<div class="stats_tab statsTrigger"></div>
		</div>
		<div class="legend_content">
			<div class="legend_contents hidden">

				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
					<h4>Show Nearby</h4>
					<div class="field x12 toggleset">
						<label><input type="checkbox" name="map[layers][]" value="schools"> <img src="/img/map/legend-school@2x.png" width="20" height="20" alt=""> Schools</label>
						<label><input type="checkbox" name="map[layers][]" value="hospitals"> <img src="/img/map/legend-hospital@2x.png" width="20" height="20" alt=""> Hospitals</label>
						<label><input type="checkbox" name="map[layers][]" value="airports"> <img src="/img/map/legend-airport@2x.png" width="20" height="20" alt=""> Airports</label>
						<label><input type="checkbox" name="map[layers][]" value="parks"> <img src="/img/map/legend-park@2x.png" width="20" height="20" alt=""> Parks</label>
						<label><input type="checkbox" name="map[layers][]" value="golf-courses"> <img src="/img/map/legend-golf@2x.png" width="20" height="20" alt=""> Golf Courses</label>
						<label><input type="checkbox" name="map[layers][]" value="churches"> <img src="/img/map/legend-church@2x.png" width="20" height="20" alt=""> Churches</label>
						<label><input type="checkbox" name="map[layers][]" value="shopping"> <img src="/img/map/legend-shopping@2x.png" width="20" height="20" alt=""> Shopping</label>
					</div>
				<?php } ?>

				<h4>Map View</h4>
				<div class="field x12">
					<select name="map[type]">
						<option value="roadmap">Normal</option>
						<option value="satellite">Satellite</option>
						<option value="hybrid">Hybrid</option>
						<option value="terrain">Terrain</option>
					</select>
				</div>

			</div>
			<div class="stats_contents hidden">
				<div class="keyvalset">

					<h4>Search Statistics</h4>

					<ul>
						<li class="keyval">
							<strong>Listings</strong>
							<strong id="stats-total">0</strong>
						</li>
					</ul>

					<h5>Listing Price</h5>

					<ul>
						<li class="keyval">
							<strong>Average</strong>
							<span id="stats-price-avg">0</span>
						</li>
						<li class="keyval">
							<strong>Highest</strong>
							<span id="stats-price-high">$0</span>
						</li>
						<li class="keyval">
							<strong>Lowest</strong>
							<span id="stats-price-low">$0</span>
						</li>
					</ul>

					<h5>Property Size</h5>

					<ul>
						<li class="keyval">
							<strong>Average</strong>
							<span id="stats-sqft-avg">0 ft&sup2;</span>
						</li>
						<li class="keyval">
							<strong>Highest</strong>
							<span id="stats-sqft-high">0 ft&sup2;</span>
						</li>
						<li class="keyval">
							<strong>Lowest</strong>
							<span id="stats-sqft-low">0 ft&sup2;</span>
						</li>
					</ul>

				</div>
			</div>
		</div>
	</div>

</div>

<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
	<p class="disclaimer">Disclaimer / Sources: <?=Locale::spell('Neighborhood');?> data provided by Onboard Informatics &copy; <?=date('Y'); ?></p>
<?php } ?>
