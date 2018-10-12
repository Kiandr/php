<?php

if(!function_exists('cms_search_fields')) {
    function cms_search_fields($idx, $filter = '', $comparison = false) {
        $IDX_SEARCH_FIELDS = array(
            array('name' => 'MLS Number', 'form_field' => 'search_mls', 'idx_field' => 'ListingMLS', 'match' => 'equals'),
            array('name' => 'Property Type', 'form_field' => 'search_type', 'idx_field' => 'ListingType', 'match' => 'equals'),
            array('name' => 'Property Sub-Type', 'form_field' => 'search_subtype', 'idx_field' => 'ListingSubType', 'match' => 'findinset'),
            array('name' => 'Status', 'form_field' => 'search_status', 'idx_field' => 'ListingStatus', 'match' => 'equals'),
            array('name' => 'Min. Price', 'form_field' => 'minimum_price', 'idx_field' => 'ListingPrice', 'match' => 'morethan'),
            array('name' => 'Max. Price', 'form_field' => 'maximum_price', 'idx_field' => 'ListingPrice', 'match' => 'lessthan'),
            array('name' => 'Min. Price', 'form_field' => 'minimum_rent', 'idx_field' => 'ListingPrice', 'match' => 'morethan'),
            array('name' => 'Max. Price', 'form_field' => 'maximum_rent', 'idx_field' => 'ListingPrice', 'match' => 'lessthan'),
            array('name' => 'Min. Bedrooms', 'form_field' => 'minimum_beds', 'idx_field' => 'NumberOfBedrooms', 'match' => 'morethan'),
            array('name' => 'Min. Bedrooms', 'form_field' => 'minimum_bedrooms', 'idx_field' => 'NumberOfBedrooms', 'match' => 'morethan'),
            array('name' => 'Max. Bedrooms', 'form_field' => 'maximum_bedrooms', 'idx_field' => 'NumberOfBedrooms', 'match' => 'lessthan'),
            array('name' => 'Min. Bathrooms', 'form_field' => 'minimum_baths', 'idx_field' => 'NumberOfBathrooms', 'match' => 'morethan'),
            array('name' => 'Min. Bathrooms', 'form_field' => 'minimum_bathrooms', 'idx_field' => 'NumberOfBathrooms', 'match' => 'morethan'),
            array('name' => 'Max. Bathrooms', 'form_field' => 'maximum_bathrooms', 'idx_field' => 'NumberOfBathrooms', 'match' => 'lessthan'),
            array('name' => 'Address', 'form_field' => 'search_address', 'idx_field' => 'Address', 'match' => 'like'),
            array('name' => 'City', 'form_field' => 'search_city', 'idx_field' => 'AddressCity', 'match' => 'equals'),
            array('name' => 'Area', 'form_field' => 'search_area', 'idx_field' => 'AddressArea', 'match' => 'equals'),
            array('name' => 'Subdivision', 'form_field' => 'search_subdivision', 'idx_field' => 'AddressSubdivision', 'match' => 'equals'),
            array('name' => 'Zip Code', 'form_field' => 'search_zip', 'idx_field' => 'AddressZipCode', 'match' => 'like'),
            array('name' => 'County', 'form_field' => 'search_county', 'idx_field' => 'AddressCounty', 'match' => 'equals'),
            array('name' => 'State', 'form_field' => 'search_state', 'idx_field' => 'AddressState', 'match' => 'equals'),
            array('name' => 'School District', 'form_field' => 'school_district', 'idx_field' => 'SchoolDistrict', 'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Elementary School', 'form_field' => 'school_elementary', 'idx_field' => 'SchoolElementary', 'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Middle School', 'form_field' => 'school_middle', 'idx_field' => 'SchoolMiddle', 'match' => 'equals', 'group' => 'schools'),
            array('name' => 'High School', 'form_field' => 'school_high', 'idx_field' => 'SchoolHigh', 'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Min. Acres', 'form_field' => 'minimum_acres', 'idx_field' => 'NumberOfAcres', 'match' => 'morethan'),
            array('name' => 'Max. Acres', 'form_field' => 'maximum_acres', 'idx_field' => 'NumberOfAcres', 'match' => 'lessthan'),
            array('name' => 'Min. Sq. Ft.', 'form_field' => 'minimum_sqft', 'idx_field' => 'NumberOfSqFt', 'match' => 'morethan'),
            array('name' => 'Max. Sq. Ft.', 'form_field' => 'maximum_sqft', 'idx_field' => 'NumberOfSqFt', 'match' => 'lessthan'),
            array('name' => 'Min. Year Built', 'form_field' => 'minimum_year', 'idx_field' => 'YearBuilt', 'match' => 'morethan'),
            array('name' => 'Max. Year Built', 'form_field' => 'maximum_year', 'idx_field' => 'YearBuilt', 'match' => 'lessthan'),
            array('name' => 'Office Name', 'form_field' => 'search_office', 'idx_field' => 'ListingOffice', 'match' => 'equals'),
            array('name' => 'Office ID', 'form_field' => 'office_id', 'idx_field' => 'ListingOfficeID', 'match' => 'equals'),
            array('name' => 'Agent Name', 'form_field' => 'search_agent', 'idx_field' => 'ListingAgent', 'match' => 'equals'),
            array('name' => 'Agent ID', 'form_field' => 'agent_id', 'idx_field' => 'ListingAgentID', 'match' => 'equals'),
            array('name' => 'Min. Days on Market', 'form_field' => 'minimum_dom', 'idx_field' => 'ListingDOM', 'match' => 'morethan'),
            array('name' => 'Max. Days on Market', 'form_field' => 'maximum_dom', 'idx_field' => 'ListingDOM', 'match' => 'lessthan'),
            array('name' => 'Foreclosure', 'form_field' => 'search_foreclosure', 'idx_field' => 'IsForeclosure', 'match' => 'equals', 'group' => ($_REQUEST['search_foreclosure'] != 'N' ? 'special' : null)),
            array('name' => 'Short Sale', 'form_field' => 'search_shortsale', 'idx_field' => 'IsShortSale', 'match' => 'equals', 'group' => ($_REQUEST['search_shortsale'] != 'N' ? 'special' : null)),
            array('name' => 'Bank Owned', 'form_field' => 'search_bankowned', 'idx_field' => 'IsBankOwned', 'match' => 'equals', 'group' => ($_REQUEST['search_bankowned'] != 'N' ? 'special' : null)),
            array('name' => 'Swimming Pool', 'form_field' => 'search_pool', 'idx_field' => 'HasPool', 'match' => 'equals'),
            array('name' => 'Has Fireplace', 'form_field' => 'search_fireplace', 'idx_field' => 'HasFireplace', 'match' => 'equals'),
            array('name' => 'Waterfront', 'form_field' => 'search_waterfront', 'idx_field' => 'IsWaterfront', 'match' => 'equals'),
        );

        if(!empty($filter)) {
            $filter = !is_array($filter) ? explode(',', str_replace(' ', '', trim($filter))) : $filter;
            foreach($IDX_SEARCH_FIELDS as $k => $v) {
                if(in_array($v['idx_field'], $filter) === $comparison) {
                    unset($IDX_SEARCH_FIELDS[$k]);
                } else {
                    $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
                    unset($IDX_SEARCH_FIELDS[$k]);
                }
            }
        } else {
            foreach($IDX_SEARCH_FIELDS as $k => $v) {
                $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
                unset($IDX_SEARCH_FIELDS[$k]);
            }
        }

        return $IDX_SEARCH_FIELDS;
    }
}

