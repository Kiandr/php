<?php

/**
 * Lang is used for displaying various Meta Information through out the framework (Page Titles, Keywords, Descriptions)
 *
 * Here are some examples on how this class is used:
 * <code>
 * <?php
 *
 * // Modify Page Title
 * Lang::$lang['IDX_MAIN_PAGE_TITLE'] = 'Search Real Estate Listings';
 *
 * // Get IDX Page Title
 * $page_title = Lang::write('IDX_MAIN_PAGE_TITLE');
 *
 * // Get IDX Details Page Title
 * $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE', $listing);
 *
 * // Page Title for Missing Listing
 * $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid']));
 *
 * ?>
 * </code>
 */
class Lang
{

    /**
     * Language Settings
     * @var array
     */
    public static $lang = array(

        // IDX Listing URL
        'IDX_LISTING_URL'                           => '{Address}-{AddressCity}-{AddressState}-{AddressZipCode}',
        'IDX_LISTING_URL_PREFIX'                    => '{ListingMLS}-',

        // IDX Defaults
        'IDX_MAIN_PAGE_TITLE'                       => 'Search Listings',
        'IDX_MAIN_META_DESCRIPTION'                 => '',
        'IDX_MAIN_META_KEYWORDS'                    => '',

        // IDX Details Page
        'IDX_DETAILS_PAGE_TITLE'                    => '{Address}, {AddressCity} Property Listing: MLS&reg; #{ListingMLS}',
        'IDX_DETAILS_META_DESCRIPTION'              => '{ListingRemarks}',
        'IDX_DETAILS_META_KEYWORDS'                 => '',

        // IDX Details Page - Map & Directory
        'IDX_DETAILS_MAP_PAGE_TITLE'                => 'Listing Map of {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_MAP_META_DESCRIPTION'          => '{ListingRemarks}',
        'IDX_DETAILS_MAP_META_KEYWORDS'             => '',

        // IDX Details Page - Bird's Eye View
        'IDX_DETAILS_BIRDSEYE_PAGE_TITLE'           => 'Bird\'s Eye View of {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_BIRDSEYE_META_DESCRIPTION'     => '{ListingRemarks}',
        'IDX_DETAILS_BIRDSEYE_META_KEYWORDS'        => '',

        // IDX Details Page - Streetview
        'IDX_DETAILS_STREETVIEW_PAGE_TITLE'         => 'Google Streetview of {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_STREETVIEW_META_DESCRIPTION'   => '{ListingRemarks}',
        'IDX_DETAILS_STREETVIEW_META_KEYWORDS'      => '',

        // IDX Details Page - Onboard Module
        'IDX_DETAILS_ONBOARD_PAGE_TITLE'            => 'Get Local Neighborhood Information for {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_ONBOARD_META_DESCRIPTION'      => '{ListingRemarks}',
        'IDX_DETAILS_ONBOARD_META_KEYWORDS'         => '',

        // IDX Details Page - Listing Inquiry
        'IDX_DETAILS_INQUIRE_PAGE_TITLE'            => 'Find out more about {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_INQUIRE_META_DESCRIPTION'      => '{ListingRemarks}',
        'IDX_DETAILS_INQUIRE_META_KEYWORDS'         => '',

        // IDX Details Page - Property Showing
        'IDX_DETAILS_SHOWING_PAGE_TITLE'            => 'Request a Property Showing of {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS})',
        'IDX_DETAILS_SHOWING_META_DESCRIPTION'      => '{ListingRemarks}',
        'IDX_DETAILS_SHOWING_META_KEYWORDS'         => '',

        // IDX Details Page - Send to Friend
        'IDX_DETAILS_SHARE_PAGE_TITLE'              => 'Share {Address}, {AddressCity} {AddressState} {AddressZipCode} (MLS&reg; #{ListingMLS}) with a Friend',
        'IDX_DETAILS_SHARE_META_DESCRIPTION'        => '{ListingRemarks}',
        'IDX_DETAILS_SHARE_META_KEYWORDS'           => '',

        // IDX Details Page - Send to Phone
        'IDX_DETAILS_PHONE_PAGE_TITLE'               => 'Send {Address} (MLS&reg; #{ListingMLS}) to your mobile device',
        'IDX_DETAILS_PHONE_META_DESCRIPTION'         => '{ListingRemarks}',
        'IDX_DETAILS_PHONE_META_KEYWORDS'            => '',

        // IDX Details Page - Listing Not Found
        'IDX_DETAILS_PAGE_TITLE_MISSING'            => 'Listing Not Found: MLS&reg; #{ListingMLS}',
        'IDX_DETAILS_META_DESCRIPTION_MISSING'      => '',
        'IDX_DETAILS_META_KEYWORDS_MISSING'         => '',

        // IDX Registration Page
        'IDX_REGISTER_PAGE_TITLE'                   => 'Register',
        'IDX_REGISTER_META_DESCRIPTION'             => '',
        'IDX_REGISTER_META_KEYWORDS'                => '',

        // IDX Login Page
        'IDX_LOGIN_PAGE_TITLE'                      => 'Sign In',
        'IDX_LOGIN_META_DESCRIPTION'                => '',
        'IDX_LOGIN_META_KEYWORDS'                   => '',

        // IDX Social Media Connect
        'IDX_CONNECT_PAGE_TITLE'                    => 'Social Media Connect',
        'IDX_CONNECT_META_DESCRIPTION'              => '',
        'IDX_CONNECT_META_KEYWORDS'                 => '',

        // IDX Search Buttons
        'IDX_SEARCH_REFINE_BUTTON'                  => 'Refine Search', // Default Refine Search (Used for search panel brew/lec-2013)
        'IDX_SEARCH_BUTTON'                         => 'Search', // Used for quick search and form brew/lec-2013

        // IDX Map Search Page
        'IDX_MAP_ONBOARD_DISCLAIMER'                => 'Disclaimer / Sources: Neighborhood data provided by Onboard Informatics &copy; {Year}',

        // IDX Listing Tags
        'IDX_LISTING_TAGS'                          => array(
            // Listing Details
            'ListingMLS', 'ListingPrice', 'ListingType', 'ListingSubType', 'ListingStyle', 'ListingStatus', 'ListingRemarks',
            // Listing Address
            'Address', 'AddressArea', 'AddressSubdivision', 'AddressCity', 'AddressCounty', 'AddressState', 'AddressZipCode',
            // Listing Stats
            'NumberOfBedrooms', 'NumberOfBathrooms', 'NumberOfSqFt', 'ListingDOM', 'YearBuilt',
            // School Information
            'SchoolDistrict', 'SchoolElementary', 'SchoolMiddle', 'SchoolHigh',
        ),

        // MLS Compliance
        'MLS'                                       => 'MLS&reg;',
        'MLS_NUMBER'                                => 'MLS&reg; #',

        // MLS Compliance Text
        'INQUIRE_ASK_ABOUT'                         => 'I was searching for a Property and found this listing (MLS&reg; #{ListingMLS}). Please send me more information regarding {Address}, {AddressCity}, {AddressState}, {AddressZipCode}. Thank you!',
        'INQUIRE_REQUEST_SHOWING'                   => "I'd like to request a showing of {Address}, {AddressCity}, {AddressState}, {AddressZipCode} (MLS&reg; #{ListingMLS}).\nThank you!",
        'MAP_POPUP_SQFT_TEXT'                       => 'sqft.',
        'IDX_DETAILS_SQFT_TEXT'                     => 'SQ. Feet',
        'IDX_BROCHURE_SQFT_TEXT'                    => ' sqft',

        // IDX Listing URL
        'RT_PROPERTY_URL'                           => '{SitusAddress}-{SitusCity}-{SitusState}-{SitusZip}',
        'RT_PROPERTY_URL_PREFIX'                    => '{RTID}-',

        // RT Search Page
        'RT_SEARCH_PAGE_TITLE'                      => 'Search Public Property Records',
        'RT_SEARCH_META_DESCRIPTION'                => '',
        'RT_SEARCH_META_KEYWORDS'                   => '',

        // RT Details Page
        'RT_DETAILS_PAGE_TITLE'                    => '{SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_META_DESCRIPTION'              => '',
        'RT_DETAILS_META_KEYWORDS'                 => '',

        // RT Details Page - Property Not Found
        'RT_DETAILS_PAGE_TITLE_MISSING'            => 'Property Not Found: RealtyTrac ID# {RTID}',
        'RT_DETAILS_META_DESCRIPTION_MISSING'      => '',
        'RT_DETAILS_META_KEYWORDS_MISSING'         => '',

        // RT Details Page - Map & Directory
        'RT_DETAILS_MAP_PAGE_TITLE'                => 'Property Map of {SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_MAP_META_DESCRIPTION'          => '',
        'RT_DETAILS_MAP_META_KEYWORDS'             => '',

        // RT Details Page - Bird's Eye View
        'RT_DETAILS_BIRDSEYE_PAGE_TITLE'           => 'Microsoft Bird\'s Eye View of {SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_BIRDSEYE_META_DESCRIPTION'     => '',
        'RT_DETAILS_BIRDSEYE_META_KEYWORDS'        => '',

        // RT Details Page - Onboard Module
        'RT_DETAILS_ONBOARD_PAGE_TITLE'            => 'Get Local Neighborhood Information for {SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_ONBOARD_META_DESCRIPTION'      => '',
        'RT_DETAILS_ONBOARD_META_KEYWORDS'         => '',

        // RT Details Page - Streetview
        'RT_DETAILS_STREETVIEW_PAGE_TITLE'         => 'Google Streetview of {SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_STREETVIEW_META_DESCRIPTION'   => '',
        'RT_DETAILS_STREETVIEW_META_KEYWORDS'      => '',

        // RT Registration Page
        'RT_REGISTER_PAGE_TITLE'                   => 'Register',
        'RT_REGISTER_META_DESCRIPTION'             => '',
        'RT_REGISTER_META_KEYWORDS'                => '',

        // RT Login Page
        'RT_LOGIN_PAGE_TITLE'                       => 'Sign In',
        'RT_LOGIN_META_DESCRIPTION'                 => '',
        'RT_LOGIN_META_KEYWORDS'                    => '',

        // RT Refine Button
        'RT_SEARCH_REFINE_BUTTON'                   => 'Refine Search',

        // RT Details Page - Nosy Neighbor
        'RT_DETAILS_NOSY_NEIGHBOR_PAGE_TITLE'         => 'Find Nearby {neighbor}s of {Address}, {AddressCity} {AddressState} {AddressZipCode}',
        'RT_DETAILS_NOSY_NEIGHBOR_META_DESCRIPTION'   => '',
        'RT_DETAILS_NOSY_NEIGHBOR_META_KEYWORDS'      => '',

        // RT Details Page - Nearby Solds
        'RT_DETAILS_NEARBY_SOLDS_PAGE_TITLE'         => 'Find Nearby Sold Properties of {SitusAddress}, {SitusCity} {SitusState} {SitusZip} (AP# {APNUnformatted})',
        'RT_DETAILS_NEARBY_SOLDS_META_DESCRIPTION'   => '',
        'RT_DETAILS_NEARBY_SOLDS_META_KEYWORDS'      => '',

        // RT Property Tags
        'RT_PROPERTY_TAGS'                          => array(
            // Property Details
            'APNUnformatted', 'PropertyGroup', 'PropertyType',
            // Property Address
            'SitusAddress', 'Subdivision', 'SitusCity', 'SitusZip', 'SitusCounty', 'SitusState',
            // Property Stats
            'Bedrooms', 'Bathrooms', 'SquareFootage', 'YearBuilt',
            // Property Taxes and Estimates
            'TaxAssessedValue', 'TaxImprovementValue', 'TaxLandValue', 'TaxAmount', 'EstimatedValue'
        ),
    );

    /**
     * Write from Language Settings
     *
     * @param string $key Language Constant
     * @return string Parsed String (or Empty If Not Set)
     */
    public static function write($key, $vars = array())
    {
        $str = (empty(self::$lang[$key])) ? '' : self::$lang[$key];
        if ((is_object($vars) && ($vars instanceof Iterator)) || (!empty($vars) && is_array($vars))) {
            foreach ($vars as $var => $val) {
                $str = str_replace('{' . $var . '}', $val, $str);
            }
        }
        // Replace neighbors for us/ca clients
        if (stripos($str, '{neighbor}') !== false) {
            $str = str_replace('{neighbor}', Locale::spell('Neighbor'), $str);
        }
        return $str;
    }
}
