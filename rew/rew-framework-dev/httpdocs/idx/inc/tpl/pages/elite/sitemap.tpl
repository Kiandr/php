<h1><?=Lang::write('MLS'); ?> Property Listing Sitemap</h1>

<?php
// Multi-IDX
if (!empty(Settings::getInstance()->IDX_FEEDS)) {
    $page->container('feeds')->addModule('idx-feeds', array(
        'mode' => 'inline',
    ))->display();
}
?>

<p>
    <span class="summary">
        <?php if (!empty($groups)) { ?>
           <em><?=number_format($search_count['total']); ?> Properties Found.</em> Showing Page <?=number_format($current_page); ?> of <?=number_format($pagination['pages']); ?>
        <?php } else { ?>
           No listings were found matching your search criteria.
        <?php } ?>
    </span>
</p>

<?php

if (!empty($groups)) {

    // Display Links
    foreach ($groups as $group => $results) {
        echo '<h2>' . $group . '</h2>';
        echo '<ul class="uk-list uk-list-striped">';
        foreach ($results as $result) {
            $address = ucwords(strtolower($result['Address'])) . ', ' . ucwords(strtolower($result['AddressCity'])) . ' ' . $result['AddressState'] . ' ' . $result['AddressZipCode'];

            // Feed-specific compliance
            if (isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
                echo '<li>';
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
                echo '<br><a href="' . $result['url_details'] . '">' . $address . '</a>';
                if ($_COMPLIANCE['results']['show_mls']) echo '<br />' . Lang::write('MLS_NUMBER') . $result['ListingMLS'];
                echo '</li>';
            } else {
                echo '<li><a href="' . $result['url_details'] . '">' . $address . '</a>';
                if ($_COMPLIANCE['results']['show_agent'] || $_COMPLIANCE['results']['show_office']) echo '<br>';
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
                if ($_COMPLIANCE['results']['show_mls']) echo '<br />' . Lang::write('MLS_NUMBER') . $result['ListingMLS'];
                echo '</li>';
            }
        }
        echo '</ul>';
    }

    // Pagination TPL
    include $page->locateTemplate('idx', 'misc', 'pagination');

}

?>