if (!function_exists('cms_parse_listing')) {
    function cms_parse_listing (IDX $idx, Database $db_idx, $listing) {
        $settings = Settings::getInstance();

        // Locate Listing Image
        $ListingImage = $db_idx->fetchQuery("SELECT `file` FROM `" . $settings->TABLES['UPLOADS'] . "` WHERE `type` = 'listing' AND `row` = '" . $listing['ListingMLS'] . "' ORDER BY `order` ASC LIMIT 1;");
        if (!empty($ListingImage['file'])) {
            $thumbSize = $settings->listings['thumbnails'];
            $listing['ListingImage'] = sprintf(
                '%s%suploads/%s',
                $settings->URLS['URL'],
                $thumbSize ? sprintf('thumbs/%s/', $thumbSize) : '',
                rawurlencode($ListingImage['file'])
            );
        }

        // Use Listing Link, Fallback to MLS Number
        $link = !empty($listing['ListingLink']) ? Format::slugify($listing['ListingLink']) : $listing['ListingMLS'];

        // Build Link Collection
        $listing['url_details']             = $settings->SETTINGS['URL'] . 'listing/' . $idx->getLink() . '/' . $link . '/';
        $listing['url_inquire']             = $listing['url_details'] . 'inquire/';
        $listing['url_phone']               = $listing['url_details'] . 'phone/';
        $listing['url_map']                 = $listing['url_details'] . 'map/';
        $listing['url_sendtofriend']        = $listing['url_details'] . 'friend/';
        $listing['url_birdseye']            = $listing['url_details'] . 'birdseye/';
        $listing['url_streetview']          = $listing['url_details'] . 'streetview/';
        $listing['url_brochure']            = $listing['url_details'] . 'brochure/';
        $listing['url_onboard']             = $listing['url_details'] . 'local/';
        $listing['url_register']            = $listing['url_details'] . 'register/';
        $listing['url_google-vr']           = $listing['url_details'] . 'google_vr/';

        // Format NumberOfSqFt
        $listing['NumberOfSqFt'] = str_replace([',', '.'], '', $listing['NumberOfSqFt']);

        // Format Feature List
        $listing['DescriptionFeatures'] = preg_replace('/,(\s)*/', ', ', $listing['DescriptionFeatures']);

        // Return Listing
        return $listing;

    }
}

