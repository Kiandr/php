<?php

/*
 * BDX Community
 */

namespace BDX;

class Community
{
    
    /*
	 * Parse BDX Community
	 * @param array $community
	 * @param object $app (PHP Slim application object)
	 * @return array
	 */
    
    public static function parse($community, $app)
    {
                    
        // Ensure urls have http prefixed
        if (!empty($community['SubWebsite']) && !preg_match('#https?:\/\/#i', $community['SubWebsite'])) {
            $community['SubWebsite'] = 'http://' . $community['SubWebsite'];
        }
        if (!empty($community['SubVideoTour']) && !preg_match('#https?:\/\/#i', $community['SubVideoTour'])) {
            $community['SubVideoTour'] = 'http://' . $community['SubVideoTour'];
        }
        if (!empty($community['BuilderWebSite']) && !preg_match('#https?:\/\/#i', $community['BuilderWebSite'])) {
            $community['BuilderWebSite'] = 'http://' . $community['BuilderWebSite'];
        }
                    
        // Build Link
        if ($app->snippet) {
            $community['Link'] = Settings::getInstance()->SETTINGS['BASE_URL_BUILDERS'] . $app->urlFor('community', array(
                'state'     => Util::slugify($app->states[$community['State']]),
                'city'      => Util::slugify($community['City']),
                'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),

            ));
        } else {
            $community['Link'] = $app->urlFor('community', array(
                'state'     => Util::slugify($app->states[$community['State']]),
                'city'      => Util::slugify($community['City']),
                'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
            ));
        }

        // Generate Community Alt Tag
        $community['Alt'] = $community['SubdivisionName'] . ' - New homes for sale in ' . $community['City'] . ', ' . $community['State'];
        $community['Alt1'] = $community['SubdivisionName'] . ' - A community of beautiful new homes in ' . $community['City'] . ', ' . $community['State'];
        $community['Alt2'] = $community['SubdivisionName'] . ' - Find new construction in ' . $community['City'] . ', ' . $community['State'];
        
        // Build Tracking ID
        $community['TrackingID'] = 'BHIC' . $community['SubdivisionID'];
        
        return $community;
    }
    
    /*
	 * Builds community information array for listing details page
	 * @return array
	 */
    
    public static function getDetails()
    {
    
        $communityDetails = array();
    
        $communityDetails[] = array('heading' => 'Community Information', 'fields' => array(
                array('value' => 'PriceFrom',                   'title' => 'Price (Low)',           'format' => 'price'),
                array('value' => 'PriceTo',                     'title' => 'Price (High)',          'format' => 'price'),
                array('value' => 'SubdivisionName',             'title' => 'Subdivision Name',      'link' => 'Link'),
                array('value' => 'SubdivisionID',               'title' => 'Subdivision ID'),
                array('value' => 'SubdivisionNumber',           'title' => 'Subdivision Number'),
        ));
    
        $communityDetails[] = array('heading' => 'Community Location Information', 'fields' => array(
                array('value' => 'Address',                     'title' => 'Address'),
                array('value' => 'City',                        'title' => 'City'),
                array('value' => 'State',                       'title' => 'State'),
                array('value' => 'Zip',                         'title' => 'Zip Code'),
        ));
    
        return $communityDetails;
    }
    
    /*
	 * Get BDX Community Images
	 * @param array $community
	 * @param object $db_bdx
	 * @param boolean $singleImage
	 * @return array|string
	 */
    public static function getImages($community, $db_bdx, $singleImage = false)
    {
        
        // Prepare/Execute Query
        $images = $db_bdx->prepare("SELECT `_cdn_url` AS `Url` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISION_IMAGES'] . "` WHERE `SubdivisionID` = :SubdivisionID AND `type` = 'Standard' AND `_cdn_url` IS NOT NULL ORDER BY `Seq`" . ($singleImage ? ' LIMIT 1' : '') . ";");
        $images->execute(array('SubdivisionID' => $community['SubdivisionID']));
        
        // Get One Image
        if ($singleImage) {
            $communityImages = str_replace('-o.jpg', '-s.jpg', $images->fetchColumn());
        // Get All Images
        } else {
            if ($images->rowCount() > 0) {
                while ($image = $images->fetch()) {
                    $communityImages[] = $image['Url'];
                }
            }
        }

        // Set images to 404 image if empty
        $communityImages = (!empty($communityImages) ? $communityImages : '/builders/res/img/404.gif');
        
        return $communityImages;
    }
    
    
    /* Processes details. Details sections without data will not be included in the returned array
	* @param array $communityDetails
	* @param array $listing
	* @return array
	*/
    
    public static function processDetails($communityDetails, $listing)
    {
    
        $details = array();
    
        foreach ($communityDetails as $data) {
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
                $fields[] = array('title' => $field['title'], 'value' => $value, 'link' => $field['link']);
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
