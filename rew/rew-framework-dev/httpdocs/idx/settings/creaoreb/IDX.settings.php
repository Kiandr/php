<?php

// IDX Resource
$IDX_RESOURCE = array(
    'settings' => array(
        'name'     => 'creaoreb',
        'database' => 'creaoreb',
        'table'    => '_rewidx_listings',
        'search_where_callback' => function ($alias, $idx) {
            $db = DB::get('users');
            $agent_ids = $db->fetch("SELECT `agent_id` FROM `agents` WHERE `id` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']));

            // Listings in CREA and in both are always displayable.
            $alias = !empty($alias) ? "`" . $alias . "`." : '';
            $agent_where = $alias . "`" . $idx->field('ListingFeed') . "` = 'crea' OR " . $alias . "`" . $idx->field('ListingFeedBoth') . "` = 'Y'";

            if (trim($agent_ids['agent_id']) == '') {
                // No agent ids are entered.
                return $agent_where;
            }

            $agent_ids = json_decode($agent_ids['agent_id'], true);

            if (trim($agent_ids['creaoreb']) == '') {
                // There is nothing set for CREAOREB
                return $agent_where;
            }

            // Split into multiples
            $agent_id_set = explode(',', $agent_ids['creaoreb']);

            // Trim any whitespace
            $agent_id_set = array_map('trim', $agent_id_set);

            // Quote
            if (count($agent_id_set) > 1) {
                $agent_id_set = "IN (" . implode(", ", array_map([$db, 'quote'], $agent_id_set)) . ")";
            } else {
                $agent_id_set = " = " . $db->quote(reset($agent_id_set));
            }

            $agent_fields = [$idx->field('ListingAgentID')];

            // Autodiscover all ListingCoAgentID fields
            for ($index = 1; ; $index++) {
                $field = $idx->field('ListingCoAgentID' . ($index === 1 ? '' : $index));

                // No more fields to search by
                if (empty($field)) break;

                $agent_fields[] = $field;
            }

            foreach ($agent_fields as $field) {
                $agent_where .= " OR " . $alias . "`" . $field . "`" . $agent_id_set;

            }

            return $agent_where;
        }
    ),
    'fields'   => array(
        'id'                          => 'id',
        'ListingUniqueID'             => 'ListingUniqueID',
        'ListingFeed'                 => 'ListingFeed',
        'ListingFeedBoth'             => 'ListingFeedBoth',
        'ListingMLS'                  => 'ListingMLS',
        'ListingPrice'                => 'ListingPrice',
        'ListingPriceOld'             => 'ListingPriceOld',
        'ListingPriceChanged'         => 'ListingPriceChanged',
        'ListingType'                 => 'ListingType',
        'ListingSubType'              => 'ListingSubType',
        'ListingStyle'                => 'ListingStyle',
        'ListingStatus'               => 'ListingStatus',
        'ListingStatusOld'            => 'ListingStatusOld',
        'ListingStatusChanged'        => 'ListingStatusChanged',
        'ListingRemarks'              => 'ListingRemarks',
        'ListingImage'                => 'ListingImage',
        'ListingDate'                 => 'ListingDate',
        'ListingDOM'                  => 'ListingDOM',
        'timestamp_created'           => 'timestamp_created',
        'Address'                     => 'Address',
        'AddressArea'                 => 'AddressArea',
        'AddressSubdivision'          => 'AddressSubdivision',
        'AddressCity'                 => 'AddressCity',
        'AddressCounty'               => 'AddressCounty',
        'AddressState'                => 'AddressState',
        'AddressZipCode'              => 'AddressZipCode',
        'SchoolDistrict'              => 'SchoolDistrict',
        'SchoolElementary'            => 'SchoolElementary',
        'SchoolMiddle'                => 'SchoolMiddle',
        'SchoolHigh'                  => 'SchoolHigh',
        'NumberOfBedrooms'            => 'NumberOfBedrooms',
        'NumberOfBathrooms'           => 'NumberOfBathrooms',
        'NumberOfBathsFull'           => 'NumberOfBathsFull',
        'NumberOfBathsHalf'           => 'NumberOfBathsHalf',
        'NumberOfSqFt'                => 'NumberOfSqFt',
        'NumberOfAcres'               => 'NumberOfAcres',
        'NumberOfStories'             => 'NumberOfStories',
        'NumberOfGarages'             => 'NumberOfGarages',
        'NumberOfParkingSpaces'       => 'NumberOfParkingSpaces',
        'NumberOfFireplaces'          => 'NumberOfFireplaces',
        'YearBuilt'                   => 'YearBuilt',
        'HasPool'                     => 'HasPool',
        'HasFireplace'                => 'HasFireplace',
        'HasBasement'                 => 'HasBasement',
        'IsWaterfront'                => 'IsWaterfront',
        'IsForeclosure'               => 'IsForeclosure',
        'IsShortSale'                 => 'IsShortSale',
        'IsBankOwned'                 => 'IsBankOwned',
        'DescriptionLot'              => 'DescriptionLot',
        'DescriptionPool'             => 'DescriptionPool',
        'DescriptionView'             => 'DescriptionView',
        'DescriptionStories'          => 'DescriptionStories',
        'DescriptionFireplace'        => 'DescriptionFireplace',
        'DescriptionWaterfront'       => 'DescriptionWaterfront',
        'DescriptionBasement'         => 'DescriptionBasement',
        'DescriptionGarages'          => 'DescriptionGarages',
        'DescriptionParking'          => 'DescriptionParking',
        'DescriptionAmenities'        => 'DescriptionAmenities',
        'DescriptionAppliances'       => 'DescriptionAppliances',
        'DescriptionUtilities'        => 'DescriptionUtilities',
        'DescriptionFeatures'         => 'DescriptionFeatures',
        'DescriptionExterior'         => 'DescriptionExterior',
        'DescriptionExteriorFeatures' => 'DescriptionExteriorFeatures',
        'DescriptionInterior'         => 'DescriptionInterior',
        'DescriptionInteriorFeatures' => 'DescriptionInteriorFeatures',
        'DescriptionHeating'          => 'DescriptionHeating',
        'DescriptionCooling'          => 'DescriptionCooling',
        'DescriptionZoning'           => 'DescriptionZoning',
        'DescriptionRoofing'          => 'DescriptionRoofing',
        'DescriptionWindows'          => 'DescriptionWindows',
        'DescriptionConstruction'     => 'DescriptionConstruction',
        'DescriptionFoundation'       => 'DescriptionFoundation',
        'DescriptionHOAFees'          => 'DescriptionHOAFees',
        'DescriptionHOAFeesFrequency' => 'DescriptionHOAFeesFrequency',
        'ListingOffice'               => 'ListingOffice',
        'ListingOfficeID'             => 'ListingOfficeID',
        'ListingAgent'                => 'ListingAgent',
        'ListingAgentID'              => 'ListingAgentID',
        'ListingCoAgentID'            => 'ListingCoAgentID',
        'ListingCoAgentID2'           => 'ListingCoAgentID2',
        'ListingCoAgentID3'           => 'ListingCoAgentID3',
        'Latitude'                    => 'Latitude',
        'Longitude'                   => 'Longitude',
        'VirtualTour'                 => 'VirtualTour',
    )
);

// IDX Collection
$IDX = array();

// Add to Collection
array_push($IDX, array('name' => $IDX_RESOURCE['settings']['name'], 'settings' => $IDX_RESOURCE['settings'], 'fields' => $IDX_RESOURCE['fields']));
