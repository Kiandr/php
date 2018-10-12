<?php

// Prepend ?popup query string
$popup = isset($_GET['popup']) ? '?popup' : '';
$popup = isset($_GET['iframe']) ? '?iframe' : $popup;

?>
<ul class="uk-tab">

    <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>

        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
            <li<?=($_GET['load_page'] == 'map') ? ' class="uk-active"' : ''; ?>><a class="map" href="<?= Format::htmlspecialchars($listing['url_map'] . $popup); ?>" rel="nofollow">Map <?php if(isset(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS']) && !empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])):?>&amp; Directions<?php endif; ?></a></li>
        <?php } ?>

        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'])) { ?>
            <li<?=($_GET['load_page'] == 'birdseye') ? ' class="uk-active"' : ''; ?>><a class="birdseye" href="<?= Format::htmlspecialchars($listing['url_birdseye'] . $popup); ?>" rel="nofollow">Bird's Eye View</a></li>
        <?php } ?>

    <?php } ?>

    <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
        <li<?=($_GET['load_page'] == 'local') ? ' class="uk-active"' : ''; ?>><a class="local" href="<?= Format::htmlspecialchars($listing['url_onboard'] . $popup); ?>" rel="nofollow">Get Local</a></li>
    <?php } ?>

    <?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>
        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'])) { ?>
            <li<?=($_GET['load_page'] == 'streetview') ? ' class="uk-active"' : ''; ?> id="streetview-tab" class="uk-hidden"><a class="streetview" href="<?= Format::htmlspecialchars($listing['url_streetview'] . $popup); ?>" rel="nofollow">Streetview</a></li>
        <?php } ?>
    <?php } ?>

</ul>

<?php include $page->locateTemplate('idx', 'misc', 'js', 'listing');
