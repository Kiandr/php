<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'not-found');
    return;
} ?>
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

    <h2>
        <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing)) { ?>
            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);?>
        <?php } ?>
        $<?= Format::number($listing['ListingPrice']) . ' - ' . $listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState']; ?>
    </h2>

    <?php if (!empty($points)) { ?>
        <div class="fw fw-idx-map"></div>
    <?php } else { ?>
        <p class="uk-alert uk-alert-danger">This listing is not able to be mapped at this time.</p>
    <?php } ?>

    <?php
    // Feed-specific compliance
    if (!empty($_COMPLIANCE['details']['disclaimer_under_map'])) {

        // Show MLS Office / Agent
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);

        // Show Disclaimer
        echo '<div class="show-immediately-below-listings">';
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
        echo '</div>';

    }

    // "Next Steps"
    include $page->locateTemplate('idx', 'misc', 'nextsteps');

    // Require Points
    if (!empty($points) && !empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
        <div id="map-directions">
            <h4>Get Directions</h4>
            <form class="uk-form" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <div class="uk-width-1-2">
                    <label>From Address</label>
                    <input class="uk-width-1-1 uk-form-large" name="from"
                           value="<?= (isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''); ?>" required>
                </div>
                <div class="uk-width-1-2">
                    <label>To Address</label>
                    <input class="uk-width-1-1 uk-form-large" name="to"
                           value="<?=htmlspecialchars(isset($_GET['to']) ? $_GET['to'] : !empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>"
                           required>
                </div>
                <div class="btnset">
                    <button class="uk-button" type="submit">Get Directions</button>
                </div>
            </form>
            <div class="directions-panel"></div>
        </div>
    <?php } ?>

    <?php

    // Show MLS Office / Agent
    if ((!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) && empty($_COMPLIANCE['details']['disclaimer_under_map'])) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
    }

    ?>
</div>
