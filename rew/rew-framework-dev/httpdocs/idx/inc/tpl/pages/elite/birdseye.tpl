<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'not-found');
    return;
}

?>
<div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
    <?php

    // Listing Title
    if (!empty($listing['ListingTitle'])) { ?>
        <h1>
            <?= Format::htmlspecialchars($listing['ListingTitle']); ?>
        </h1>
    <?php } ?>

    <?php

        // Details Tabset
        include $page->locateTemplate('idx', 'misc', 'details');

    ?>

    <?php if (empty($_COMPLIANCE['details']['remove_heading'])) { ?>
        <h2>$<?= Format::number($listing['ListingPrice']); ?>
            <?= $_COMPLIANCE['results']['show_mls'] ? ' - ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] : ''; ?>
            - <?= Format::htmlspecialchars($listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState']); ?>
        </h2>
    <?php } ?>

    <?php if (!empty($points)) { ?>

        <div id="birdseye-container" class="uk-width-1-1 uk-position-relative fw-idx-map"></div>

    <?php } else { ?>
        <p class="uk-alert uk-alert-danger">This listing is not able to be mapped at this time.</p>
    <?php } ?>

    <?php require $page->locateTemplate('idx', 'misc', 'nextsteps'); ?>

    <?php

    // Show MLS Office / Agent
    if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
    }

    ?>
</div>
