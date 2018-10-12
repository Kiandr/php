<?php

require $this->locateFile('module.php', __FILE__);

if (strpos($placeholder, '/blank.gif') !== false) {
    $placeholder = preg_replace('#/thumbs/([0-9]{1,4})?x([0-9]{1,4})?/([fr]/)?#', '/', $placeholder);
}

if (!empty($community['search_criteria'])) {
    // Call snippet outside $SAVE_REQUEST
    $_REQUEST['snippet'] = true;

    // loading idx-search module with predefined conditions
    $SAVE_REQUEST = $_REQUEST;

    // Set sort order
    if (!empty($community['search_criteria']['sort_by'])) {
        list ($community['search_criteria']['sort'], $community['search_criteria']['order']) = explode("-", $community['search_criteria']['sort_by'], 2);
    }

    if (is_array($community['search_criteria'])) {
        $_REQUEST['snippet_price_table'] = ($community['search_criteria']['price_ranges'] == 'true');
        $_REQUEST = array_merge($_REQUEST, $community['search_criteria']);
    }

    $idx_search = $this->getContainer()->getPage()->load('idx', 'search');
    $_REQUEST = $SAVE_REQUEST;

    $idx_results = $idx_search['category_html'];
}
