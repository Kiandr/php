<?php

// Listing Not Found
if (empty($listing)) {
    echo '<h1>Listing Not Found</h1>';
    echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

    // Check if mapping features are available for this property listing
    $mapping = !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']);
    if (empty($listing['Latitude']) || empty($listing['Longitude'])) {
        $mapping = false;
    }

    // Show "Get Local"
    $onboard = $mapping && !empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']);

    // Show "Google Streetview"
    $streetview = $mapping && Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'];

    // Show "Bird's Eye View"
    $birdseye = $mapping && Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'];

    // Listing data for <script>
    $listingData = json_encode([
        'geo'  => $mapping ? [$listing['Latitude'], $listing['Longitude']] : false,
        'mls'  => $listing['ListingMLS'],
        'feed' => $listing['idx']
    ]);

    // Listing details
    $_DETAILS = $idx->getDetails() ? $idx->getDetails() : [];

    // Compliance requirement
    $show_agent = $_COMPLIANCE['details']['show_agent'];
    $show_office = $_COMPLIANCE['details']['show_office'];
    $show_office_phone = $_COMPLIANCE['details']['show_office_phone'];
    $show_icon = $_COMPLIANCE['details']['show_icon'];
    if ($show_agent || $show_office || $show_office_phone || $show_icon) {
        if (is_callable($_COMPLIANCE['details']['extra'])) {
            if ($details_extra = $_COMPLIANCE['details']['extra']($idx, $db_idx, $listing, $_COMPLIANCE)) {
                foreach ($details_extra as $extra) {
                    if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
                        array_unshift($_DETAILS, $extra);
                    } else {
                        array_push($_DETAILS, $extra);
                    }
                }
            }
        } else {
            $provider_info = [
                'heading' => $_COMPLIANCE['details']['lang']['listing_details'] ?: 'Listing Details',
                'fields' => [
                    $show_agent ? ['title' => 'Agent', 'value' => 'ListingAgent'] : false,
                    $show_office ? [
                        'title' => $_COMPLIANCE['details']['lang']['provider'] ?: 'Office',
                        'value' => 'ListingOffice',
                    ] : false,
                    $show_office_phone ? ['title' => 'Office #', 'value' => 'ListingOfficePhoneNumber'] : false,
                    $show_icon ? ['title' => 'COMPLIANCE_ICON', 'value' => $_COMPLIANCE['details']['show_icon']] : false
                ]
            ];
            if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
                array_unshift($_DETAILS, $provider_info);
            } else {
                array_push($_DETAILS, $provider_info);
            }
        }
    }

    $details = [];
    foreach ($_DETAILS as $data) {
        $fields = [];
        $paragraphs = [];
        foreach ($data['fields'] as $k => $field) {

            // Set icon value for Compliance
            if ($field['title'] == 'COMPLIANCE_ICON') {
                $field['title'] = '';
                $value = $field['value'];
            } else {
                $value = $listing[$field['value']];
            }

            // Format Value
            if (isset($field['format']) && !empty($value)) $value = tpl_format($value, $field['format']);

            // Skip Empty
            if (empty($value)) continue;

            // Length Over 30 Characters
            if (strlen($value) > 30) {
                $paragraphs[] = ['heading' => $field['title'], 'value' => $value];
                continue;
            }

            // Add Data
            $fields[] = ['title' => $field['title'], 'value' => $value];

        }

        // Skip Empty
        if (empty($fields) && empty($paragraphs)) continue;

        // Add Details
        $details[] = ['heading' => $data['heading'], 'fields' => $fields];
        $details = array_merge($details, $paragraphs);
    }

    if(!empty($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
    }

    // Main header
    $header = empty($listing['Address']) || ($listing['Address'] == 'N/A') ? '(Undisclosed Address)' : $listing['Address'];

    // Sub-header
    $subheader = [];
    $subheader[] = '$' . Format::number($listing['ListingPrice']) . ', ';
    if ($listing['NumberOfBedrooms'] > 0)   $subheader[] = $listing['NumberOfBedrooms'] . Format::plural($listing['NumberOfBedrooms'], ' Beds', ' Bed');
    if ($listing['NumberOfBathrooms'] > 0)  $subheader[] = Format::fraction($listing['NumberOfBathrooms']) . Format::plural($listing['NumberOfBathrooms'], ' Baths', ' Bath');
    if ($listing['NumberOfSqFt'] > 0)       $subheader[] = Format::number($listing['NumberOfSqFt']) . ' Sqft';
    if ($listing['NumberOfAcres'] > 0)      $subheader[] = Format::fraction($listing['NumberOfAcres']) . ' Acres';
    if ($listing['NumberOfGarages'] > 0)    $subheader[] = Format::fraction($listing['NumberOfGarages']) . ' Garages';
    $subheader = rtrim(implode(' ', $subheader), ', ');

?>
<div id="listing-details"<?=!empty($bookmarked) ? ' class="saved"' : ''; ?> data-listing='<?=$listingData; ?>'>

    <h1><?=$header; ?></h1>
    <h2><?=$subheader; ?></h2>

    <div class="navbar">
        <div id="idx-paginate" class="hidden"></div>
        <div id="idx-links">
            <a class="inquire popup strong L"  rel="nofollow" href="<?=$listing['url_inquire']; ?>">Contact Agent</a>
            <?php if ($listing['idx'] !== 'cms') { ?>
                <a class="L" data-save='<?=json_encode([
                    'remove'    => 'Remove ' . Locale::spell('Favorite'),
                    'save'      => 'Save as ' . Locale::spell('Favorite'),
                    'feed'		=> !empty($listing['idx']) ? $listing['idx'] : Settings::getInstance()->IDX_FEED,
                    'mls'		=> $listing['ListingMLS']
                ]); ?>'>
                    <?=!empty($bookmarked) ? 'Remove ' . Locale::spell('Favorite') : 'Save as ' . Locale::spell('Favorite'); ?>
                </a>
            <?php } ?>
            <a rel="nofollow" class="L popup" href="<?=$listing['url_sendtofriend']; ?>">Share This Listing</a>
            <?php if (!empty($listing['VirtualTour'])) { ?>
                <a target="_blank" href="<?=$listing['VirtualTour']; ?>">Virtual Tour</a>
            <?php } ?>
            <a class="R" target="_blank" rel="nofollow" href="<?=$listing['url_brochure']; ?>">Print This Listing</a>
        </div>
    </div>

    <?php

        // Photo Gallery
        $this->container('gallery')->module('fgallery', [
            'register' => (Settings::getInstance()->SETTINGS['registration_on_more_pics'] ? $listing['url_register'] : NULL),
            'images' => $listing['thumbnails'],
        ])->display();

    ?>

    <?php if (!empty($listing['ListingRemarks'])) { ?>
        <div class="ld-about-property">
            <h2 class="page-h2 mar0">About This Property</h2>
            <p class="remarks"><?=($idx->getLink() == 'cms')
                ? nl2br(htmlspecialchars($listing['ListingRemarks']))
                : htmlspecialchars($listing['ListingRemarks']);
            ?></p>
        </div>
    <?php } ?>

    <?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
    <?php } ?>

    <div class="data listing-data">
        <?php

            // Listing Details
            if (!empty($details)) {
                foreach ($details as $info) {
                    echo '<div class="kvs">';
                    echo '<h3 class="page-h3">' . $info['heading'] . '</h3>';
                    if (!empty($info['fields'])) {
                        foreach ($info['fields'] as $field) {
                            echo '<div class="kv">';
                            echo '<strong class="k">' . $field['title'] . '</strong>';
                            echo '<span class="v">' . $field['value'] . '</span>';
                            echo '</div>';
                        }

                    } elseif(!empty($info['value'])) {
                        echo '<p>' . $info['value'] . '</p>';
                    }
                    echo '</div>';
                }
            }

        ?>
    </div>

    <?php if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
    <div class="details-logos">
        <?php foreach ($_COMPLIANCE['details']['logos'] as $logo) { ?>
        <img src="<?=$logo; ?>">
        <?php } ?>
    </div>
    <?php } ?>

    <?php if (!empty($mapping)) { ?>
        <ul class="mnu mnu--tabs marT-md switch">
            <li class="mnu-item mnu-item--cur" data-target="map">
                <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
                    <a>Map &amp; Directions</a>
                <?php } else { ?>
                    <a>Map</a>
                <?php } ?>
            </li>
            <?php if (!empty($streetview)) { ?>
                <li class="mnu-item hidden" data-target="streetview"><a>Streetview</a></li>
            <?php } ?>
            <?php if (!empty($birdseye)) { ?>
                <li class="mnu-item" data-target="birdseye"><a>Bird's Eye View</a></li>
            <?php } ?>
            <?php if (!empty($onboard)) { ?>
                <li class="mnu-item">
                    <a class="get-local" href="<?=$listing['url_onboard']; ?>" target="_blank">
                        Get Local
                    </a>
                </li>
            <?php } ?>
        </ul>
        <div id="tabbed-content">
            <div id="tab-map">
                <div id="map-canvas" class="h1/3 h1/2-md h1/1-sm"></div>
                <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
                    <div id="map-directions" class="map-directions pad-sm">
                        <form action="#map-directions" class="pad-sm">
                            <div class="fld">
                                <h4>Get directions to this property:</h4>
                                <div class="input">
                                    <input name="from" value="<?=Format::htmlspecialchars($_GET['from']); ?>" placeholder="From&hellip;" required>
                                </div>
                            </div>
                            <input type="hidden" name="to" value="<?=Format::htmlspecialchars(!empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>">
                            <div class="btns"><button type="submit" class="btn btn--primary">Get Directions</button></div>
                        </form>
                        <div class="directions-panel"></div>
                    </div>
                <?php } ?>
            </div>
            <?php if (!empty($streetview)) { ?>
            <div id="tab-streetview" class="hidden">
                <div id="map-streetview" class="h1/3 h1/2-md h1/1-sm"></div>
            </div>
            <?php } ?>
            <?php if (!empty($birdseye)) { ?>
            <div id="tab-birdseye" class="hidden">
                <div id="map-birdseye" class="h1/3 h1/2-md h1/1-sm"></div>
            </div>
            <?php } ?>
        </div>
    <?php } ?>

