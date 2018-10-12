<?php

namespace BDX;

/**
 * Setup Auto-Loader
 */
spl_autoload_register(array('BDX\Settings', 'autoload'));

/*
 * BDX Settings
 */

class Settings
{
        
    /**
     * Configuration Settings
     * @var array
     */
    private $config = array(
        'databases' => array(
            'bdx' => array(
                'hostname' => 'idxdb-bdx.gce.rewhosting.com',
                'username' => 'rew_dev',
                'password' => 'e0gd62f0XX8O8hV8',
                'database' => 'bdx'
            )
        ),
        'partnerid'     => 'rew',
        'token'         => 'e8a08a13',
        'disclaimer'    => 'New Home Builder Search developed by Real Estate Webmasters. Data provided by NewHomeSourceProfessional.com.',
        'framework'     => true,
        'states'        => false, // List of states that will be enabled
        'tables' => array(
            'BDX_SUBDIVISIONS'                => 'bdx_subdivisions',
            'BDX_SUBDIVISION_IMAGES'          => 'bdx_subdivision_images',
            'BDX_LISTING_IMAGES'              => 'bdx_listing_images',
            'BDX_LISTING_AMENITIES'           => 'bdx_listing_amenities',
            'BDX_LISTINGS'                    => 'bdx_listings',
        ),
        'dirs' => array(
            'BUILDER'                         => '%root%/builders/',
            'BUILDER_XML'                     => '%root%/builders/xml/'
        ),
        'meta' => array(
            // BDX Defaults (States Page)
            'BDX_MAIN_PAGE_TITLE'             => 'Search BDX Listings',
            'BDX_MAIN_META_DESCRIPTION'       => '',
            'BDX_MAIN_META_KEYWORDS'          => '',
        
            // BDX State Page
            'BDX_STATE_PAGE_TITLE'            => 'Search Cities in {State}',
            'BDX_STATE_META_DESCRIPTION'      => '',
            'BDX_STATE_META_KEYWORDS'         => '',
                    
            // BDX Search Page
            'BDX_SEARCH_PAGE_TITLE'           => 'New Home Communities in {City}{State}',
            'BDX_SEARCH_META_DESCRIPTION'     => '',
            'BDX_SEARCH_META_KEYWORDS'        => '',
                    
            // BDX Community Page
            'BDX_COMMUNITY_PAGE_TITLE'        => '{ID} {Community}, {City}, {State}',
            'BDX_COMMUNITY_META_DESCRIPTION'  => '{ID} {Description}',
            'BDX_COMMUNITY_META_KEYWORDS'     => '',
                    
            // BDX Details Page
            'BDX_DETAILS_PAGE_TITLE'          => '{ID} {PlanName} ({ListingType})',
            'BDX_DETAILS_META_DESCRIPTION'    => '{ID} {Description}',
            'BDX_DETAILS_META_KEYWORDS'       => '',
                
            // BDX Sitemap Page
            'BDX_SITEMAP_PAGE_TITLE'          => 'Builder {Type} Sitemap',
            'BDX_SITEMAP_META_DESCRIPTION'    => '',
            'BDX_SITEMAP_META_KEYWORDS'       => '',
        )
    );
    
    /**
     * Singleton Instance
     * @var Configuration
     */
    static $instance;
    
    
    /**
     * Prevent Clone of Singleton Object
     * @return void
     */
    private function __clone()
    {
    }
    
    /**
     * Prevent Creation of Singleton Object
     * @return void
     */
    /**
     * Prevent Creation of Singleton Object
     *
     * @return void
     */
    private function __construct()
    {

        // HTTP Host
        $http_host = $_SERVER['HTTP_HOST'];
        
        // Home
        if (is_array($this->config['states']) && count($this->config['states']) == 1) {
            $stateList = State::getStates();
            $this->config['settings']['URL_BUILDERS'] = Settings::getInstance()->SETTINGS['URL_RAW'] . '/builders/' . str_replace(' ', '-', strtolower($stateList[$this->config['states'][0]]));
        } else {
            $this->config['settings']['URL_BUILDERS'] = Settings::getInstance()->SETTINGS['URL_RAW'] . '/builders';
        }
        $this->config['settings']['BASE_URL_BUILDERS'] = Settings::getInstance()->SETTINGS['URL_RAW']. '/builders';
        
        // Replace %host% and %root% For Web Addresses
        if (!empty($this->config['urls']) && is_array($this->config['urls'])) {
            foreach ($this->config['urls'] as $k => $v) {
                $this->config['urls'][$k] = str_replace(array(
                    '%host%',
                    '%root%'
                ), array(
                    Http_Uri::getScheme() . '://' . $http_host,
                    Http_Uri::getScheme() . '://' . $http_host
                ), $v);
            }
        }

        // Replace %root% For Directory Paths
        if (!empty($this->config['dirs']) && is_array($this->config['dirs'])) {
            foreach ($this->config['dirs'] as $k => $v) {
                $this->config['dirs'][$k] = str_replace('%root%', $_SERVER['DOCUMENT_ROOT'], $v);
            }
        }
    }
    
    /**
     * Get Instance of Singleton Object
     * @return Setting
     */
    public static function getInstance()
    {
    
        // Check Instance
        if (!(self::$instance instanceof self)) {
            self::$instance = new self ();
        }
    
        // Return
        return self::$instance;
    }
    
    /**
     * Getter
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        $name = strtolower($name);
        if (isset($name)) {
            if (array_key_exists($name, $this->config)) {
                return $this->config[$name];
            }
        }
        return null;
    }
    
    /**
     * Class Auto-Loader
     *
     * @param string $class
     * @return void
     */
    public static function autoload($class)
    {
        if (strpos($class, 'BDX\\') !== false) {
            $file = dirname(__FILE__) . '/' . str_replace('BDX\\', '', $class) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
    
    /* Returns an array of the BDX settings defined in the backend
	 * @param object $db
	* @return array|bool $settings
	*/
    public static function getBDXSettings($db)
    {
        $query = $db->query("SELECT `settings` FROM `bdx_settings` WHERE `id` = 1 LIMIT 1");
        $settings = $query->fetch();
        if (!empty($settings)) {
            $settings = unserialize($settings['settings']);
            return $settings;
        }
        return false;
    }
}
