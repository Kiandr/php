<?php

// Listing Details
$_DETAILS = array();

// Add To Collection
$_DETAILS[] = array('heading' => 'Essential Information', 'fields' => array(
                array('value' => 'ListingMLS',                      'title' => Lang::write('MLS_NUMBER')),
                array('value' => 'ListingPrice',                    'title' => 'Price',               'format' => 'us_currency'),
                array('value' => 'NumberOfBedrooms',                'title' => 'Bedrooms'),
                array('value' => 'NumberOfBathrooms',               'title' => 'Bathrooms'),
                array('value' => 'NumberOfBathsFull',               'title' => 'Full Baths'),
                array('value' => 'NumberOfBathsHalf',               'title' => 'Half Baths'),
                array('value' => 'NumberOfSqFt',                    'title' => 'Square Footage',     'format' => 'number_format'),
                array('value' => 'DescriptionSqFt',                 'title' => 'Square Footage'),
                array('value' => 'NumberOfAcres',                   'title' => 'Acres',              'format' => 'number_format2'),
                array('value' => 'YearBuilt',                       'title' => 'Year Built'),
                array('value' => 'ListingType',                     'title' => 'Type'),
                array('value' => 'ListingSubType',                  'title' => 'Sub-Type'),
                array('value' => 'ListingStyle',                    'title' => 'Style'),
                array('value' => 'ListingStatus',                   'title' => 'Status'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'Community Information', 'fields' => array(
                array('value' => 'Address',                         'title' => 'Address'),
                array('value' => 'AddressArea',                     'title' => 'Area'),
                array('value' => 'AddressSubdivision',              'title' => 'Subdivision'),
                array('value' => 'AddressCity',                     'title' => 'City'),
                array('value' => 'AddressCounty',                   'title' => 'County'),
                array('value' => 'AddressState',                    'title' => 'State'),
                array('value' => 'AddressZipCode',                  'title' => 'Zip Code'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'Amenities', 'fields' => array(
                array('value' => 'DescriptionAmenities',            'title' => 'Amenities'),
                array('value' => 'DescriptionUtilities',            'title' => 'Utilities'),
                array('value' => 'DescriptionFeatures',             'title' => 'Features'),
                array('value' => 'NumberOfParkingSpaces',           'title' => 'Parking Spaces'),
                array('value' => 'DescriptionParking',              'title' => 'Parking'),
                array('value' => 'NumberOfGarages',                 'title' => '# of Garages'),
                array('value' => 'DescriptionGarages',              'title' => 'Garages'),
                array('value' => 'DescriptionView',                 'title' => 'View'),
                array('value' => 'IsWaterfront',                    'title' => 'Is Waterfront',        'format' => 'enum_YN'),
                array('value' => 'DescriptionWaterfront',           'title' => 'Waterfront'),
                array('value' => 'HasPool',                         'title' => 'Has Pool',             'format' => 'enum_YN'),
                array('value' => 'DescriptionPool',                 'title' => 'Pool'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'Interior', 'fields' => array(
                array('value' => 'DescriptionInterior',             'title' => 'Interior'),
                array('value' => 'DescriptionInteriorFeatures',     'title' => 'Interior Features'),
                array('value' => 'DescriptionAppliances',           'title' => 'Appliances'),
                array('value' => 'DescriptionHeating',              'title' => 'Heating'),
                array('value' => 'DescriptionCooling',              'title' => 'Cooling'),
                array('value' => 'HasFireplace',                    'title' => 'Fireplace',            'format' => 'enum_YN'),
                array('value' => 'NumberOfFireplaces',              'title' => '# of Fireplaces'),
                array('value' => 'DescriptionFireplace',            'title' => 'Fireplaces'),
                array('value' => 'NumberOfStories',                 'title' => '# of Stories'),
                array('value' => 'DescriptionStories',              'title' => 'Stories'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'Exterior', 'fields' => array(
                array('value' => 'DescriptionExterior',             'title' => 'Exterior'),
                array('value' => 'DescriptionExteriorFeatures',     'title' => 'Exterior Features'),
                array('value' => 'DescriptionLot',                  'title' => 'Lot Description'),
                array('value' => 'DescriptionWindows',              'title' => 'Windows'),
                array('value' => 'DescriptionRoofing',              'title' => 'Roof'),
                array('value' => 'DescriptionConstruction',         'title' => 'Construction'),
                array('value' => 'DescriptionFoundation',           'title' => 'Foundation'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'School Information', 'fields' => array(
                array('value' => 'SchoolDistrict',                  'title' => 'District'),
                array('value' => 'SchoolElementary',                'title' => 'Elementary'),
                array('value' => 'SchoolMiddle',                    'title' => 'Middle'),
                array('value' => 'SchoolHigh',                      'title' => 'High'),
            ));

// Add To Collection
$_DETAILS[] = array('heading' => 'Additional Information', 'fields' => array(
                //array('value' => 'ListingDate',                     'title' => 'Date Listed',          'format' => 'date_format'),	// Disallowed by Compliance
                //array('value' => 'ListingDOM',                      'title' => 'Days on Market'),										// Disallowed by Compliance
                array('value' => 'DescriptionZoning',               'title' => 'Zoning'),
                array('value' => 'IsForeclosure',                   'title' => 'Foreclosure',          'format' => 'enum_YN'),
                array('value' => 'IsShortSale',                     'title' => 'Short Sale',           'format' => 'enum_YN'),
                array('value' => 'IsBankOwned',                     'title' => 'RE / Bank Owned',      'format' => 'enum_YN'),
            ));
