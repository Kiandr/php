<?php

/*
 * BDX State
 */

namespace BDX;

class State
{
    
    /**
     * USA State List
     * @var array
     */
    private static $statesUSA = array(
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District Of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming'
    );
    
    /**
     * Parse BDX State
     * @param array $state
     * @param object $app (PHP Slim application object)
     * @return array
     */
    
    public static function parse($state, $app)
    {
            
        // Replace State abbreviation with full name
        if (!empty($state['State']) && !empty(self::$statesUSA[$state['State']])) {
            $state['State'] = self::$statesUSA[$state['State']];
        }
        
        // Build state link
        $state['Link'] = $app->urlFor('state', array('state' => Util::slugify($state['State'])));
    
        // Return State
        return $state;
    }
    
    /*
	 * Get state images
	 * @param array $state
	 * @param object $db_bdx
	 * @param object $app (PHP Slim application object)
	 * @limit int
	 * @return array
	 */
    public static function getImages($state, $app, $db_bdx, $limit = 3)
    {
        
        // Find images for communities within this state
        $in = implode(',', array_map(function ($SubdivisionID) use ($db_bdx) {
            return $db_bdx->quote($SubdivisionID);
        }, explode(',', $state['SubdivisionList'])));
        $images = $db_bdx->query("SELECT DISTINCT `_cdn_url` AS `Url` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISION_IMAGES'] . "` WHERE `Type` = 'Standard' AND `SubdivisionID` IN (" . $in . ") AND `_cdn_url` IS NOT NULL GROUP BY SubdivisionID ORDER BY `Seq` ASC LIMIT " . $limit . ";");
        
        if ($images->rowCount() > 0) {
            while ($img = $images->fetch()) {
                $stateImages[] = str_replace('-o.jpg', '-s.jpg', $img['Url']);
            }
        }
        
        return $stateImages;
    }
    
    /*
	 * Get State List
	 * @return array
	 */
    public static function getStates()
    {
        return self::$statesUSA;
    }
    
    /*
	 * Get States that are currently enabled through the backend settings. Defaults to the states defined in the Settings file.
	 * @param array backend bdx_settings
	 * @return array
	 */
    public static function getStateSettings($bdx_settings)
    {
        $states = array();
        if (!empty($bdx_settings['states']) && is_array($bdx_settings['states'])) {
            if (is_array(Settings::getInstance()->STATES)) {
                foreach ($bdx_settings['states'] as $key => $val) {
                    if (in_array($key, Settings::getInstance()->STATES) && $val['enabled'] == 'true') {
                        $states[] = $key;
                    }
                }
            } else {
                foreach ($bdx_settings['states'] as $key => $val) {
                    if ($val['enabled'] == 'true') {
                        $states[] = $key;
                    }
                }
            }
        }
        
        // Default
        if (empty($states) && is_array(Settings::getInstance()->STATES)) {
            foreach (Settings::getInstance()->STATES as $state) {
                $states[] = $state;
            }
        }
        
        return $states;
    }
}
