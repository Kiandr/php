<?php

// Listing Details
$_DETAILS = array();

$_DETAILS[] = array('heading' => 'Essential Information', 'fields' => array(
				array('format' => '',               'title' => 'Price Change History',      'unlock' => true,  'value' => 'ListingPriceChangeHistory'),
				array('format' => '',               'title' => 'Days on Market',            'unlock' => true,  'value' => 'ListingDOM'),
				array('format' => '',               'title' => 'Cumulative Days on Market', 'unlock' => true,  'value' => 'ListingDaysOnMarketCumulative'),
				array('format' => '',               'title' => 'HOA',                       'unlock' => false, 'value' => 'DescriptionHOA'),
				array('format' => 'us_currency',    'title' => 'HOA Fees',                  'unlock' => false, 'value' => 'DescriptionHOAFees'),
				array('format' => '',               'title' => 'HOA Fees Frequency',        'unlock' => false, 'value' => 'DescriptionHOAFeesFrequency'),
				array('format' => '',               'title' => Lang::write('MLS_NUMBER'),   'unlock' => false, 'value' => 'ListingMLS'),
				array('format' => 'us_currency',    'title' => 'Price',                     'unlock' => false, 'value' => 'ListingPrice'),
				array('format' => '',               'title' => 'Bedrooms',                  'unlock' => false, 'value' => 'NumberOfBedrooms'),
				array('format' => '',               'title' => 'Bathrooms',                 'unlock' => false, 'value' => 'NumberOfBathrooms'),
				array('format' => '',               'title' => 'Full Baths',                'unlock' => false, 'value' => 'NumberOfBathsFull'),
				array('format' => '',               'title' => 'Half Baths',                'unlock' => false, 'value' => 'NumberOfBathsHalf'),
				array('format' => 'number_format',  'title' => 'Square Footage',            'unlock' => false, 'value' => 'NumberOfSqFt'),
				array('format' => '',               'title' => 'Stories',                   'unlock' => false, 'value' => 'NumberOfStories'),
				array('format' => 'number_format2', 'title' => 'Acres',                     'unlock' => false, 'value' => 'NumberOfAcres'),
				array('format' => '',               'title' => 'Year Built',                'unlock' => false, 'value' => 'YearBuilt'),
				array('format' => '',               'title' => 'Type',                      'unlock' => false, 'value' => 'ListingType'),
				array('format' => '',               'title' => 'Sub-Type',                  'unlock' => false, 'value' => 'ListingSubType'),
				array('format' => '',               'title' => 'Style',                     'unlock' => false, 'value' => 'ListingStyle'),
				array('format' => '',               'title' => 'Status',                    'unlock' => false, 'value' => 'ListingStatus'),
			));

$_DETAILS[] = array('heading' => 'Community Information', 'fields' => array(
				array('value' => 'Address',            'title' => 'Address'),
				array('value' => 'AddressArea',        'title' => 'Area'),
				array('value' => 'AddressSubdivision', 'title' => 'Subdivision'),
				array('value' => 'AddressCity',        'title' => 'City'),
				array('value' => 'AddressCounty',      'title' => 'County'),
				array('value' => 'AddressState',       'title' => 'State'),
				array('value' => 'AddressZipCode',     'title' => 'Zip Code'),
			));

$_DETAILS[] = array('heading' => 'Amenities', 'fields' => array(
				array('value' => 'DescriptionAmenities',     'title' => 'Amenities'),
				array('value' => 'DescriptionGreenFeatures', 'title' => 'Green Features'),
				array('value' => 'DescriptionUtilities',     'title' => 'Utilities'),
				array('value' => 'DescriptionFeatures',      'title' => 'Features'),
				array('value' => 'NumberOfParkingSpaces',    'title' => 'Parking Spaces'),
				array('value' => 'DescriptionParking',       'title' => 'Parking'),
				array('value' => 'NumberOfGarages',          'title' => '# of Garages'),
				array('value' => 'DescriptionGarages',       'title' => 'Garages'),
				array('value' => 'DescriptionView',          'title' => 'View'),
				array('value' => 'IsWaterfront',             'title' => 'Waterfront', 'format' => 'enum_YN'),
				array('value' => 'DescriptionWaterfront',    'title' => 'Waterfront'),
				array('value' => 'HasPool',                  'title' => 'Has Pool',   'format' => 'enum_YN'),
				array('value' => 'DescriptionPool',          'title' => 'Pool'),
			));

