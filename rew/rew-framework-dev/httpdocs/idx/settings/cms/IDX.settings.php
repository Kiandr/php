<?php

/* IDX Resource */
$IDX_RESOURCE = array(
    'settings' => array(
        'name'     => 'cms',
        'database' => 'cms',
        'table'    => '_listings',
        'geo_table'=> null,
        'title'    => 'Pocket Listings',
        'search_where_callback' => function ($alias, $idx) {

            $agent_where = '';

            $alias = !empty($alias) ? "`" . $alias . "`." : '';

            $db_idx = Util_IDX::getDatabase($idx->getDatabase());

            // Team Subdomain Filter
            if (!empty(Settings::getInstance()->SETTINGS['team'])) {

                $team = Backend_Team::load(Settings::getInstance()->SETTINGS['team']);

                // Get agents sharing with this Team
                $agentCollection = $team->getAgentCollection()
                    ->filterByGrantingPermissions([Backend_Team::PERM_SHARE_FEATURE_LISTINGS])
                    ->cache();
                $agents = $agentCollection->getAllAgents();

                if ($agents) {
                    $agent_where = $alias . "`" . $idx->field('ListingAgentID') . "` IN (" . implode(", ", array_map([$db_idx, 'cleanInput'], array_values($agents))) . ")"
                        . " AND " . $alias . "`" . $idx->field('ListingTeamID') . "`  = " . $db_idx->cleanInput(Settings::getInstance()->SETTINGS['team']);
                // Don't show any CMS listings if there are no agents sharing their listings
                } else {
                    $agent_where = 'FALSE';
                }
            // Agent Subdomain Filter
            } else if (isset(Settings::getInstance()->SETTINGS['agent']) && Settings::getInstance()->SETTINGS['agent'] !== 1) {
                $agent_where = $alias . "`" . $idx->field('ListingAgentID') . "` = " . $db_idx->cleanInput(Settings::getInstance()->SETTINGS['agent']);
            }

            return $agent_where;
        }
    ),
    'fields'   => array(
        'timestamp_created'           => 'timestamp_created',
        'ListingLink'                 => 'link',
        'ListingTitle'                => 'title',
        'ListingMLS'                  => 'id',
        'ListingMLSNumber'            => 'mls_number',
        'ListingPrice'                => 'price',
        'ListingType'                 => 'type',
        'ListingSubType'              => null,
        'ListingStyle'                => null,
        'ListingStatus'               => 'status',
        'ListingRemarks'              => 'description',
        'ListingImage'                => null,
        'ListingDate'                 => null,
        'ListingDOM'                  => null,
        'Address'                     => 'address',
        'AddressArea'                 => null,
        'AddressSubdivision'          => 'subdivision',
        'AddressCity'                 => 'city',
        'AddressCounty'               => null,
        'AddressState'                => 'state',
        'AddressZipCode'              => 'zip',
        'SchoolDistrict'              => 'school_district',
        'SchoolElementary'            => 'school_elementary',
        'SchoolMiddle'                => 'school_middle',
        'SchoolHigh'                  => 'school_high',
        'NumberOfBedrooms'            => 'bedrooms',
        'NumberOfBathrooms'           => 'bathrooms',
        'NumberOfBathsFull'           => null,
        'NumberOfBathsHalf'           => 'bathrooms_half',
        'NumberOfSqFt'                => 'squarefeet',
        'NumberOfAcres'               => null,
        'NumberOfStories'             => null,
        'NumberOfGarages'             => 'garages',
        'NumberOfParkingSpaces'       => null,
        'NumberOfFireplaces'          => null,
        'YearBuilt'                   => 'yearbuilt',
        'HasPool'                     => null,
        'HasFireplace'                => null,
        'IsWaterfront'                => null,
        'IsForeclosure'               => null,
        'IsShortSale'                 => null,
        'IsBankOwned'                 => null,
        'DescriptionLot'              => 'lotsize',
        'DescriptionPool'             => null,
        'DescriptionView'             => null,
        'DescriptionStories'          => 'stories',
        'DescriptionFireplace'        => null,
        'DescriptionWaterfront'       => null,
        'DescriptionGarages'          => null,
        'DescriptionParking'          => null,
        'DescriptionAmenities'        => null,
        'DescriptionAppliances'       => null,
        'DescriptionUtilities'        => null,
        'DescriptionFeatures'         => 'features',
        'DescriptionExterior'         => null,
        'DescriptionExteriorFeatures' => null,
        'DescriptionInterior'         => null,
        'DescriptionInteriorFeatures' => null,
        'DescriptionHeating'          => null,
        'DescriptionCooling'          => null,
        'DescriptionZoning'           => null,
        'DescriptionRoofing'          => null,
        'DescriptionWindows'          => null,
        'DescriptionConstruction'     => null,
        'DescriptionFoundation'       => null,
        'ListingOffice'               => null,
        'ListingOfficeID'             => null,
        'ListingAgent'                => null,
        'ListingAgentID'              => 'agent',
        'ListingTeamID'               => 'team',
        'Latitude'                    => 'latitude',
        'Longitude'                   => 'longitude',
        'VirtualTour'                 => 'virtual_tour',
    ),
);

/* IDX Collection */
$IDX = array();

/* Add to Collection */
array_push($IDX, array('name' => $IDX_RESOURCE['settings']['name'], 'settings' => $IDX_RESOURCE['settings'], 'fields' => $IDX_RESOURCE['fields']));

?>
