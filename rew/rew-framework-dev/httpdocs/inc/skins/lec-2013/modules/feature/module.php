<?php

// Page Instance
$page = $this->getContainer()->getPage();

// Show Search Form
$showSearch = $page->variable('showSearch');
if (!empty($showSearch)) {
    // IDX Quick Search
    $search = $page->container('snippet')->module('idx-search', array(
        'mode'      => 'quicksearch',
        'button'    => Lang::write('IDX_SEARCH_BUTTON'),
        'panels'    => array(
            'location' => array(
                'display'       => true,
                'toggle'        => false,
                'inputClass'    => 'autocomplete',
                'placeholder'   => 'City, ' . Locale::spell('Neighborhood') . ', ' . Locale::spell('ZIP') . ' or ' . Lang::write('MLS_NUMBER'),
            ),
            // Search by Price Range
            'price' => array(
                'display'       => true,
                'toggle'        => false
            )
        )
    ));

    // Search Options
    $this->config('idx-search', $search->getConfig());
}