// Add To Collection
$_DETAILS[] = array('heading' => 'Interior', 'fields' => array(
				array('format' => '',        'title' => 'Interior',           'value' => 'DescriptionInterior'),
				array('format' => '',        'title' => 'Interior Features',  'value' => 'DescriptionInteriorFeatures'),
				array('format' => '',        'title' => 'Appliances',         'value' => 'DescriptionAppliances'),
				array('format' => '',        'title' => 'Kitchen',            'value' => 'DescriptionKitchen'),
				array('format' => '',        'title' => 'Bedroom / Bathroom', 'value' => 'DescriptionBedroomBathroom'),
				array('format' => '',        'title' => 'Specialty Rooms',    'value' => 'DescriptionSpecialtyRooms'),
				array('format' => '',        'title' => 'Utility Room',       'value' => 'DescriptionUtilityRoom'),
				array('format' => '',        'title' => 'Floors',             'value' => 'DescriptionFloors'),
				array('format' => '',        'title' => 'Heating',            'value' => 'DescriptionHeating'),
				array('format' => '',        'title' => 'Cooling',            'value' => 'DescriptionCooling'),
				array('format' => 'enum_YN', 'title' => 'Fireplace',          'value' => 'HasFireplace'),
				array('format' => '',        'title' => '# of Fireplaces',    'value' => 'NumberOfFireplaces'),
				array('format' => '',        'title' => 'Fireplaces',         'value' => 'DescriptionFireplace'),
				array('format' => '',        'title' => 'Stories',            'value' => 'DescriptionStories'),
			));

$_DETAILS[] = array('heading' => 'Exterior', 'fields' => array(
				array('value' => 'DescriptionExterior',             'title' => 'Exterior'),
				array('value' => 'DescriptionExteriorFeatures',     'title' => 'Exterior Features'),
				array('value' => 'DescriptionLot',                  'title' => 'Lot Description'),
				array('value' => 'DescriptionWindows',              'title' => 'Windows'),
				array('value' => 'DescriptionRoofing',              'title' => 'Roof'),
				array('value' => 'DescriptionConstruction',         'title' => 'Construction'),
				array('value' => 'DescriptionFoundation',           'title' => 'Foundation'),
			));

$_DETAILS[] = array('heading' => 'Additional Information', 'fields' => array(
				array('value' 	=> 'DescriptionZoning', 'title' => 'Zoning'),
				array('value' 	=> 'IsForeclosure',     'title' => 'Foreclosure',     'format' => 'enum_YN'),
				array('value' 	=> 'IsShortSale',       'title' => 'Short Sale',      'format' => 'enum_YN'),
				array('value' 	=> 'IsBankOwned',       'title' => 'RE / Bank Owned', 'format' => 'enum_YN'),
			));

$_DETAILS[] = array('heading' => 'Room Sizes', 'fields' => array(
				array('dimensions' => 'DimensionsMasterBedroom', 'level' => 'LevelMasterBedroom', 'title' => 'Master Bedroom'),
				array('dimensions' => 'DimensionsBedroom2',      'level' => 'LevelBedroom2',      'title' => 'Bedroom 2'),
				array('dimensions' => 'DimensionsBedroom3',      'level' => 'LevelBedroom3',      'title' => 'Bedroom 3'),
				array('dimensions' => 'DimensionsBedroom4',      'level' => 'LevelBedroom4',      'title' => 'Bedroom 4'),
				array('dimensions' => 'DimensionsBedroom5',      'level' => 'LevelBedroom5',      'title' => 'Bedroom 5'),
				array('dimensions' => 'DimensionsBreakfastRoom', 'level' => 'LevelBreakfastRoom', 'title' => 'Breakfast Room'),
				array('dimensions' => 'DimensionsDiningRoom',    'level' => 'LevelDiningRoom',    'title' => 'Dining Room'),
				array('dimensions' => 'DimensionsKitchen',       'level' => 'LevelKitchen',       'title' => 'Kitchen'),
				array('dimensions' => 'DimensionsLivingRoom1',   'level' => 'LevelLivingRoom1',   'title' => 'Living Room 1'),
				array('dimensions' => 'DimensionsLivingRoom2',   'level' => 'LevelLivingRoom2',   'title' => 'Living Room 2'),
				array('dimensions' => 'DimensionsLivingRoom3',   'level' => 'LevelLivingRoom3',   'title' => 'Living Room 3'),
				array('dimensions' => 'DimensionsOther1',        'level' => 'LevelOtherRoom1',    'title' => 'Other Room 1'),
				array('dimensions' => 'DimensionsOther2',        'level' => 'LevelOtherRoom2',    'title' => 'Other Room 2'),
				array('dimensions' => 'DimensionsStudy',         'level' => 'LevelStudy',         'title' => 'Study'),
				array('dimensions' => 'DimensionsUtility',       'level' => 'LevelUtilityRoom',   'title' => 'Utility Room'),
			));
