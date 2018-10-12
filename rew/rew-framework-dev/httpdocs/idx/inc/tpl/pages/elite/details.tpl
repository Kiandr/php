<?php if (empty($listing)) { ?>
    <div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
        <h1>Listing Not Found</h1>
        <p class="uk-text-large">The selected listing could not be found. This is probably because
            the listing has been sold, removed from the market, or simply moved.</p>
        <p><a href="/idx/" class="uk-button">Return to Listings</a></p>
        <div class="uk-margin-large-bottom"></div>
    </div>
    <?php return;
} ?>

<?php

// Get skins settings
$settings = $this->getSkin()->getSettings();

require_once $page->locateTemplate('idx', 'misc', 'js', 'listing');

// Disable CTA if agent spotlight is disabled
if (empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) {
    $settings['agent_id'] = false;
}

$page->info('class', 'idx-data');

// Back URL
$url_back = (User_Session::get()->url_back() ? Http_Uri::getScheme() . "://" . $_SERVER['HTTP_HOST'] . User_Session::get()->url_back() : Settings::getInstance()->SETTINGS['URL_IDX']);

// Add data to $listing so we can access it in page.tpl
$listing['saved'] = !empty($bookmarked);

// Listing details
$_DETAILS = $idx->getDetails() ? $idx->getDetails() : array();

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
        $provider_info = array(
            'heading' => $_COMPLIANCE['details']['lang']['listing_details'] ?: 'Listing Details',
            'fields' => array(
                $show_agent ? array('title' => 'Agent', 'value' => 'ListingAgent') : false,
                $show_office ? array(
                    'title' => $_COMPLIANCE['details']['lang']['provider'] ?: 'Office',
                    'value' => 'ListingOffice',
                ) : false,
                $show_office_phone ? array('title' => 'Office #', 'value' => 'ListingOfficePhoneNumber') : false,
                $show_icon ? array('title' => 'COMPLIANCE_ICON', 'value' => $_COMPLIANCE['details']['show_icon']) : false
            )
        );
        if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
            array_unshift($_DETAILS, $provider_info);
        } else {
            array_push($_DETAILS, $provider_info);
        }
    }
}

$details = array();
foreach ($_DETAILS as $data) {
    $fields = array();
    $paragraphs = array();
    foreach ($data['fields'] as $k => $field) {

        if ($field['title'] == 'COMPLIANCE_ICON') {
            // Set icon value for Compliance
            $field['title'] = '';
            $value = $field['value'];

        } else {
            // Field Value
            $value = $listing[$field['value']];
        }

        // Format Value
        if (isset($field['format']) && !empty($value)) $value = tpl_format($value, $field['format']);

        // Skip Empty
        if (empty($value)) continue;

        // Length Over 30 Characters
        if (strlen($value) > 30) {
            $paragraphs[] = array('heading' => $field['title'], 'value' => $value);
            continue;
        }

        // Add Data
        $fields[] = array('title' => $field['title'], 'value' => $value);

    }

    // Skip Empty
    if (empty($fields) && empty($paragraphs)) continue;

    // Add Details
    $details[] = array('heading' => $data['heading'], 'fields' => $fields);
    $details = array_merge($details, $paragraphs);
}

// Store listing data in page
$page->info('listing', $listing);

$summary = array();
if ($listing['NumberOfBedrooms'] > 0) {
    $summary[] = array('value' => $listing['NumberOfBedrooms'], 'title' => Format::plural($listing['NumberOfBedrooms'], 'Beds', 'Bed'));
}
if ($listing['NumberOfBathrooms'] > 0) {
    $summary[] = array('value' => Format::fraction($listing['NumberOfBathrooms']), 'title' => Format::plural($listing['NumberOfBathrooms'], 'Baths', 'Bath'));
}
if ($listing['NumberOfSqFt'] > 0) {
    $summary[] = array('value' => Format::number($listing['NumberOfSqFt']), 'title' => 'Sqft');
}
if ($listing['NumberOfAcres'] > 0) {
    $summary[] = array('value' => Format::fraction($listing['NumberOfAcres']), 'title' => 'Acres');
}
if ($listing['ListingDOM'] > 0) {
    $summary[] = array('value' => Format::fraction($listing['ListingDOM']), 'title' => 'DOM');
}

