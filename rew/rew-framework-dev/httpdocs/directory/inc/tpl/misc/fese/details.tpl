<?php /* @global \REW\Core\Interfaces\PageInterface $page */ ?>
<?php /* @global array $entry */ ?>
<div id="directory-details">
    <h1><?=Format::htmlspecialchars($entry['business_name']); ?></h1>
    <?php

        // Photo gallery
        $galleryImages = [];
        if (!empty($entry['logo'])) {
            $galleryImages[] = $entry['logo'];
        }
        if (!empty($entry['thumbnails'])) {
            $galleryImages = array_merge(
                $galleryImages,
                $entry['thumbnails']
            );
        }
        if (!empty($galleryImages)) {
            $page->container('fgallery')->module('fgallery', [
                'images' => $galleryImages
            ])->display();
        }

    ?>
    <ul class="kvs">
        <?php if (!empty($entry['address'])) { ?>
            <li class="kv">
                <strong class="k">Address</strong>
                <span class="v"><?=Format::htmlspecialchars($entry['address']); ?></span>
            </li>
        <?php } ?>
        <?php if (!empty($entry['phone'])) { ?>
            <li class="kv">
                <strong class="k">Phone #</strong>
                <span class="v"><?=Format::htmlspecialchars($entry['phone']); ?></span>
            </li>
        <?php } ?>
        <?php if (!empty($entry['alt_phone'])) { ?>
            <li class="kv">
                <strong class="k">Secondary Phone #</strong>
                <span class="v"><?=Format::htmlspecialchars($entry['alt_phone']); ?></span>
            </li>
        <?php } ?>
        <?php if (!empty($entry['toll_free'])) { ?>
            <li class="kv">
                <strong class="k">Toll Free #</strong>
                <span class="v"><?=Format::htmlspecialchars($entry['toll_free']); ?></span>
            </li>
        <?php } ?>
        <?php if (!empty($entry['fax'])) { ?>
            <li class="kv">
                <strong class="k">Fax #</strong>
                <span class="v"><?=Format::htmlspecialchars($entry['fax']); ?></span>
            </li>
        <?php } ?>
        <?php if (!empty($entry['website'])) { ?>
            <li class="kv">
                <strong class="k">Website</strong>
                <span class="v"><?=$entry['website']; ?></span>
            </li>
        <?php } ?>
    </ul>
    <div class="description">
        <?=$entry['description']; ?>
    </div>
</div>
<?php

// Show Map
if (!empty($entry['latitude']) && !empty($entry['longitude']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {

    // Map Container
    echo '<div id="directory-map"></div>';

    // Map Options
    $map = json_encode(array(
        'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
        'center' => array('lat' => $entry['latitude'], 'lng' => $entry['longitude']),
        'manager' => array('bounds' => false)
    ));

    // Start Javascript
    ob_start();

?>
/* <script> */

    // Load Map
    var $map = $('#directory-map').REWMap($.extend(<?=$map; ?>, {
        onInit : function () {

            // Marker Icon
            var icon = new google.maps.MarkerImage('/img/map/marker-shopping@2x.png', null, null, null, new google.maps.Size(20, 25));

            // Add Marker to Map
            var marker = new REWMap.Marker({
                'map' : $map.data('Map'),
                'icon' : icon,
                'lat' : <?=floatval($entry['latitude']); ?>,
                'lng' : <?=floatval($entry['longitude']); ?>
            });

        }
    }));

/* </script> */
<?php

    // Write Javascript
    $page->writeJS(ob_get_clean());

}