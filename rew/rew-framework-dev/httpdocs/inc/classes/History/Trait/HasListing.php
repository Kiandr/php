<?php

/**
 * Use this Trait for History Events that need to store Listing Blob
 * @package History
 */
trait History_Trait_HasListing
{

    /**
     * Get listing blob
     * @return NULL|string
     */
    protected function getNormalDataToSave()
    {

        // Remove message from data
        $listing = $this->data['listing'];

        // Trim array to only important info
        if (is_array($listing) && !empty($listing)) {
            foreach ($listing as $k => $v) {
                if (!in_array($k, array('Address', 'AddressCity', 'AddressState', 'AddressSubdivision', 'AddressZipCode', 'ListingImage', 'ListingMLS', 'ListingPrice', 'NumberOfBathrooms', 'NumberOfBedrooms', 'NumberOfSqFt', 'url_details'))) {
                    unset($listing[$k]);
                }
            }
        }

        // No listing data
        if (empty($listing)) {
            return null;
        }

        // Ensure listing data to save
        $listing = json_encode($listing);
        unset($this->data['listing']);
        return $listing;
    }

    /**
     * Get listing data
     * @return array
     */
    public function getListingData()
    {
        $data = $this->loadNormalData();
        if (!empty($data)) {
            return json_decode($data, true);
        }
        return $this->getData('listing');
    }

    /**
     * Return HTML to link to IDX Listing
     * @return string
     */
    protected function getListingLink()
    {

        // Require listing information
        $listing = $this->getListingData();
        if (empty($listing)) {
            return '[Listing Information Unavailable]';
        }

        // Generate listing address
        $address = $listing['Address'] ?: '<em>[Unknown Address]</em>';
        $address .= $listing['AddressCity'] ? ', ' . $listing['AddressCity'] : '';

        // No link available return details
        if (empty($listing['url_details'])) {
            return $address. ' (' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . ')';
        }

        // Return link to property details
        return '<a href="' . $listing['url_details'] . '">'
            . $address. ' (' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . ')'
        . '</a>';
    }

    /**
     * Return HTML to preview IDX Listing
     * @return string
     */
    protected function getListingPreview()
    {

        // Property listing data
        $listing = $this->getListingData();
        if (is_string($listing)) {
            return $listing;
        }


        // Listing not available
        if (empty($listing)) {
            return '<div class="item_content_summary">'
                . '<h4 class="item_content_title">Listing information unavailable.</h4>'
                . '<div class="item_content_thumb">'
                    . '<img src="/thumbs/60x60/uploads/listings/na.png" alt="">'
                . '</div>'
            . '</div>';
        }

        // Listing image
        $listing['ListingImage'] = Format::thumbUrl($listing['ListingImage'], '60x60');
        $listing['ListingImage'] = $listing['ListingImage'] ?: '/thumbs/60x60/uploads/listings/na.png';

        // Require listing data
        if (!empty($listing)) {
            ob_start();

?>
<div class="item_content_summary">
    <h4 class="item_content_title">
        <?=$listing['url_details'] ? '<a href="' . $listing['url_details'] . '" target="_blank">' : ''; ?>
        <?=$listing['Address'] ?: '<em>[Unknown Property Address]</em>'; ?>
        <?=$listing['url_details'] ? '</a>' : ''; ?>
        (<?=Lang::write('MLS_NUMBER'); ?><?=$listing['ListingMLS']; ?>)
    </h4>
    <strong>$<?=Format::number($listing['ListingPrice']); ?></strong> -
    <?=!empty($listing['AddressSubdivision']) ? ucwords(strtolower($listing['AddressSubdivision'])) . ', ' : ''; ?>
    <?=ucwords(strtolower($listing['AddressCity'])); ?>, <?=ucwords(strtolower($listing['AddressState'])); ?>
    <?=$listing['AddressZipCode']; ?>
    <br />
        <?=Format::number($listing['NumberOfBedrooms']); ?> Bedrooms,
        <?=Format::fraction($listing['NumberOfBathrooms']); ?> Bathrooms,
        <?=Format::number($listing['NumberOfSqFt']); ?> Sq. Ft.
    <div class="item_content_thumb">
        <img src="<?=$listing['ListingImage']; ?>" alt="">
    </div>
    <?php if (!empty($listing['url_details'])) { ?>
        <div class="actions">
            <a class="button view" href="<?=$listing['url_details']; ?>" target="_blank">View</a>
        </div>
    <?php } ?>
</div>
<?php

            // Return HTML markup
            return ob_get_clean();
        }
    }
}