// Show up to 4 blocks
$summary = array_slice($summary, 0, 4);
if (count($summary) < 4) {
    array_unshift($summary, array('value' => '$' . Format::shortNumber($listing['ListingPrice']), 'title' => 'Price'));
}

$address = implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState'])));

// Photo Gallery
$this->container('gallery')->module('gallery', array(
    'enlarge' => false,
    'flag' => !empty($listing['flag']) ? $listing['flag'] : null,
    'images' => $listing['thumbnails'],
    'title' => $address
))->display();

?>

<?php //TOOLBAR NOTE: THE USE OF IMAGE LINKS IS NOT GOOD ENOUGH. REPLACE WITH SVG/PHP DRIVEN GRAPHIC AFTER ?>
<section class="fw fw-idx-details-toolbar uk-nbfc">
    <div class="toolbar-left">
        <a href="<?= Format::htmlspecialchars($url_back); ?>">
            <img
                src="<?= Format::htmlspecialchars($page->getSkin()->getUrl()); ?>/img/back-to-results.png">
            <span class="back-to-results">Back to Results</span>
        </a>
    </div>
    <div class="toolbar-right">
        <a href="" class="uk-hidden fw-prev-listing">
            <img
                src="<?= Format::htmlspecialchars($page->getSkin()->getUrl()); ?>/img/prev-arrow.png">
            <span class="idx-details-prev">Prev</span>
        </a>
        <a href="" class="uk-hidden fw-next-listing">
            <span class="idx-details-next">Next</span>
            <img
                src="<?= Format::htmlspecialchars($page->getSkin()->getUrl()); ?>/img/next-arrow.png">
        </a>
    </div>
    <div class="uk-container uk-container-center toolbar-content">
        <div class="uk-grid">
            <div class="uk-width-large-2-3 uk-width-medium-2-3 uk-width-small-1-1 uk-row-first">
                <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing)) { ?>
                    <h2><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?></h2>
                <?php } ?>
                <h2 class="uk-margin-small"><?= htmlspecialchars($address); ?></h2>
                <span class="property-summary">
                    $<?= Format::number($listing['ListingPrice']); ?>
                    <?php foreach ($summary as $index => $detail) { ?>
                        -
                        <span>
                            <?= Format::htmlspecialchars($detail['value']); ?>
                            <?= Format::htmlspecialchars($detail['title']); ?>
                        </span>
                    <?php } ?>
                </span>
            </div>
            <div
                class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1 showing-btn-container">
                <a href="<?= Format::htmlspecialchars($listing['url_inquire']); ?>?inquire_type=Property Showing"
                   data-modal-auto="inquire-showing" class="uk-button uk-button-medium">REQUEST A
                    SHOWING <i class="uk-icon-justified uk-icon-angle-down" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</section>

