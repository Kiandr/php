<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'not-found');
    return;
}

if (!empty($location) && (empty($listing['Latitude']) || empty($listing['Longitude']))) {

    // Map Center
    $listing['Latitude'] = $location['LATITUDE'];
    $listing['Longitude'] = $location['LONGITUDE'];
}

$disableMapTooltip = false;
if ($_COMPLIANCE['local']['disable_popup']) {
    $disableMapTooltip = true;
}

?>
<script>var amenities = [], schools = [], iconShopping, iconSchool;</script>
<div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
    <?php

    // Listing Title
    if (!empty($listing['ListingTitle'])) { ?>
        <h1>
            <?= Format::htmlspecialchars($listing['ListingTitle']); ?>
        </h1>
    <?php } ?>

    <?php require $page->locateTemplate('idx', 'misc', 'details'); ?>

    <?php if (empty($_COMPLIANCE['details']['remove_heading'])) { ?>
        <h2>$<?= Format::number($listing['ListingPrice']); ?>
            <?= $_COMPLIANCE['results']['show_mls'] ? ' - ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] : ''; ?>
            - <?= Format::htmlspecialchars($listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState']); ?>
        </h2>
    <?php } ?>
    <?php if (empty($location)) { ?>
        <p class="uk-alert uk-alert-danger">This feature is currently unavailable in this listing\'s region.</p>
    <?php } else { ?>
        <div id="idx-map-onboard" class="fw" data-gapi-key="<?= Format::htmlspecialchars(Settings::get('google.maps.api_key')); ?>"></div>
        <?php require $page->locateTemplate('idx', 'misc', 'nextsteps'); ?>

        <div class="tabbed-content">

            <div class="tabset">
                <ul class="uk-subnav uk-subnav-pill">
                    <li<?= ($view == 'nearby-amenities') ? ' class="uk-active" ' : ''; ?>><a
                            href="?iframe&view=nearby-amenities" data-panel="#nearby-amenities">Nearby Amenities</a></li>
                    <li<?= ($view == 'nearby-schools') ? ' class="uk-active" ' : ''; ?>><a href="?view=nearby-schools"
                                                                                         data-panel="#nearby-schools">Nearby
                            Schools</a></li>
                    <li<?= ($view == 'community-information') ? ' class="uk-active" ' : ''; ?>><a
                            href="?iframe&view=community-information" data-panel="#community-information">Neighborhood
                            Information</a></li>
                </ul>
            </div>

            <div id="nearby-amenities" class="panel <?= ($view == 'nearby-amenities') ? 'loaded' : 'hidden'; ?>">

                <div class="clear"></div>

                <?php if ($view == 'nearby-amenities') : ?>

                    <?php if (!empty($_POST['ajax'])) ob_clean(); ?>

                    <?php if (count($nearby_amenities) > 0) { ?>

                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th width="10">&nbsp;</th>
                                <th>Business Name</th>
                                <th>Category</th>
                                <th>Distance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $count = 0; ?>
                            <?php $class = 'odd'; ?>
                            <?php foreach ($nearby_amenities as $nearby_amenity) : ?>
                                <?php $class = !empty($class) ? '' : ' class="odd"'; ?>
                                <tr id="amenity-<?= $count; ?>" valign="top"<?= $class; ?>>
                                    <td width="50"><img src="/img/map/legend-shopping@2x.png" width="20" height="20"
                                                        alt=""></td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           onclick="amenities[<?= $count; ?>].select();"><?= ucwords(strtolower($nearby_amenity['BUSNAME'])); ?></a>
                                        <?= ucwords(strtolower($nearby_amenity['STREET'])); ?>
                                        . <?= ucwords(strtolower($nearby_amenity['CITY'])); ?>
                                        , <?= ucwords(strtolower($nearby_amenity['STATENAME'])); ?>.
                                        <?= ucwords(strtolower($nearby_amenity['PHONE'])); ?>
                                        <?php

                                        // HTML Tooltip
                                        $tooltip = '<div class="popover">'
                                            . '<header class="title">'
                                            . '<strong>' . ucwords(strtolower($nearby_amenity['BUSNAME'])) . '</strong>'
                                            . '<a class="action-close hidden" href="javascript:void(0);">&times;</a>'
                                            . '</header>'
                                            . '<div class="body">'
                                            . ucwords(strtolower($nearby_amenity['STREET'])) . '. ' . ucwords(strtolower($nearby_amenity['CITY'])) . '<br>'
                                            . ucwords(strtolower($nearby_amenity['INDUSTRY']))
                                            . '</div>'
                                            . '<div class="tail"></div>'
                                            . '</div>';

                                        ?>
                                        <script>
                                            //<![CDATA[
                                            amenities.push(new REWMap.Marker({
                                                'map': $('#idx-map-onboard').data('REWMap'),
                                                'tooltip': '<?=addslashes($tooltip); ?>',
                                                'icon': iconShopping,
                                                'lat': <?=floatval($nearby_amenity['LATITUDE']); ?>,
                                                'lng': <?=floatval($nearby_amenity['LONGITUDE']); ?>,
                                                'zIndex': 1
                                            }));
                                            //]]>
                                        </script>
                                    </td>
                                    <td><?= ucwords(strtolower($nearby_amenity['CATEGORY'])); ?></td>
                                    <td><?= number_format($nearby_amenity['distance'], 2); ?> mi</td>
                                </tr>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php } else { ?>

                        <div class="uk-alert">
                            <p>No nearby amenities could be found at this time.</p>
                        </div>

                    <?php } ?>

                    <?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

                <?php endif; ?>

            </div>

            <div id="nearby-schools" class="panel <?= ($view == 'nearby-schools') ? 'loaded' : 'hidden'; ?>">

                <div class="clear"></div>

                <?php if ($view == 'nearby-schools') : ?>

                    <?php if (!empty($_POST['ajax'])) ob_clean(); ?>

                    <?php if (count($nearby_schools) > 0) : ?>

                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th width="10">&nbsp;</th>
                                <th>School Name</th>
                                <th>Grades</th>
                                <th>Distance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $count = 0; ?>
                            <?php $class = 'odd'; ?>
                            <?php foreach ($nearby_schools as $nearby_school) : ?>
                                <?php $class = !empty($class) ? '' : ' class="odd"'; ?>
                                <tr id="school-<?= $count; ?>" valign="top"<?= $class; ?>>
                                    <td width="50"><img src="/img/map/legend-school@2x.png" width="20" height="20"
                                                        alt=""></td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           onclick="schools[<?= $count; ?>].select();"><?= ucwords(strtolower($nearby_school['INSTITUTION_NAME'])); ?></a><br/>
                                        <?= ucwords(strtolower($nearby_school['LOCATION_ADDRESS'])); ?>
                                        . <?= ucwords(strtolower($nearby_school['LOCATION_CITY'])); ?>
                                        , <?= $nearby_school['STATE_ABBREV']; ?>.
                                        <?php if (!empty($nearby_school['WEBSITE_URL'])) : ?>
                                            <a href="<?= $nearby_school['WEBSITE_URL']; ?>" target="_blank"
                                               rel="nofollow">School Website</a>
                                        <?php endif; ?>
                                        <?php

                                        // HTML Tooltip
                                        $tooltip = '<div class="popover">'
                                            . '<header class="title">'
                                            . '<strong>' . $nearby_school['GRADE_SPAN_CODE_BLDG_TEXT'] . ' - ' . ucwords(strtolower($nearby_school['INSTITUTION_NAME'])) . '</strong>'
                                            . '<a class="action-close hidden" href="javascript:void(0);">&times;</a>'
                                            . '</header>'
                                            . '<div class="body">'
                                            . ucwords(strtolower($nearby_school['LOCATION_ADDRESS'])) . '<br>'
                                            . ucwords(strtolower($nearby_school['LOCATION_CITY'])) . ', ' . $nearby_school['STATE_ABBREV']
                                            . '</div>'
                                            . '<div class="tail"></div>'
                                            . '</div>';

                                        ?>
                                        <script>
                                            //<![CDATA[
                                            schools.push(new REWMap.Marker({
                                                'map': $('#idx-map-onboard').data('REWMap'),
                                                'tooltip': '<?=addslashes($tooltip); ?>',
                                                'icon': iconSchool,
                                                'lat': <?=floatval($nearby_school['LATITUDE']); ?>,
                                                'lng': <?=floatval($nearby_school['LONGITUDE']); ?>,
                                                'zIndex': 1
                                            }));
                                            //]]>
                                        </script>
                                    </td>
                                    <td><?= $nearby_school['GRADE_SPAN_CODE_BLDG_TEXT']; ?></td>
                                    <td><?= number_format($nearby_school['distance'], 2); ?> mi</td>
                                </tr>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php else : ?>

                        <div class="msg">
                            <p>No nearby schools could be found at this time.</p>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

                <?php endif; ?>

            </div>

            <div id="community-information"
                 class="panel <?= ($view == 'community-information') ? 'loaded' : 'hidden'; ?>">

                <?php if ($view == 'community-information') : ?>

                    <?php if (!empty($_POST['ajax'])) ob_clean(); ?>

                    <div class="details-extended">
                        <?php foreach (array_chunk($statistics, ceil(count($statistics) / 2)) as $statistics) { ?>
                            <div class="col">
                                <?php foreach ($statistics as $statistic) { ?>
                                    <div class="keyvalset">
                                        <?php if (!empty($statistic['title'])) { ?>
                                            <h3><?= $statistic['title']; ?>:</h3>
                                        <?php } ?>
                                        <ul>
                                            <?php foreach ($statistic['statistics'] as $stats) { ?>
                                                <li class="keyval">
                                                    <strong><?= $stats['title']; ?>:</strong>
                                                    <span><?= $stats['value']; ?></span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

                <?php endif; ?>

            </div>

        </div>

        <p class="disclaimer">Disclaimer / Sources: <?= Lang::write('MLS'); ?> local resources application developed
            and powered by <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate
                Webmasters</a> - Neighborhood data provided by Onboard Informatics &copy; <?= date('Y'); ?> -
            Mapping Technologies powered by Google Maps&trade;</p>
    </div>

<?php

}

// Show MLS Office / Agent
if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
}
