<?php

// Actions
$actions = array();
$actions[] = array('link' => $listing['url_inquire'], 'title' => 'Inquire', 'class' => 'popup', 'modal' => 'inquire');
$actions[] = array('link' => $listing['url_inquire'] . "?inquire_type=Property+Showing", 'title' => 'Request Showing', 'class' => 'popup', 'modal' => 'inquire-showing');
if (empty(Settings::getInstance()->MODULES['REW_IDX_SOCIAL_NETWORK'])) {
    $actions[] = array('link' => 'mailto:?subject=Listing from ' . $_SERVER['HTTP_HOST'] . ' - ' . $listing['Address'] . '&body=While searching for property on ' . Settings::getInstance()->SETTINGS['URL_IDX'] . ', I thought you might be interested in the following property ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . '.%0D%0A%0D%0AThe URL below will take you to the property\'s details:%0D%0A' . $listing['url_details'], 'title' => 'Share this Listing');
} else {
    $actions[] = array('link' => $listing['url_sendtofriend'], 'title' => 'Share this Listing', 'class' => 'popup', 'modal' => 'share');
}
$actions[] = array('link' => $listing['url_brochure'], 'title' => 'Print this Listing', 'target' => '_blank');

// Virtual Tour Link
if (!empty($listing['VirtualTour'])) {
    $actions[] = array('link' => $listing['VirtualTour'], 'title' => 'Virtual Tour', 'extra' => ' data-uk-lightbox data-lightbox-type="iframe"');
}

// Send to phone
if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
    $actions[] = array('link' => $listing['url_phone'], 'title' => 'Send to Mobile Device', 'class' => 'popup');
}

// Display Action Buttons.
?>
<ul class="uk-list uk-subnav">
    <li>Next Steps:</li>
    <?php foreach ($actions as $k => $action) { ?>
        <li><a class="action<?= (!empty($action['class']) ? ' ' . $action['class'] : ''); ?>"
            href="<?= Format::htmlspecialchars($action['link']); ?>"
            <?= (!empty($action['modal']) ? ' data-modal="' . Format::htmlspecialchars($action['modal']) . '"' : ''); ?>
            <?= (!empty($action['target']) ? ' target="' . Format::htmlspecialchars($action['target']) . '"' : ''); ?>
            <?= (!empty($action['extra']) ? $extra : ''); ?>
            rel="nofollow"><?= Format::htmlspecialchars($action['title']); ?></a>
        </li>
    <?php } ?>
</ul>
