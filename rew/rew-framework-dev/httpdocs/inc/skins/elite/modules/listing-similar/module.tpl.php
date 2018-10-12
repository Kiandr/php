<?php

// Skip if no listings found
if (empty($similar)) return;

// Ensure detailed view is never used.
$view = 'grid';
?>
<div id="<?=$this->getUID(); ?>" class="similar-listings">
    <h2>Similar Listings <span class="uk-hidden">Properties for Sale Similar to <?= Format::htmlspecialchars(implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState'])))); ?></span></h2>
    <div class="uk-grid uk-grid-medium">
        <?php foreach ($similar as $result) { ?>
            <?php require $result_tpl; ?>
        <?php } ?>
    </div>
</div>
