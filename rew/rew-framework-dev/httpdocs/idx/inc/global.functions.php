<?php

function images(IDX $idx, Database $db_idx, $listing, $thumbnails = false, $size = null)
{

    $noimages = array(Settings::getInstance()->SETTINGS['URL_IMG'] . 'no-image.jpg');
    if (empty($listing)) {
        return $noimages;
    }

    $imgfunc = str_replace('-', '_', $idx->getLink()) . '_images';
    $imgfunc = function_exists($imgfunc) ? $imgfunc : str_replace('-', '_', $idx->getDatabase()) . '_images';
    if (function_exists($imgfunc)) {
        return $imgfunc($idx, $db_idx, $listing, $thumbnails, $size);
    }

    // If $size was not given, or if in CLI mode, or if the page is among
    // the ones listed then use the large photo instead of the original.
    if (empty($size)
        && ((PHP_SAPI === 'cli' || 'cron' === $_GET['page'])
        || (!empty($_GET['load_page']) && in_array($_GET['load_page'], array(
            'details',
            'print',
            'brochure'
            ))))
    ) {
        $size = IDX_Feed::IMAGE_SIZE_LARGE;
    }
    $size = $size ?: IDX_Feed::IMAGE_SIZE_MEDIUM;

    $images = array();
    if ($thumbnails) {
        if (empty($listing['ListingMLS'])) {
            return $noimages;
        }
        $sql =
            "SELECT `ImageURL` ".
            "FROM `" . $idx->getTable('image') . "` ".
            "WHERE `ListingMLS` = '" . $db_idx->cleanInput($listing['ListingMLS']) . "' ".
            "ORDER BY `ImageOrder` ASC";
        if (!$thumbnails = $db_idx->query($sql)) {
            return $noimages;
        }
        while ($thumb = $db_idx->fetchArray($thumbnails)) {
            if (empty($thumb['ImageURL'])) {
                continue;
            }
            $images[] = IDX_Feed::thumbUrl($thumb['ImageURL'], $size);
        }
    } else if (!empty($listing['ListingImage'])) {
        $images[] = IDX_Feed::thumbUrl($listing['ListingImage'], $size);
    }

    return $images ?: $noimages;
}

function search_criteria(IDX $idx, $request = array())
{
    $search_criteria_func = str_replace('-', '_', $idx->getLink()) . '_search_criteria';
    $search_criteria_func = function_exists($search_criteria_func) ? $search_criteria_func : str_replace('-', '_', $idx->getDatabase()) . '_search_criteria';
    if (function_exists($search_criteria_func)) {
        return $search_criteria_func($idx, $request);
    } else {
        return $request;
    }
}

function search_fields(IDX $idx, $filter = '', $comparison = false)
{

    $search_fields_func = str_replace('-', '_', $idx->getLink()) . '_search_fields';
    $search_fields_func = function_exists($search_fields_func) ? $search_fields_func : str_replace('-', '_', $idx->getDatabase()) . '_search_fields';
    if (function_exists($search_fields_func)) {
        $search_fields = $search_fields_func($idx, $filter, $comparison);
    } else {
        $search_fields = array();
    }

    $IDX_SEARCH_FIELDS = array(
        // Search Price Reduced
        array('name' => 'Reduced Listings', 'form_field' => 'search_reduced_price', 'idx_field' => 'ListingPriceChanged', 'idx_fields'=> array('current' => 'ListingPrice', 'old' => 'ListingPriceOld', 'changed' => 'ListingPriceChanged'), 'match' => 'reduced'),
        // Search New Listings
        array('name' => 'New Listings', 'form_field' => 'search_new', 'idx_field' => 'timestamp_created', 'match' => 'morethaninterval'),
        // Search Open Houses
        array('name' => 'Open Houses', 'form_field' => 'search_has_openhouse', 'idx_field' => 'HasOpenHouse', 'match' => 'equals'),
    );

    if (!empty($filter)) {
        $filter = !is_array($filter) ? explode(',', str_replace(' ', '', trim($filter))) : $filter;
        foreach ($IDX_SEARCH_FIELDS as $k => $v) {
            if (in_array($v['idx_field'], $filter) === $comparison) {
                unset($IDX_SEARCH_FIELDS[$k]);
            } else {
                $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
                unset($IDX_SEARCH_FIELDS[$k]);
            }
        }
    } else {
        foreach ($IDX_SEARCH_FIELDS as $k => $v) {
            $IDX_SEARCH_FIELDS[$v['form_field']] = $IDX_SEARCH_FIELDS[$k];
            unset($IDX_SEARCH_FIELDS[$k]);
        }
    }

    // Search Location
    $search_location = [
        array('name' => 'Search Location', 'form_field' => 'search_location', 'idx_field' => 'AddressCity',        'match' => 'like', 'group' => 'location'),
        array('name' => 'Search Location', 'form_field' => 'search_location', 'idx_field' => 'AddressSubdivision', 'match' => 'like', 'group' => 'location'),
        array('name' => 'Search Location', 'form_field' => 'search_location', 'idx_field' => 'AddressZipCode',     'match' => 'beginslike', 'group' => 'location'),
        array('name' => 'Search Location', 'form_field' => 'search_location', 'idx_field' => 'ListingMLS',         'match' => 'equals', 'group' => 'location'),
        array('name' => 'Search Location', 'form_field' => 'search_location', 'idx_field' => 'Address',            'match' => 'like', 'group' => 'location'),
    ];

    $search_fields = array_merge($search_fields, $search_location, $IDX_SEARCH_FIELDS);

    return $search_fields;
}
