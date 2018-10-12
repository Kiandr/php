<?php

// Listing Not Found
if (empty($listing)) {

?>
<div class="hero hero--cover">
    <div class="hero__fg">
        <div class="hero__body -flex">
            <div class="container">
                <h1>Listing Not Found</h1>
                <div class="notice notice--negative">
                    <div class="notice__message">
                        The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

} else {

    $registrationOnMorePics = Settings::getInstance()->SETTINGS['registration_on_more_pics'];

    // Enhanced feature data
    $enhancedFeatureData = [];
    $enhancedFeatureType = NULL;
    if (!empty($listing['enhanced'])) {
        $enhancedFeatureType = $listing['enhanced']['feature'];
        $page->getSkin()->addBackgroundDependencies($enhancedFeatureType);
        switch ($enhancedFeatureType) {
            case 'slides':
            case 'video':
            case 'pano':
            case '360':
                $enhancedFeatureData = $listing['enhanced'][$enhancedFeatureType];
                break;
        }

    }

    // <body class> for enhanced listings
    if (!empty($listing['enhanced'])) {
        $bodyClass = $page->info('class');
        $bodyClass .= ' enhanced-listing';
        $page->info('class', $bodyClass);
    }

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
            if (empty($_COMPLIANCE['details']['details_provider_first'])) {
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

    // Main header
    $header = empty($listing['Address']) || ($listing['Address'] == 'N/A') ? '(Undisclosed Address)' : $listing['Address'];

    // Photo gallery
    $this->container('here')->addModule('fgallery', [
        'images' => $listing['thumbnails'],
        'registration_on_more_pics' => $registrationOnMorePics,
        'hidePhotos' => true
    ])->display();

?>
<?php if (!empty($listing['enhanced'])) { ?>
    <div class="hero hero--cover">
        <div class="hero--details container">
            <?php if(!empty($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) { ?>
            <div class="columns -mar-0@xs">
                <div class="column -left -width-1/1@xs -width-1/1@sm">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
                </div>
            </div>
            <?php } ?>
            <div class="columns -mar-0@xs">
                <div class="column -left -width-1/1@xs -width-1/1@sm">
                    <h1 class="-fg-invert -mar-0@xs -mar-0@sm"><?=htmlspecialchars($header); ?></h1>
                    <h3 class="-fg-invert"><?=htmlspecialchars($listing['AddressCity']); ?></h3>
                </div>
                <div class="column -right -width-1/1@xs -width-1/1@sm -text-right -text-left@sm -text-left@xs">
                    <h1 class="-fg-invert -mar-0@xs -mar-0@sm">$<?=Format::number($listing['ListingPrice']); ?></h1>
                    <h3 class="-fg-invert">
                        <a <?php if ($registrationOnMorePics) { ?> href="<?= Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>" class="popup"<?php } else { ?>class="action-gallery"<?php } ?>>
                            View Gallery
                        </a>
                        <?php $need_margin = empty($listing['enhanced']['google-vr']) || empty($listing['enhanced']['immoviewer']['tour_id']); ?>
                        <?php if (!empty($listing['enhanced']['google-vr'])) { ?>
                            <span class="-pad-horizontal-xs cardboard-vr">|</span>
                            <a href="<?=$listing['url_google-vr']; ?>" class="action-vr-tour<?=$need_margin ? ' -mar-right-sm' : ''; ?>">
                                <svg class="icon icon--lg">
                                    <use xlink:href="/inc/skins/ce/img/assets.svg#icon--vr"/>
                                </svg>
                            </a>
                        <?php } ?>
                        <?php if (!empty($listing['enhanced']['immoviewer']['tour_id'])) { ?>
                            <span class="-pad-horizontal-xs">|</span>
                            <a href="<?=sprintf('https://app.immoviewer.com/portal/tour/%s', $listing['enhanced']['immoviewer']['tour_id']); ?>" target="_blank" class="action-vr-tour<?=$need_margin ? ' -mar-right-sm' : ''; ?>" title="Click to view Immoviewer Tour">
                                <svg class="icon icon--lg">
                                    <title>Immoviewer icon will open a new window</title>
                                    <use xlink:href="/inc/skins/ce/img/assets.svg#icon--immoviewer"/>
                                </svg>
                            </a>
                        <?php } ?>
                     </h3>
                </div>
            </div>
        </div>
        <div id="feature"
             <?=$enhancedFeatureType === 'video' ? sprintf('data-video-id="%s" data-video-mute="%s" data-video-autoplay="%s" data-video-autopause="%s"', (string) $enhancedFeatureData['id'], (int) $enhancedFeatureData['mute'], (string) $enhancedFeatureData['autoplay'], (string) $enhancedFeatureData['pause']) : ''; ?>
             <?=$enhancedFeatureType === 'pano' ? sprintf('data-pano-src="%s"', $enhancedFeatureData ? str_replace('/uploads/', '/thumbs/x983/r/uploads/', $enhancedFeatureData) : str_replace('/uploads/', '/thumbs/x983/r/uploads/', $listing['ListingImage'])) : ''; ?>
             class="hero__bg
             <?=$enhancedFeatureType === 'video' && empty($enhancedFeatureData['interact']) ? ' -is-pointer-disabled' : ''; ?>
             <?=$enhancedFeatureType === 'pano' ? ' -is-grabbable' : ''; ?>
            ">
            <?php if ($enhancedFeatureType !== 'video') { ?>
                <div class="hero__bg-content">
                    <?php

                        // Enhanced listing slideshow photos
                        if ($enhancedFeatureType === 'slides') {
                            $this->container('gallery')->module('slideshow', [
                                'images' => $enhancedFeatureData ?: $listing['thumbnails'],
                                'registration_on_more_pics' => $registrationOnMorePics
                            ])->display();

                            // Enhanced listing 360 Photo
                        } else if ($enhancedFeatureType === '360') {
                            $this->container('gallery')->module('slideshow', [
                                '360'    => $enhancedFeatureData ?: $listing['ListingImage'],
                                'images' => $listing['thumbnails'],
                                'registration_on_more_pics' => $registrationOnMorePics
                            ])->display();

                        // Listing slideshow photos (using IDX photos)
                        } elseif (!in_array($enhancedFeatureType, ['pano', '360', 'video'])) {
                            $this->container('gallery')->module('slideshow', [
                                'images' => $listing['thumbnails'],
                                'registration_on_more_pics' => $registrationOnMorePics
                            ])->display();

                        }

                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<!-- END IDX Enhanced Listings View -->

<div id="listing-details" data-listing='<?=$listingData; ?>' class="container<?=!empty($bookmarked) ? ' saved' : ''; ?>">
    <div class="<?=!empty($listing['enhanced']) ? '-pad-top' : ''; ?>">
        <div class="columns">
            <div class="column -width-3/4 -width-2/3@md -width-1/1@sm -width-1/1@xs -pad-right-lg -pad-0@sm -pad-0@xs">

				<?php if (empty($listing['enhanced'])) { ?>

                    <div class="-clear">
                        <?php if(!empty($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) { ?>
                            <div class="columns -mar-0@xs">
                                <div class="column -left -width-1/1@xs -width-1/1@sm -mar-0@xs">
                                    <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <h1 class="-mar-0@xs -mar-0@sm -text-lg -left -text-plain -pad-right-sm@md -pad-right-sm@sm -pad-0@xs"><?=htmlspecialchars($header); ?>, <small><?=htmlspecialchars($listing['AddressCity']); ?></small></h1>
                        <h2 class="-text-lg -right -left@xs">$<?=Format::number($listing['ListingPrice']); ?></h2>
                    </div>

                    <div class="hero hero--landscape">
                        <div class="hero__bg">
                            <div class="hero__bg-content">
                                <?php

                                    // Display listing photos
                                    $this->container('gallery')->module('slideshow', [
                                        'images' => $listing['thumbnails'],
                                        'registration_on_more_pics' => $registrationOnMorePics,
                                    ])->display();

                                ?>
                            </div>
                        </div>
                    </div>

				<?php } ?>

                <?php if (empty($listing['enhanced'])) { ?>
                    <div class="columns -pad-top-xs">
                        <?php if (!empty($listing['VirtualTour'])) { ?>
                            <a class="column -width-1/2 -width-1/1@sm button" target="_blank" href="<?=htmlspecialchars($listing['VirtualTour']); ?>">
                                <svg class="icon icon--xs -mar-right-xs">
                                    <use xlink:href="/inc/skins/ce/img/assets.svg#icon--tour" />
                                </svg> Virtual Tour
                            </a>
                        <?php } ?>
                        <?php if ($registrationOnMorePics) { ?>
                            <a href="<?= Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>" class="column <?php if (!empty($listing['VirtualTour'])) { ?>-width-1/2<?php } else { ?>-width-1/1<?php } ?> -width-1/1@sm button popup">
                        <?php } else { ?>
                            <a href="#" class="column <?php if (!empty($listing['VirtualTour'])) { ?>-width-1/2<?php } else { ?>-width-1/1<?php } ?> -width-1/1@sm button action-gallery">
                        <?php } ?>
                            <svg class="icon icon--xs -mar-right-xs">
                                <use xlink:href="/inc/skins/ce/img/assets.svg#icon--photo" />
                            </svg>
                            Photo Gallery
                        </a>
                    </div>
                <?php } ?>

                <?php if (!empty($listing['enhanced'])) {?>
                    <h2>About This Property</h2>
                <?php } ?>

                <div class="columns columns--space-between columns--no-wrap -text-sm -text-center columns--glyphs -pad-top">
                    <div class="column">
                        <svg class="glyph -sm">
                            <use xlink:href="/inc/skins/ce/img/assets.svg#glyph--bed" />
                        </svg>
                        <div><?=htmlspecialchars($listing['NumberOfBedrooms']) ?: 0; ?> Beds</div>
                    </div>
                    <div class="column">
                        <svg class="glyph -sm">
                            <use xlink:href="/inc/skins/ce/img/assets.svg#glyph--bath" />
                        </svg>
                        <div><?=Format::fraction($listing['NumberOfBathrooms']) ?: 0; ?> Baths</div>
                    </div>
                    <div class="column">
                        <svg class="glyph -sm">
                            <use xlink:href="/inc/skins/ce/img/assets.svg#glyph--measure" />
                        </svg>
                        <div><?=Format::number($listing['NumberOfSqFt']) ?: 0; ?> Sqft</div>
                    </div>
                    <div class="column">
                        <svg class="glyph -sm">
                            <use xlink:href="/inc/skins/ce/img/assets.svg#glyph--fence" />
                        </svg>
                        <div><?=Format::fraction($listing['NumberOfAcres']) ?: 0; ?> Acres</div>
                    </div>
                    <div class="column">
                        <svg class="glyph -sm">
                            <use xlink:href="/inc/skins/ce/img/assets.svg#glyph--garage" />
                        </svg>
                        <div><?=Format::fraction($listing['NumberOfGarages']) ?: 0; ?> Garages</div>
                    </div>
                </div>

                <br>

                <?php if (!empty($listing['ListingRemarks'])) { ?>
                    <p><?=nl2br(htmlspecialchars($listing['ListingRemarks'])); ?></p>
                <?php } ?>

                <?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
                    <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
                <?php } ?>

                <div class="data listing-data">
                    <?php

                    // Listing Details
                    if (!empty($details)) {
                        foreach ($details as $info) {
                            echo '<div class="keyvals -mar-bottom">';
                            if (!empty($info['fields'])) {
                                echo '<div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">' . $info['heading'] . '</span></div>';
                                echo '<div class="keyvals__body">';
                                foreach ($info['fields'] as $field) {
                                    echo '<div class="keyval">';
                                    echo '<strong class="keyval__key">' . $field['title'] . '</strong>';
                                    echo '<span class="keyval__val">' . $field['value'] . '</span>';
                                    echo '</div>';
                                }
                                echo '</div>';

                            } elseif(!empty($info['value'])) {
                                echo '<div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">' . $info['heading'] . '</span></div>';
                                echo '<div class="keyvals__body">';
                                echo '<p><strong>' . $info['value'] . '</strong></p>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                    }

                    if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
                    <div class="details-logos">
                    <?php foreach ($_COMPLIANCE['details']['logos'] as $logo) { ?>
                        <img src="<?=$logo; ?>">
                    <?php } ?>
                    </div>
                    <?php
                    }

                    // Include status/price change history
                    include $page->locateTemplate('idx', 'misc', 'history');

                    ?>
                </div>

                <?php if (!empty($mapping)) { ?>
                    <div class="nav nav--tabs -mar-vertical-md -clear">
                        <ul class="nav__list switch">
                            <li class="nav__item -is-current" >
                                <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
                                    <a class="nav__link" data-target="map">Map &amp; Directions</a>
                                <?php } else { ?>
                                    <a class="nav__link" data-target="map">Map</a>
                                <?php } ?>
                            </li>
                            <?php if (!empty($streetview)) { ?>
                                <li class="nav__item hidden"><a class="nav__link" data-target="streetview">Streetview</a></li>
                            <?php } ?>
                            <?php if (!empty($birdseye)) { ?>
                                <li class="nav__item"><a class="nav__link" data-target="birdseye">Bird's Eye View</a></li>
                            <?php } ?>
                            <?php if (!empty($onboard)) { ?>
                                <li class="nav__item"><a class="nav__link" href="<?=$listing['url_onboard']; ?>">Get Local</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div id="tabbed-content">
                        <div id="tab-map">
                            <div id="map-canvas" class="content--map"></div>
                            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
                                <div id="map-directions" class="map-directions pad-sm">
                                    <form action="#map-directions" class="pad-sm">
                                        <div class="field">
                                            <h4>Get directions to this property:</h4>
                                            <input name="from" value="<?=htmlspecialchars($_GET['from']); ?>" placeholder="From&hellip;" required>
                                        </div>
                                        <input type="hidden" name="to" value="<?=htmlspecialchars( !empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>">
                                        <div class="buttons -mar-top-xs"><button type="submit" class="button -text-sm -mar-bottom-xs -mar-right-md">Get Directions</button></div>
                                    </form>
                                    <div class="directions-panel"></div>
                                </div>
                            <?php } ?>
                        </div>
                        <div id="tab-streetview" class="hidden">
                            <div id="map-streetview" class="content--map"></div>
                        </div>
                        <div id="tab-birdseye" class="hidden">
                            <div id="map-birdseye" class="content--map"></div>
                        </div>
                    </div>
                <?php } ?>

                <?php
                // Include similar listings
                $this->container('listing-similar')->module('listing-similar', [
                    'uid' => 'similar-listings',
                    'listing' => $listing
                ])->display();
                ?>

            </div>

            <div class="column -width-1/4 -width-1/3@md -width-1/1@sm -width-1/1@xs listing-contact" id="listing-contact">
                <div class="nav nav--stacked listing-nav -mar-top-0@sm -mar-top-0@xs">
                    <div class="buttons  -mar-bottom-sm -mar-bottom-0@sm -mar-bottom-0@xs">
                        <a class="button button--strong -text-center -cta block inquire popup" rel="nofollow" href="<?=$listing['url_inquire']; ?>">Ask about this Property</a>
                    </div>
                    <div class="nav__wrap">
                    <ul class="nav__list -pad-bottom-0">
                            <?php if ($listing['idx'] !== 'cms') { ?>
                            <li class="nav__item">
                            <a class="nav__link action-save<?=!empty($bookmarked) ? ' saved' : ''; ?>" data-save='<?=json_encode([
                                'remove' => 'Remove ' . Locale::spell('Favorite'),
                                'save'   => 'Save as ' . Locale::spell('Favorite'),
                                'feed'   => !empty($listing['idx']) ? $listing['idx'] : Settings::getInstance()->IDX_FEED,
                                'mls'    => $listing['ListingMLS']
                            ]); ?>'>
                                <svg class="icon icon--xs">
                                    <use xlink:href="/inc/skins/ce/img/assets.svg#icon--star" />
                                </svg>
								<span class="-is-hidden@sm">
                                	<?=!empty($bookmarked) ? 'Remove ' . Locale::spell('Favorite') : 'Save as ' . Locale::spell('Favorite'); ?>
								</span>
                            </a>
                            </li>
                            <?php } ?>
                        <li class="nav__item">
                            <a rel="nofollow" class="nav__link popup" href="<?=$listing['url_sendtofriend']; ?>">
                                <svg class="icon icon--xs">
                                    <use xlink:href="/inc/skins/ce/img/assets.svg#icon--share" />
                                </svg>
								<span class="-is-hidden@sm">
                                	Share This Listing
								</span>
                            </a>
                        </li>
                        <?php if (!empty($listing['enhanced'])) { ?>
                            <?php if (!empty($listing['VirtualTour'])) { ?>
                                <li class="nav__item">
                                    <a class="nav__link" target="_blank" href="<?=htmlspecialchars($listing['VirtualTour']); ?>">
                                        <svg class="icon icon--xs">
                                            <use xlink:href="/inc/skins/ce/img/assets.svg#icon--tour" />
                                        </svg>
                                        <span class="-is-hidden@sm">View Virtual Tour</span>
                                    </a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        <li class="nav__item">
                            <a class="nav__link" target="_blank" rel="nofollow" href="<?=$listing['url_brochure']; ?>" style="border: none">
								<svg class="icon icon--xs">
									<use xlink:href="/inc/skins/ce/img/assets.svg#icon--print" />
								</svg>
								<span class="-is-hidden@sm">
									Print This Listing
								</span>
							</a>
                        </li>
                    </ul>

                    <div class="columns buttons -mar-top-lg -text-xs" id="idx-paginate"></div>

                    <div class="details__agent -mar-top-md -is-hidden@sm -mar-bottom-md">
                        <?php

                        // Display listing agent
                        $this->container('listing-agent')->module('listing-agent', [
                            'thumbnails' => false,
                            'listing' => $listing,
                            'phone' => true,
                            'cell' => true
                        ])->display();

                        ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

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
                                'tooltip' => call_user_func(function ($listing_tooltip, $_COMPLIANCE) {
                                    ob_start();
                                    include $this->locateTemplate('idx', 'misc', 'tooltip');
                                    return str_replace(["\r\n", "\n", "\t"], "", ob_get_clean());
                                }, $listing, $_COMPLIANCE),
                                'lat' => $listing['Latitude'],
                                'lng' => $listing['Longitude']
                            ]]
                        ]
                    ]) . ';
                ', 'dynamic', false);
            }
        ?>
    </div>
</div>
<?php

}

?>