<?php

// Include IDX Configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Send as Plain Text
header("Content-Type: text/plain");

// Invalid User
if (!$user->isValid()) {
    return;
}

// Close session
@session_write_close();

// Load Saved Favorites
$favorite_listings = array();
$saved_favorites_count = $db_users->fetchQuery("SELECT COUNT(id) AS total FROM " . TABLE_SAVED_LISTINGS . " WHERE user_id = '" . $user->user_id() . "'");
if ($saved_favorites_count['total'] != 0) {
    $load_saved_favorites = $db_users->getQuery("*", TABLE_SAVED_LISTINGS, "user_id='" . $user->user_id() . "'");
    while ($saved_favorites = $db_users->fetchArray($load_saved_favorites)) {
        // Multi-IDX
        try {
            $listing_idx = Util_IDX::getIdx($saved_favorites['idx']);
            $listing_db = Util_IDX::getDatabase($saved_favorites['idx']);

        // Error occurred
        } catch (Exception $e) {
            Log::error($e);
            continue;
        }

        // Load MLS Listing
        if ($listing_idx instanceof $idx) {
            $search_where = "`" . $listing_idx->field('ListingMLS') . "` = '" . $saved_favorites['mls_number'] . "'";

            // Any global criteria
            $idx->executeSearchWhereCallback($search_where);

            $listing_details = $listing_db->getRow($listing_idx->selectColumns(), $listing_idx->getTable(), $search_where);
            if ($listing_details) {
                $favorite_listing = Util_IDX::parseListing($listing_idx, $listing_db, $listing_details);
                $favorite_listing['idx'] = $listing_idx->getLink();
                $favorite_listings[] = $favorite_listing;
            }
        }
    }

    // Total # of Listings
    $saved_favorites_count['total'] = count($favorite_listings);
}

?>
<div class="set listings compact">
    <?php if (!empty($favorite_listings)) :?>
        <?php $count = 0; ?>
        <?php foreach ($favorite_listings as $result) : ?>
            <div class="listing">
                <h4 class="title"><a href="<?=$result['url_details']; ?>"><?=Lang::write('MLS_NUMBER'); ?> <?=$result['ListingMLS']; ?></a></h4>
                <div class="details">
                    <div class="summary">
                        <strong>$<?=Format::number($result['ListingPrice']); ?></strong>
                        <div class="basics"><?=$result['NumberOfBedrooms']; ?> Bed, <?=$result['NumberOfBathrooms']; ?> Bath, <?=Format::number($result['NumberOfSqFt']); ?> sqft. <em><?=$result['ListingType']; ?></em></div>
                        <div class="location"><?=$result['Address']; ?>, <?=$result['AddressCity']; ?></div>
                    </div>
                    <dl class="data-price"><dt>Price:</dt> <dd>$<?=Format::number($result['ListingPrice']); ?></dd></dl>
                    <dl class="data-city"><dt>City:</dt> <dd><?=$result['AddressCity']; ?></dd></dl>
                    <dl class="data-beds"><dt>Beds:</dt> <dd><?=$result['NumberOfBedrooms']; ?></dd></dl>
                    <dl class="data-baths"><dt>Baths:</dt> <dd><?=$result['NumberOfBathrooms']; ?></dd></dl>
                    <dl class="data-type"><dt>Type:</dt> <dd><?=$result['ListingType']; ?></dd></dl>
                    <dl class="data-mlsid"><dt><?=Lang::write('MLS_NUMBER'); ?>:</dt> <dd><?=$result['ListingMLS']; ?></dd></dl>
                    <?php if (!empty($_COMPLIANCE['results']['show_office'])) : ?>
                        <dl class="data-office" title="<?=$result['ListingOffice']; ?>"><dt>Office:</dt> <dd><?=$result['ListingOffice']; ?></dd></dl>
                    <?php endif; ?>
                    <p class="remarks"><?=Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 110); ?></p>
                </div>
                <div class="photos">
                    <a href="<?=$result['url_details']; ?>"><img src="<?=IDX_Feed::thumbUrl($listing['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="Photo of Listing #<?=$result['ListingMLS']; ?>" class="photo" /></a>
                </div>
                <div class="actions">
                    <a href="<?=$result['url_details']; ?>" class="action-moreinfo">View<span class="extra"> Details</span></a>
                    <?php if (isset(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) : ?>
                        <a href="<?=$result['url_map']; ?>" class="action-map">Map<span class="extra"> Location</span></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $count++; ?>
            <?php if ($count >= 4) {
                break;
            } ?>
        <?php endforeach; ?>
        <div class="nav"><a href="/idx/dashboard.html" class="popup" data-popup='{"header":false}'>View All <?=$saved_favorites_count['total'];?> Listings.</a></div>
    <?php else : ?>
        <div>
            <img src="/img/no-bookmarks.png" alt="You currently have no <?=Locale::spell('favorite');?> listings" />
        </div>
    <?php endif; ?>
</div>