<section class="fw fw-idx-details-desc">
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="uk-width-large-2-3 uk-width-medium-2-3 uk-width-small-1-1 uk-row-first">
                <p class="idx-deets-main-desc"><?= ($idx->getLink() == 'cms') ? nl2br(htmlspecialchars($listing['ListingRemarks'])) : htmlspecialchars($listing['ListingRemarks']); ?></p>
                <?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
                    <p><?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?></p>
                <?php } ?>
            </div>
            <div class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1">

                <div class="idx-deets-save-listing">
                    <button
                        class="uk-button uk-button-medium uk-button-secondary action-save<?= empty($bookmarked) ? ' js-save-listing' : ''; ?>"
                        data-save>Save this
                        listing <?= (!empty($bookmarked) ? '<i class="uk-icon-heart" aria-hidden="true"></i>' : '<i class="uk-icon-heart-o" aria-hidden="true"></i>'); ?></button>
                </div><!-- /.idx-deets-save-listing -->

                <div class="idx-deets-extras uk-margin-top">
                    <a href="<?= Format::htmlspecialchars($listing['url_inquire']); ?>?inquire_type=More Info"
                       data-modal-auto="inquire">
                        <i class="uk-icon-justified uk-icon-comment" aria-hidden="true"></i> Inquire
                    </a>
                    <a href="<?= Format::htmlspecialchars($listing['url_sendtofriend']); ?>"
                       data-modal-auto="share">
                        <i class="uk-icon-justified uk-icon-share-square-o" aria-hidden="true"></i>
                        Share
                    </a>
                    <?php if (!empty($listing['VirtualTour'])) { ?>
                        <a href="<?= Format::htmlspecialchars($listing['VirtualTour']); ?>"
                           data-uk-lightbox data-lightbox-type="iframe" class="js-view-vtour">
                            <i class="uk-icon-justified uk-icon-video-camera"
                               aria-hidden="true"></i> V-Tour
                        </a>
                    <?php } else { ?>
                        <span class="uk-text-muted vtour-muted">
                                                            <i class="uk-icon-justified uk-icon-video-camera"
                                                               aria-hidden="true"></i> V-Tour
                                                    </span>
                    <?php } ?>
                    <a id="view-map"
                       href="<?= Format::htmlspecialchars($listing['url_map']); ?>?iframe"
                       data-uk-lightbox data-lightbox-type="iframe">
                        <i class="uk-icon-justified uk-icon-map-marker" aria-hidden="true"></i>
                        Locate
                    </a>
                    <a href="<?= Format::htmlspecialchars($listing['url_brochure']); ?>"
                       target="_blank">
                        <i class="uk-icon-justified uk-icon-print" aria-hidden="true"></i> Print
                    </a>
                    <!-- @TODO: don't link to a static page -->
                    <a href="/mortgage-calculator.php?sale_price=<?= ((int)$listing['ListingPrice']); ?>"
                       data-modal-auto>
                        <i class="uk-icon-justified uk-icon-dollar" aria-hidden="true"></i>
                        Calculate
                    </a>
                </div><!-- /.idx-deets-extras -->
            </div>
        </div>
    </div>
</section><!-- /.fw-idx-details-desc -->

<section class="fw fw-idx-details fw-idx-listing-groups uk-nbfc">
    <div class="uk-container uk-container-center">
        <?php // Listing Details ?>
        <?php foreach ($details ?: array() as $i => $info) { ?>
            <div
                class="uk-grid listing-detail-group group-<?= str_replace(' ', '_', strtolower(Format::htmlspecialchars($info['heading']))); ?>">
                <div
                    class="uk-width-large-1-1 uk-width-xlarge-1-4 uk-width-medium-1-1 uk-width-small-1-1 listing-group-title">
                    <h2><?= Format::htmlspecialchars($info['heading']); ?></h2>
                    <small>
                        For <?= implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))); ?></small>
                    <a class="listing-group-toggle"
                       data-uk-toggle="{target: '.listing-group-panel.fw-panel-<?= $i; ?>'}">
                        <i class="uk-icon-angle-down"></i>
                    </a>
                </div>
                <?php if (!empty($info['fields'])) { ?>
                    <div
                        class="uk-width-large-1-1 uk-width-xlarge-3-4 uk-width-medium-1-1 uk-width-small-1-1">
                        <div class="uk-hidden listing-group-panel fw-panel-<?= $i; ?>">
                            <ul class="uk-list">
                                <?php foreach ($info['fields'] as $field) { ?>
                                    <li class="listing-group-li">
                                        <strong><?= Format::htmlspecialchars($field['title']); ?></strong>
                                        <span><?= Format::htmlspecialchars($field['value']); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } else if (!empty($info['value'])) { ?>
                    <div class="uk-hidden listing-group-panel fw-panel-<?= $i; ?>">
                        <p><?= Format::htmlspecialchars($info['value']); ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>

<?php if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
<div class="details-logos">
    <?php foreach ($_COMPLIANCE['details']['logos'] as $logo) { ?>
    <img src="<?=$logo; ?>">
    <?php } ?>
</div>
<?php } ?>

<?php include $page->locateTemplate('idx', 'misc', 'history'); ?>

<?php
// Similar Listings
if ($similarListings = $this->container('listing-similar')->module('listing-similar', array(
    'listing' => $listing,
    'limit' => 4
))->display(false)) { ?>
    <div class="fw fw-idx-details fw-idx-similar-listings">
        <div class="uk-container uk-container-center">
            <?= $similarListings; ?>
        </div>
    </div>
<?php } ?>
