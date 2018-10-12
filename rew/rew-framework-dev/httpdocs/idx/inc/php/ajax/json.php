<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DBInterface;

// Include IDX Configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Backend User
$backend_user = Auth::get();
if (!$backend_user->isValid()) {
    unset($backend_user);
}

$container = Container::getInstance();
$settings = $container->get(SettingsInterface::class);

// JSON Data
$json = array();

// Process ID
$_POST['pid'] = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
if (!empty($_POST['pid'])) {
    $json['pid'] = $_POST['pid'];
}

/**
 * Load Module
 */
if (isset($_GET['module']) && !empty($_GET['module'])) {
    // Module Options
    $options = isset($_POST['options']) ? $_POST['options'] : $_GET['options'];
    $options = is_array($options) ? $options : array();

    // Set AJAX
    $options['ajax'] = true;

    // Create Page
    $container = Container::getInstance();
    $page = $container->get(PageInterface::class);

    // Load Module
    $module = null;
    if ($container->has($_GET['module'])) {
        // Load the already-built module, if possible. This is a minor efficiency improvement so that 2 modules don't
        // get created for external packages, but, it is also the only way that said external packages can access
        // options other than superglobals or having a separate controller for ajax and html.
        $module = $container->get($_GET['module']);
        if ($module instanceof ModuleInterface && $module->getId() == $_GET['module']) {
            foreach ($options as $key => $val) {
                $module->config($key, $val);
            }
        } else {
            $module = null;
        }
    }
    if (!$module) {
        $module = $container->make(ModuleInterface::class, ['id' => $_GET['module'], 'config' => $options]);
    }
    $module = $page->container('ajax')->module($module, $options);

    // Module CSS, JS & HTML
    $module = array(
        'uid'       => $module->getUID(),
        'stylesheet' => $module->css(),
        'javascript' => $module->javascript(),
        'content'   => $module->display(false)
    );

    // Minify HTML
    if (!empty($module['content'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_HTML'])) {
            $module['content'] = Minify_HTML::minify($module['content']);
        }
    } else {
        unset($module['content']);
    }

    // Minify CSS
    if (!empty($module['stylesheet'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_CSS'])) {
            $module['stylesheet'] = Minify_CSS::minify($module['stylesheet']);
        }
    } else {
        unset($module['stylesheet']);
    }

    // Minify Javascript
    if (!empty($module['javascript'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_JS'])) {
            $module['javascript'] = JSMin::minify($module['javascript']);
        }
    } else {
        unset($module['javascript']);
    }

    // Return Module
    $json['module'] = $module;
}

/**
 * Paginate Listing
 */
if (isset($_GET['paginateListing'])) {
    try {
        // Require $_POST data
        if (empty($_POST['mls_number']) || empty($_POST['feed'])) {
            throw new UnexpectedValueException('Invalid request');
        }

        // Get idx feed
        $idx = Util_IDX::getIdx();

        // Get database connection
        $db_idx = Util_IDX::getDatabase();

        // MLS field name
        $mls = $idx->field('ListingMLS');

        // Require valid IDX listing
        $sql_where = "`" . $mls . "` = '" . $db_idx->cleanInput($_POST['mls_number']) . "'";

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Require listing
        $listing = $db_idx->fetchQuery("SELECT `" . $mls . "` FROM `" . $idx->getTable() . "` WHERE " . $sql_where . " LIMIT 1;");
        if (empty($listing)) {
            throw new UnexpectedValueException('Listing not found');
        }

        // Get last search from session
        $last_search = $_SESSION['last_search'];
        session_write_close();

        // Listing pagination links
        $paginate = Util_IDX::paginateListing($idx, $db_idx, $listing[$mls], $last_search, isset($_POST['bounds']));
        if (!empty($paginate['next'])) {
            $json['next'] = str_replace(Settings::getInstance()->URLS['URL'], '/', $paginate['next']);
        }
        if (!empty($paginate['prev'])) {
            $json['prev'] = str_replace(Settings::getInstance()->URLS['URL'], '/', $paginate['prev']);
        }

    // Error occurred
    } catch (UnexpectedValueException $e) {
        $json['error'] = $e->getMessage();
    }
}

/**
 * Auto Complete
 */
if (!empty($_REQUEST['q'])) {
    // We do not need the session - close it!
    session_write_close();

    // Result Limit
    $limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : false;

    // Available Options
    $json['options'] = array();

    /**
     * Search by City
     */
    if (isset($_REQUEST['search']) && ($_REQUEST['search'] == 'search_city' || $_REQUEST['search'] == 'search_location')) {
        // Available Cities
        $city = IDX_Panel::get('City');
        $options = $city->getOptions();
        foreach ($options as $option) {
            if (preg_match("#^" . preg_quote($_REQUEST['q'], '#') . "#i", $option['title']) == 1) {
                $json['options'][] = $option;
                if (!empty($limit) && count($json['options']) >= $limit) {
                    break;
                }
            }
        }
    }

    /**
     * Search by Subdivision
     */
    if (isset($_REQUEST['search']) && ($_REQUEST['search'] == 'search_subdivision' || $_REQUEST['search'] == 'search_location')) {
        // Subdivisions
        $field = $idx->field('AddressSubdivision');

        // Limit to City
        $sql_where = array();
        if (!empty($_REQUEST['search_city'])) {
            if (is_array($_REQUEST['search_city'])) {
                foreach ($_REQUEST['search_city'] as $city) {
                    $sql_where[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($city) . "'";
                }
            } else {
                $sql_where[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($_REQUEST['search_city']) . "'";
            }
        }

        // SQL Extra
        $sql_where = "`" . $field . "` IS NOT NULL AND `" . $field . "` != '' AND `" . $field . "` LIKE '" . $db_idx->cleanInput($_REQUEST['q']) . "%'"
            . (!empty($sql_where) ? " AND (" . implode(" OR ", $sql_where) . ")" : "");

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Select Options
        $result = $db_idx->query("SELECT SQL_CACHE `" . $field . "` AS `value`, `" . $field . "` AS `title` FROM " . $idx->getTable() . " WHERE " . $sql_where . " GROUP BY `" . $field . "` ORDER BY `" . $field . "` LIMIT 50");
        while ($option = $db_idx->fetchArray($result)) {
            $option['title'] = ucwords(strtolower($option['title']));
            $json['options'][] = $option;
            if (!empty($limit) && count($json['options']) >= $limit) {
                break;
            }
        }
    }

    /**
     * Search by Address
     */
    if (isset($_REQUEST['search']) && (
            $_REQUEST['search'] == 'search_address' ||
            $_REQUEST['search'] == 'search_location')
        ) {
        // Search Address
        $field = $idx->field('Address');

        // Limit to City
        $sql_where = array();
        if (!empty($_REQUEST['search_city'])) {
            if (is_array($_REQUEST['search_city'])) {
                foreach ($_REQUEST['search_city'] as $city) {
                    $sql_where[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($city) . "'";
                }
            } else {
                $sql_where[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($_REQUEST['search_city']) . "'";
            }
        }

        // SQL Extra
        $sql_where = "`" . $field . "` IS NOT NULL AND `" . $field . "` != '' AND `" . $field . "` LIKE '" . $db_idx->cleanInput($_REQUEST['q']) . "%'"
            . (!empty($sql_where) ? " AND (" . implode(" OR ", $sql_where) . ")" : "");

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Load Results
        $result = $db_idx->query("SELECT SQL_CACHE `" . $field . "` AS `value`, `" . $field . "` AS `title` FROM " . $idx->getTable() . " WHERE " . $sql_where . " GROUP BY `" . $field . "` ORDER BY `" . $field . "` LIMIT 50;");
        while ($option = $db_idx->fetchArray($result)) {
            // Format Title
            $option['title'] = ucwords(strtolower($option['title']));

            // Add Option
            $json['options'][] = $option;

            // Limit Reached
            if (!empty($limit) && count($json['options']) >= $limit) {
                break;
            }
        }
    }

    /**
     * Search Schools
     */
    if (isset($_GET['search']) && stristr($_GET['search'], 'school') !== false) {
        // School Types
        $schools = array();
        $schools['search_school']       = array('field' => array('SchoolDistrict', 'SchoolElementary', 'SchoolMiddle', 'SchoolHigh'));
        $schools['school_district']     = array('field' => 'SchoolDistrict');
        $schools['school_elementary']   = array('field' => 'SchoolElementary');
        $schools['school_middle']       = array('field' => 'SchoolMiddle');
        $schools['school_high']         = array('field' => 'SchoolHigh');

        // School Type
        $school = $schools[$_GET['search']];
        if (!empty($school)) {
            // Bad Options
            $bad = "'', ' '";

            // Search Fields
            $fields = is_array($school['field']) ? $school['field'] : array($school['field']);
            foreach ($fields as $field) {
                // IDX Column
                $field = $idx->field($field);
                if (empty($field)) {
                    continue;
                }

                // Search Options
                $sql_where = array();

                // Limit to City
                if (!empty($_REQUEST['search_city'])) {
                    if (is_array($_REQUEST['search_city'])) {
                        $sql_or = array();
                        foreach ($_REQUEST['search_city'] as $city) {
                            $sql_or[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($city) . "'";
                        }
                        if (!empty($sql_or)) {
                            $sql_where[] = '(' . implode(' OR ', $sql_or) . ')';
                        }
                    } else {
                        $sql_where[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($_REQUEST['search_city']) . "'";
                    }
                }

                // Search by Query
                if (!empty($_GET['q'])) {
                    $sql_where[] = "`" . $field . "` LIKE '" . $db_idx->cleanInput($_GET['q']) . "%'";
                }

                // Not in Badlist
                $sql_where[] = "`" . $field . "` NOT IN (" . $bad . ")";

                // Field is non-NULL and non-empty
                $sql_where[] = "`" . $field . "` IS NOT NULL AND `" . $field . "` != ''";

                // Any global criteria
                $idx->executeSearchWhereCallback($sql_where);

                // Load Options
                $query = "SELECT `" . $field . "` FROM `" . $idx->getTable() . "` WHERE "
                    . implode(' AND ', $sql_where)
                    . " GROUP BY `" . $field . "`"
                    . " ORDER BY `" . $field . "`"
                    . (!empty($limit) ? ' LIMIT ' . $limit : '')
                . ";";

                if ($result = $db_idx->query($query)) {
                    while ($option = $db_idx->fetchArray($result)) {
                        $json['options'][] = array('value' => $option[$field], 'title' => ucwords(strtolower($option[$field])));
                        if (!empty($limit) && count($json['options']) >= $limit) {
                            break 2; // break both loops
                        }
                    }
                }
            }
        }
    }

    /**
     * Search Auto-Complete
     */
    $autocomplete = array();
    $autocomplete[] = array('search' => array('search_office'),     'field' => 'ListingOffice');
    $autocomplete[] = array('search' => array('office_id'),         'field' => 'ListingOfficeID');
    $autocomplete[] = array('search' => array('search_agent'),      'field' => 'ListingAgent');
    $autocomplete[] = array('search' => array('agent_id'),          'field' => 'ListingAgentID');
    $autocomplete[] = array('search' => array('search_area'),       'field' => 'AddressArea');
    $autocomplete[] = array('search' => array('search_county'),     'field' => 'AddressCounty');
    $autocomplete[] = array('search' => array('search_zip', 'search_location'), 'field' => 'AddressZipCode');
    $autocomplete[] = array('search' => array('search_mls', 'search_location'), 'field' => 'ListingMLS');
    if (!empty($_REQUEST['search'])) {
        foreach ($autocomplete as $ac) {
            if (in_array($_REQUEST['search'], $ac['search'])) {
                $field = $idx->field($ac['field']);
                if (!empty($field)) {
                    $sql_where = "`" . $field . "` IS NOT NULL AND `" . $field . "` != '' AND `" . $field . "` LIKE '" . $db_idx->cleanInput($_REQUEST['q']) . "%'";

                    // Any global criteria
                    $idx->executeSearchWhereCallback($sql_where);

                    $result = $db_idx->query("SELECT SQL_CACHE `" . $field . "` AS `value`, `" . $field . "` AS `title` FROM " . $idx->getTable() . " WHERE " . $sql_where . " GROUP BY `" . $field . "` ORDER BY `" . $field . "` LIMIT 50");
                    while ($option = $db_idx->fetchArray($result)) {
                        $json['options'][] = $option;
                        if (!empty($limit) && count($json['options']) >= $limit) {
                            break 2;
                        }
                    }
                }
            }
        }
    }

    // Find MLS Listing by MLS # or Street Address
    if (isset($_REQUEST['search']) && $_REQUEST['search'] == 'search_listing') {
        $sql_where = "("
            . "`" . $idx->field('ListingMLS') . "` LIKE '" . $db_idx->cleanInput($_REQUEST['q']) . "%' OR "
            . "`" . $idx->field('Address') . "` LIKE '%" . $db_idx->cleanInput($_REQUEST['q']) . "%'"
            . ")";

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Build Query
        $query = "SELECT SQL_CACHE "
            . "`" . $idx->field('ListingMLS')           . "` AS `ListingMLS`, "
            . ($idx->field('ListingMLSNumber') ? "`" . $idx->field('ListingMLSNumber')           . "` AS `ListingMLSNumber`, " : "")
            . "`" . $idx->field('Address')              . "` AS `Address`, "
            . "`" . $idx->field('AddressCity')          . "` AS `AddressCity`, "
            . "`" . $idx->field('AddressState')         . "` AS `AddressState`, "
            . "`" . $idx->field('AddressZipCode')       . "` AS `AddressZipCode`, "
            . "`" . $idx->field('ListingPrice')         . "` AS `ListingPrice`, "
            . (!empty($idx->field('ListingImage')) ? "`" . $idx->field('ListingImage') . "` AS `ListingImage`," : "")
            . "`" . $idx->field('NumberOfBedrooms')     . "` AS `NumberOfBedrooms`, "
            . "`" . $idx->field('NumberOfBathrooms')    . "` AS `NumberOfBathrooms`, "
            . "`" . $idx->field('NumberOfSqFt')         . "` AS `NumberOfSqFt` "
            . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where
            . " GROUP BY `" . $idx->field('ListingMLS') . "`"
            . " ORDER BY `" . $idx->field('Address') . "`"
            . " LIMIT 50";

        // Get CMS DB
        $db = $container->get(DBInterface::class);

        // Select Options
        if ($result = $db_idx->query($query)) {
            // Put listings into array $idx and $db conflicts otherwise
            while ($option = $db_idx->fetchArray($result)) {
                if($option) {
                    $options[] = $option;
                }
            }

            // Use prepared listings array
            foreach ($options as $option) {
                if($idx->getLink() !== 'cms') {
                    $image = IDX_Feed::thumbUrl($option['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL);
                    $mls_number = $option['ListingMLS'];
                } else {
                    // Get cms listing image
                    $result = $db->fetch(
                        "SELECT CONCAT('/thumbs/84x64/uploads/', `file`) AS `image`
                        FROM `cms_uploads`
                        WHERE `type` = 'listing' AND `row` = :row ORDER BY `order` LIMIT 1;",
                        ['row' => $option['ListingMLS']]
                    );
                    $image = $result['image'];
                    $mls_number = $option['ListingMLSNumber'];
                }
                // Add Option
                $json['options'][] = array(
                    'value' => $option['ListingMLS'],
                    'title' => $option['Address'] . ' (' . Lang::write('MLS_NUMBER') . $mls_number .')',
                    'image' => $image,
                    'lines' => array(
                        // Line #1
                        '$' . Format::number($option['ListingPrice']) . ' - ' . implode(', ', array_filter(array(
                            $option['AddressCity'],
                            $option['AddressState']
                        ))) . ' ' . $option['AddressZipCode'],
                        // Line #2
                        implode(', ', array_filter(array(
                            (!empty($option['NumberOfBedrooms'])    ? Format::number($option['NumberOfBedrooms'])       . ' Beds'       : ''),
                            (!empty($option['NumberOfBathrooms'])   ? Format::number($option['NumberOfBathrooms'], 1)   . ' Baths'      : ''),
                            (!empty($option['NumberOfSqFt'])        ? Format::number($option['NumberOfSqFt'])           . ' Sq. Ft.'    : ''),
                            (!empty($option['NumberOfAcres'])       ? Format::number($option['NumberOfAcres'], 2)       . ' Acres'      : '')
                        )))
                    )
                );

                // Stop after $limit Reached
                if (!empty($limit) && count($json['options']) >= $limit) {
                    break;
                }
            }
        }
    }
}

/**
 * Search Sub-Types
 */
if (isset($_REQUEST['searchTypes'])) {
    // Close session
    @session_write_close();

    $subtype = IDX_Panel::get('Subtype', array('placeholder' => false));
    if ($subtype && $subtype->isAvailable()) {
        $json['options'] = $subtype->getOptions();
        $json['returnCode'] = 200;
    }
}

/**
 * Search Count
 */
if (isset($_REQUEST['searchCount'])) {
    // Close session
    @session_write_close();

    // Select Requested IDX
    if (!empty($criteria['idx'])) {
        $idx = Util_IDX::getIdx($criteria['idx']);
        $db_idx = Util_IDX::getDatabase($criteria['idx']);
    }

    // Build Query
    $search_vars = $idx->buildWhere($idx, $db_idx, 't1');
    $search_where = $search_vars['search_where'];

    // Suggested Search Title
    $json['suggested_title'] = $search_vars['search_title'];

    // WHERE Queries
    $search_where = !empty($search_where) ? array($search_where) : array();

    // HAVING Queries
    $search_having = array();

    // Search Group
    $search_group = array();

    // Latitude / Longitude Columns
    $col_latitude   = "`t1`.`" . $idx->field('Latitude') . "`";
    $col_longitude  = "`t1`.`" . $idx->field('Longitude') . "`";

    // Search in Bounds
    if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
        $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
    }

    // Search in Radiuses
    $radiuses = $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

    // Search in Polygons
    $polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
    if (!empty($polygons)) {
        $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
    }

    // Add to Search Criteria
    if (!empty($search_group)) {
        $search_where[] = '(' . implode(' OR ', $search_group) . ')';
    }

    // Search Where
    $search_where = (!empty($search_where) ? " WHERE " . implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

    // Generate Count Query
    if ($idx->getLink() == 'cms' || empty($polygons)) {
        $count_query = "SELECT SQL_CACHE COUNT(*) AS `total` FROM `" . $idx->getTable() . "` `t1`" . $search_where . ";";
    } else {
        $count_query = "SELECT SQL_CACHE COUNT(*) AS total"
            . " FROM (SELECT `t1`.`" . $idx->field('ListingMLS') . "` AS `total`, `t2`.`Point`"
                . " FROM `" . $idx->getTable() . "` `t1`"
                . " JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
                . $search_where
            . ") AS `listings`;";
    }

    // Result Count
    $json['count'] = 0;
    if ($result = $db_idx->query($count_query)) {
        $count = $db_idx->fetchArray($result);
        $json['count'] = (int) $count['total'];

    // Query Error
    } else {
        //$json['query'] = $count_query;
        $json['error'] = $db_idx->error();
    }
}

/**
 * Save Search
 */
if (isset($_GET['saveSearch'])) {
    // Agent Acting as Lead
    if (!empty($_POST['lead_id'])) {
        $user_id = $_POST['lead_id'];
        $user_hook = $db->getCollection('users')->getRow($user_id);

        // Must be Logged In
        if (empty($backend_user) || (!$backend_user->isAgent() && !$backend_user->isAssociate())) {
            $json['error'] = 'You must be logged in to create a save search.';
        }

    // Logged In User
    } else {
        $user_id = $user->user_id();
        $user_hook = $user->getRow();
    }

    // Require User
    if (empty($user_id)) {
        // Error Message
        $json['error'] = 'You must be logged in to save this search.';

        // Redirect to Registration Form
        $json['register'] = true;
        $json['redirect'] = Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'];

        // Save Search in Session (To be used after register/login)
        $_POST['trigger'] = true;
        $_SESSION['saveSearch'] = json_encode($_POST);

    // Require Search Title
    } else if (empty($_POST['search_title'])) {
        $json['error'] = 'You must provide a title to save this search.';
    }

    // Check Error
    if (empty($json['error'])) {
        // Result Sort
        if (!empty($_POST['sort_by'])) {
            list($_POST['sort'], $_POST['order']) = explode('-', $_POST['sort_by']);
        }

        // Map Search Criteria
        if ($_POST['search_by'] == 'map') {
            parse_str($_POST['criteria'], $post);
            $post['search_by'] = 'map';
            $post['feed'] = isset($post['feed']) ? $post['feed'] : $_POST['feed'];

        // Search Criteria
        } else {
            $post = $_POST;
        }

        // Suggested from Viewed Search
        $suggested = false;
        if (!empty($post['suggested'])) {
            // Locate Viewed Search
            $suggested = $db_users->fetchQuery("SELECT `id`, `agent_id`, `associate` FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($post['suggested']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "';");
            if (!empty($suggested)) {
                // Delete Viewed Search (No Longer Needed)
                $db_users->query("DELETE FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($suggested['id']) . "';");
            }
            unset($post['suggested']);
        }

        // Unset undesirable criteria
        $search_actions = array('create_search', 'edit_search');
        
        foreach ($search_actions as $search_action) {
            unset($post[$search_action]);
        }

        // Generate Snippet
        ksort($post);
        $criteria = serialize($post);

        // Check duplicates
        $duplicate = $db_users->fetchQuery("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_SAVED_SEARCHES . "` WHERE"
            . "`title`		= '" . $db_users->cleanInput($_POST['search_title']) . "' AND "
            . "`user_id`	= '" . $db_users->cleanInput($user_id) . "' AND "
            . "`criteria`	= '" . $db_users->cleanInput($criteria) . "'"
        . ";");

        // Duplicate Exists
        if (!empty($duplicate['total'])) {
            $json['error'] = 'This exact search has already been saved.';
        } else {
            // Suggested/Saved by Agent/Associate
            $agent_id       = !empty($suggested['agent_id'])    ? $suggested['agent_id']    : (!empty($backend_user) && $backend_user->isAgent()        ? $backend_user->info('id') : false);
            $associate_id   = !empty($suggested['associate'])   ? $suggested['associate']   : (!empty($backend_user) && $backend_user->isAssociate()    ? $backend_user->info('id') : false);

            // Build INSERT Query
            $query = "INSERT INTO " . TABLE_SAVED_SEARCHES . " SET "
                . (!empty($suggested)       ? "`suggested`	= 'true', " : '')
                . (!empty($agent_id)        ? "`agent_id`	= '" . $db_users->cleanInput($agent_id)     . "', " : '')
                . (!empty($associate_id)    ? "`associate`	= '" . $db_users->cleanInput($associate_id) . "', " : '')
                . "`user_id`	= '" . $db_users->cleanInput($user_id) . "', "
                . "`title`		= '" . $db_users->cleanInput($_POST['search_title']) . "', "
                . "`frequency`	= '" . $db_users->cleanInput($_POST['frequency']) . "', "
                . "`criteria`	= '" . $db_users->cleanInput($criteria) . "', "
                . "`table`		= '" . $db_users->cleanInput($idx->getTable()) . "', "
                . "`idx`		= '" . $db_users->cleanInput($idx->getName()) . "', "
                . "`timestamp_created` = NOW()"
            . ";";

            // Save Search
            if ($db_users->query($query)) {
                // Insert ID
                $insert_id = $db_users->insert_id();

                // Success
                $json['success'] = 'Your search has successfully been saved.';

                // Increment Saved Search Count
                $db_users->query("UPDATE `" . TABLE_USERS . "` SET `num_saved` = `num_saved` + 1 WHERE `id` = '" . $user_id . "';");

                // Select Saved Search
                $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $insert_id . "';");

                // Search ID
                $json['search'] = $search['id'];

                // Log Event: Lead Saved Search
                $event = new History_Event_Action_SavedSearch(array(
                    'search' => $search
                ), array(
                    new History_User_Lead($user_id),
                    (!empty($backend_user) ? $backend_user->getHistoryUser() : null)
                ));

                // Save to DB
                $event->save();

                // Send Notification to Assigned Agent (If Saved by Lead)
                if (empty($_POST['lead_id']) && $user->info('notify_searches') == 'yes') {
                    // Get Assigned Agent
                    $agent = Backend_Agent::load($user->info('agent'));

                    // Setup Mailer
                    $mailer = new Backend_Mailer_SavedSearch(array(
                        'lead' => $user->getRow(),
                        'search' => $search
                    ));

                    // Check Incoming Notification Settings
                    $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_SEARCH_SAVED);

                    // Send Email
                    if (!empty($check)) {
                        $mailer->Send();
                    }
                }

                // Complete the backend user's pending save search tasks for this lead
                if (!empty($backend_user)) {
                    if ($backend_user->isAgent() || $backend_user->isAssociate()) {
                        $task_filters = [];
                        if ($backend_user->isAgent()) {
                            $task_filters[] = " `u`.`agent` = '" . $db_users->cleanInput($backend_user->info('id')) . "' ";
                            $task_filters[] = " `ut`.`performer` = 'Agent' ";
                        } else {
                            $task_filters[] = " `ut`.`performer` = 'Associate' ";
                        }
                        $task_filters[] = " `ut`.`type` = 'Search' ";
                        $task_filters[] = " `ut`.`status` = 'Pending' ";
                        $task_filters[] = " `u`.`id` = '" . $db_users->cleanInput($user_id) . "' ";
                        if ($query = $db_users->query(sprintf(
                            "SELECT "
                            . " `u`.`id` AS `user_id`, "
                            . " `ut`.`task_id` "
                            . " FROM `%s` `ut` "
                            . " LEFT JOIN `%s` `u` ON `u`.`id` = `ut`.`user_id` "
                            . " WHERE " . implode(" AND ", $task_filters),
                            $settings->TABLES['LM_USER_TASKS'],
                            $settings->TABLES['LM_LEADS']
                        ))) {
                            while ($task = $db_users->fetchArray($query)) {
                                $this_task = Backend_Task::load($task['task_id']);
                                $this_task->resolve(
                                    $task['user_id'],
                                    [
                                        'id' => $backend_user->info('id'),
                                        'type' => ($backend_user->isAgent() ? 'Agent' : 'Associate')
                                    ],
                                    'Completed'
                                );
                            }
                        }
                    }
                }

                if ($_POST['email_results_immediately'] == 'true') {
                    // Send listings email to lead
                    $savedSearchInstantEmail = Container::getInstance()->get(IDX_SavedSearch_InstantEmail::class);
                    if (!empty($backend_user)) {
                        // Saved search created by agent
                        if ($backend_user->isAgent()) {
                            $db_users->cleanInput($backend_user->info('id'));
                            $savedSearchInstantEmail->setData($search['id'], $db_users->cleanInput($backend_user->info('id')));
                        }
                    } else {
                        // Saved search created by lead
                        $savedSearchInstantEmail->setData($search['id']);
                    }
                    $savedSearchInstantEmail->sendEmail();
                    $json['success'] .= ' ' . implode('. ', $savedSearchInstantEmail->getSuccessMessages()) .
                        implode('. ', $savedSearchInstantEmail->getErrorMessages());
                }

                // Run hook
                Hooks::hook(Hooks::HOOK_LEAD_SEARCH_SAVED)->run($user_hook, $idx, unserialize($criteria), $_POST['search_title'], $_POST['frequency'], !empty($suggested));

            // Query Error
            } else {
                $json['error'] = 'An error occurred while attempting to save this search.';
            }
        }
    }
}

/**
 * Edit Search
 */
if (isset($_GET['editSearch'])) {
    // Agent Acting as Lead
    if (!empty($_POST['lead_id'])) {
        $user_id = $_POST['lead_id'];

        // Must be Logged In
        if (empty($backend_user) || (!$backend_user->isAgent() && !$backend_user->isAssociate())) {
            $json['error'] = 'You must be logged in to create a save search.';
        }

    // Logged In User
    } else {
        $user_id = $user->user_id();
    }

    // Locate Search
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($_POST['saved_search_id']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "';");

    // Require Search
    if (empty($search)) {
        $json['error'] = 'Saved search could not be found.';

    // Require Search Title
    } else if (empty($_POST['search_title'])) {
        $json['error'] = 'You must provide a title to save this search.';

    // Require Search Frequency
    } else if (empty($_POST['frequency'])) {
        $json['error'] = 'You must provide an update freqency for this saved search.';
    }

    // Check Error
    if (empty($json['error'])) {
        // Map Search Criteria
        if ($_POST['search_by'] == 'map') {
            parse_str($_POST['criteria'], $post);
            $post['search_by'] = 'map';

        // Search Criteria
        } else {
            $post = $_POST;
        }
        
        // Unset undesirable criteria
        $search_actions = array('create_search', 'edit_search');
        
        foreach ($search_actions as $search_action) {
            unset($post[$search_action]);
        }
        
        // Search Criteria
        $criteria = serialize($post);

        // Build UPDATE Query
        $query = "UPDATE " . TABLE_SAVED_SEARCHES . " SET "
            . "`title`		= '" . $db_users->cleanInput($_POST['search_title']) . "', "
            . "`frequency`	= '" . $db_users->cleanInput($_POST['frequency']) . "', "
            . "`criteria`	= '" . $db_users->cleanInput($criteria) . "', "
            . "`table`		= '" . $db_users->cleanInput($idx->getTable()) . "', "
            . "`idx`		= '" . $db_users->cleanInput($idx->getName()) . "', "
            . "`timestamp_created` = NOW()"
            . " WHERE `id` = '" . $db_users->cleanInput($search['id']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "'"
        . ";";

        // Save Search
        if ($db_users->query($query)) {
            $json['success'] = 'Your saved search has successfully been updated.';
            $json['search'] = $search['id'];

            // Compare Search Criteria
            $old = Util_IDX::parseCriteria(unserialize($search['criteria']), $search['idx']);
            $new = Util_IDX::parseCriteria($post, $idx->getName());
            if ($old !== $new) {
                // Send Notification to Assigned Agent (If Saved by Lead)
                if (empty($_POST['lead_id']) && $user->info('notify_searches') == 'yes') {
                    // Get Assigned Agent
                    $agent = Backend_Agent::load($user->info('agent'));

                    // Setup Mailer
                    $mailer = new Backend_Mailer_SavedSearch(array(
                        'lead'      => $user->getRow(),
                        'search'    => $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $search['id'] . "';"),
                        'updated'   => $search
                    ));

                    // Check Incoming Notification Settings
                    $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_SEARCH_SAVED);

                    // Send Email
                    if (!empty($check)) {
                        $mailer->Send();
                    }
                }
            }

            if ($_POST['email_results_immediately'] == 'true') {
                // Send listings email to lead
                $savedSearchInstantEmail = Container::getInstance()->get(IDX_SavedSearch_InstantEmail::class);
                if (!empty($backend_user)) {
                    // Saved search created by agent
                    if ($backend_user->isAgent()) {
                        $db_users->cleanInput($backend_user->info('id'));
                        $savedSearchInstantEmail->setData($db_users->cleanInput($_POST['saved_search_id']), $db_users->cleanInput($backend_user->info('id')));
                    }
                } else {
                    // Saved search created by lead
                    $savedSearchInstantEmail->setData($db_users->cleanInput($_POST['saved_search_id']));
                }
                $savedSearchInstantEmail->sendEmail();
                $json['emailMessages'] = implode('. ', $savedSearchInstantEmail->getSuccessMessages()) .
                    implode('. ', $savedSearchInstantEmail->getErrorMessages());
            }

        // Query Error
        } else {
            $json['error'] = 'An error occurred while attempting to update this saved search.';
        }
    }
}

/**
 * Delete Search
 */
if (isset($_GET['deleteSearch'])) {
    // Agent Acting as Lead
    if (!empty($_POST['lead_id'])) {
        $user_id = $_POST['lead_id'];

        // Must be Logged In
        if (empty($backend_user) || (!$backend_user->isAgent() && !$backend_user->isAssociate())) {
            $json['error'] = 'You must be logged in to delete a save search.';
        }

    // Logged In User
    } else {
        $user_id = $user->user_id();
    }

    // Require Search
    if (!$db_users->query("DELETE FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($_POST['saved_search_id']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "';")) {
        $json['error'] = 'An error occurred while attempting to delete this saved search.';
    } else if ($db_users->affected_rows() == 0) {
        $json['error'] = 'Saved search could not be found.';
    } else {
        $json['success'] = 'Your saved search has successfully been deleted.';
    }
}

// Return JSON Response
header('Content-Type: application/json');
die(json_encode($json));
