<?php

/*
 * BDX Listing
 */

namespace BDX;

class Listing
{
    
    /**
     * Parse BDX Listing
     * @param array $listing
     * @param object $app (PHP Slim application object)
     * @return array
     */
    
    public static function parse($listing, $app)
    {
    
        // Build Tracking ID
        $listing['TrackingID'] = 'BHI' . $listing['ListingType'] . $listing['ListingID'];
        
        // Listing Type
        if (!empty($listing['ListingType'])) {
            if ($listing['ListingType'] == 'P') {
                $listing['ListingType'] = 'Plan';
            } elseif ($listing['ListingType'] == 'S') {
                $listing['ListingType'] = 'Spec';
            }
        }
    
        // SingleFamily or MultiFamily
        if (!empty($listing['PlanType'])) {
            $listing['PlanType'] = trim(preg_replace('/[A-Z]/', ' $0', $listing['PlanType']));
            $listing['PlanType'] = ($listing['PlanType'] === 'Multi Family') ? 'Condo / Townhome' : $listing['PlanType'];
        }
    
        // Build Link
        if ($app->snippet) {
            $listing['Link'] = Settings::getInstance()->SETTINGS['BASE_URL_BUILDERS'] . $app->urlFor('listing', array(
                'state'     => Util::slugify($app->states[$listing['State']]),
                'city'      => Util::slugify($listing['City']),
                'community' => $listing['SubdivisionID'] . '-' . Util::slugify($listing['SubdivisionName']),
                'listing'   => $listing['ListingID'] . '-' . Util::slugify($listing['PlanName']),
            ));
        } else {
            $listing['Link'] = $app->urlFor('listing', array(
                'state'     => Util::slugify($app->states[$listing['State']]),
                'city'      => Util::slugify($listing['City']),
                'community' => $listing['SubdivisionID'] . '-' . Util::slugify($listing['SubdivisionName']),
                'listing'   => $listing['ListingID'] . '-' . Util::slugify($listing['PlanName']),
            ));
        }
                
        // Generate Listing Alt Tag
        $listing['Alt'] = $listing['PlanName'] . ' - New construction in ' . $listing['SubdivisionName'] . ', ' . $listing['City'] . ', ' . $listing['State'];
        $listing['Alt1'] = $listing['PlanName'] . ', a new home listing in ' . $listing['SubdivisionName'] . ', ' . $listing['City'];
        $listing['Alt2'] = $listing['PlanName'] . ', a new home for sale in ' . $listing['SubdivisionName'] . ', ' . $listing['City'];
        
        // Builder Build Name Link
        if (!empty($listing['BrandName'])) {
            if ($app->snippet) {
                $listing['BrandNameLink'] =  Settings::getInstance()->SETTINGS['BASE_URL_BUILDERS'] .$app->urlFor('search', array(
                    'search' => 'communities',
                    'state' => Util::slugify($app->states[$listing['State']]))) . '?search[City]=' . urlencode($listing['City']) . '&search[Builder]=' . urlencode($listing['BrandName']);
            } else {
                $listing['BrandNameLink'] = $app->urlFor('search', array(
                    'search' => 'communities',
                    'state' => Util::slugify($app->states[$listing['State']]))) . '?search[City]=' . urlencode($listing['City']) . '&search[Builder]=' . urlencode($listing['BrandName']);
            }
        }
        
        // Ensure urls have http prefixed
        if (!empty($listing['VirtualTour']) && !preg_match('#https?:\/\/#i', $listing['VirtualTour'])) {
            $listing['VirtualTour'] = 'http://' . $listing['VirtualTour'];
        }
                
        // Return Listing
        return $listing;
    }
    
    /*
	 * Get BDX Listing Images
	 * @param array $listing
	 * @param object $db_bdx
	 * @return array|string
	 */
    
    public static function getImages($listing, $db_bdx, $singleImage = false)
    {
        
        $images = $db_bdx->prepare("SELECT `_cdn_url` AS `Url` FROM `" . Settings::getInstance()->TABLES['BDX_LISTING_IMAGES'] . "` WHERE `SubdivisionID` = :SubdivisionID AND `ListingID` = :ListingID AND `_cdn_url` IS NOT NULL ORDER BY `Seq`" . ($singleImage ? ' LIMIT 1' : '') . ";");
        $images->execute(array('SubdivisionID' => $listing['SubdivisionID'], 'ListingID' => $listing['ListingID']));
        
        // Get One Image
        if ($singleImage) {
            $listingImages = str_replace('-o.jpg', '-s.jpg', $images->fetchColumn());
        // Get All Images
        } else {
            if ($images->rowCount() > 0) {
                while ($image = $images->fetch()) {
                    $listingImages[] = $image['Url'];
                }
            }
        }
        
        // Set images to 404 image if empty
        $listingImages = (!empty($listingImages) ? $listingImages : '/builders/res/img/404.gif');
        
        return $listingImages;
    }
    
    /*
	 * Builds listing information array for listing details page
	 * @return array
	 */
    
    public static function getDetails()
    {
    
        $listingDetails = array();
    
        $listingDetails[] = array('heading' => 'Listing Information', 'fields' => array(
                array('value' => 'BasePrice',                   'title' => 'Price',     'format' => 'price'),
                array('value' => 'ListingMoveInDate',           'title' => 'Move-In Date'),
                array('value' => 'ListingType',                 'title' => 'Type'),
                array('value' => 'Stories',                     'title' => '# Stories'),
                array('value' => 'Garages',                     'title' => '# Garages'),
                array('value' => 'BaseSqft',                    'title' => 'Sq. Ft.'),
                array('value' => 'ListingID',                   'title' => 'Listing ID'),
                array('value' => 'BrandName',                   'title' => 'Builder Name'),
        ));
    
        $listingDetails[] = array('heading' => 'Listing Room Information', 'fields' => array(
                array('value' => 'Bedrooms',                    'title' => 'Bedrooms'),
                array('value' => 'Baths',                       'title' => 'Bathrooms'),
                array('value' => 'DiningAreas',                 'title' => 'Dining Areas'),
                array('value' => 'LivingAreas',                 'title' => 'Living Areas'),
                array('value' => 'MasterBedLocation',           'title' => 'Master Bedroom Location'),
        ));
    
        $listingDetails[] = array('heading' => 'Listing Location Information', 'fields' => array(
                array('value' => 'ListingAddress',              'title' => 'Address'),
                array('value' => 'ListingCity',                 'title' => 'City'),
                array('value' => 'ListingState',                'title' => 'State'),
                array('value' => 'ListingZIP',                  'title' => 'Zip Code'),
        ));
    
        return $listingDetails;
    }
    
    /* Processes details. Details sections without data will not be included in the returned array
	 * @param array $listingDetails
	 * @param array $listing
	 * @return array $details
	 */
    
    public static function processDetails($listingDetails, $listing)
    {

        $details = array();
        
        foreach ($listingDetails as $data) {
            $fields = array();
            foreach ($data['fields'] as $k => $field) {
                // Field Value
                $value = $listing[$field['value']];
                
                // Skip Empty
                if (empty($value)) {
                    continue;
                }
        
                // Formatting
                if ($field['format'] == 'price') {
                    $value = "$" . number_format($value);
                }
                
                // Add Data
                $fields[] = array('title' => $field['title'], 'value' => $value);
            }
            
            // Skip Empty
            if (empty($fields)) {
                continue;
            }
        
            // Add Details
            $details[] = array('heading' => $data['heading'], 'fields' => $fields);
        }
        
        return $details;
    }
}
