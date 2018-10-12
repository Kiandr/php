<?php if(!empty(Settings::get('google.maps.api_key'))) { ?>
<div id="<?=$this->getUID(); ?>">

    <div style="overflow: hidden; padding-bottom: 20px;">

        <div class="section section-b" style="padding: 0;">
            <div class="body">
                <form>
                    <input type="hidden" name="feed" value="<?=Format::htmlspecialchars(Settings::getInstance()->IDX_FEED); ?>">
                    <div class="body-a">
                        <header>
                            <?=($heading = $this->config('heading')) ? '<h2>' . Format::htmlspecialchars($heading) . '</h2>' : ''; ?>
                            <?=($content = $this->config('content')) ? '<p>' . $content . '</p>' : ''; ?>
                        </header>
                        <div id="eval-step-location">
                            <input id="<?=$this->getUID(); ?>-ac" class="ac-search" name="adr" value="<?=Format::htmlspecialchars($_GET['adr']); ?>"<?=(empty($_GET['adr']) ? ' autofocus' : ''); ?>>
                            <div class="btnset">
                                <button class="ac-locate btn--primary">Locate</button>
                            </div>
                        </div>
                        <div class="ac-message hidden"></div>
                    </div>
                    <div class="body-b cols">
                        <?php if (!empty($idx_types)) { ?>
                            <div class="fld col w1/1">
                                <label>Property Type</label>
                                <select name="type">
                                    <option value="">...</option>
                                    <?php foreach ($idx_types as $option) { ?>
                                        <option value="<?=Format::htmlspecialchars($option['value']); ?>"<?=($option['value'] == $searchCriteria['type'] ? ' selected' : ''); ?>><?=Format::htmlspecialchars($option['title']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <?php if (!empty($idx_subtypes)) { ?>
                            <div class="fld col w1/1 hidden">
                                <label>Sub-Type</label>
                                <div class="subtypes">
                                    <select name="subtype">
                                        <option value="">...</option>
                                        <?php foreach ($idx_subtypes as $option) { ?>
                                            <option value="<?=Format::htmlspecialchars($option['value']); ?>"<?=($option['value'] == $searchCriteria['subtype'] ? ' selected' : ''); ?>><?=Format::htmlspecialchars($option['title']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="fld col w1/2">
                            <label>Beds</label>
                            <select name="beds">
                                <option value="0">...</option>
                                <?php

                                    // # of Bedrooms
                                    $curr = $searchCriteria['beds'];
                                    foreach (range(1, 6) as $opt) {
                                        echo '<option value="' . $opt . '"' . ($opt == $curr ? ' selected' : '') . '>' . $opt . '</option>';
                                    }
                                    echo '<option value="7+"' . ($baths === '7+' ? ' selected' : '') . '>7+</option>';

                                ?>
                            </select>
                        </div>
                        <div class="fld col w1/2">
                            <label>Baths</label>
                            <select name="baths">
                                <option value="0">...</option>
                                <?php

                                    // # of Bathrooms
                                    $curr = $searchCriteria['baths'];
                                    foreach (range(1, 6) as $opt) {
                                        echo '<option value="' . $opt . '"' . ($opt == $curr ? ' selected' : '') . '>' . $opt . '</option>';
                                    }
                                    echo '<option value="7+"' . ($baths === '7+' ? ' selected' : '') . '>7+</option>';

                                ?>
                            </select>
                        </div>
                        <div class="fld col w1/2">
                            <label>Sq. Ft.</label>
                            <select name="sqft">
                                <option value="0">...</option>
                                <?php

                                    // Sqft range
                                    $curr = (string) $searchCriteria['sqft'];
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
                        <div class="fld col w1/2">
                            <label>Condition</label>
                            <select name="condition">
                                <option value="0">...</option>
                                <?php

                                    // Property condition
                                    $curr = $searchCriteria['condition'];
                                    foreach (array(
                                        array(110, 'Excellent'),	// Well Maintained and upgraded and/or quality building materials.
                                        array(100, 'Good'),			// Well maintained and few/no evidence of deferred maintenance.
                                        array(95, 'Average'),		// Maintained and evidence of typical wear and tear for age and neighborhood.
                                        array(90, 'Fair'),			// Lacks maintenance and/or minor repairs are needed to bring it into average condition.
                                        array(85, 'Poor')			// Major repairs needed.
                                    ) as $opt) {
                                        list ($value, $title) = $opt;
                                        echo '<option value="' . $value. '"' . (!empty($curr) && in_array($curr, array($value, $title)) ? ' selected' : '') . '>' . $title . '</option>';
                                    }

                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="section section-a" style="padding: 0;">
            <div class="row estimate-values">
                <div class="col col-a strong">
                    Estimate
                    <strong class="avg_price">...</strong>
                </div>
                <div class="col col-b">
                    Low
                    <strong class="min_price">...</strong>
                </div>
                <div class="col col-c">
                    High
                    <strong class="max_price">...</strong>
                </div>
            </div>
            <div id="<?=$this->getUID(); ?>-map" class="map">
                <span class="ph">Please provide a location</span>
            </div>
            <?php

            // Radius & Polygon Controls
            echo '<div class="map-controls hidden">';
            echo '<div id="field-polygon">' . IDX_Panel::get('Polygon', array('control_id' => $this->getUID() . '-polygon', 'tooltip' => false))->getMarkup() . '</div>';
            echo '<div id="field-radius">' . IDX_Panel::get('Radius', array('control_id' => $this->getUID() . '-radius', 'tooltip' => false))->getMarkup() . '</div>';
            echo '</div>';

            ?>
        </div>


    </div>

    <?php if ($this->config('cta')) { ?>
        <div class="signup">
            <?=($photo = $this->config('cta.photo')) ? '<img class="pleft" src="' . Format::htmlspecialchars($photo) . '" alt="">' : ''; ?>
            <div class="body">
                <?=($heading = $this->config('cta.heading')) ? '<h3>' . Format::htmlspecialchars($heading) . '</h3>' : ''; ?>
                <?=($content = $this->config('cta.content')) ? '<p>' . $content . '</p>' : ''; ?>
                <?=($button = $this->config('cta.button')) ? '<div class="btnset"><a class="cta-link btn--primary btn strong">' . Format::htmlspecialchars($button) . '</a></div>' : ''; ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($this->config('results')) { ?>
        <div class="matches hidden">
            <header>
                <h2>Comparable Properties</h2>
                <a href="#" class="view-all"></a>
            </header>
            <div class="listings cols padB-lg"></div>
        </div>
    <?php } ?>

</div>

<?php } else {
    rew_snippet('form-cma');
} ?>

<?php
if (!empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
}
?>
