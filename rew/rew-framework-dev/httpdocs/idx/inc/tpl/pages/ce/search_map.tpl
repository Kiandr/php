<?php

// Map search variables
$page->addJavascript(sprintf(
    'var mapOptions = $.extend(true, mapOptions || {}, %s);',
    json_encode([
        'manager' => [
            'icon' => $this->getSkin()->getUrl() . '/img/map-flag.png',
            'iconWidth' => 22,
            'iconHeight' => 25
        ],
        'tooltip' => [
            'parentEl' => '#map-tooltip-block'
        ]
    ])
), 'dynamic', false);

// Map search javascript
$page->addJavascript('js/idx/search_map.js', 'page');

// Display sort options
$sortOptions = IDX_Builder::getSortOptions();
$sortOrder = current($sortOptions);
foreach ($sortOptions as $sortOption) {
    if ($_REQUEST['sortorder'] === $sortOption['value']) {
        $sortOrder = $sortOption;
    }
}

?>
<div id="search-toolbar" class="bar idx-sort-bar marB-sm">

    <div class="bar -pad-top -text-center@md -text-center@sm">
        <div class="bar__title -text-sm -left@lg -left@xl -text-center@md -text-center@sm -pad-left" id="idx-map-message"></div>
        <div class="buttons -text-bold -mar-bottom-xs -right@lg -right@xl">
            <?php include $page->locateTemplate('idx', 'misc', 'search-controls'); ?>
        </div>
    </div>
</div>
<?php

// Display search message
include $page->locateTemplate('idx', 'misc', 'search-message');

?>

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
                        <label><input type="checkbox" name="map[layers][]" value="schools"> <img data-src="/img/map/legend-school@2x.png" width="20" height="20" alt=""> Schools</label>
                        <label><input type="checkbox" name="map[layers][]" value="hospitals"> <img data-src="/img/map/legend-hospital@2x.png" width="20" height="20" alt=""> Hospitals</label>
                        <label><input type="checkbox" name="map[layers][]" value="airports"> <img data-src="/img/map/legend-airport@2x.png" width="20" height="20" alt=""> Airports</label>
                        <label><input type="checkbox" name="map[layers][]" value="parks"> <img data-src="/img/map/legend-park@2x.png" width="20" height="20" alt=""> Parks</label>
                        <label><input type="checkbox" name="map[layers][]" value="golf-courses"> <img data-src="/img/map/legend-golf@2x.png" width="20" height="20" alt=""> Golf Courses</label>
                        <label><input type="checkbox" name="map[layers][]" value="churches"> <img data-src="/img/map/legend-church@2x.png" width="20" height="20" alt=""> Churches</label>
                        <label><input type="checkbox" name="map[layers][]" value="shopping"> <img data-src="/img/map/legend-shopping@2x.png" width="20" height="20" alt=""> Shopping</label>
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
                <h4 class="page-h3">Search Statistics</h4>
                <ul class="kvs">
                    <li class="kv">
                        <strong class="k">Listings</strong>
                        <span class="v" id="stats-total">0</span>
                    </li>
                </ul>
                <h4 class="page-h3">Listing Prices</h4>
                <ul class="kvs">
                    <li class="kv">
                        <strong class="k">Avg.</strong>
                        <span class="v" id="stats-price-avg">0</span>
                    </li>
                    <li class="kv">
                        <strong class="k">Max.</strong>
                        <span class="v" id="stats-price-high">$0</span>
                    </li>
                    <li class="kv">
                        <strong class="k">Min.</strong>
                        <span class="v" id="stats-price-low">$0</span>
                    </li>
                </ul>
                <h4 class="page-h3">Property Size</h4>
                <ul class="kvs">
                    <li class="kv">
                        <strong class="k">Avg.</strong>
                        <span class="v" id="stats-sqft-avg">0 ft&sup2;</span>
                    </li>
                    <li class="kv">
                        <strong class="k">Max.</strong>
                        <span class="v" id="stats-sqft-high">0 ft&sup2;</span>
                    </li>
                    <li class="kv">
                        <strong class="k">Min.</strong>
                        <span class="v" id="stats-sqft-low">0 ft&sup2;</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div id="map-tooltip-block"></div>

<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
    <p class="disclaimer">Disclaimer / Sources: <?=Locale::spell('Neighborhood');?> data provided by Onboard Informatics &copy; <?=date('Y'); ?></p>
<?php } ?>