<?php

if (!function_exists('car_search_fields')) {

	function car_search_fields($idx, $filter = '', $comparison = false)  {

        $IDX_SEARCH_FIELDS = array(
            array('name' => 'MLS Number',          'form_field' => 'search_mls',            'idx_field' => 'ListingMLS',            'match' => 'equals'),
            array('name' => 'Property Type',       'form_field' => 'search_type',           'idx_field' => 'ListingType',           'match' => 'equals'),
            array('name' => 'Property Sub-Type',   'form_field' => 'search_subtype',        'idx_field' => 'ListingSubType',        'match' => 'findinset'),
            array('name' => 'Status',              'form_field' => 'search_status',         'idx_field' => 'ListingStatus',         'match' => 'equals'),
            array('name' => 'Min. Price',          'form_field' => 'minimum_price',         'idx_field' => 'ListingPrice',          'match' => 'morethan'),
            array('name' => 'Max. Price',          'form_field' => 'maximum_price',         'idx_field' => 'ListingPrice',          'match' => 'lessthan'),
            array('name' => 'Min. Price',          'form_field' => 'minimum_rent',          'idx_field' => 'ListingPrice',          'match' => 'morethan'),
            array('name' => 'Max. Price',          'form_field' => 'maximum_rent',          'idx_field' => 'ListingPrice',          'match' => 'lessthan'),
            array('name' => 'Min. Bedrooms',       'form_field' => 'minimum_beds',          'idx_field' => 'NumberOfBedrooms',      'match' => 'morethan'),
            array('name' => 'Min. Bedrooms',       'form_field' => 'minimum_bedrooms',      'idx_field' => 'NumberOfBedrooms',      'match' => 'morethan'),
            array('name' => 'Max. Bedrooms',       'form_field' => 'maximum_bedrooms',      'idx_field' => 'NumberOfBedrooms',      'match' => 'lessthan'),
            array('name' => 'Min. Bathrooms',      'form_field' => 'minimum_baths',         'idx_field' => 'NumberOfBathrooms',     'match' => 'morethan'),
            array('name' => 'Min. Bathrooms',      'form_field' => 'minimum_bathrooms',     'idx_field' => 'NumberOfBathrooms',     'match' => 'morethan'),
            array('name' => 'Max. Bathrooms',      'form_field' => 'maximum_bathrooms',     'idx_field' => 'NumberOfBathrooms',     'match' => 'lessthan'),
            array('name' => 'Address',             'form_field' => 'search_address',        'idx_field' => 'Address',               'match' => 'like'),
            array('name' => 'City',                'form_field' => 'search_city',           'idx_field' => 'AddressCity',           'match' => 'equals'),
            array('name' => 'Area',                'form_field' => 'search_area',           'idx_field' => 'AddressArea',           'match' => 'equals'),
            array('name' => 'Subdivision',         'form_field' => 'search_subdivision',    'idx_field' => 'AddressSubdivision',    'match' => 'equals'),
            array('name' => 'Zip Code',            'form_field' => 'search_zip',            'idx_field' => 'AddressZipCode',        'match' => 'beginslike'),
            array('name' => 'County',              'form_field' => 'search_county',         'idx_field' => 'AddressCounty',         'match' => 'equals'),
            array('name' => 'State',               'form_field' => 'search_state',          'idx_field' => 'AddressState',          'match' => 'equals'),
            array('name' => 'School District',     'form_field' => 'school_district',       'idx_field' => 'SchoolDistrict',        'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Elementary School',   'form_field' => 'school_elementary',     'idx_field' => 'SchoolElementary',      'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Middle School',       'form_field' => 'school_middle',         'idx_field' => 'SchoolMiddle',          'match' => 'equals', 'group' => 'schools'),
            array('name' => 'High School',         'form_field' => 'school_high',           'idx_field' => 'SchoolHigh',            'match' => 'equals', 'group' => 'schools'),
            array('name' => 'Min. Acres',          'form_field' => 'minimum_acres',         'idx_field' => 'NumberOfAcres',         'match' => 'morethan'),
            array('name' => 'Max. Acres',          'form_field' => 'maximum_acres',         'idx_field' => 'NumberOfAcres',         'match' => 'lessthan'),
            array('name' => 'Min. Sq. Ft.',        'form_field' => 'minimum_sqft',          'idx_field' => 'NumberOfSqFt',          'match' => 'morethan'),
            array('name' => 'Max. Sq. Ft.',        'form_field' => 'maximum_sqft',          'idx_field' => 'NumberOfSqFt',          'match' => 'lessthan'),
            array('name' => 'Min. Year Built',     'form_field' => 'minimum_year',          'idx_field' => 'YearBuilt',             'match' => 'morethan'),
            array('name' => 'Max. Year Built',     'form_field' => 'maximum_year',          'idx_field' => 'YearBuilt',             'match' => 'lessthan'),
            array('name' => 'Office Name',         'form_field' => 'search_office',         'idx_field' => 'ListingOffice',         'match' => 'equals'),
            array('name' => 'Office ID',           'form_field' => 'office_id',             'idx_field' => 'ListingOfficeID',       'match' => 'equals'),
            array('name' => 'Agent Name',          'form_field' => 'search_agent',          'idx_field' => 'ListingAgent',          'match' => 'equals'),
            array('name' => 'Agent ID',            'form_field' => 'agent_id',              'idx_field' => 'ListingAgentID',        'match' => 'equals'),
            array('name' => 'Min. Days on Market', 'form_field' => 'minimum_dom',           'idx_field' => 'ListingDOM',            'match' => 'morethan'),
            array('name' => 'Max. Days on Market', 'form_field' => 'maximum_dom',           'idx_field' => 'ListingDOM',            'match' => 'lessthan'),
            array('name' => 'Foreclosure',         'form_field' => 'search_foreclosure',    'idx_field' => 'IsForeclosure',         'match' => 'equals', 'group' => ($_REQUEST['search_foreclosure'] != 'N' ? 'special' : null)),
            array('name' => 'Short Sale',          'form_field' => 'search_shortsale',      'idx_field' => 'IsShortSale',           'match' => 'equals', 'group' => ($_REQUEST['search_shortsale']   != 'N' ? 'special' : null)),
            array('name' => 'Bank Owned',          'form_field' => 'search_bankowned',      'idx_field' => 'IsBankOwned',           'match' => 'equals', 'group' => ($_REQUEST['search_bankowned']   != 'N' ? 'special' : null)),
            array('name' => 'Swimming Pool',       'form_field' => 'search_pool',           'idx_field' => 'HasPool',               'match' => 'equals'),
            array('name' => 'Has Fireplace',       'form_field' => 'search_fireplace',      'idx_field' => 'HasFireplace',          'match' => 'equals'),
            array('name' => 'Waterfront',          'form_field' => 'search_waterfront',     'idx_field' => 'IsWaterfront',          'match' => 'equals'),
        );

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
