<?php $page->info('class', $page->info('class') . ' idx-data'); ?>
<div class="cma-property-valuation"<?= !empty(Settings::get('google.maps.api_key')) ? ' data-mapping' : ''; ?>>
    <?php if (!empty(Settings::get('google.maps.api_key'))) { ?>
        <div class="uk-grid uk-grid-small uk-margin-large-top">
            <div class="uk-width-medium-1-2 uk-push-1-2">
                <div id="<?= $this->getUID(); ?>">
                    <div class="uk-grid uk-grid-collapse estimate-values">
                        <div class="uk-width-1-3 cma-estimate-average uk-text-center uk-text-large">
                            <span>Estimate</span>
                            <strong class="avg_price">...</strong>
                        </div>
                        <div class="uk-width-1-3 cma-estimate-minimum uk-text-center uk-text-large">
                            <span>Low</span>
                            <strong class="min_price">...</strong>
                        </div>
                        <div class="uk-width-1-3 cma-estimate-maximum uk-text-center uk-text-large">
                            <span>High</span>
                            <strong class="max_price">...</strong>
                        </div>
                    </div>
                    <div class="uk-grid uk-grid-collapse">
                        <div class="uk-width-1-1">
                            <div id="cma-map" class="map uk-margin-large uk-position-relative">
                                <span class="ph uk-alert abs-center"><em>To begin, please provide a location</em></span>
                            </div>
                            <?php // Radius & Polygon Controls ?>
                            <form id="map-draw-controls" class="uk-hidden  uk-margin-top js-map-draw-controls uk-margin-right">
                                <div id="field-polygon" class="uk-button"><?=IDX_Panel::get('Polygon', array('control_id' => 'cma-polygon', 'tooltip' => false))->getMarkup()?></div>
                                <div id="field-radius" class="uk-button"><?=IDX_Panel::get('Radius', array('control_id' => 'cma-radius', 'tooltip' => false))->getMarkup() ?></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-width-medium-1-2 uk-pull-1-2">
                <form class="uk-form uk-form-stacked js-section-b" data-capture="<?= (!User_Session::get()->isValid() && !Auth::get()->isValid() ? $this->config('capture') : '0'); ?>">
                    <input type="hidden" name="feed" value="<?= Format::htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>">
                    <fieldset>
                        <?= ($heading = $this->config('heading')) ? '<h2>' . Format::htmlspecialchars($heading) . '</h2>' : ''; ?>
                        <?= ($content = $this->config('content')) ? '<p>' . $content . '</p>' : ''; ?>
                        <div class="uk-form-row uk-margin-bottom">
                            <input id="<?= $this->getUID(); ?>-ac" class="ac-search uk-form-large uk-width" name="adr" value="<?= Format::htmlspecialchars($_GET['adr']); ?>"<?= (empty($_GET['adr']) ? ' autofocus' : ''); ?>>
                            <button class="ac-locate uk-button uk-button-inline-form-large uk-margin-top">Locate Property</button>
                        </div>
                        <div class="ac-message uk-hidden uk-alert"></div>
                    </fieldset>
                    <fieldset>
                        <legend>Refine your search</legend>
                        <?php if (!empty($idx_types)) { ?>
                        <div class="uk-form-row">
                            <label class="uk-form-label" for="property-type">Property Type</label>
                            <div class="uk-form-controls">
                                <select name="type" class="uk-width-1-1 uk-form-large" id="property-type">
                                <option value="">...</option>
                                <?php foreach ($idx_types as $option) { ?>
                                <option value="<?= Format::htmlspecialchars($option['value']); ?>"<?= ($option['value'] == $searchCriteria['type'] ? ' selected' : ''); ?>><?= Format::htmlspecialchars($option['title']); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($idx_subtypes)) { ?>
                        <div class="uk-form-row">
                            <label class="uk-form-label" for="sub-type">Sub-Type</label>
                            <div class="uk-form-controls">
                                <select name="subtype" class="uk-width-1-1 uk-form-large" id="sub-type">
                                <option value="">...</option>
                                <?php foreach ($idx_subtypes as $option) { ?>
                                    <option value="<?= Format::htmlspecialchars($option['value']); ?>"<?= ($option['value'] == $searchCriteria['subtype'] ? ' selected' : ''); ?>><?= Format::htmlspecialchars($option['title']); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="uk-grid uk-margin-top">
                            <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                                <label class="uk-form-label" for="beds">Beds</label>
                                <select id="beds" name="beds" class="uk-width-1-1 uk-form-large">
                                    <option value="0">...</option>
                                    <?php // # of Bedrooms
                                    $curr = $searchCriteria['beds'];
                                    foreach (range(1, 6) as $opt) {
                                        echo '<option value="' . $opt . '"' . ($opt == $curr ? ' selected' : '') . '>' . $opt . '</option>';
                                    }
                                    echo '<option value="7+"' . ($baths === '7+' ? ' selected' : '') . '>7+</option>';
                                    ?>
                                </select>
                            </div>
                            <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                                <label class="uk-form-label" for="baths">Baths</label>
                                <select id="baths" name="baths" class="uk-width-1-1 uk-form-large">
                                    <option value="0">...</option>
                                    <?php // # of Bathrooms
                                    $curr = $searchCriteria['baths'];
                                    foreach (range(1, 6) as $opt) {
                                        echo '<option value="' . $opt . '"' . ($opt == $curr ? ' selected' : '') . '>' . $opt . '</option>';
                                    }
                                    echo '<option value="7+"' . ($baths === '7+' ? ' selected' : '') . '>7+</option>';
                                    ?>
                                </select>
                            </div>
                            <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                                <label class="uk-form-label" for="sqft">Sq. Ft.</label>
                                <select id="sqft" name="sqft" class="uk-width-1-1 uk-form-large">
                                    <option value="0">...</option>
                                    <?php // Sqft range
                                        $curr = (string)$searchCriteria['sqft'];
                                        foreach (array(
                                                 array(NULL, 1000),
                                                 array(1000, 2000),
                                                 array(2000, 3000),
                                                 array(3000, 4000),
                                                 array(4000, 5000),
                                                 array(5000, 6000),
                                                 array(6000, 7000),
                                                 array(7000, 8000),
                                                 array(8000, NULL)
                                             ) as $range) {
                                        list ($min, $max) = $range;
                                        if (is_null($min)) {
                                            $title = 'Under ' . number_format($max) . ' ft&sup2;';
                                            $value = '0-' . $max;
                                        } elseif (is_null($max)) {
                                            $title = 'Over ' . number_format($min) . ' ft&sup2;';
                                            $value = $min;
                                        } else {
                                            $title = number_format($min) . ' ft&sup2; - ' . number_format($max) . ' ft&sup2;';
                                            $value = $min . '-' . $max;
                                        }
                                        echo '<option value="' . $value . '"' . ($value == $curr ? ' selected' : '') . '>' . $title . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
                                <label class="uk-form-label" for="condition">Condition</label>
                                <select id="condition" name="condition" class="uk-width-1-1 uk-form-large">
                                    <option value="0">...</option>
                                    <?php // Property condition
                                        $curr = $searchCriteria['condition'];
                                        foreach (array(
                                                     array(110, 'Excellent'),    // Well Maintained and upgraded and/or quality building materials.
                                                     array(100, 'Good'),            // Well maintained and few/no evidence of deferred maintenance.
                                                     array(95, 'Average'),        // Maintained and evidence of typical wear and tear for age and neighborhood.
                                                     array(90, 'Fair'),            // Lacks maintenance and/or minor repairs are needed to bring it into average condition.
                                                     array(85, 'Poor')            // Major repairs needed.
                                                 ) as $opt) {
                                            list ($value, $title) = $opt;
                                            echo '<option value="' . $value . '"' . (!empty($curr) && in_array($curr, array($value, $title)) ? ' selected' : '') . '>' . $title . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    <?php } else { ?>
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-1-1">
                <?php rew_snippet('form-cma'); ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($this->config('cta')) { ?>
        <div class="uk-block">
            <?= ($photo = $this->config('cta.photo')) ? '<img class="uk-float-left" src="' . Format::htmlspecialchars($photo) . '" alt="">' : ''; ?>
            <div class="body">
                <?= ($heading = $this->config('cta.heading')) ? '<h3>' . Format::htmlspecialchars($heading) . '</h3>' : ''; ?>
                <?= ($content = $this->config('cta.content')) ? '<p>' . $content . '</p>' : ''; ?>
                <?= ($button = $this->config('cta.button')) ? '<a class="uk-button cta-link">' . Format::htmlspecialchars($button) . '</a>' : ''; ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($this->config('results')) { ?>
        <div class="matches uk-hidden">
            <div class="uk-clearfix">
                <h2 class="uk-float-left">Comparable Properties</h2>
                <a href="#" class="view-all uk-float-right"></a>
            </div>
            <div class="fw fw-idx-listings fw-nbh-listings">
                <div class="js-show-idx-properties uk-grid uk-grid-medium"></div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
}
?>

<script>
    var defaultLocation = <?= json_encode($this->config('defaults.location') ? : 'Orlando FL, USA'); ?>;
</script>
