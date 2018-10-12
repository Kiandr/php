<?php

// Include search message
include $page->locateTemplate('idx', 'misc', 'search-message');

?>
<div class="uk-margin-top uk-margin-left uk-margin-right">
    <h1>Interactive Map Search</h1>
    <div id="idx-map-message" class="uk-panel uk-panel-box uk-margin-top uk-margin-bottom"></div>

    <?php if (!empty($_COMPLIANCE['limit'])) { ?>
        <p class="uk-alert uk-hidden" id="compliance-message">Only <?=number_format($_COMPLIANCE['limit']); ?> properties may be displayed per search. To see all of your results, try narrowing your search criteria.</p>
    <?php } ?>
</div>

<div class="uk-position-relative uk-nbfc">
    <?php require Page::locateTemplate('idx', 'misc', 'map-container'); ?>
    <div id="idx-map-legend" class="<?= $_COOKIE['idx-map-legend'] == 'open' ? '' : 'closed '; ?>uk-position-absolute uk-animation-fade-in">
        <div class="map-tabs">
            <div class="map-tab options" data-tab=".legend-content .options-content"></div>
            <div class="map-tab stats" data-tab=".legend-content .stats-content"></div>
        </div>
        <div class="legend-content uk-position-absolute">
            <div class="options-content">
                <form class="idx-map-search">
                <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
                    <h4>Show Nearby</h4>
                    <div class="uk-width-1-1">
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="schools"> <img src="/img/map/legend-school@2x.png" width="20" height="20" alt=""> Schools</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="hospitals"> <img src="/img/map/legend-hospital@2x.png" width="20" height="20" alt=""> Hospitals</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="airports"> <img src="/img/map/legend-airport@2x.png" width="20" height="20" alt=""> Airports</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="parks"> <img src="/img/map/legend-park@2x.png" width="20" height="20" alt=""> Parks</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="golf-courses"> <img src="/img/map/legend-golf@2x.png" width="20" height="20" alt=""> Golf Courses</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="churches"> <img src="/img/map/legend-church@2x.png" width="20" height="20" alt=""> Churches</label>
                        <label class="uk-display-block"><input type="checkbox" name="map[layers][]" value="shopping"> <img src="/img/map/legend-shopping@2x.png" width="20" height="20" alt=""> Shopping</label>
                    </div>
                <?php } ?>
                <h4>Map View</h4>
                <select name="map[type]" class="uk-width-1-1">
                    <option value="roadmap">Normal</option>
                    <option value="satellite">Satellite</option>
                    <option value="hybrid">Hybrid</option>
                    <option value="terrain">Terrain</option>
                </select>
                </form>
            </div>
            <div class="stats-content uk-hidden">
                <div class="keyvalset">
                    <h4>Search Statistics</h4>
                    <ul class="uk-list">
                        <li class="keyval">
                            <strong>Listings</strong>
                            <strong id="stats-total">0</strong>
                        </li>
                    </ul>
                    <h5>Listing Price</h5>
                    <ul class="uk-list">
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
                    <ul class="uk-list">
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

<div class="uk-container uk-container-center">
    <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
        <p class="uk-text-muted">Disclaimer / Sources: <?=Locale::spell('Neighborhood');?> data provided by Onboard Informatics &copy; <?=date('Y'); ?></p>
    <?php } ?>
</div>
