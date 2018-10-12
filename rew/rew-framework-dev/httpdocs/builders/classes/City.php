<?php

/*
 * BDX City
 */

namespace BDX;

class City
{

    /**
     * Parse BDX City
     * @param array $city
     * @param object $app (PHP Slim application object)
     * @string $state
     * @return array
     */
    public static function parse($city, $app, $state)
    {
        
        // Build Link
        $city['Link'] = $app->urlFor('search', array('search' => 'communities', 'state' => Util::slugify($state))) . '?search[City]=' . urlencode($city['City']);
        
        // Return City
        return $city;
    }
    
    /*
	 * Get city images
	* @param array $city
	* @param object $db_bdx
	* @param object $app (PHP Slim application object)
	* @limit int
	* @return array
	*/
    public static function getImages($city, $app, $db_bdx, $limit = 3)
    {
    
        // Find images for communities within this state
        $in = implode(',', array_map(function ($SubdivisionID) use ($db_bdx) {
            return $db_bdx->quote($SubdivisionID);
        }, explode(',', $city['SubdivisionList'])));
        $images = $db_bdx->query("SELECT DISTINCT `_cdn_url` AS `Url` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISION_IMAGES'] . "` WHERE `Type` = 'Standard' AND `SubdivisionID` IN (" . $in . ") AND `_cdn_url` IS NOT NULL GROUP BY SubdivisionID ORDER BY `Seq` ASC LIMIT " . $limit . ";");
    
        if ($images->rowCount() > 0) {
            while ($img = $images->fetch()) {
                $cityImages[] = str_replace('-o.jpg', '-s.jpg', $img['Url']);
            }
        }
    
        return $cityImages;
    }
}