<?php

    // Include similar listings
    //$this->container('listing-similar')->module('listing-similar', [
    //    'uid' => 'similar-listings',
    //    'listing' => $listing
    //])->display();

    // Include status/price change history
    include $page->locateTemplate('idx', 'misc', 'history');

    // Include required javascript code for this page
    $this->addJavascript('js/idx/details.js', 'page');

    // window.MAP_OPTIONS
    if (!empty($mapping)) {
        $this->addJavascript('
            window.MAP_OPTIONS = ' . json_encode([
                'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
                'center' => ['lat' => $listing['Latitude'], 'lng' => $listing['Longitude']],
                'manager' => [
                    'icon' => $this->getSkin()->getUrl() . '/img/map-flag.png',
                    'iconWidth' => 22,
                    'iconHeight' => 25,
                    'bounds' => false,
                    'markers' => [[
                        'tooltip' => call_user_func(function ($listing_tooltip) {
                            ob_start();
                            include $this->locateTemplate('idx', 'misc', 'tooltip');
                            return str_replace(["\r\n", "\n", "\t"], "", ob_get_clean());
                        }, $listing),
                        'lat' => $listing['Latitude'],
                        'lng' => $listing['Longitude']
                    ]]
                ]
            ]) . ';
        ', 'dynamic', false);
    }

}