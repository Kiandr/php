<?php

// Check for Team Management
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
if (isset($_GET['id'])) {
    $team = Backend_Team::load($_GET['id']);
}

// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Team\SubdomainAuth::class);

/* Throw Missing Team Exception */
if (empty($team) || !$team instanceof Backend_Team) {
    throw new \REW\Backend\Exceptions\MissingId\MissingTeamException();
}

// Authorized to Manage All Teams
if (!$teamAuth->canManageTeams($authuser)) {
    // Authorized to Manage Own Teams
    if (!$teamAuth->canViewTeams($authuser) && $team->getPrimaryAgent() == $authuser->info('id')) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit this team')
        );
    }
}

// Authorized to Assign/Unassign/Edit Team Members
if ($teamAuth->canManageTeams($authuser) ||
    ($teamAuth->canManageOwn($authuser)
    && $team->getPrimaryAgent() == $authuser->info('id'))) {
    $can_feature = true;
    $can_edit = true;
    $can_delete = true;
}

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Get Agents Sharing with this Team
$agentCollection = $team->getAgentCollection()
    ->filterByGrantingPermissions([Backend_Team::PERM_SHARE_FEATURE_LISTINGS])
    ->cache();
$agents = $agentCollection->getAllAgents();
$agents_idx_ids = $agentCollection->getAllIdxAgentIds();
$listingCollection = $team->getListingCollection($agentCollection)->cache()->loadFeaturedAgents();

$cms_listings_count = $listingCollection->countCms();
$idx_listings_count = $listingCollection->countIdx();

// Get Total Listing Count, Page Limit & Currenty Page
$listings_count = $cms_listings_count + array_sum($idx_listings_count);
$page_limit = isset($_GET['limit']) && $_GET['limit'] < 25 ? $_GET['limit'] : 25;
$current_page = isset($_GET['p']) && ($_GET['p'] > 0) ? $_GET['p'] : 1;
$previous_source = 0;

// Limit Values
$limitvalue = ($current_page * $page_limit) - $page_limit;
$limitvalue = ($limitvalue > 0) ? $limitvalue : 0;

// Manage CMS Listings
$listings = array();

if (!empty($cms_listings_count)) {
    if ($cms_listings_count > $limitvalue) {
        $listings = array_merge($listings, $listingCollection->getCmsResults($page_limit, $limitvalue));
    }

    // Increment LimitValue
    $limitvalue -= $cms_listings_count;
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
}

// Manage IDX Listings
if (!empty($idx_listings_count)) {
    foreach ($idx_listings_count as $feed => $idx_listing_count) {
        if (!empty($idx_listing_count) && count($listings) <= $page_limit) {
            if ($idx_listing_count > $limitvalue) {
                // SQL Limit
                $remaining_page_limit = $page_limit-count($listings);
                $listings = array_merge($listings, $listingCollection->getIdxResults(
                    $feed,
                    $remaining_page_limit,
                    $limitvalue
                ));
            }

            // Increment LimitValue
            $limitvalue -= $idx_listing_count;
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        }
    }
}

// Generate Pagination
$pagination = generate_pagination($listings_count, $_GET['p'], $page_limit, $query_string);

// Get Primary Agent
$owning_agent = Backend_Agent::load($team->getPrimaryAgent());