if (!function_exists('cms_images')) {
    function cms_images (IDX $idx, Database $db_idx, $listing, $thumbnails = false) {
        $settings = Settings::getInstance();
        $thumbSize = $settings->listings['thumbnails'];
        $images = [];
        if ($thumbnails) {
            $thumbnails = $db_idx->query("SELECT SQL_CACHE `file` FROM `" . $settings->TABLES['UPLOADS'] . "` WHERE `type` = 'listing' AND `row` = '" . $db_idx->cleanInput($listing['ListingMLS']) . "' ORDER BY `order` ASC;");
            if ($thumbnails) {
                while ($thumb = $db_idx->fetchArray($thumbnails)) {
                    if (!empty($thumb['file'])) {
                        $images[] = sprintf('%s%suploads/%s',
                            $settings->URLS['URL'],
                            $thumbSize ? sprintf('thumbs/%s/', $thumbSize) : '',
                            rawurlencode($thumb['file'])
                        );
                    }
                }
            }
        } else {
            if (!empty($listing['ListingImage'])) {
                $images[] = $listing['ListingImage'];
            }
        }
        $images = !empty($images) ? $images : [$settings->SETTINGS['URL_IMG'] . 'no-image.jpg'];
        return $images;
    }
}

if (!function_exists('cms_search_fields')) {

    function cms_search_fields($idx, $filter = '', $comparison = false)  {
        $IDX_SEARCH_FIELDS = [
            ['name' => 'Agent ID',            'form_field' => 'agent_id',              'idx_field' => 'ListingAgentID',        'match' => 'equals'],
            ['name' => 'Team ID',             'form_field' => 'team_id',               'idx_field' => 'ListingTeamID',         'match' => 'equals'],
        ];

        if (!empty($filter))  {
            $filter = !is_array($filter) ? explode(',', str_replace(' ', '', trim($filter))) : $filter;
            foreach ($IDX_SEARCH_FIELDS as $k => $v)  {
                if (in_array($v['idx_field'], $filter) === $comparison) {
                    unset($IDX_SEARCH_FIELDS[$k]);
                } else {
                    $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
                    unset($IDX_SEARCH_FIELDS[$k]);
                }
            }
        } else {
            foreach ($IDX_SEARCH_FIELDS as $k => $v)  {
                $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
                unset($IDX_SEARCH_FIELDS[$k]);
            }
        }

        return $IDX_SEARCH_FIELDS;

    }

}